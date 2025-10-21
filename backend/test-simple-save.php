<?php

// Configuração do banco de dados
$host = 'db';
$dbname = 'recicla_facil';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🧪 Testando salvamento de pontuação com PDO...\n\n";
    
    // Verificar se as tabelas existem
    echo "📊 Verificando tabelas...\n";
    
    $tabelas = ['users', 'pontuacoes'];
    foreach ($tabelas as $tabela) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela '$tabela' existe\n";
        } else {
            echo "❌ Tabela '$tabela' NÃO existe\n";
        }
    }
    
    // Verificar usuários existentes
    echo "\n👥 Verificando usuários...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total de usuários: " . $result['total'] . "\n";
    
    if ($result['total'] == 0) {
        echo "❌ Nenhum usuário encontrado. Criando usuário de teste...\n";
        
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, telefone, senha, pontuacao) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            'Usuário Teste',
            'teste@teste.com',
            '11999999999',
            password_hash('123456', PASSWORD_DEFAULT),
            0
        ]);
        
        $userId = $pdo->lastInsertId();
        echo "✅ Usuário criado com ID: $userId\n";
    } else {
        $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $user['id'];
        echo "✅ Usando usuário existente ID: $userId\n";
    }
    
    // Verificar pontuação existente
    echo "\n🎯 Verificando pontuação existente...\n";
    $stmt = $pdo->prepare("SELECT * FROM pontuacoes WHERE user_id = ?");
    $stmt->execute([$userId]);
    $pontuacaoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pontuacaoExistente) {
        echo "Pontuação existente: {$pontuacaoExistente['pontos']} pontos (Nível {$pontuacaoExistente['nivel']})\n";
    } else {
        echo "❌ Nenhuma pontuação encontrada para o usuário\n";
    }
    
    // Criar ou atualizar pontuação
    echo "\n🔧 Testando criação/atualização de pontuação...\n";
    
    if (!$pontuacaoExistente) {
        echo "Criando nova pontuação...\n";
        $stmt = $pdo->prepare("INSERT INTO pontuacoes (user_id, pontos, nivel, nivel_nome, descartes, sequencia_dias, badges_conquistadas, pontos_semana_atual, total_pontos_ganhos, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            0,
            1,
            'Iniciante',
            0,
            0,
            0,
            0,
            0,
            date('Y-m-d H:i:s')
        ]);
        
        $pontuacaoId = $pdo->lastInsertId();
        echo "✅ Pontuação criada com ID: $pontuacaoId\n";
    } else {
        echo "✅ Pontuação existente encontrada com ID: {$pontuacaoExistente['id']}\n";
    }
    
    // Testar adição de pontos
    echo "\n🎯 Testando adição de pontos...\n";
    
    // Simular lógica de adicionar pontos
    $pontosAtuais = $pontuacaoExistente ? $pontuacaoExistente['pontos'] : 0;
    $descartesAtuais = $pontuacaoExistente ? $pontuacaoExistente['descartes'] : 0;
    $sequenciaAtual = $pontuacaoExistente ? $pontuacaoExistente['sequencia_dias'] : 0;
    
    $novosPontos = $pontosAtuais + 50;
    $novosDescartes = $descartesAtuais + 1;
    $novaSequencia = $sequenciaAtual + 1;
    
    echo "Pontos antes: $pontosAtuais\n";
    echo "Descartes antes: $descartesAtuais\n";
    echo "Sequência antes: $sequenciaAtual\n";
    
    // Atualizar no banco
    $stmt = $pdo->prepare("UPDATE pontuacoes SET pontos = ?, descartes = ?, sequencia_dias = ?, ultima_atualizacao = ? WHERE user_id = ?");
    $stmt->execute([
        $novosPontos,
        $novosDescartes,
        $novaSequencia,
        date('Y-m-d H:i:s'),
        $userId
    ]);
    
    echo "Pontos depois: $novosPontos\n";
    echo "Descartes depois: $novosDescartes\n";
    echo "Sequência depois: $novaSequencia\n";
    
    // Verificar se foi salvo no banco
    $stmt = $pdo->prepare("SELECT * FROM pontuacoes WHERE user_id = ?");
    $stmt->execute([$userId]);
    $pontuacaoVerificacao = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pontuacaoVerificacao) {
        echo "\n✅ Dados salvos no banco:\n";
        echo "- Pontos: {$pontuacaoVerificacao['pontos']}\n";
        echo "- Descartes: {$pontuacaoVerificacao['descartes']}\n";
        echo "- Sequência: {$pontuacaoVerificacao['sequencia_dias']}\n";
        echo "- Última atualização: {$pontuacaoVerificacao['ultima_atualizacao']}\n";
        
        if ($pontuacaoVerificacao['pontos'] == $novosPontos && 
            $pontuacaoVerificacao['descartes'] == $novosDescartes && 
            $pontuacaoVerificacao['sequencia_dias'] == $novaSequencia) {
            echo "\n🎉 SUCESSO: Dados foram salvos corretamente no banco!\n";
        } else {
            echo "\n❌ ERRO: Dados não foram salvos corretamente!\n";
        }
    } else {
        echo "\n❌ ERRO: Dados não foram salvos no banco!\n";
    }
    
    echo "\n🎉 Teste concluído!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
