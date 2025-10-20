<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Configuração do banco de dados
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_DATABASE'] ?? 'recicla_facil',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🧪 Testando sistema de pontuação...\n\n";

try {
    // Verificar se as tabelas existem
    echo "📊 Verificando tabelas...\n";
    
    $tabelas = ['users', 'sessions', 'pontuacoes', 'conquistas'];
    foreach ($tabelas as $tabela) {
        if (Capsule::schema()->hasTable($tabela)) {
            echo "✅ Tabela '$tabela' existe\n";
        } else {
            echo "❌ Tabela '$tabela' NÃO existe\n";
        }
    }
    
    echo "\n👥 Verificando usuários...\n";
    $users = Capsule::table('users')->get();
    echo "Total de usuários: " . count($users) . "\n";
    
    if (count($users) > 0) {
        $user = $users[0];
        echo "Primeiro usuário: {$user->nome} ({$user->email})\n";
        
        // Verificar se tem pontuação
        $pontuacao = Capsule::table('pontuacoes')->where('user_id', $user->id)->first();
        if ($pontuacao) {
            echo "✅ Usuário tem pontuação: {$pontuacao->pontos} pontos (Nível {$pontuacao->nivel})\n";
        } else {
            echo "❌ Usuário NÃO tem pontuação\n";
        }
        
        // Verificar sessões
        $sessoes = Capsule::table('sessions')->where('user_id', $user->id)->get();
        echo "Sessões ativas: " . count($sessoes) . "\n";
    }
    
    echo "\n🔧 Testando API endpoints...\n";
    
    // Simular requisição para estatísticas
    $url = 'http://localhost:9161/api/pontuacao/estatisticas';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer test-token'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "GET /api/pontuacao/estatisticas - Status: $httpCode\n";
    if ($httpCode === 200) {
        echo "✅ API respondendo\n";
    } else {
        echo "❌ API com problema (Status: $httpCode)\n";
        echo "Resposta: $response\n";
    }
    
    echo "\n📋 Próximos passos:\n";
    echo "1. Execute: php backend/setup-pontuacao.php\n";
    echo "2. Execute: php backend/run-migrations.php\n";
    echo "3. Execute: php backend/init-database.php\n";
    echo "4. Acesse: http://localhost:9161\n";
    echo "5. Faça login e vá para 'Minha Pontuação'\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n🔧 Soluções possíveis:\n";
    echo "1. Verifique se o banco de dados está rodando\n";
    echo "2. Verifique as configurações de conexão\n";
    echo "3. Execute as migrations primeiro\n";
}
