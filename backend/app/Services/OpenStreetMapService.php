<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OpenStreetMapService
{
    private $overpassUrl = 'https://overpass-api.de/api/interpreter';
    private $cacheTimeout = 3600; // 1 hora

    /**
     * Busca pontos de reciclagem próximos via Overpass API
     */
    public function buscarPontosReciclagem($latitude, $longitude, $raioMetros = 5000)
    {
        $cacheKey = "osm_recycling_{$latitude}_{$longitude}_{$raioMetros}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($latitude, $longitude, $raioMetros) {
            return $this->fazerRequisicaoOverpass($latitude, $longitude, $raioMetros);
        });
    }

    /**
     * Faz requisição para Overpass API
     */
    private function fazerRequisicaoOverpass($latitude, $longitude, $raioMetros)
    {
        try {
            // Query Overpass para buscar pontos de reciclagem
            $query = $this->construirQueryOverpass($latitude, $longitude, $raioMetros);
            
            $response = Http::timeout(30)->post($this->overpassUrl, [
                'data' => $query
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->processarRespostaOverpass($data, $latitude, $longitude);
            }

            throw new \Exception('Erro na requisição Overpass: ' . $response->status());

        } catch (\Exception $e) {
            \Log::error('Erro Overpass API: ' . $e->getMessage());
            return [
                'pontos' => [],
                'erro' => 'Erro ao buscar pontos de reciclagem: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Constrói query Overpass
     */
    private function construirQueryOverpass($latitude, $longitude, $raioMetros)
    {
        return "
        [out:json][timeout:25];
        (
          node['amenity'='recycling'](around:{$raioMetros},{$latitude},{$longitude});
          way['amenity'='recycling'](around:{$raioMetros},{$latitude},{$longitude});
          relation['amenity'='recycling'](around:{$raioMetros},{$latitude},{$longitude});
        );
        out center meta;
        ";
    }

    /**
     * Processa resposta da Overpass API
     */
    private function processarRespostaOverpass($data, $latitude, $longitude)
    {
        $pontos = [];
        
        if (!isset($data['elements']) || empty($data['elements'])) {
            return [
                'pontos' => [],
                'total' => 0,
                'fonte' => 'OpenStreetMap',
                'atribuicao' => '© OpenStreetMap contributors'
            ];
        }

        foreach ($data['elements'] as $element) {
            $ponto = $this->normalizarElemento($element, $latitude, $longitude);
            if ($ponto) {
                $pontos[] = $ponto;
            }
        }

        // Ordenar por distância
        usort($pontos, function($a, $b) {
            return $a['distancia_km'] <=> $b['distancia_km'];
        });

        return [
            'pontos' => $pontos,
            'total' => count($pontos),
            'fonte' => 'OpenStreetMap',
            'atribuicao' => '© OpenStreetMap contributors',
            'localizacao' => [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]
        ];
    }

    /**
     * Normaliza elemento OSM (node/way/relation)
     */
    private function normalizarElemento($element, $latitude, $longitude)
    {
        $coords = $this->obterCoordenadas($element);
        if (!$coords) return null;

        $distancia = $this->calcularDistancia($latitude, $longitude, $coords['lat'], $coords['lng']);
        
        return [
            'id' => 'osm_' . $element['id'],
            'nome' => $this->obterNome($element),
            'endereco' => $this->obterEndereco($element),
            'latitude' => $coords['lat'],
            'longitude' => $coords['lng'],
            'distancia' => round($distancia, 2) . ' km',
            'distancia_km' => round($distancia, 2),
            'tipo' => $this->obterTipo($element),
            'telefone' => $element['tags']['phone'] ?? null,
            'horario' => $element['tags']['opening_hours'] ?? null,
            'materiais' => $this->obterMateriais($element),
            'fonte' => 'OpenStreetMap',
            'osm_id' => $element['id'],
            'osm_type' => $element['type']
        ];
    }

    /**
     * Obtém coordenadas do elemento
     */
    private function obterCoordenadas($element)
    {
        if ($element['type'] === 'node') {
            return [
                'lat' => $element['lat'],
                'lng' => $element['lon']
            ];
        } elseif (isset($element['center'])) {
            return [
                'lat' => $element['center']['lat'],
                'lng' => $element['center']['lon']
            ];
        }
        return null;
    }

    /**
     * Obtém nome do ponto
     */
    private function obterNome($element)
    {
        $tags = $element['tags'] ?? [];
        
        return $tags['name'] ?? 
               $tags['operator'] ?? 
               'Ponto de Reciclagem';
    }

    /**
     * Obtém endereço do ponto
     */
    private function obterEndereco($element)
    {
        $tags = $element['tags'] ?? [];
        
        $endereco = [];
        if (isset($tags['addr:street'])) {
            $endereco[] = $tags['addr:street'];
            if (isset($tags['addr:housenumber'])) {
                $endereco[0] .= ', ' . $tags['addr:housenumber'];
            }
        }
        if (isset($tags['addr:city'])) {
            $endereco[] = $tags['addr:city'];
        }
        
        return implode(' - ', $endereco) ?: 'Endereço não informado';
    }

    /**
     * Obtém tipo do ponto
     */
    private function obterTipo($element)
    {
        $tags = $element['tags'] ?? [];
        
        if (isset($tags['recycling:glass'])) return 'Reciclagem de Vidro';
        if (isset($tags['recycling:paper'])) return 'Reciclagem de Papel';
        if (isset($tags['recycling:plastic'])) return 'Reciclagem de Plástico';
        if (isset($tags['recycling:metal'])) return 'Reciclagem de Metal';
        
        return 'Centro de Reciclagem';
    }

    /**
     * Obtém materiais aceitos
     */
    private function obterMateriais($element)
    {
        $tags = $element['tags'] ?? [];
        $materiais = [];
        
        if (isset($tags['recycling:glass']) && $tags['recycling:glass'] === 'yes') {
            $materiais[] = 'Vidro';
        }
        if (isset($tags['recycling:paper']) && $tags['recycling:paper'] === 'yes') {
            $materiais[] = 'Papel';
        }
        if (isset($tags['recycling:plastic']) && $tags['recycling:plastic'] === 'yes') {
            $materiais[] = 'Plástico';
        }
        if (isset($tags['recycling:metal']) && $tags['recycling:metal'] === 'yes') {
            $materiais[] = 'Metal';
        }
        
        return empty($materiais) ? ['Reciclagem Geral'] : $materiais;
    }

    /**
     * Calcula distância entre dois pontos (Haversine)
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Raio da Terra em km
        
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        
        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}
