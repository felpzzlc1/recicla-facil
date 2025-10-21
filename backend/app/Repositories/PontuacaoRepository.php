<?php

namespace App\Repositories;

use PDO;

class PontuacaoRepository
{
    private $pdo;

    public function __construct()
    {
        $host = 'db';
        $dbname = 'recicla_facil';
        $username = 'root';
        $password = 'root';
        
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function obterPontuacaoUsuario($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pontuacoes WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function criarPontuacaoInicial($userId)
    {
        $stmt = $this->pdo->prepare("INSERT INTO pontuacoes (user_id, pontos, nivel, nivel_nome, descartes, sequencia_dias, badges_conquistadas, pontos_semana_atual, total_pontos_ganhos, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            0,
            1,
            'Iniciante',
            0,
            0,
            0,
            0,
            0,
            date('Y-m-d H:i:s')
        ]);

        return $this->obterPontuacaoUsuario($userId);
    }

    public function adicionarPontos($userId, $pontos, $motivo = 'descarte')
    {
        $pontuacao = $this->obterPontuacaoUsuario($userId);
        
        if (!$pontuacao) {
            $pontuacao = $this->criarPontuacaoInicial($userId);
        }

        $hoje = date('Y-m-d H:i:s');
        $ultimaAtualizacao = $pontuacao->ultima_atualizacao ? $pontuacao->ultima_atualizacao : null;
        $inicioSemana = date('Y-m-d', strtotime('monday this week'));
        if (!$ultimaAtualizacao || date('Y-m-d', strtotime($ultimaAtualizacao)) < $inicioSemana) {
            $pontosSemanaAtual = 0;
        } else {
            $pontosSemanaAtual = $pontuacao->pontos_semana_atual;
        }

        $sequenciaDias = $pontuacao->sequencia_dias;
        if ($ultimaAtualizacao) {
            $diferencaDias = (strtotime($hoje) - strtotime($ultimaAtualizacao)) / (60 * 60 * 24);
            
            if ($diferencaDias == 0) {
                // Mesmo dia
            } elseif ($diferencaDias == 1) {
                $sequenciaDias += 1;
            } else {
                $sequenciaDias = 1;
            }
        } else {
            $sequenciaDias = 1;
        }
        $novosPontos = $pontuacao->pontos + $pontos;
        $novoNivel = $this->calcularNivel($novosPontos);
        $stmt = $this->pdo->prepare("UPDATE pontuacoes SET pontos = ?, nivel = ?, nivel_nome = ?, descartes = ?, sequencia_dias = ?, pontos_semana_atual = ?, total_pontos_ganhos = ?, ultima_atualizacao = ? WHERE user_id = ?");
        $stmt->execute([
            $novosPontos,
            $novoNivel['nivel'],
            $novoNivel['nome'],
            $pontuacao->descartes + 1,
            $sequenciaDias,
            $pontosSemanaAtual + $pontos,
            $pontuacao->total_pontos_ganhos + $pontos,
            $hoje,
            $userId
        ]);

        $pontuacaoAtualizada = $this->obterPontuacaoUsuario($userId);

        return [
            'pontuacao' => $pontuacaoAtualizada,
            'novas_conquistas' => [] // Por enquanto, sem conquistas
        ];
    }

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

        $pontosParaProximo = $this->pontosParaProximoNivel($pontuacao->pontos, $pontuacao->nivel);
        $progressoNivel = $this->calcularProgressoNivel($pontuacao->pontos, $pontuacao->nivel);

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
            'conquistas' => []
        ];
    }

    private function pontosParaProximoNivel($pontos, $nivel)
    {
        $niveis = [
            1 => 100, 2 => 500, 3 => 1000, 4 => 2500, 5 => 5000,
            6 => 10000, 7 => 25000, 8 => 50000, 9 => 100000, 10 => 999999
        ];

        $proximoNivel = $nivel + 1;
        $pontosNecessarios = $niveis[$proximoNivel] ?? 999999;

        return max(0, $pontosNecessarios - $pontos);
    }

    private function calcularProgressoNivel($pontos, $nivel)
    {
        $niveis = [
            1 => 100, 2 => 500, 3 => 1000, 4 => 2500, 5 => 5000,
            6 => 10000, 7 => 25000, 8 => 50000, 9 => 100000, 10 => 999999
        ];

        $pontosNivelAtual = $niveis[$nivel - 1] ?? 0;
        $pontosProximoNivel = $niveis[$nivel] ?? 999999;

        $pontosNecessarios = $pontosProximoNivel - $pontosNivelAtual;
        $pontosAtuais = $pontos - $pontosNivelAtual;

        return min(100, max(0, ($pontosAtuais / $pontosNecessarios) * 100));
    }

    public function obterRanking($limite = 10)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.nome 
            FROM pontuacoes p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.pontos DESC 
            LIMIT " . (int)$limite
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
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

    public function resetarPontosSemanais()
    {
        $stmt = $this->pdo->prepare("UPDATE pontuacoes SET pontos_semana_atual = 0");
        return $stmt->execute();
    }

    public function obterEstatisticasGerais()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total_usuarios, SUM(pontos) as total_pontos, SUM(descartes) as total_descartes FROM pontuacoes");
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        $mediaPontos = $result->total_usuarios > 0 ? round($result->total_pontos / $result->total_usuarios, 2) : 0;

        return [
            'total_usuarios' => $result->total_usuarios,
            'total_pontos' => $result->total_pontos,
            'total_descartes' => $result->total_descartes,
            'media_pontos' => $mediaPontos
        ];
    }
}
