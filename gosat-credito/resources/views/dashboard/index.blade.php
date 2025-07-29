@extends('layouts.app')

@section('title', 'Dashboard - GoSat Crédito')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Estatísticas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="h6 font-weight-bold text-uppercase mb-1">Total Consultas</div>
                        <div class="h3 mb-0">{{ number_format($estatisticas['total_consultas']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-search fa-2x text-white opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="h6 font-weight-bold text-uppercase mb-1">Total Ofertas</div>
                        <div class="h3 mb-0">{{ number_format($estatisticas['total_ofertas']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-white opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="h6 font-weight-bold text-uppercase mb-1">Instituições</div>
                        <div class="h3 mb-0">{{ number_format($estatisticas['total_instituicoes']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-white opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="h6 font-weight-bold text-uppercase mb-1">Consultas Hoje</div>
                        <div class="h3 mb-0">{{ number_format($estatisticas['consultas_hoje']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-white opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seção de Ações Rápidas -->
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('dashboard.consulta') }}" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search me-2"></i>
                            Nova Consulta
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('dashboard.relatorios') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-chart-bar me-2"></i>
                            Ver Relatórios
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('dashboard.historico') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-history me-2"></i>
                            Ver Histórico
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    CPFs de Teste
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Use os CPFs abaixo para testar:</p>
                <div class="mb-2">
                    <code class="user-select-all">111.111.111-11</code>
                </div>
                <div class="mb-2">
                    <code class="user-select-all">123.123.123.12</code>
                </div>
                <div class="mb-2">
                    <code class="user-select-all">222.222.222.22</code>
                </div>
                <small class="text-muted">Clique nos CPFs para copiar</small>
            </div>
        </div>
    </div>
</div>

<!-- Documentação da API -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-code me-2"></i>
                    Documentação da API
                </h5>
            </div>
            <div class="card-body">
                <p>API disponível para consulta de ofertas de crédito:</p>
                
                <h6>Endpoint Principal:</h6>
                <pre class="bg-light p-3 rounded"><code>POST /api/consultar-credito
Content-Type: application/json

{
  "cpf": "11111111111"
}</code></pre>

                <h6 class="mt-3">Resposta de Sucesso:</h6>
                <pre class="bg-light p-3 rounded"><code>{
  "success": true,
  "data": {
    "cpf": "11111111111",
    "total_ofertas_encontradas": 5,
    "ofertas_selecionadas": 3,
    "ofertas": [...]
  },
  "message": "Ofertas consultadas com sucesso!"
}</code></pre>

                <div class="mt-3">
                    <a href="/api/documentation" target="_blank" class="btn btn-outline-secondary">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Ver Documentação Completa (Swagger)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Copiar CPF ao clicar
    document.querySelectorAll('code.user-select-all').forEach(function(element) {
        element.style.cursor = 'pointer';
        element.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent.replace(/\D/g, ''));
            
            // Feedback visual
            const originalText = this.textContent;
            this.textContent = 'Copiado!';
            this.classList.add('text-success');
            
            setTimeout(() => {
                this.textContent = originalText;
                this.classList.remove('text-success');
            }, 1000);
        });
    });
</script>
@endsection 