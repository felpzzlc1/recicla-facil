<?php

namespace App\Http\Controllers;

use App\Repositories\PontoColetaRepository;
use App\Helpers\ApiResponse;
use App\Models\PontoColeta;
use Illuminate\Http\Request;

class PontoColetaController extends Controller
{
    private $repo;

    public function __construct(PontoColetaRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Lista todos os pontos de coleta
     * GET /api/pontos
     */
    public function index()
    {
        try {
            $pontos = PontoColeta::where('ativo', true)
                                ->orderBy('nome')
                                ->get();
            
            return ApiResponse::success([
                'pontos' => $pontos,
                'total' => $pontos->count()
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao carregar pontos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Busca pontos próximos a uma localização
     * GET /api/pontos/proximos?lat=-23.5505&lng=-46.6333&raio=50
     */
    public function proximos(Request $request)
    {
        // Debug: verificar se o método está sendo chamado
        \Log::info('Método proximos chamado', ['request' => $request->all()]);
        
        try {
            $latitude = $request->input('lat');
            $longitude = $request->input('lng');

            // Validação simples
            if (!$latitude || !$longitude) {
                return ApiResponse::error('Latitude e longitude são obrigatórios', 400);
            }

            // Busca pontos simples
            $pontos = PontoColeta::where('ativo', true)
                                ->whereNotNull('latitude')
                                ->whereNotNull('longitude')
                                ->limit(10)
                                ->get();

            if ($pontos->count() > 0) {
                // Calcula distância para cada ponto
                $pontosComDistancia = $pontos->map(function ($ponto) use ($latitude, $longitude) {
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
                })->sortBy('distancia_km')->values();

                return ApiResponse::success([
                    'pontos' => $pontosComDistancia,
                    'localizacao' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ],
                    'total' => $pontosComDistancia->count()
                ]);
            }

            // Se não há pontos, retorna vazio
            return ApiResponse::success([
                'pontos' => [],
                'localizacao' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ],
                'total' => 0
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao buscar pontos próximos: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Cadastra um novo ponto de coleta
     * POST /api/pontos
     */
    public function store(Request $request)
    {
        try {
            // Validação dos dados
            $request->validate([
                'nome' => 'required|string|max:255',
                'tipo' => 'nullable|string|max:100',
                'endereco' => 'required|string|max:500',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'telefone' => 'nullable|string|max:20',
                'horario' => 'nullable|string|max:100',
                'materiais' => 'nullable|array',
                'materiais.*' => 'string|max:50'
            ]);

            // Criar novo ponto
            $ponto = new PontoColeta();
            $ponto->nome = $request->input('nome');
            $ponto->tipo = $request->input('tipo', 'Cooperativa');
            $ponto->endereco = $request->input('endereco');
            $ponto->latitude = $request->input('latitude'); // Pode ser null
            $ponto->longitude = $request->input('longitude'); // Pode ser null
            $ponto->telefone = $request->input('telefone');
            $ponto->horario = $request->input('horario');
            $ponto->materiais_aceitos = $request->input('materiais', []);
            $ponto->ativo = true;
            $ponto->fonte = 'Usuario';
            $ponto->tipo_origem = 'cadastro';
            
            $ponto->save();

            return ApiResponse::success([
                'ponto' => $ponto,
                'message' => 'Ponto de coleta cadastrado com sucesso!'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Dados inválidos: ' . implode(', ', $e->errors()), 422);
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao cadastrar ponto: ' . $e->getMessage(), 500);
        }
    }
}


