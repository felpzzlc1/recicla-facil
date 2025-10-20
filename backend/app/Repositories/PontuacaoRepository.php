<?php

namespace App\Repositories;

use App\Models\Pontuacao;
use App\Models\User;
use App\Models\Conquista;
use Illuminate\Support\Facades\DB;

class PontuacaoRepository
{
    public function obterPontuacaoUsuario($userId)
    {
        return Pontuacao::where('user_id', $userId)->first();
    }

    public function criarPontuacaoInicial($userId)
    {
        return Pontuacao::create([
            'user_id' => $userId,
            'pontos' => 0,
            'nivel' => 1,
            'nivel_nome' => 'Iniciante',
            'descartes' => 0,
            'sequencia_dias' => 0,
            'badges_conquistadas' => 0,
            'pontos_semana_atual' => 0,
            'total_pontos_ganhos' => 0,
            'ultima_atualizacao' => now()
        ]);
    }

    public function adicionarPontos($userId, $pontos, $motivo = 'descarte')
    {
        $pontuacao = $this->obterPontuacaoUsuario($userId);
        
        if (!$pontuacao) {
            $pontuacao = $this->criarPontuacaoInicial($userId);
        }

        $pontuacao->adicionarPontos($pontos, $motivo);
        
        // Verificar conquistas
        $novasConquistas = $pontuacao->verificarConquistas();
        
        return [
            'pontuacao' => $pontuacao,
            'novas_conquistas' => $novasConquistas
        ];
    }

    public function obterEstatisticasUsuario($userId)
    {
        $pontuacao = $this->obterPontuacaoUsuario($userId);
        
        if (!$pontuacao) {
            return [
                'pontos' => 0,
                'nivel' => 1,
                'nivel_nome' => 'Iniciante',
                'descartes' => 0,
                'sequencia_dias' => 0,
                'badges_conquistadas' => 0,
                'pontos_semana_atual' => 0,
                'pontos_para_proximo_nivel' => 100,
                'progresso_nivel' => 0,
                'conquistas' => []
            ];
        }

        $pontosParaProximo = $pontuacao->pontosParaProximoNivel();
        $progressoNivel = $this->calcularProgressoNivel($pontuacao);
        
        $conquistas = $pontuacao->conquistas()
            ->orderBy('desbloqueada_em', 'desc')
            ->get();

        return [
            'pontos' => $pontuacao->pontos,
            'nivel' => $pontuacao->nivel,
            'nivel_nome' => $pontuacao->nivel_nome,
            'descartes' => $pontuacao->descartes,
            'sequencia_dias' => $pontuacao->sequencia_dias,
            'badges_conquistadas' => $pontuacao->badges_conquistadas,
            'pontos_semana_atual' => $pontuacao->pontos_semana_atual,
            'pontos_para_proximo_nivel' => $pontosParaProximo,
            'progresso_nivel' => $progressoNivel,
            'conquistas' => $conquistas
        ];
    }

    public function obterRanking($limite = 10)
    {
        return Pontuacao::with('user')
            ->orderBy('pontos', 'desc')
            ->limit($limite)
            ->get()
            ->map(function ($pontuacao) {
                return [
                    'user_id' => $pontuacao->user_id,
                    'nome' => $pontuacao->user->nome,
                    'pontos' => $pontuacao->pontos,
                    'nivel' => $pontuacao->nivel,
                    'nivel_nome' => $pontuacao->nivel_nome
                ];
            });
    }

    public function obterConquistasDisponiveis()
    {
        return [
            ['nome' => 'Iniciante', 'icone' => '🌱', 'requisito' => 100, 'tipo' => 'pontos', 'descricao' => 'Alcance 100 pontos'],
            ['nome' => 'Reciclador', 'icone' => '♻️', 'requisito' => 500, 'tipo' => 'pontos', 'descricao' => 'Alcance 500 pontos'],
            ['nome' => 'Eco Warrior', 'icone' => '☀️', 'requisito' => 1000, 'tipo' => 'pontos', 'descricao' => 'Alcance 1000 pontos'],
            ['nome' => 'Guardião Verde', 'icone' => '🌳', 'requisito' => 2500, 'tipo' => 'pontos', 'descricao' => 'Alcance 2500 pontos'],
            ['nome' => 'Mestre Sustentável', 'icone' => '🏆', 'requisito' => 5000, 'tipo' => 'pontos', 'descricao' => 'Alcance 5000 pontos'],
            ['nome' => 'Primeiro Descarte', 'icone' => '🎯', 'requisito' => 1, 'tipo' => 'descartes', 'descricao' => 'Faça seu primeiro descarte'],
            ['nome' => 'Sequência de 7 dias', 'icone' => '⚡', 'requisito' => 7, 'tipo' => 'sequencia', 'descricao' => 'Mantenha uma sequência de 7 dias'],
            ['nome' => 'Sequência de 30 dias', 'icone' => '🔥', 'requisito' => 30, 'tipo' => 'sequencia', 'descricao' => 'Mantenha uma sequência de 30 dias']
        ];
    }

    private function calcularProgressoNivel($pontuacao)
    {
        $niveis = [
            1 => 100, 2 => 500, 3 => 1000, 4 => 2500, 5 => 5000,
            6 => 10000, 7 => 25000, 8 => 50000, 9 => 100000, 10 => 999999
        ];
        
        $nivelAtual = $pontuacao->nivel;
        $pontosNivelAtual = $niveis[$nivelAtual - 1] ?? 0;
        $pontosProximoNivel = $niveis[$nivelAtual] ?? 999999;
        
        $pontosNecessarios = $pontosProximoNivel - $pontosNivelAtual;
        $pontosAtuais = $pontuacao->pontos - $pontosNivelAtual;
        
        return min(100, max(0, ($pontosAtuais / $pontosNecessarios) * 100));
    }

    public function resetarPontosSemanais()
    {
        return Pontuacao::query()->update(['pontos_semana_atual' => 0]);
    }

    public function obterEstatisticasGerais()
    {
        $totalUsuarios = Pontuacao::count();
        $totalPontos = Pontuacao::sum('pontos');
        $totalDescartes = Pontuacao::sum('descartes');
        $mediaPontos = $totalUsuarios > 0 ? round($totalPontos / $totalUsuarios, 2) : 0;
        
        return [
            'total_usuarios' => $totalUsuarios,
            'total_pontos' => $totalPontos,
            'total_descartes' => $totalDescartes,
            'media_pontos' => $mediaPontos
        ];
    }
}
