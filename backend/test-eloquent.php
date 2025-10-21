<?php

// Configuração do banco de dados
$host = 'db';
$dbname = 'recicla_facil';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar Eloquent
    require_once __DIR__ . '/vendor/autoload.php';
    
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => $host,
        'database' => $dbname,
        'username' => $username,
        'password' => $password,
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ]);
    
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    
    echo "🧪 Testando Eloquent com modelos...\n\n";
    
    // Testar modelo User
    echo "👤 Testando modelo User...\n";
    $user = \App\Models\User::find(1);
    if ($user) {
        echo "✅ Usuário encontrado: {$user->nome} ({$user->email})\n";
    } else {
        echo "❌ Usuário não encontrado\n";
    }
    
    // Testar modelo Pontuacao
    echo "\n🎯 Testando modelo Pontuacao...\n";
    $pontuacao = \App\Models\Pontuacao::where('user_id', 1)->first();
    
    if ($pontuacao) {
        echo "✅ Pontuação encontrada: {$pontuacao->pontos} pontos (Nível {$pontuacao->nivel})\n";
        
        // Testar adição de pontos
        echo "\n🔧 Testando adição de pontos...\n";
        echo "Pontos antes: {$pontuacao->pontos}\n";
        
        $pontuacao->adicionarPontos(25, 'teste-eloquent');
        
        echo "Pontos depois: {$pontuacao->pontos}\n";
        echo "Descartes: {$pontuacao->descartes}\n";
        echo "Sequência: {$pontuacao->sequencia_dias}\n";
        
        // Verificar se foi salvo
        $pontuacaoVerificacao = \App\Models\Pontuacao::where('user_id', 1)->first();
        if ($pontuacaoVerificacao && $pontuacaoVerificacao->pontos == $pontuacao->pontos) {
            echo "\n🎉 SUCESSO: Dados salvos com Eloquent!\n";
        } else {
            echo "\n❌ ERRO: Dados não foram salvos com Eloquent!\n";
        }
    } else {
        echo "❌ Nenhuma pontuação encontrada\n";
        
        // Criar pontuação
        echo "Criando pontuação...\n";
        $pontuacao = new \App\Models\Pontuacao();
        $pontuacao->user_id = 1;
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
    }
    
    echo "\n🎉 Teste do Eloquent concluído!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
