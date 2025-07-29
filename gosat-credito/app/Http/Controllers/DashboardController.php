<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Oferta;
use App\Models\Instituicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Página inicial do dashboard
     */
    public function index()
    {
        $estatisticas = [
            'total_consultas' => Consulta::count(),
            'total_ofertas' => Oferta::count(),
            'total_instituicoes' => Instituicao::count(),
            'consultas_hoje' => Consulta::whereDate('created_at', today())->count()
        ];

        return view('dashboard.index', compact('estatisticas'));
    }

    /**
     * Página de consulta de crédito
     */
    public function consulta()
    {
        return view('dashboard.consulta');
    }

    /**
     * Página de relatórios
     */
    public function relatorios()
    {
        // Dados para gráficos
        $consultasPorDia = Consulta::select(
                DB::raw('DATE(created_at) as data'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('data')
            ->orderBy('data', 'desc')
            ->limit(30)
            ->get();

        $ofertasPorInstituicao = Oferta::select(
                'instituicao_nome',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('instituicao_nome')
            ->orderBy('total', 'desc')
            ->get();

        $modalidadesMaisUsadas = Oferta::select(
                'modalidade_credito',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('modalidade_credito')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.relatorios', compact(
            'consultasPorDia',
            'ofertasPorInstituicao',
            'modalidadesMaisUsadas'
        ));
    }

    /**
     * Página de histórico
     */
    public function historico()
    {
        $consultas = Consulta::with('ofertas')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.historico', compact('consultas'));
    }
}
