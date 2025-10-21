<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Configuração do banco de dados
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'db',
    'database' => 'recicla_facil',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🧪 Testando salvamento de pontuação...\n\n";

try {
    // Verificar se as tabelas existem
    echo "📊 Verificando tabelas...\n";
    
    $tabelas = ['users', 'pontuacoes'];
    foreach ($tabelas as $tabela) {
        if (Capsule::schema()->hasTable($tabela)) {
            echo "✅ Tabela '$tabela' existe\n";
        } else {
            echo "❌ Tabela '$tabela' NÃO existe\n";
        }
    }
    
    // Verificar usuários existentes
    echo "\n👥 Verificando usuários...\n";
    $users = Capsule::table('users')->get();
    echo "Total de usuários: " . count($users) . "\n";
    
    if (count($users) == 0) {
        echo "❌ Nenhum usuário encontrado. Criando usuário de teste...\n";
        
        $userId = Capsule::table('users')->insertGetId([
            'nome' => 'Usuário Teste',
            'email' => 'teste@teste.com',
            'telefone' => '11999999999',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'pontuacao' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Usuário criado com ID: $userId\n";
    } else {
        $userId = $users[0]->id;
        echo "✅ Usando usuário existente ID: $userId\n";
    }
    
    // Verificar pontuação existente
    echo "\n🎯 Verificando pontuação existente...\n";
    $pontuacaoExistente = Capsule::table('pontuacoes')->where('user_id', $userId)->first();
    
    if ($pontuacaoExistente) {
        echo "Pontuação existente: {$pontuacaoExistente->pontos} pontos (Nível {$pontuacaoExistente->nivel})\n";
    } else {
        echo "❌ Nenhuma pontuação encontrada para o usuário\n";
    }
    
    // Testar criação de pontuação usando Eloquent
    echo "\n🔧 Testando criação de pontuação com Eloquent...\n";
    
    // Buscar usuário
    $user = \App\Models\User::find($userId);
    if (!$user) {
        throw new Exception("Usuário não encontrado");
    }
    
    echo "Usuário encontrado: {$user->nome}\n";
    
    // Criar ou buscar pontuação
    $pontuacao = \App\Models\Pontuacao::where('user_id', $userId)->first();
    
    if (!$pontuacao) {
        echo "Criando nova pontuação...\n";
        $pontuacao = new \App\Models\Pontuacao();
        $pontuacao->user_id = $userId;
        $pontuacao->pontos = 0;
        $pontuacao->nivel = 1;
        $pontuacao->nivel_nome = 'Iniciante';
        $pontuacao->descartes = 0;
        $pontuacao->sequencia_dias = 0;
        $pontuacao->badges_conquistadas = 0;
        $pontuacao->pontos_semana_atual = 0;
        $pontuacao->total_pontos_ganhos = 0;
        $pontuacao->ultima_atualizacao = now();
        $pontuacao->save();
        
        echo "✅ Pontuação criada com ID: {$pontuacao->id}\n";
    } else {
        echo "✅ Pontuação existente encontrada com ID: {$pontuacao->id}\n";
    }
    
    // Testar adição de pontos
    echo "\n🎯 Testando adição de pontos...\n";
    echo "Pontos antes: {$pontuacao->pontos}\n";
    
    $pontuacao->adicionarPontos(50, 'teste');
    
    echo "Pontos depois: {$pontuacao->pontos}\n";
    echo "Descartes: {$pontuacao->descartes}\n";
    echo "Sequência dias: {$pontuacao->sequencia_dias}\n";
    
    // Verificar se foi salvo no banco
    $pontuacaoVerificacao = Capsule::table('pontuacoes')->where('user_id', $userId)->first();
    if ($pontuacaoVerificacao) {
        echo "\n✅ Dados salvos no banco:\n";
        echo "- Pontos: {$pontuacaoVerificacao->pontos}\n";
        echo "- Descartes: {$pontuacaoVerificacao->descartes}\n";
        echo "- Sequência: {$pontuacaoVerificacao->sequencia_dias}\n";
        echo "- Última atualização: {$pontuacaoVerificacao->ultima_atualizacao}\n";
    } else {
        echo "\n❌ ERRO: Dados não foram salvos no banco!\n";
    }
    
    echo "\n🎉 Teste concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
