<?php

namespace App\Http\Controllers;

use App\Repositories\PontuacaoRepository;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PontuacaoController extends Controller
{
    protected $pontuacaoRepository;

    public function __construct(PontuacaoRepository $pontuacaoRepository)
    {
        $this->pontuacaoRepository = $pontuacaoRepository;
    }

    /**
     * Obter estatísticas de pontuação do usuário
     */
    public function obterEstatisticas(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $estatisticas = $this->pontuacaoRepository->obterEstatisticasUsuario($userId);
            
            return ApiResponse::success($estatisticas, 'Estatísticas obtidas com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao obter estatísticas: ' . $e->getMessage());
        }
    }

    /**
     * Adicionar pontos ao usuário
     */
    public function adicionarPontos(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'pontos' => 'required|integer|min:1|max:1000',
                'motivo' => 'string|max:255'
            ]);

            $userId = $request->user()->id;
            $pontos = $request->input('pontos');
            $motivo = $request->input('motivo', 'descarte');

            $resultado = $this->pontuacaoRepository->adicionarPontos($userId, $pontos, $motivo);
            
            $response = [
                'pontuacao' => $resultado['pontuacao'],
                'novas_conquistas' => $resultado['novas_conquistas']
            ];

            return ApiResponse::success($response, 'Pontos adicionados com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao adicionar pontos: ' . $e->getMessage());
        }
    }

    /**
     * Obter ranking de usuários
     */
    public function obterRanking(Request $request): JsonResponse
    {
        try {
            $limite = $request->input('limite', 10);
            $ranking = $this->pontuacaoRepository->obterRanking($limite);
            
            return ApiResponse::success($ranking, 'Ranking obtido com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao obter ranking: ' . $e->getMessage());
        }
    }

    /**
     * Obter conquistas disponíveis
     */
    public function obterConquistas(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $estatisticas = $this->pontuacaoRepository->obterEstatisticasUsuario($userId);
            $conquistasDisponiveis = $this->pontuacaoRepository->obterConquistasDisponiveis();
            
            // Marcar conquistas como desbloqueadas ou não
            $conquistasComStatus = array_map(function ($conquista) use ($estatisticas) {
                $valorAtual = match($conquista['tipo']) {
                    'pontos' => $estatisticas['pontos'],
                    'descartes' => $estatisticas['descartes'],
                    'sequencia' => $estatisticas['sequencia_dias'],
                    default => 0
                };
                
                $desbloqueada = $valorAtual >= $conquista['requisito'];
                
                return array_merge($conquista, [
                    'desbloqueada' => $desbloqueada,
                    'progresso' => min(100, ($valorAtual / $conquista['requisito']) * 100)
                ]);
            }, $conquistasDisponiveis);
            
            return ApiResponse::success($conquistasComStatus, 'Conquistas obtidas com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao obter conquistas: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas gerais da plataforma
     */
    public function obterEstatisticasGerais(): JsonResponse
    {
        try {
            $estatisticas = $this->pontuacaoRepository->obterEstatisticasGerais();
            
            return ApiResponse::success($estatisticas, 'Estatísticas gerais obtidas com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao obter estatísticas gerais: ' . $e->getMessage());
        }
    }

    /**
     * Simular descarte e adicionar pontos
     */
    public function simularDescarte(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'material' => 'required|string|in:papel,plastico,vidro,metal,organico',
                'peso' => 'required|numeric|min:0.1|max:100'
            ]);

            $userId = $request->user()->id;
            $material = $request->input('material');
            $peso = $request->input('peso');

            // Calcular pontos baseado no material e peso
            $pontosPorKg = match($material) {
                'papel' => 10,
                'plastico' => 15,
                'vidro' => 20,
                'metal' => 25,
                'organico' => 5,
                default => 10
            };

            $pontos = round($peso * $pontosPorKg);
            $motivo = "Descarte de {$material} ({$peso}kg)";

            $resultado = $this->pontuacaoRepository->adicionarPontos($userId, $pontos, 'simular-descarte');
            
            $response = [
                'pontos_ganhos' => $pontos,
                'material' => $material,
                'peso' => $peso,
                'pontuacao' => $resultado['pontuacao'],
                'novas_conquistas' => $resultado['novas_conquistas']
            ];

            return ApiResponse::success($response, 'Descarte simulado com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao simular descarte: ' . $e->getMessage());
        }
    }

    /**
     * Registrar descarte e adicionar pontos
     */
    public function registrarDescarte(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'material' => 'required|string|in:papel,plastico,vidro,metal,organico',
                'peso' => 'required|numeric|min:0.1|max:100'
            ]);

            $userId = $request->user()->id;
            $material = $request->input('material');
            $peso = $request->input('peso');

            // Calcular pontos baseado no material e peso
            $pontosPorKg = match($material) {
                'papel' => 10,
                'plastico' => 15,
                'vidro' => 20,
                'metal' => 25,
                'organico' => 5,
                default => 10
            };

            $pontos = round($peso * $pontosPorKg);
            $motivo = "Descarte de {$material} ({$peso}kg)";

            $resultado = $this->pontuacaoRepository->adicionarPontos($userId, $pontos, 'descarte');
            
            $response = [
                'pontos_ganhos' => $pontos,
                'material' => $material,
                'peso' => $peso,
                'pontuacao' => $resultado['pontuacao'],
                'novas_conquistas' => $resultado['novas_conquistas']
            ];

            return ApiResponse::success($response, 'Descarte registrado com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao registrar descarte: ' . $e->getMessage());
        }
    }

    /**
     * Resetar pontos semanais (para uso administrativo)
     */
    public function resetarPontosSemanais(): JsonResponse
    {
        try {
            $this->pontuacaoRepository->resetarPontosSemanais();
            
            return ApiResponse::success([], 'Pontos semanais resetados com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao resetar pontos semanais: ' . $e->getMessage());
        }
    }
}
