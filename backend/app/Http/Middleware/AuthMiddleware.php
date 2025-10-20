<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Helpers\ApiResponse;

class AuthMiddleware
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        
        if (!$token) {
            return ApiResponse::error('Token de autenticação não fornecido', 401);
        }

        // Remove "Bearer " do token se presente
        $token = str_replace('Bearer ', '', $token);
        
        // Verificar se o token é válido
        $user = $this->userRepo->findByToken($token);
        
        if (!$user) {
            return ApiResponse::error('Token inválido ou expirado', 401);
        }

        // Adicionar usuário à requisição
        $request->merge(['user' => $user]);
        
        return $next($request);
    }
}
