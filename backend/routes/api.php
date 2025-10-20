<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ColetaController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\PontoColetaController;

Route::get('/pontos', [PontoColetaController::class, 'index']);

Route::get('/coletas', [ColetaController::class, 'index']);
Route::post('/coletas', [ColetaController::class, 'store']);
Route::get('/coletas/{id}', [ColetaController::class, 'show']);
Route::put('/coletas/{id}', [ColetaController::class, 'update']);
Route::delete('/coletas/{id}', [ColetaController::class, 'destroy']);

Route::get('/doacoes', [DoacaoController::class, 'index']);
Route::post('/doacoes', [DoacaoController::class, 'store']);
Route::get('/doacoes/{id}', [DoacaoController::class, 'show']);
Route::put('/doacoes/{id}', [DoacaoController::class, 'update']);
Route::delete('/doacoes/{id}', [DoacaoController::class, 'destroy']);


