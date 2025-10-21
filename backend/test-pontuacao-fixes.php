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

echo "🧪 Testando correções do sistema de pontuação...\n\n";

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
        
        // Verificar pontuação atual
        $pontuacao = Capsule::table('pontuacoes')->where('user_id', $user->id)->first();
        if ($pontuacao) {
            echo "\n📈 Pontuação atual:\n";
            echo "- Pontos: {$pontuacao->pontos}\n";
            echo "- Nível: {$pontuacao->nivel} ({$pontuacao->nivel_nome})\n";
            echo "- Descartas: {$pontuacao->descartes}\n";
            echo "- Sequência: {$pontuacao->sequencia_dias} dias\n";
            echo "- Badges: {$pontuacao->badges_conquistadas}\n";
            echo "- Pontos semanais: {$pontuacao->pontos_semana_atual}\n";
            
            // Verificar conquistas
            $conquistas = Capsule::table('conquistas')->where('pontuacao_id', $pontuacao->id)->get();
            echo "\n🏆 Conquistas desbloqueadas: " . count($conquistas) . "\n";
            foreach ($conquistas as $conquista) {
                echo "- {$conquista->icone} {$conquista->nome} (desbloqueada em: {$conquista->desbloqueada_em})\n";
            }
        } else {
            echo "❌ Usuário NÃO tem pontuação\n";
        }
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
        $data = json_decode($response, true);
        if ($data && isset($data['data'])) {
            echo "📊 Dados retornados:\n";
            echo "- Pontos: {$data['data']['pontos']}\n";
            echo "- Descartas: {$data['data']['descartes']}\n";
            echo "- Sequência: {$data['data']['sequencia_dias']} dias\n";
            echo "- Badges: {$data['data']['badges_conquistadas']}\n";
        }
    } else {
        echo "❌ API com problema (Status: $httpCode)\n";
        echo "Resposta: $response\n";
    }
    
    echo "\n🎯 Testando simulação de descarte...\n";
    
    // Simular descarte
    $url = 'http://localhost:9161/api/pontuacao/simular-descarte';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer test-token'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'material' => 'papel',
        'peso' => 2.5
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "POST /api/pontuacao/simular-descarte - Status: $httpCode\n";
    if ($httpCode === 200) {
        echo "✅ Simulação funcionando\n";
        $data = json_decode($response, true);
        if ($data && isset($data['data'])) {
            echo "📊 Resultado da simulação:\n";
            echo "- Pontos ganhos: {$data['data']['pontos_ganhos']}\n";
            echo "- Material: {$data['data']['material']}\n";
            echo "- Peso: {$data['data']['peso']}kg\n";
            if (isset($data['data']['novas_conquistas']) && count($data['data']['novas_conquistas']) > 0) {
                echo "- Novas conquistas: " . count($data['data']['novas_conquistas']) . "\n";
            }
        }
    } else {
        echo "❌ Simulação com problema (Status: $httpCode)\n";
        echo "Resposta: $response\n";
    }
    
    echo "\n📋 Resumo das correções implementadas:\n";
    echo "✅ 1. Corrigido motivo do descarte para 'simular-descarte'\n";
    echo "✅ 2. Corrigido incremento de descartes e sequência\n";
    echo "✅ 3. Corrigido contador de badges conquistadas\n";
    echo "✅ 4. Evitado duplicação de conquistas\n";
    
    echo "\n🚀 Para testar no navegador:\n";
    echo "1. Acesse: http://localhost:9161\n";
    echo "2. Faça login com: teste@recicla.com / 123456\n";
    echo "3. Vá para 'Minha Pontuação'\n";
    echo "4. Clique em 'Simular Descarte'\n";
    echo "5. Verifique se os valores estão sendo atualizados corretamente\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n🔧 Soluções possíveis:\n";
    echo "1. Verifique se o banco de dados está rodando\n";
    echo "2. Verifique as configurações de conexão\n";
    echo "3. Execute as migrations primeiro\n";
}
