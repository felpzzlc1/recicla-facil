<?php

namespace App\Repositories;

use App\Models\Recompensa;
use App\Models\ResgateRecompensa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecompensaRepository
{
    public function getAll()
    {
        return Recompensa::where('ativo', true)
            ->orderBy('pontos', 'asc')
            ->get();
    }

    public function getById($id)
    {
        return Recompensa::find($id);
    }

    public function getDisponiveis()
    {
        return Recompensa::where('ativo', true)
            ->where('disponivel', '>', 0)
            ->orderBy('pontos', 'asc')
            ->get();
    }

    public function resgatarRecompensa($userId, $recompensaId)
    {
        return DB::transaction(function () use ($userId, $recompensaId) {
            // Verificar se a recompensa existe e está disponível
            $recompensa = Recompensa::where('id', $recompensaId)
                ->where('ativo', true)
                ->where('disponivel', '>', 0)
                ->lockForUpdate()
                ->first();

            if (!$recompensa) {
                throw new \Exception('Recompensa não disponível');
            }

            // Verificar se o usuário tem pontos suficientes
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('Usuário não encontrado');
            }

            // Obter pontos do usuário através da tabela pontuacoes
            $pontuacao = DB::table('pontuacoes')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

            $pontosUsuario = $pontuacao ? $pontuacao->pontos_totais : 0;

            if ($pontosUsuario < $recompensa->pontos) {
                throw new \Exception('Pontos insuficientes');
            }

            // Criar o resgate
            $resgate = ResgateRecompensa::create([
                'user_id' => $userId,
                'recompensa_id' => $recompensaId,
                'pontos_gastos' => $recompensa->pontos,
                'status' => 'PENDENTE',
                'data_resgate' => now()
            ]);

            // Decrementar disponibilidade da recompensa
            $recompensa->decrement('disponivel');

            // Subtrair pontos do usuário
            DB::table('pontuacoes')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->update([
                    'pontos_totais' => $pontosUsuario - $recompensa->pontos,
                    'updated_at' => now()
                ]);

            return $resgate;
        });
    }

    public function getResgatesByUser($userId)
    {
        return ResgateRecompensa::with('recompensa')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateStatusResgate($resgateId, $status, $observacoes = null)
    {
        $resgate = ResgateRecompensa::find($resgateId);
        if (!$resgate) {
            throw new \Exception('Resgate não encontrado');
        }

        $resgate->update([
            'status' => $status,
            'observacoes' => $observacoes
        ]);

        return $resgate;
    }
}
