<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DoacaoRepository;
use App\Helpers\ApiResponse;

class DoacaoController extends Controller
{
    private DoacaoRepository $repo;

    public function __construct(DoacaoRepository $repo)
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
            'qtd' => 'required|integer',
            'contato' => 'required|string',
            'entregue' => 'sometimes|boolean',
        ]);

        $data['entregue'] = $data['entregue'] ?? false;
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
            'qtd' => 'sometimes|integer',
            'contato' => 'sometimes|string',
            'entregue' => 'sometimes|boolean',
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

