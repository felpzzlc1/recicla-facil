<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColetaController;
use App\Http\Controllers\CronogramaColetaController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\PontoColetaController;
use App\Http\Controllers\PontuacaoController;
use App\Http\Middleware\AuthMiddleware;

// Rotas de autenticação
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware(AuthMiddleware::class);
Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware(AuthMiddleware::class);
Route::put('/auth/profile', [AuthController::class, 'updateProfile'])->middleware(AuthMiddleware::class);

// Rotas públicas - Pontos de Coleta
Route::get('/pontos', [PontoColetaController::class, 'index']);
Route::get('/pontos/proximos', [PontoColetaController::class, 'proximos']);
Route::post('/pontos', [PontoColetaController::class, 'store']);

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

// Rotas de cronograma de coleta
Route::get('/cronograma', [CronogramaColetaController::class, 'index']);
Route::post('/cronograma', [CronogramaColetaController::class, 'store']);
Route::get('/cronograma/proximos', [CronogramaColetaController::class, 'proximos']);
Route::get('/cronograma/material/{material}', [CronogramaColetaController::class, 'porMaterial']);
Route::get('/cronograma/dia/{diaSemana}', [CronogramaColetaController::class, 'porDiaSemana']);
Route::get('/cronograma/cidade/{cidade}', [CronogramaColetaController::class, 'porCidade']);
Route::get('/cronograma/{id}', [CronogramaColetaController::class, 'show']);
Route::put('/cronograma/{id}', [CronogramaColetaController::class, 'update']);
Route::delete('/cronograma/{id}', [CronogramaColetaController::class, 'destroy']);

// Rotas de pontuação
Route::get('/pontuacao/estatisticas', [PontuacaoController::class, 'obterEstatisticas'])->middleware(AuthMiddleware::class);
Route::post('/pontuacao/adicionar', [PontuacaoController::class, 'adicionarPontos'])->middleware(AuthMiddleware::class);
Route::get('/pontuacao/ranking', [PontuacaoController::class, 'obterRanking']);
Route::get('/pontuacao/conquistas', [PontuacaoController::class, 'obterConquistas'])->middleware(AuthMiddleware::class);
Route::get('/pontuacao/estatisticas-gerais', [PontuacaoController::class, 'obterEstatisticasGerais']);
Route::post('/pontuacao/simular-descarte', [PontuacaoController::class, 'simularDescarte'])->middleware(AuthMiddleware::class);
Route::post('/pontuacao/resetar-semanais', [PontuacaoController::class, 'resetarPontosSemanais']);
