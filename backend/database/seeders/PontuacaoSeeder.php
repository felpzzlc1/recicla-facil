<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pontuacao;
use App\Models\Conquista;

class PontuacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar pontuação para usuários existentes
        $users = User::all();
        
        foreach ($users as $user) {
            // Verificar se já existe pontuação para o usuário
            if (!Pontuacao::where('user_id', $user->id)->exists()) {
                $pontuacao = Pontuacao::create([
                    'user_id' => $user->id,
                    'pontos' => rand(0, 5000),
                    'nivel' => 1,
                    'nivel_nome' => 'Iniciante',
                    'descartes' => rand(0, 50),
                    'sequencia_dias' => rand(0, 30),
                    'badges_conquistadas' => 0,
                    'pontos_semana_atual' => rand(0, 500),
                    'total_pontos_ganhos' => rand(0, 5000),
                    'ultima_atualizacao' => now()
                ]);
                
                // Recalcular nível baseado nos pontos
                $nivelInfo = $pontuacao->calcularNivel($pontuacao->pontos);
                $pontuacao->update([
                    'nivel' => $nivelInfo['nivel'],
                    'nivel_nome' => $nivelInfo['nome']
                ]);
                
                // Criar algumas conquistas aleatórias
                $conquistasDisponiveis = [
                    ['nome' => 'Iniciante', 'icone' => '🌱'],
                    ['nome' => 'Reciclador', 'icone' => '♻️'],
                    ['nome' => 'Eco Warrior', 'icone' => '☀️'],
                    ['nome' => 'Primeiro Descarte', 'icone' => '🎯'],
                    ['nome' => 'Sequência de 7 dias', 'icone' => '⚡']
                ];
                
                $numConquistas = rand(0, min(3, count($conquistasDisponiveis)));
                $conquistasSelecionadas = array_slice($conquistasDisponiveis, 0, $numConquistas);
                
                foreach ($conquistasSelecionadas as $conquista) {
                    Conquista::create([
                        'pontuacao_id' => $pontuacao->id,
                        'nome' => $conquista['nome'],
                        'icone' => $conquista['icone'],
                        'desbloqueada_em' => now()->subDays(rand(1, 30))
                    ]);
                }
                
                // Atualizar contador de badges
                $pontuacao->update([
                    'badges_conquistadas' => $numConquistas
                ]);
            }
        }
        
        // Criar usuário de exemplo com pontuação alta
        $usuarioExemplo = User::firstOrCreate(
            ['email' => 'exemplo@reciclafacil.com'],
            [
                'nome' => 'Usuário Exemplo',
                'telefone' => '11999999999',
                'senha' => '123456'
            ]
        );
        
        if (!Pontuacao::where('user_id', $usuarioExemplo->id)->exists()) {
            $pontuacaoExemplo = Pontuacao::create([
                'user_id' => $usuarioExemplo->id,
                'pontos' => 8350,
                'nivel' => 12,
                'nivel_nome' => 'Reciclador Expert',
                'descartes' => 156,
                'sequencia_dias' => 23,
                'badges_conquistadas' => 5,
                'pontos_semana_atual' => 250,
                'total_pontos_ganhos' => 8350,
                'ultima_atualizacao' => now()
            ]);
            
            // Criar conquistas para o usuário exemplo
            $conquistasExemplo = [
                ['nome' => 'Iniciante', 'icone' => '🌱'],
                ['nome' => 'Reciclador', 'icone' => '♻️'],
                ['nome' => 'Eco Warrior', 'icone' => '☀️'],
                ['nome' => 'Primeiro Descarte', 'icone' => '🎯'],
                ['nome' => 'Sequência de 7 dias', 'icone' => '⚡']
            ];
            
            foreach ($conquistasExemplo as $conquista) {
                Conquista::create([
                    'pontuacao_id' => $pontuacaoExemplo->id,
                    'nome' => $conquista['nome'],
                    'icone' => $conquista['icone'],
                    'desbloqueada_em' => now()->subDays(rand(1, 30))
                ]);
            }
        }
    }
}
