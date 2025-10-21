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

            // Subtrair pontos do usuário e recalcular nível
            $novosPontos = $pontosUsuario - $recompensa->pontos;
            $novoNivel = $this->calcularNivel($novosPontos);
            
            DB::table('pontuacoes')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->update([
                    'pontos' => $novosPontos,
                    'pontos_totais' => $novosPontos,
                    'nivel' => $novoNivel['nivel'],
                    'nivel_nome' => $novoNivel['nome'],
                    'updated_at' => now()
                ]);

            return $resgate;
        });
    }

    /**
     * Calcular nível baseado nos pontos
     */
    private function calcularNivel($pontos)
    {
        if ($pontos < 100) return ['nivel' => 1, 'nome' => 'Iniciante'];
        if ($pontos < 500) return ['nivel' => 2, 'nome' => 'Reciclador'];
        if ($pontos < 1000) return ['nivel' => 3, 'nome' => 'Eco Warrior'];
        if ($pontos < 2500) return ['nivel' => 4, 'nome' => 'Guardião Verde'];
        if ($pontos < 5000) return ['nivel' => 5, 'nome' => 'Mestre Sustentável'];
        if ($pontos < 10000) return ['nivel' => 6, 'nome' => 'Lenda Verde'];
        if ($pontos < 25000) return ['nivel' => 7, 'nome' => 'Herói Ambiental'];
        if ($pontos < 50000) return ['nivel' => 8, 'nome' => 'Defensor da Terra'];
        if ($pontos < 100000) return ['nivel' => 9, 'nome' => 'Guardião Supremo'];
        return ['nivel' => 10, 'nome' => 'Lenda Viva'];
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
