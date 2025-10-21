<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ColetaRepository;
use App\Helpers\ApiResponse;

class ColetaController extends Controller
{
    private ColetaRepository $repo;

    public function __construct(ColetaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        return ApiResponse::success($this->repo->all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material' => 'required|string',
            'quantidade' => 'required|numeric',
            'endereco' => 'required|string',
            'data_preferida' => 'required|date',
            'obs' => 'nullable|string',
        ]);

        $data['status'] = 'ABERTA';
        $data['user_id'] = $request->header('X-User-ID', 1); // Simulação - em produção usar middleware de auth
        $created = $this->repo->create($data);
        return ApiResponse::success($created, 'Criado', 201);
    }

    public function show($id)
    {
        $model = $this->repo->find($id);
        if (!$model) {
            return ApiResponse::error('Não encontrado', 404);
        }
        return ApiResponse::success($model);
    }

    public function update($id, Request $request)
    {
        $data = $request->validate([
            'material' => 'sometimes|string',
            'quantidade' => 'sometimes|numeric',
            'endereco' => 'sometimes|string',
            'data_preferida' => 'sometimes|date',
            'obs' => 'nullable|string',
            'status' => 'sometimes|in:ABERTA,CONCLUIDA',
        ]);

        $updated = $this->repo->update($id, $data);
        if (!$updated) {
            return ApiResponse::error('Não encontrado', 404);
        }
        return ApiResponse::success($updated, 'Atualizado');
    }

    public function destroy($id)
    {
        $deleted = $this->repo->delete($id);
        if (!$deleted) {
            return ApiResponse::error('Não encontrado', 404);
        }
        return ApiResponse::success(null, 'Removido', 204);
    }
}

