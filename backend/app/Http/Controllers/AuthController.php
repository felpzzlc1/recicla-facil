<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telefone' => 'required|string|max:20',
            'senha' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Dados inválidos', 422, $validator->errors());
        }

        $data = $request->only(['nome', 'email', 'telefone', 'senha']);
        $user = $this->userRepo->create($data);
        
        // Remove a senha da resposta
        unset($user->senha);
        
        return ApiResponse::success($user, 'Usuário criado com sucesso', 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'senha' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Dados inválidos', 422, $validator->errors());
        }

        $user = $this->userRepo->authenticate($request->email, $request->senha);
        
        if (!$user) {
            return ApiResponse::error('Credenciais inválidas', 401);
        }

        // Gerar token de sessão
        $token = Str::random(60);
        $this->userRepo->createSession($user->id, $token, $request->ip(), $request->userAgent());

        // Remove a senha da resposta
        unset($user->senha);
        $user->token = $token;
        
        return ApiResponse::success($user, 'Login realizado com sucesso');
    }

    public function profile(Request $request)
    {
        $user = $request->get('user');
        
        if (!$user) {
            return ApiResponse::error('Usuário não autenticado', 401);
        }

        unset($user->senha);
        return ApiResponse::success($user);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->get('user');
        
        if (!$user) {
            return ApiResponse::error('Usuário não autenticado', 401);
        }
        
        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|string|max:255',
            'telefone' => 'sometimes|string|max:20',
            'senha' => 'sometimes|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Dados inválidos', 422, $validator->errors());
        }

        $data = $request->only(['nome', 'telefone', 'senha']);
        $user = $this->userRepo->update($user->id, $data);
        
        if (!$user) {
            return ApiResponse::error('Usuário não encontrado', 404);
        }

        unset($user->senha);
        return ApiResponse::success($user, 'Perfil atualizado com sucesso');
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $token);
        
        $this->userRepo->deleteSession($token);
        
        return ApiResponse::success([], 'Logout realizado com sucesso');
    }
}
