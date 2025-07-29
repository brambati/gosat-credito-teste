@extends('layouts.app')

@section('title', 'Consultar Crédito - GoSat Crédito')
@section('page-title', 'Consultar Crédito')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Formulário de Consulta -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-search me-2"></i>
                    Consulta de Ofertas de Crédito
                </h5>
            </div>
            <div class="card-body">
                <form id="consultaForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="cpf" class="form-label">CPF do Cliente</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" 
                                       placeholder="Digite apenas números (ex: 11111111111)" 
                                       maxlength="11" required>
                                <div class="form-text">Digite apenas os números do CPF (11 dígitos)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-lg w-100" id="btnConsultar">
                                    <i class="fas fa-search me-2"></i>
                                    Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Loading -->
                <div id="loading" class="text-center loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Consultando ofertas disponíveis...</p>
                </div>

                <!-- CPFs de Teste -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>CPFs disponíveis para teste:</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-outline-info btn-sm w-100 cpf-teste" data-cpf="11111111111">
                                111.111.111-11
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-info btn-sm w-100 cpf-teste" data-cpf="12312312312">
                                123.123.123.12
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-info btn-sm w-100 cpf-teste" data-cpf="22222222222">
                                222.222.222.22
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados -->
        <div id="resultados" class="d-none">
            <!-- Resumo -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Consulta Realizada com Sucesso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-primary" id="totalOfertas">0</div>
                                <small class="text-muted">Ofertas Encontradas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-success" id="ofertasSelecionadas">0</div>
                                <small class="text-muted">Melhores Ofertas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-info" id="cpfConsultado">-</div>
                                <small class="text-muted">CPF Consultado</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-warning" id="dataConsulta">-</div>
                                <small class="text-muted">Data/Hora</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ofertas -->
            <div id="listaOfertas"></div>
        </div>

        <!-- Erros -->
        <div id="erros" class="d-none">
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Erro na Consulta</h5>
                <p id="mensagemErro"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('consultaForm');
        const cpfInput = document.getElementById('cpf');
        const btnConsultar = document.getElementById('btnConsultar');
        const loading = document.getElementById('loading');
        const resultados = document.getElementById('resultados');
        const erros = document.getElementById('erros');

        // Máscara de CPF
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            e.target.value = value;
        });

        // Botões de CPF de teste
        document.querySelectorAll('.cpf-teste').forEach(btn => {
            btn.addEventListener('click', function() {
                cpfInput.value = this.dataset.cpf;
                cpfInput.focus();
            });
        });

        // Submit do formulário
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            consultarCredito();
        });

        async function consultarCredito() {
            const cpf = cpfInput.value.trim();

            if (cpf.length !== 11) {
                mostrarErro('CPF deve conter exatamente 11 dígitos');
                return;
            }

            // Mostrar loading
            loading.style.display = 'block';
            resultados.classList.add('d-none');
            erros.classList.add('d-none');
            btnConsultar.disabled = true;

            try {
                const response = await axios.post('/api/consultar-credito', { cpf });
                
                if (response.data.success) {
                    mostrarResultados(response.data.data);
                } else {
                    mostrarErro(response.data.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                const mensagem = error.response?.data?.message || 'Erro ao consultar ofertas de crédito';
                mostrarErro(mensagem);
            } finally {
                loading.style.display = 'none';
                btnConsultar.disabled = false;
            }
        }

        function mostrarResultados(data) {
            // Atualizar resumo
            document.getElementById('totalOfertas').textContent = data.total_ofertas_encontradas;
            document.getElementById('ofertasSelecionadas').textContent = data.ofertas_selecionadas;
            document.getElementById('cpfConsultado').textContent = formatarCPF(data.cpf);
            document.getElementById('dataConsulta').textContent = new Date().toLocaleString('pt-BR');

            // Gerar cards das ofertas
            const listaOfertas = document.getElementById('listaOfertas');
            listaOfertas.innerHTML = '';

            data.ofertas.forEach((oferta, index) => {
                const card = criarCardOferta(oferta, index + 1);
                listaOfertas.appendChild(card);
            });

            resultados.classList.remove('d-none');
        }

        function criarCardOferta(oferta, posicao) {
            const div = document.createElement('div');
            div.className = 'card mb-3';
            
            const badge = posicao === 1 ? 'bg-success' : posicao === 2 ? 'bg-warning' : 'bg-info';
            const icone = posicao === 1 ? 'fas fa-trophy' : posicao === 2 ? 'fas fa-medal' : 'fas fa-award';

            div.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="${icone} me-2"></i>
                        ${oferta.instituicaoFinanceira}
                    </h6>
                    <span class="badge ${badge}">${posicao}ª Melhor Oferta</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Modalidade:</strong> ${oferta.modalidadeCredito}
                            </div>
                            <div class="mb-2">
                                <strong>Taxa de Juros:</strong> 
                                <span class="text-primary">${(oferta.taxaJuros * 100).toFixed(4)}% a.m.</span>
                            </div>
                            <div class="mb-2">
                                <strong>Score de Vantagem:</strong> 
                                <span class="text-info">${oferta.scoreVantagem}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Valor Solicitado:</strong> 
                                <span class="text-success">R$ ${formatarMoeda(oferta.valorSolicitado)}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Valor a Pagar:</strong> 
                                <span class="text-danger">R$ ${formatarMoeda(oferta.valorAPagar)}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Parcelas:</strong> ${oferta.qntParcelas}x
                            </div>
                        </div>
                    </div>
                </div>
            `;

            return div;
        }

        function mostrarErro(mensagem) {
            document.getElementById('mensagemErro').textContent = mensagem;
            erros.classList.remove('d-none');
            resultados.classList.add('d-none');
        }

        function formatarCPF(cpf) {
            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        }

        function formatarMoeda(valor) {
            return parseFloat(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    });
</script>
@endsection 