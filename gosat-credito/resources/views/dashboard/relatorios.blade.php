@extends('layouts.app')

@section('title', 'Relatórios - GoSat Crédito')
@section('page-title', 'Relatórios')

@section('content')
<div class="row">
    <!-- Gráfico de Consultas por Dia -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Consultas por Dia (Últimos 30 dias)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="consultasPorDiaChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Estatísticas Gerais
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-primary mb-1">{{ $consultasPorDia->sum('total') }}</div>
                            <small class="text-muted">Total de Consultas</small>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-success mb-1">{{ $ofertasPorInstituicao->sum('total') }}</div>
                            <small class="text-muted">Total de Ofertas</small>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-info mb-1">{{ $ofertasPorInstituicao->count() }}</div>
                            <small class="text-muted">Instituições Ativas</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <div class="h4 text-warning mb-1">
                                {{ $consultasPorDia->count() > 0 ? round($ofertasPorInstituicao->sum('total') / $consultasPorDia->sum('total'), 1) : 0 }}
                            </div>
                            <small class="text-muted">Ofertas por Consulta</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Ofertas por Instituição -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Ofertas por Instituição
                </h5>
            </div>
            <div class="card-body">
                <canvas id="ofertasPorInstituicaoChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Modalidades Mais Usadas -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Modalidades Mais Procuradas
                </h5>
            </div>
            <div class="card-body">
                <canvas id="modalidadesChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabela de Resumo -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    Resumo Detalhado por Instituição
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Instituição</th>
                                <th>Total de Ofertas</th>
                                <th>Participação (%)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ofertasPorInstituicao as $instituicao)
                            <tr>
                                <td>
                                    <strong>{{ $instituicao->instituicao_nome }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ number_format($instituicao->total) }}</span>
                                </td>
                                <td>
                                    @php
                                        $percentual = $ofertasPorInstituicao->sum('total') > 0 
                                            ? round(($instituicao->total / $ofertasPorInstituicao->sum('total')) * 100, 1)
                                            : 0;
                                    @endphp
                                    {{ $percentual }}%
                                </td>
                                <td>
                                    <span class="badge bg-success">Ativo</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Nenhuma oferta encontrada ainda
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dados do PHP para JavaScript
        const consultasPorDia = @json($consultasPorDia);
        const ofertasPorInstituicao = @json($ofertasPorInstituicao);
        const modalidadesMaisUsadas = @json($modalidadesMaisUsadas);

        // Cores para os gráficos
        const cores = [
            '#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe',
            '#fa709a', '#fee140', '#a8edea', '#fed6e3', '#d299c2', '#fef9d7'
        ];

        // Gráfico de Consultas por Dia
        const ctxConsultas = document.getElementById('consultasPorDiaChart').getContext('2d');
        new Chart(ctxConsultas, {
            type: 'line',
            data: {
                labels: consultasPorDia.map(item => {
                    return new Date(item.data).toLocaleDateString('pt-BR');
                }).reverse(),
                datasets: [{
                    label: 'Consultas',
                    data: consultasPorDia.map(item => item.total).reverse(),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Gráfico de Ofertas por Instituição (Pizza)
        const ctxInstituicoes = document.getElementById('ofertasPorInstituicaoChart').getContext('2d');
        new Chart(ctxInstituicoes, {
            type: 'doughnut',
            data: {
                labels: ofertasPorInstituicao.map(item => item.instituicao_nome),
                datasets: [{
                    data: ofertasPorInstituicao.map(item => item.total),
                    backgroundColor: cores.slice(0, ofertasPorInstituicao.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Modalidades (Barras)
        const ctxModalidades = document.getElementById('modalidadesChart').getContext('2d');
        new Chart(ctxModalidades, {
            type: 'bar',
            data: {
                labels: modalidadesMaisUsadas.map(item => item.modalidade_credito),
                datasets: [{
                    label: 'Ofertas',
                    data: modalidadesMaisUsadas.map(item => item.total),
                    backgroundColor: cores.slice(0, modalidadesMaisUsadas.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 