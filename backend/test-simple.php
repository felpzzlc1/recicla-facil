<?php

echo "🔐 Testando login simples...\n\n";

// Dados de teste
$loginData = [
    'email' => 'felpzzl1@gmail.com',
    'senha' => '123456'
];

echo "📤 Enviando requisição de login...\n";
echo "Email: {$loginData['email']}\n";
echo "Senha: {$loginData['senha']}\n\n";

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

echo "📥 Resposta recebida:\n";
echo "Status HTTP: $httpCode\n";
echo "Resposta: $response\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    if ($data['success']) {
        echo "✅ Login bem-sucedido!\n";
        echo "Dados do usuário:\n";
        echo "- ID: {$data['data']['id']}\n";
        echo "- Nome: {$data['data']['nome']}\n";
        echo "- Email: {$data['data']['email']}\n";
        echo "- Token: " . (isset($data['data']['token']) ? $data['data']['token'] : 'NÃO ENCONTRADO') . "\n";
        
        if (isset($data['data']['token'])) {
            echo "\n✅ Token presente na resposta!\n";
            echo "Token: {$data['data']['token']}\n";
        } else {
            echo "\n❌ Token NÃO encontrado na resposta!\n";
            echo "Campos disponíveis: " . implode(', ', array_keys($data['data'])) . "\n";
        }
    } else {
        echo "❌ Login falhou: {$data['message']}\n";
    }
} else {
    echo "❌ Erro HTTP: $httpCode\n";
    echo "Resposta: $response\n";
}

echo "\n🔧 Para testar no navegador:\n";
echo "1. Acesse: http://localhost:9161/frontend/debug-auth.html\n";
echo "2. Clique em 'Testar Login'\n";
echo "3. Verifique se o token é salvo no localStorage\n";
