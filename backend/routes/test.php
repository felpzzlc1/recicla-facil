<?php

use Illuminate\Support\Facades\Route;

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
