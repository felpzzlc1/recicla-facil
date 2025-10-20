<?php

echo "🚀 Configurando sistema completo de pontuação...\n\n";

// 1. Executar migrations
echo "📊 Executando migrations...\n";
$output = shell_exec('php run-migrations.php 2>&1');
echo $output . "\n";

// 2. Testar sistema
echo "🧪 Testando sistema...\n";
$output = shell_exec('php test-pontuacao.php 2>&1');
echo $output . "\n";

// 3. Testar autenticação
echo "🔐 Testando autenticação...\n";
$output = shell_exec('php test-auth.php 2>&1');
echo $output . "\n";

echo "✨ Setup completo!\n";
echo "\n📋 Próximos passos:\n";
echo "1. Acesse: http://localhost:9161\n";
echo "2. Faça login com: teste@recicla.com / 123456\n";
echo "3. Vá para 'Minha Pontuação'\n";
echo "4. Teste a funcionalidade de simular descarte\n";
