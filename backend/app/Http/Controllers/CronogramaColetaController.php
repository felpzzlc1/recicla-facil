<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CronogramaColetaRepository;
use App\Helpers\ApiResponse;

class CronogramaColetaController extends Controller
{
    private CronogramaColetaRepository $repo;

    public function __construct(CronogramaColetaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $filtros = $request->only(['material', 'dia_semana', 'cidade']);
        
        if (!empty($filtros)) {
            $cronogramas = $this->repo->buscarPorFiltros($filtros);
        } else {
            $cronogramas = $this->repo->all();
        }

        return ApiResponse::success($cronogramas);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material' => 'required|string|max:255',
            'dia_semana' => 'required|string|max:255',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
            'endereco' => 'required|string|max:500',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        $data['ativo'] = true;
        $created = $this->repo->create($data);
        return ApiResponse::success($created, 'Cronograma criado com sucesso', 201);
    }

    public function show($id)
    {
        $cronograma = $this->repo->find($id);
        if (!$cronograma) {
            return ApiResponse::error('Cronograma não encontrado', 404);
        }
        return ApiResponse::success($cronograma);
    }

    public function update($id, Request $request)
    {
        $data = $request->validate([
            'material' => 'sometimes|string|max:255',
            'dia_semana' => 'sometimes|string|max:255',
            'horario_inicio' => 'sometimes|date_format:H:i',
            'horario_fim' => 'sometimes|date_format:H:i|after:horario_inicio',
            'endereco' => 'sometimes|string|max:500',
            'bairro' => 'sometimes|string|max:255',
            'cidade' => 'sometimes|string|max:255',
            'estado' => 'sometimes|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'observacoes' => 'nullable|string|max:1000',
            'ativo' => 'sometimes|boolean',
        ]);

        $updated = $this->repo->update($id, $data);
        if (!$updated) {
            return ApiResponse::error('Cronograma não encontrado', 404);
        }
        return ApiResponse::success($updated, 'Cronograma atualizado com sucesso');
    }

    public function destroy($id)
    {
        $deleted = $this->repo->delete($id);
        if (!$deleted) {
            return ApiResponse::error('Cronograma não encontrado', 404);
        }
        return ApiResponse::success(null, 'Cronograma removido com sucesso', 204);
    }

    public function proximos(Request $request)
    {
        try {
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $raio = $request->get('raio', 50);

            if (!$latitude || !$longitude) {
                return ApiResponse::error('Latitude e longitude são obrigatórios', 400);
            }

            $cronogramas = $this->repo->buscarProximos($latitude, $longitude, $raio);
            return ApiResponse::success($cronogramas);
        } catch (\Exception $e) {
            return ApiResponse::error('Erro interno: ' . $e->getMessage(), 500);
        }
    }

    public function porMaterial($material)
    {
        $cronogramas = $this->repo->buscarPorMaterial($material);
        return ApiResponse::success($cronogramas);
    }

    public function porDiaSemana($diaSemana)
    {
        $cronogramas = $this->repo->buscarPorDiaSemana($diaSemana);
        return ApiResponse::success($cronogramas);
    }

    public function porCidade($cidade)
    {
        $cronogramas = $this->repo->buscarPorCidade($cidade);
        return ApiResponse::success($cronogramas);
    }
}
