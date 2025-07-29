<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GoSat Crédito')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: box-shadow 0.15s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .loading {
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">GoSat Crédito</h4>
                        <small class="text-light">Sistema de Consulta</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-light {{ request()->routeIs('dashboard.index') ? 'active bg-white bg-opacity-25' : '' }}" 
                               href="{{ route('dashboard.index') }}">
                                <i class="fas fa-home me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light {{ request()->routeIs('dashboard.consulta') ? 'active bg-white bg-opacity-25' : '' }}" 
                               href="{{ route('dashboard.consulta') }}">
                                <i class="fas fa-search me-2"></i>
                                Consultar Crédito
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light {{ request()->routeIs('dashboard.relatorios') ? 'active bg-white bg-opacity-25' : '' }}" 
                               href="{{ route('dashboard.relatorios') }}">
                                <i class="fas fa-chart-bar me-2"></i>
                                Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light {{ request()->routeIs('dashboard.historico') ? 'active bg-white bg-opacity-25' : '' }}" 
                               href="{{ route('dashboard.historico') }}">
                                <i class="fas fa-history me-2"></i>
                                Histórico
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios for API calls -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Configurar CSRF token para requests AJAX
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    
    @yield('scripts')
</body>
</html> 