<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pontuacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pontos',
        'nivel',
        'nivel_nome',
        'descartes',
        'sequencia_dias',
        'badges_conquistadas',
        'ultima_atualizacao',
        'pontos_semana_atual',
        'total_pontos_ganhos'
    ];

    protected $casts = [
        'pontos' => 'integer',
        'nivel' => 'integer',
        'descartes' => 'integer',
        'sequencia_dias' => 'integer',
        'badges_conquistadas' => 'integer',
        'pontos_semana_atual' => 'integer',
        'total_pontos_ganhos' => 'integer',
        'ultima_atualizacao' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conquistas()
    {
        return $this->hasMany(Conquista::class);
    }

    /**
     * Calcula o nível baseado nos pontos
     */
    public function calcularNivel($pontos)
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

    /**
     * Calcula pontos necessários para o próximo nível
     */
    public function pontosParaProximoNivel()
    {
        $niveis = [
            1 => 100, 2 => 500, 3 => 1000, 4 => 2500, 5 => 5000,
            6 => 10000, 7 => 25000, 8 => 50000, 9 => 100000, 10 => 999999
        ];
        
        $proximoNivel = $this->nivel + 1;
        $pontosNecessarios = $niveis[$proximoNivel] ?? 999999;
        
        return max(0, $pontosNecessarios - $this->pontos);
    }

    /**
     * Adiciona pontos e atualiza nível
     */
    public function adicionarPontos($pontos, $motivo = 'descarte')
    {
        $hoje = now()->startOfDay();
        $ultimaAtualizacao = $this->ultima_atualizacao ? $this->ultima_atualizacao->startOfDay() : null;
        
        // Verificar se precisa resetar pontos semanais (nova semana)
        $inicioSemana = now()->startOfWeek();
        if (!$this->ultima_atualizacao || $this->ultima_atualizacao->startOfWeek()->lt($inicioSemana)) {
            $this->pontos_semana_atual = 0;
        }
        
        // Verificar sequência de dias
        if ($ultimaAtualizacao) {
            $diferencaDias = $hoje->diffInDays($ultimaAtualizacao);
            
            if ($diferencaDias == 0) {
                // Mesmo dia - não incrementar sequência
            } elseif ($diferencaDias == 1) {
                // Dia seguinte - incrementar sequência
                $this->sequencia_dias += 1;
            } else {
                // Mais de 1 dia de diferença - resetar sequência
                $this->sequencia_dias = 1;
            }
        } else {
            // Primeira vez - iniciar sequência
            $this->sequencia_dias = 1;
        }
        
        $this->pontos += $pontos;
        $this->total_pontos_ganhos += $pontos;
        $this->pontos_semana_atual += $pontos;
        $this->descartes += 1;
        
        // Recalcular nível
        $nivelInfo = $this->calcularNivel($this->pontos);
        $this->nivel = $nivelInfo['nivel'];
        $this->nivel_nome = $nivelInfo['nome'];
        
        $this->ultima_atualizacao = now();
        $this->save();
        
        return $this;
    }

    /**
     * Verifica se conquistou nova conquista
     */
    public function verificarConquistas()
    {
        // Usar as conquistas definidas no repository para evitar duplicação
        $conquistasDisponiveis = [
            ['nome' => 'Iniciante', 'icone' => '🌱', 'requisito' => 100, 'tipo' => 'pontos'],
            ['nome' => 'Reciclador', 'icone' => '♻️', 'requisito' => 500, 'tipo' => 'pontos'],
            ['nome' => 'Eco Warrior', 'icone' => '☀️', 'requisito' => 1000, 'tipo' => 'pontos'],
            ['nome' => 'Guardião Verde', 'icone' => '🌳', 'requisito' => 2500, 'tipo' => 'pontos'],
            ['nome' => 'Mestre Sustentável', 'icone' => '🏆', 'requisito' => 5000, 'tipo' => 'pontos'],
            ['nome' => 'Primeiro Descarte', 'icone' => '🎯', 'requisito' => 1, 'tipo' => 'descartes'],
            ['nome' => 'Sequência de 7 dias', 'icone' => '⚡', 'requisito' => 7, 'tipo' => 'sequencia'],
            ['nome' => 'Sequência de 30 dias', 'icone' => '🔥', 'requisito' => 30, 'tipo' => 'sequencia']
        ];

        $novasConquistas = [];
        
        foreach ($conquistasDisponiveis as $conquista) {
            $jaConquistada = $this->conquistas()
                ->where('nome', $conquista['nome'])
                ->exists();
                
            if (!$jaConquistada) {
                $valorAtual = match($conquista['tipo']) {
                    'pontos' => $this->pontos,
                    'descartes' => $this->descartes,
                    'sequencia' => $this->sequencia_dias,
                    default => 0
                };
                
                if ($valorAtual >= $conquista['requisito']) {
                    $this->conquistas()->create([
                        'nome' => $conquista['nome'],
                        'icone' => $conquista['icone'],
                        'desbloqueada_em' => now()
                    ]);
                    
                    // Atualizar contador de badges
                    $this->badges_conquistadas += 1;
                    $this->save();
                    
                    $novasConquistas[] = $conquista;
                }
            }
        }
        
        return $novasConquistas;
    }
}
