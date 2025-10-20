<?php

namespace App\Repositories;

use App\Models\CronogramaColeta;
use Illuminate\Database\Eloquent\Collection;

class CronogramaColetaRepository
{
    protected CronogramaColeta $model;

    public function __construct(CronogramaColeta $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->ativo()->orderBy('dia_semana')->orderBy('horario_inicio')->get();
    }

    public function find(int $id): ?CronogramaColeta
    {
        return $this->model->ativo()->find($id);
    }

    public function create(array $data): CronogramaColeta
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?CronogramaColeta
    {
        $model = $this->model->find($id);
        if (!$model) {
            return null;
        }

        $model->update($data);
        return $model;
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        if (!$model) {
            return false;
        }

        return $model->delete();
    }

    public function buscarPorMaterial(string $material): Collection
    {
        return $this->model->ativo()->porMaterial($material)->get();
    }

    public function buscarPorDiaSemana(string $diaSemana): Collection
    {
        return $this->model->ativo()->porDiaSemana($diaSemana)->get();
    }

    public function buscarPorCidade(string $cidade): Collection
    {
        return $this->model->ativo()->porCidade($cidade)->get();
    }

    public function buscarProximos(float $latitude, float $longitude, int $raioKm = 50): Collection
    {
        try {
            // Para simplificar, retornamos todos os cronogramas ativos
            // Em uma implementação mais robusta, usaríamos cálculos de distância
            $cronogramas = $this->model->ativo()->get();
            
            // Log para debug
            \Log::info('Busca por cronogramas próximos', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'raio' => $raioKm,
                'encontrados' => $cronogramas->count()
            ]);
            
            return $cronogramas;
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar cronogramas próximos: ' . $e->getMessage());
            return collect();
        }
    }

    public function buscarPorFiltros(array $filtros): Collection
    {
        $query = $this->model->ativo();

        if (isset($filtros['material'])) {
            $query->porMaterial($filtros['material']);
        }

        if (isset($filtros['dia_semana'])) {
            $query->porDiaSemana($filtros['dia_semana']);
        }

        if (isset($filtros['cidade'])) {
            $query->porCidade($filtros['cidade']);
        }

        return $query->orderBy('dia_semana')->orderBy('horario_inicio')->get();
    }
}
