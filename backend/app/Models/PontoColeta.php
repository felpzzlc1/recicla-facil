<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontoColeta extends Model
{
    use HasFactory;
    
    protected $table = 'ponto_coletas';

    protected $fillable = [
        'nome',
        'tipo',
        'endereco',
        'telefone',
        'horario',
        'latitude',
        'longitude',
        'materiais_aceitos',
        'ativo'
    ];

    protected $casts = [
        'materiais_aceitos' => 'array',
        'ativo' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Calcula a distância entre o ponto e uma localização específica
     */
    public function calcularDistancia($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // Raio da Terra em km

        $lat1 = deg2rad($latitude);
        $lon1 = deg2rad($longitude);
        $lat2 = deg2rad($this->latitude);
        $lon2 = deg2rad($this->longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Scope para buscar pontos próximos
     */
    public function scopeProximos($query, $latitude, $longitude, $raioKm = 50)
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->where('ativo', true)
                    ->selectRaw("*, 
                        (6371 * acos(cos(radians(" . $latitude . ")) 
                        * cos(radians(latitude)) 
                        * cos(radians(longitude) - radians(" . $longitude . ")) 
                        + sin(radians(" . $latitude . ")) 
                        * sin(radians(latitude)))) AS distancia")
                    ->having('distancia', '<=', $raioKm)
                    ->orderBy('distancia');
    }
}


