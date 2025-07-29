@extends('layouts.app')

@section('title', 'Histórico - GoSat Crédito')
@section('page-title', 'Histórico de Consultas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Consultas Realizadas
                </h5>
                <div>
                    <span class="badge bg-primary">{{ $consultas->total() }} consultas</span>
                </div>
            </div>
            <div class="card-body">
                @if($consultas->count() > 0)
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="filtrarCPF" placeholder="Filtrar por CPF...">
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="filtrarData">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary" onclick="limparFiltros()">
                                <i class="fas fa-clear-all me-2"></i>
                                Limpar Filtros
                            </button>
                        </div>
                    </div>

                    <!-- Tabela de Consultas -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaHistorico">
                            <thead class="table-light">
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>CPF</th>
                                    <th>Total Ofertas</th>
                                    <th>Instituições</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultas as $consulta)
                                <tr data-cpf="{{ $consulta->cpf }}" data-data="{{ $consulta->created_at->format('Y-m-d') }}">
                                    <td>
                                        <div class="fw-bold">{{ $consulta->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $consulta->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <code>{{ $consulta->cpf }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $consulta->total_ofertas }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($consulta->ofertas->groupBy('instituicao_nome') as $instituicao => $ofertas)
                                                <span class="badge bg-outline-secondary">{{ $instituicao }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="verDetalhes({{ $consulta->id }})">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver Detalhes
                                        </button>
                                    </td>
                                </tr>

                                <!-- Linha de detalhes (inicialmente oculta) -->
                                <tr id="detalhes-{{ $consulta->id }}" class="table-secondary d-none">
                                    <td colspan="5">
                                        <div class="p-3">
                                            <h6><i class="fas fa-credit-card me-2"></i>Ofertas Encontradas:</h6>
                                            
                                            @if($consulta->ofertas->count() > 0)
                                                <div class="row">
                                                    @foreach($consulta->ofertas->sortBy('score_vantagem')->take(3) as $index => $oferta)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="card card-sm">
                                                            <div class="card-header py-2">
                                                                <div class="d-flex justify-content-between">
                                                                    <small class="fw-bold">{{ $oferta->instituicao_nome }}</small>
                                                                    @if($index === 0)
                                                                        <span class="badge bg-success">Melhor</span>
                                                                    @elseif($index === 1)
                                                                        <span class="badge bg-warning">2ª</span>
                                                                    @else
                                                                        <span class="badge bg-info">3ª</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="card-body py-2">
                                                                <div class="small">
                                                                    <div><strong>Modalidade:</strong> {{ $oferta->modalidade_credito }}</div>
                                                                    <div><strong>Taxa:</strong> {{ number_format($oferta->taxa_juros * 100, 4) }}% a.m.</div>
                                                                    <div><strong>Valor:</strong> R$ {{ number_format($oferta->valor_solicitado, 2, ',', '.') }}</div>
                                                                    <div><strong>Parcelas:</strong> {{ $oferta->qnt_parcelas }}x</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">Nenhuma oferta foi processada para esta consulta.</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="d-flex justify-content-center">
                        {{ $consultas->links() }}
                    </div>
                @else
                    <!-- Estado vazio -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma consulta encontrada</h5>
                        <p class="text-muted">Realize sua primeira consulta de crédito.</p>
                        <a href="{{ route('dashboard.consulta') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Nova Consulta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Detalhes da Consulta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="conteudoModal">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtrarCPF = document.getElementById('filtrarCPF');
        const filtrarData = document.getElementById('filtrarData');
        const tabela = document.getElementById('tabelaHistorico');

        // Filtro por CPF
        if (filtrarCPF) {
            filtrarCPF.addEventListener('input', function() {
                filtrarTabela();
            });
        }

        // Filtro por data
        if (filtrarData) {
            filtrarData.addEventListener('change', function() {
                filtrarTabela();
            });
        }

        function filtrarTabela() {
            const cpfFiltro = filtrarCPF ? filtrarCPF.value.toLowerCase() : '';
            const dataFiltro = filtrarData ? filtrarData.value : '';
            const linhas = tabela.querySelectorAll('tbody tr:not([id^="detalhes"])');

            linhas.forEach(linha => {
                const cpf = linha.dataset.cpf || '';
                const data = linha.dataset.data || '';
                
                const cpfMatch = cpf.includes(cpfFiltro);
                const dataMatch = !dataFiltro || data === dataFiltro;

                if (cpfMatch && dataMatch) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                    // Ocultar também os detalhes se estiverem abertos
                    const detalhes = linha.nextElementSibling;
                    if (detalhes && detalhes.id.startsWith('detalhes')) {
                        detalhes.classList.add('d-none');
                    }
                }
            });
        }
    });

    function verDetalhes(consultaId) {
        const detalhes = document.getElementById(`detalhes-${consultaId}`);
        
        if (detalhes.classList.contains('d-none')) {
            // Fechar outros detalhes abertos
            document.querySelectorAll('[id^="detalhes-"]').forEach(el => {
                el.classList.add('d-none');
            });
            
            // Abrir este
            detalhes.classList.remove('d-none');
        } else {
            // Fechar
            detalhes.classList.add('d-none');
        }
    }

    function limparFiltros() {
        const filtrarCPF = document.getElementById('filtrarCPF');
        const filtrarData = document.getElementById('filtrarData');
        
        if (filtrarCPF) filtrarCPF.value = '';
        if (filtrarData) filtrarData.value = '';

        // Mostrar todas as linhas
        const linhas = document.querySelectorAll('#tabelaHistorico tbody tr:not([id^="detalhes"])');
        linhas.forEach(linha => {
            linha.style.display = '';
        });
    }
</script>
@endsection 