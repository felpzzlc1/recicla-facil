<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronogramaColeta extends Model
{
    use HasFactory;

    protected $table = 'cronograma_coletas';

    protected $fillable = [
        'material',
        'dia_semana',
        'horario_inicio',
        'horario_fim',
        'endereco',
        'bairro',
        'cidade',
        'estado',
        'latitude',
        'longitude',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'horario_inicio' => 'datetime:H:i',
        'horario_fim' => 'datetime:H:i',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ativo' => 'boolean',
    ];

    /**
     * Scope para buscar cronogramas ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para buscar por material
     */
    public function scopePorMaterial($query, $material)
    {
        return $query->where('material', $material);
    }

    /**
     * Scope para buscar por dia da semana
     */
    public function scopePorDiaSemana($query, $diaSemana)
    {
        return $query->where('dia_semana', $diaSemana);
    }

    /**
     * Scope para buscar por cidade
     */
    public function scopePorCidade($query, $cidade)
    {
        return $query->where('cidade', 'like', '%' . $cidade . '%');
    }

    /**
     * Retorna o horário formatado
     */
    public function getHorarioFormatadoAttribute()
    {
        return $this->horario_inicio->format('H:i') . ' - ' . $this->horario_fim->format('H:i');
    }

    /**
     * Retorna a localização completa
     */
    public function getLocalizacaoCompletaAttribute()
    {
        return $this->endereco . ', ' . $this->bairro . ', ' . $this->cidade . ' - ' . $this->estado;
    }
}
