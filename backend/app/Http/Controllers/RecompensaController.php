<?php

namespace App\Http\Controllers;

use App\Repositories\RecompensaRepository;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class RecompensaController extends Controller
{
    protected $recompensaRepository;

    public function __construct(RecompensaRepository $recompensaRepository)
    {
        $this->recompensaRepository = $recompensaRepository;
    }

    /**
     * Listar todas as recompensas disponíveis
     */
    public function index()
    {
        try {
            $recompensas = $this->recompensaRepository->getDisponiveis();
            return ApiResponse::success($recompensas);
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao buscar recompensas: ' . $e->getMessage());
        }
    }

    /**
     * Obter recompensa por ID
     */
    public function show($id)
    {
        try {
            $recompensa = $this->recompensaRepository->getById($id);
            if (!$recompensa) {
                return ApiResponse::error('Recompensa não encontrada', 404);
            }
            return ApiResponse::success($recompensa);
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao buscar recompensa: ' . $e->getMessage());
        }
    }

    /**
     * Resgatar uma recompensa
     */
    public function resgatar(Request $request)
    {
        try {
            $userId = $request->user_id;
            $recompensaId = $request->recompensa_id;

            if (!$userId || !$recompensaId) {
                return ApiResponse::error('Dados obrigatórios não fornecidos');
            }

            $resgate = $this->recompensaRepository->resgatarRecompensa($userId, $recompensaId);
            
            return ApiResponse::success($resgate, 'Recompensa resgatada com sucesso!');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    /**
     * Obter resgates do usuário
     */
    public function meusResgates(Request $request)
    {
        try {
            $userId = $request->user_id;
            if (!$userId) {
                return ApiResponse::error('ID do usuário não fornecido');
            }

            $resgates = $this->recompensaRepository->getResgatesByUser($userId);
            return ApiResponse::success($resgates);
        } catch (\Exception $e) {
            return ApiResponse::error('Erro ao buscar resgates: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar status de um resgate (admin)
     */
    public function atualizarStatusResgate(Request $request, $id)
    {
        try {
            $status = $request->status;
            $observacoes = $request->observacoes;

            if (!in_array($status, ['PENDENTE', 'APROVADO', 'REJEITADO', 'ENTREGUE'])) {
                return ApiResponse::error('Status inválido');
            }

            $resgate = $this->recompensaRepository->updateStatusResgate($id, $status, $observacoes);
            return ApiResponse::success($resgate, 'Status atualizado com sucesso!');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
