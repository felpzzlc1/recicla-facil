<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Recicla Fácil API - Backend funcionando!',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});