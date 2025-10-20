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

echo "🔐 Testando sistema de autenticação...\n\n";

try {
    // Testar login via API
    $loginData = [
        'email' => 'teste@recicla.com',
        'senha' => '123456'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:9161/api/auth/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "POST /api/auth/login - Status: $httpCode\n";
    echo "Resposta: $response\n\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data['success'] && isset($data['data']['token'])) {
            $token = $data['data']['token'];
            echo "✅ Login bem-sucedido! Token: $token\n\n";
            
            // Testar endpoint de pontuação com token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:9161/api/pontuacao/estatisticas');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "GET /api/pontuacao/estatisticas - Status: $httpCode\n";
            echo "Resposta: $response\n\n";
            
            if ($httpCode === 200) {
                echo "✅ Sistema de pontuação funcionando!\n";
            } else {
                echo "❌ Erro ao acessar pontuação\n";
            }
        } else {
            echo "❌ Login falhou\n";
        }
    } else {
        echo "❌ Erro no login (Status: $httpCode)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
