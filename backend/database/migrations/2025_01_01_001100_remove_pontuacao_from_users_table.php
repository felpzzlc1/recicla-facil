<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePontuacaoFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Sincronizar dados existentes antes de remover a coluna
        $this->syncPontuacaoData();
        
        // Remover coluna pontuacao da tabela users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pontuacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Adicionar coluna pontuacao de volta
        Schema::table('users', function (Blueprint $table) {
            $table->integer('pontuacao')->default(0);
        });
        
        // Sincronizar dados de volta
        $this->syncPontuacaoDataBack();
    }
    
    /**
     * Sincronizar dados de pontuacao da tabela users para pontuacoes
     */
    private function syncPontuacaoData()
    {
        $pdo = new PDO("mysql:host=db;dbname=recicla_facil;charset=utf8mb4", 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Buscar todos os usuários com pontuação
        $stmt = $pdo->query("SELECT id, pontuacao FROM users WHERE pontuacao > 0");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user) {
            // Verificar se já existe registro na tabela pontuacoes
            $stmt = $pdo->prepare("SELECT id FROM pontuacoes WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Atualizar pontos existentes
                $stmt = $pdo->prepare("UPDATE pontuacoes SET pontos = ? WHERE user_id = ?");
                $stmt->execute([$user['pontuacao'], $user['id']]);
            } else {
                // Criar novo registro
                $nivel = $this->calcularNivel($user['pontuacao']);
                $stmt = $pdo->prepare("INSERT INTO pontuacoes (user_id, pontos, nivel, nivel_nome, descartes, sequencia_dias, badges_conquistadas, pontos_semana_atual, total_pontos_ganhos, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user['id'],
                    $user['pontuacao'],
                    $nivel['nivel'],
                    $nivel['nome'],
                    1, // Assumir pelo menos 1 descarte
                    1, // Sequência inicial
                    0, // Sem badges
                    $user['pontuacao'], // Pontos semanais
                    $user['pontuacao'], // Total pontos ganhos
                    date('Y-m-d H:i:s')
                ]);
            }
        }
    }
    
    /**
     * Sincronizar dados de volta da tabela pontuacoes para users
     */
    private function syncPontuacaoDataBack()
    {
        $pdo = new PDO("mysql:host=db;dbname=recicla_facil;charset=utf8mb4", 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Buscar todos os registros de pontuação
        $stmt = $pdo->query("SELECT user_id, pontos FROM pontuacoes");
        $pontuacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($pontuacoes as $pontuacao) {
            $stmt = $pdo->prepare("UPDATE users SET pontuacao = ? WHERE id = ?");
            $stmt->execute([$pontuacao['pontos'], $pontuacao['user_id']]);
        }
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
}
