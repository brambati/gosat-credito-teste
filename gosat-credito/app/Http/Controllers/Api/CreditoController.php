<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Oferta;
use App\Models\Instituicao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="GoSat Crédito API",
 *     version="1.0.0",
 *     description="API para consulta de ofertas de crédito"
 * )
 */
class CreditoController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/consultar-credito",
     *     summary="Consulta ofertas de crédito para um CPF",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cpf", type="string", example="11111111111")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ofertas encontradas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function consultarCredito(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'cpf' => 'required|string|size:11'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPF inválido. Deve conter 11 dígitos.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $cpf = $request->cpf;

            // Validar se é um dos CPFs permitidos
            $cpfsPermitidos = ['11111111111', '12312312312', '22222222222'];
            if (!in_array($cpf, $cpfsPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPF não encontrado na base de dados.',
                    'cpfs_disponiveis' => $cpfsPermitidos
                ], 404);
            }

            // 1. Consultar ofertas de crédito disponíveis
            $ofertasDisponiveis = $this->consultarOfertasDisponiveis($cpf);
            
            if (!$ofertasDisponiveis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao consultar ofertas de crédito.'
                ], 500);
            }

            // 2. Salvar consulta
            $consulta = Consulta::create([
                'cpf' => $cpf,
                'resposta_api' => $ofertasDisponiveis,
                'total_ofertas' => count($ofertasDisponiveis['instituicoes'] ?? [])
            ]);

            // 3. Processar ofertas e simular cada uma
            $ofertasProcessadas = $this->processarOfertas($cpf, $ofertasDisponiveis, $consulta->id);

            // 4. Retornar até 3 melhores ofertas
            $melhoresOfertas = $this->selecionarMelhoresOfertas($ofertasProcessadas);

            return response()->json([
                'success' => true,
                'data' => [
                    'cpf' => $cpf,
                    'total_ofertas_encontradas' => count($ofertasProcessadas),
                    'ofertas_selecionadas' => count($melhoresOfertas),
                    'ofertas' => $melhoresOfertas
                ],
                'message' => 'Ofertas consultadas com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao consultar crédito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor.'
            ], 500);
        }
    }

    /**
     * Consulta ofertas disponíveis na API externa
     */
    private function consultarOfertasDisponiveis(string $cpf): ?array
    {
        try {
            $response = Http::timeout(30)->post('https://dev.gosat.org/api/v1/simulacao/credito', [
                'cpf' => $cpf
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erro na API externa - Consultar ofertas', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao chamar API externa: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Simula uma oferta específica
     */
    private function simularOferta(string $cpf, int $instituicaoId, string $codModalidade): ?array
    {
        try {
            $response = Http::timeout(30)->post('https://dev.gosat.org/api/v1/simulacao/oferta', [
                'cpf' => $cpf,
                'instituicao_id' => $instituicaoId,
                'codModalidade' => $codModalidade
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erro na API externa - Simular oferta', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao simular oferta: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Processa todas as ofertas encontradas
     */
    private function processarOfertas(string $cpf, array $ofertasDisponiveis, int $consultaId): array
    {
        $ofertasProcessadas = [];

        foreach ($ofertasDisponiveis['instituicoes'] ?? [] as $instituicao) {
            // Salvar/atualizar instituição
            Instituicao::updateOrCreate(
                ['instituicao_id' => $instituicao['id']],
                [
                    'nome' => $instituicao['nome'],
                    'modalidades' => $instituicao['modalidades']
                ]
            );

            // Processar cada modalidade
            foreach ($instituicao['modalidades'] ?? [] as $modalidade) {
                $simulacao = $this->simularOferta($cpf, $instituicao['id'], $modalidade['cod']);
                
                if ($simulacao) {
                    $oferta = $this->criarOferta($cpf, $consultaId, $instituicao, $modalidade, $simulacao);
                    if ($oferta) {
                        $ofertasProcessadas[] = $oferta;
                    }
                }
            }
        }

        return $ofertasProcessadas;
    }

    /**
     * Cria uma oferta processada
     */
    private function criarOferta(string $cpf, int $consultaId, array $instituicao, array $modalidade, array $simulacao): ?Oferta
    {
        try {
            // Calcular score de vantagem (menor taxa de juros = melhor)
            $scoreVantagem = $this->calcularScoreVantagem($simulacao);

            $oferta = Oferta::create([
                'consulta_id' => $consultaId,
                'cpf' => $cpf,
                'instituicao_id' => $instituicao['id'],
                'instituicao_nome' => $instituicao['nome'],
                'modalidade_credito' => $modalidade['nome'],
                'cod_modalidade' => $modalidade['cod'],
                'valor_pagar' => $simulacao['valorMax'] ?? 0,
                'valor_solicitado' => $simulacao['valorMin'] ?? 0,
                'taxa_juros' => $simulacao['jurosMes'] ?? 0,
                'qnt_parcelas' => $simulacao['qntParcelaMax'] ?? 0,
                'score_vantagem' => $scoreVantagem
            ]);

            return $oferta;
        } catch (\Exception $e) {
            Log::error('Erro ao criar oferta: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcula score de vantagem para ordenação
     * Score mais baixo = melhor oferta
     */
    private function calcularScoreVantagem(array $simulacao): float
    {
        $taxaJuros = $simulacao['jurosMes'] ?? 0;
        $valorMax = $simulacao['valorMax'] ?? 1;
        $qntParcelas = $simulacao['qntParcelaMax'] ?? 1;
        
        // Fórmula: Taxa de juros tem peso maior, seguido de parcelas
        // Quanto menor o score, melhor a oferta
        $score = ($taxaJuros * 100) + ($qntParcelas * 0.1) + (1 / max($valorMax, 1) * 1000);
        
        return round($score, 4);
    }

    /**
     * Seleciona as 3 melhores ofertas
     */
    private function selecionarMelhoresOfertas(array $ofertas): array
    {
        // Ordenar por score de vantagem (menor = melhor)
        usort($ofertas, function($a, $b) {
            return $a->score_vantagem <=> $b->score_vantagem;
        });

        // Pegar apenas as 3 primeiras
        $melhoresOfertas = array_slice($ofertas, 0, 3);

        // Formatar para retorno
        return array_map(function($oferta) {
            return [
                'instituicaoFinanceira' => $oferta->instituicao_nome,
                'modalidadeCredito' => $oferta->modalidade_credito,
                'valorAPagar' => $oferta->valor_pagar,
                'valorSolicitado' => $oferta->valor_solicitado,
                'taxaJuros' => $oferta->taxa_juros,
                'qntParcelas' => $oferta->qnt_parcelas,
                'scoreVantagem' => $oferta->score_vantagem
            ];
        }, $melhoresOfertas);
    }

    /**
     * @OA\Get(
     *     path="/api/historico/{cpf}",
     *     summary="Consulta histórico de consultas por CPF",
     *     @OA\Parameter(
     *         name="cpf",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Histórico encontrado com sucesso"
     *     )
     * )
     */
    public function historico(string $cpf): JsonResponse
    {
        $consultas = Consulta::where('cpf', $cpf)
                            ->with('ofertas')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $consultas,
            'message' => 'Histórico consultado com sucesso!'
        ]);
    }
}
