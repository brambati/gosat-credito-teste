<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CreditoController;
use App\Http\Controllers\DashboardController;

// Rotas da API
Route::prefix('api')->group(function () {
    Route::post('/consultar-credito', [CreditoController::class, 'consultarCredito']);
    Route::get('/historico/{cpf}', [CreditoController::class, 'historico']);
});

// Rotas Web (Dashboard)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/consulta', [DashboardController::class, 'consulta'])->name('dashboard.consulta');
Route::get('/relatorios', [DashboardController::class, 'relatorios'])->name('dashboard.relatorios');
Route::get('/historico', [DashboardController::class, 'historico'])->name('dashboard.historico');
