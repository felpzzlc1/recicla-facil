<?php

namespace App\Repositories;

use App\Models\PontoColeta;

class PontoColetaRepository
{
    public function all()
    {
        return PontoColeta::where('ativo', true)->orderBy('nome')->get();
    }

    /**
     * Busca pontos próximos a uma localização específica
     */
    public function buscarProximos($latitude, $longitude, $raioKm = 50, $limite = 20)
    {
        try {
            $pontos = PontoColeta::proximos($latitude, $longitude, $raioKm)
                                 ->limit($limite)
                                 ->get()
                                 ->map(function ($ponto) {
                                     return [
                                         'id' => $ponto->id,
                                         'nome' => $ponto->nome,
                                         'tipo' => $ponto->tipo,
                                         'endereco' => $ponto->endereco,
                                         'telefone' => $ponto->telefone,
                                         'horario' => $ponto->horario,
                                         'latitude' => $ponto->latitude,
                                         'longitude' => $ponto->longitude,
                                         'materiais' => $ponto->materiais_aceitos ?? [],
                                         'distancia' => round($ponto->distancia, 2) . ' km',
                                         'distancia_km' => round($ponto->distancia, 2)
                                     ];
                                 });
            
            return $pontos;
        } catch (\Exception $e) {
            return PontoColeta::where('ativo', true)
                             ->whereNotNull('latitude')
                             ->whereNotNull('longitude')
                             ->limit($limite)
                             ->get()
                             ->map(function ($ponto) use ($latitude, $longitude) {
                                 $distancia = $ponto->calcularDistancia($latitude, $longitude);
                                 return [
                                     'id' => $ponto->id,
                                     'nome' => $ponto->nome,
                                     'tipo' => $ponto->tipo,
                                     'endereco' => $ponto->endereco,
                                     'telefone' => $ponto->telefone,
                                     'horario' => $ponto->horario,
                                     'latitude' => $ponto->latitude,
                                     'longitude' => $ponto->longitude,
                                     'materiais' => $ponto->materiais_aceitos ?? [],
                                     'distancia' => $distancia ? round($distancia, 2) . ' km' : 'N/A',
                                     'distancia_km' => $distancia ? round($distancia, 2) : 0
                                 ];
                             })
                             ->sortBy('distancia_km')
                             ->values();
        }
    }

    /**
     * Busca pontos por tipo de material
     */
    public function buscarPorMaterial($material, $latitude = null, $longitude = null, $raioKm = 50)
    {
        $query = PontoColeta::where('ativo', true)
                             ->whereJsonContains('materiais_aceitos', $material);

        if ($latitude && $longitude) {
            $query = $query->proximos($latitude, $longitude, $raioKm);
        } else {
            $query = $query->orderBy('nome');
        }

        return $query->get();
    }

    /**
     * Gera pontos de exemplo baseados na localização (para desenvolvimento)
     */
    public function gerarPontosExemplo($latitude, $longitude, $quantidade = 5)
    {
        $pontosExemplo = [];
        $materiais = ['Plástico', 'Metal', 'Vidro', 'Papel', 'Orgânico'];
        $tipos = ['Cooperativa', 'Ponto Verde', 'Ecoponto', 'Centro de Triagem'];
        $horarios = [
            'Seg-Sex: 8h-18h | Sáb: 8h-12h',
            'Seg-Sex: 7h-19h | Sáb: 7h-13h',
            'Seg-Sex: 8h-17h | Sáb: 8h-12h',
            'Seg-Sex: 6h-20h | Sáb: 6h-14h'
        ];

        for ($i = 0; $i < $quantidade; $i++) {
            $offsetLat = (rand(-100, 100) / 1000); // Variação de ~100m
            $offsetLon = (rand(-100, 100) / 1000);
            
            $pontosExemplo[] = [
                'id' => 'exemplo_' . ($i + 1),
                'nome' => 'Ponto de Coleta ' . ($i + 1),
                'tipo' => $tipos[array_rand($tipos)],
                'endereco' => 'Rua Exemplo ' . ($i + 1) . ', ' . rand(100, 999),
                'telefone' => '(11) ' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'horario' => $horarios[array_rand($horarios)],
                'latitude' => $latitude + $offsetLat,
                'longitude' => $longitude + $offsetLon,
                'materiais' => array_slice($materiais, 0, rand(2, 4)),
                'distancia' => round(rand(50, 2000) / 1000, 2) . ' km',
                'distancia_km' => round(rand(50, 2000) / 1000, 2),
                'tipo_origem' => 'exemplo'
            ];
        }

        usort($pontosExemplo, function($a, $b) {
            return $a['distancia_km'] <=> $b['distancia_km'];
        });

        return $pontosExemplo;
    }
}


