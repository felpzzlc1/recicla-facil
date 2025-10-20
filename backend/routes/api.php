<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColetaController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\PontoColetaController;
use App\Http\Middleware\AuthMiddleware;

// Rotas de teste
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando!',
        'timestamp' => now()
    ]);
});

Route::post('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'POST funcionando!',
        'data' => request()->all()
    ]);
});

// Rotas de autenticação
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware(AuthMiddleware::class);
Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware(AuthMiddleware::class);
Route::put('/auth/profile', [AuthController::class, 'updateProfile'])->middleware(AuthMiddleware::class);

// Rotas públicas
Route::get('/pontos', [PontoColetaController::class, 'index']);

// Rotas de coletas
Route::get('/coletas', [ColetaController::class, 'index']);
Route::post('/coletas', [ColetaController::class, 'store']);
Route::get('/coletas/{id}', [ColetaController::class, 'show']);
Route::put('/coletas/{id}', [ColetaController::class, 'update']);
Route::delete('/coletas/{id}', [ColetaController::class, 'destroy']);

// Rotas de doações
Route::get('/doacoes', [DoacaoController::class, 'index']);
Route::post('/doacoes', [DoacaoController::class, 'store']);
Route::get('/doacoes/{id}', [DoacaoController::class, 'show']);
Route::put('/doacoes/{id}', [DoacaoController::class, 'update']);
Route::delete('/doacoes/{id}', [DoacaoController::class, 'destroy']);


