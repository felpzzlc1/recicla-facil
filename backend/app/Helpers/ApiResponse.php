<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = null, string $message = 'OK', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(string $message = 'Erro', int $code = 400, $details = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'details' => $details,
        ], $code);
    }
}


