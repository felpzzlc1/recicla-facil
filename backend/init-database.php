<?php

// Script para inicializar o banco de dados
$host = 'db';
$dbname = 'recicla_facil';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado ao banco de dados com sucesso!\n";
    
    // Criar tabela users se não existir
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        telefone VARCHAR(20) NOT NULL,
        senha VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabela users criada/verificada\n";
    
    // Criar tabela coletas se não existir
    $sql = "CREATE TABLE IF NOT EXISTS coletas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        material VARCHAR(255) NOT NULL,
        quantidade DECIMAL(10,2) NOT NULL,
        endereco VARCHAR(500) NOT NULL,
        data_preferida DATE NOT NULL,
        obs TEXT,
        status ENUM('ABERTA', 'CONCLUIDA') DEFAULT 'ABERTA',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabela coletas criada/verificada\n";
    
    // Criar tabela doacoes se não existir
    $sql = "CREATE TABLE IF NOT EXISTS doacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        material VARCHAR(255) NOT NULL,
        qtd INT NOT NULL,
        contato VARCHAR(255) NOT NULL,
        entregue BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabela doacoes criada/verificada\n";
    
    // Criar tabela ponto_coletas se não existir (nome correto do banco)
    $sql = "CREATE TABLE IF NOT EXISTS ponto_coletas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        tipo VARCHAR(255) NOT NULL,
        endereco VARCHAR(500) NOT NULL,
        latitude DECIMAL(10,8) NULL,
        longitude DECIMAL(11,8) NULL,
        telefone VARCHAR(20) NULL,
        horario TEXT NULL,
        materiais_aceitos JSON NULL,
        ativo BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabela ponto_coletas criada/verificada\n";
    
    // Criar tabela cronograma_coletas se não existir
    $sql = "CREATE TABLE IF NOT EXISTS cronograma_coletas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        material VARCHAR(255) NOT NULL,
        dia_semana VARCHAR(50) NOT NULL,
        horario_inicio TIME NOT NULL,
        horario_fim TIME NOT NULL,
        endereco VARCHAR(500) NOT NULL,
        bairro VARCHAR(255) NOT NULL,
        cidade VARCHAR(255) NOT NULL,
        estado VARCHAR(2) NOT NULL,
        latitude DECIMAL(10,8) NULL,
        longitude DECIMAL(11,8) NULL,
        observacoes TEXT NULL,
        ativo BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabela cronograma_coletas criada/verificada\n";
    
    // Criar tabela sessions se não existir
    $sql = "CREATE TABLE IF NOT EXISTS sessions (
        id VARCHAR(255) PRIMARY KEY,
        user_id INT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        payload LONGTEXT NOT NULL,
        last_activity INT NOT NULL,
        INDEX idx_user_id (user_id),
        INDEX idx_last_activity (last_activity),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabela sessions criada/verificada\n";
    
    // Criar tabela pontuacoes se não existir
    $sql = "CREATE TABLE IF NOT EXISTS pontuacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pontos INT DEFAULT 0,
        nivel INT DEFAULT 1,
        nivel_nome VARCHAR(100) DEFAULT 'Iniciante',
        descartes INT DEFAULT 0,
        sequencia_dias INT DEFAULT 0,
        badges_conquistadas INT DEFAULT 0,
        pontos_semana_atual INT DEFAULT 0,
        total_pontos_ganhos INT DEFAULT 0,
        ultima_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_pontuacao (user_id)
    )";
    $pdo->exec($sql);
    echo "Tabela pontuacoes criada/verificada\n";
    
    // Criar tabela conquistas se não existir
    $sql = "CREATE TABLE IF NOT EXISTS conquistas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        conquista_id INT NOT NULL,
        conquistada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        progresso INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_conquista (user_id, conquista_id)
    )";
    $pdo->exec($sql);
    echo "Tabela conquistas criada/verificada\n";
    
    // Criar tabela tipos_conquistas se não existir
    $sql = "CREATE TABLE IF NOT EXISTS tipos_conquistas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        icone VARCHAR(10),
        requisito INT NOT NULL,
        pontos_bonus INT DEFAULT 0,
        ativo BOOLEAN DEFAULT TRUE
    )";
    $pdo->exec($sql);
    echo "Tabela tipos_conquistas criada/verificada\n";
    
    // Criar tabela recompensas se não existir
    $sql = "CREATE TABLE IF NOT EXISTS recompensas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        descricao TEXT,
        icone VARCHAR(10),
        categoria VARCHAR(100),
        categoria_icone VARCHAR(10),
        pontos INT NOT NULL,
        disponivel INT DEFAULT 0,
        ativo BOOLEAN DEFAULT TRUE,
        imagem_url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabela recompensas criada/verificada\n";
    
    // Criar tabela resgate_recompensas se não existir
    $sql = "CREATE TABLE IF NOT EXISTS resgate_recompensas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        recompensa_id INT NOT NULL,
        pontos_gastos INT NOT NULL,
        status ENUM('PENDENTE', 'APROVADO', 'REJEITADO', 'ENTREGUE') DEFAULT 'PENDENTE',
        data_resgate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        observacoes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (recompensa_id) REFERENCES recompensas(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabela resgate_recompensas criada/verificada\n";
    
    // Inserir dados de exemplo se não existirem
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        // Inserir usuário demo
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            'Usuário Demo',
            'demo@recicla.com',
            '(11) 99999-9999',
            password_hash('123456', PASSWORD_DEFAULT)
        ]);
        echo "Usuário demo criado\n";
        
        // Inserir pontos de coleta
        $pontos = [
            ['Eco Ponto Centro', 'Público', 'Av. Central, 100', -23.5505, -46.6333, '(11) 1234-5678', 'Seg-Sex: 8h-18h | Sáb: 8h-12h', '["Papel", "Plástico"]'],
            ['Coleta Verde', 'Cooperativa', 'Rua das Flores, 200', -23.5515, -46.6343, '(11) 2345-6789', 'Seg-Sex: 7h-19h | Sáb: 7h-13h', '["Vidro", "Metal"]'],
            ['Recicla Bairro', 'Privado', 'Praça da Matriz, 50', -23.5495, -46.6323, '(11) 3456-7890', 'Seg-Sex: 8h-17h | Sáb: 8h-12h', '["Eletrônicos", "Papel"]']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO ponto_coletas (nome, tipo, endereco, latitude, longitude, telefone, horario, materiais_aceitos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($pontos as $ponto) {
            $stmt->execute($ponto);
        }
        echo "Pontos de coleta criados\n";
        
        // Inserir cronogramas de coleta de exemplo
        $cronogramas = [
            ['Papel', 'Segunda-feira', '08:00:00', '12:00:00', 'Av. Central, 100', 'Centro', 'São Paulo', 'SP', -23.5505, -46.6333, 'Coleta de papel reciclável'],
            ['Plástico', 'Terça-feira', '08:00:00', '12:00:00', 'Rua das Flores, 200', 'Jardim', 'São Paulo', 'SP', -23.5515, -46.6343, 'Coleta de plástico reciclável'],
            ['Vidro', 'Quarta-feira', '08:00:00', '12:00:00', 'Praça da Matriz, 50', 'Centro', 'São Paulo', 'SP', -23.5495, -46.6323, 'Coleta de vidro reciclável'],
            ['Metal', 'Quinta-feira', '08:00:00', '12:00:00', 'Av. Central, 100', 'Centro', 'São Paulo', 'SP', -23.5505, -46.6333, 'Coleta de metal reciclável'],
            ['Eletrônicos', 'Sexta-feira', '08:00:00', '12:00:00', 'Rua das Flores, 200', 'Jardim', 'São Paulo', 'SP', -23.5515, -46.6343, 'Coleta de eletrônicos']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO cronograma_coletas (material, dia_semana, horario_inicio, horario_fim, endereco, bairro, cidade, estado, latitude, longitude, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($cronogramas as $cronograma) {
            $stmt->execute($cronograma);
        }
        echo "Cronogramas de coleta criados\n";
        
        // Inserir conquistas padrão se não existirem
        $stmt = $pdo->query("SELECT COUNT(*) FROM tipos_conquistas");
        if ($stmt->fetchColumn() == 0) {
            $conquistas = [
                ['Primeiro Descarte', 'Realize seu primeiro descarte', '🌱', 1, 10],
                ['Reciclador Iniciante', 'Realize 5 descartes', '♻️', 5, 25],
                ['Eco-amigo', 'Realize 10 descartes', '🌍', 10, 50],
                ['Guardião Verde', 'Realize 25 descartes', '🛡️', 25, 100],
                ['Defensor do Planeta', 'Realize 50 descartes', '🌿', 50, 200],
                ['Herói Ambiental', 'Realize 100 descartes', '🏆', 100, 500],
                ['Mestre da Reciclagem', 'Realize 250 descartes', '👑', 250, 1000],
                ['Lenda Verde', 'Realize 500 descartes', '🌟', 500, 2500]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO tipos_conquistas (nome, descricao, icone, requisito, pontos_bonus) VALUES (?, ?, ?, ?, ?)");
            foreach ($conquistas as $conquista) {
                $stmt->execute($conquista);
            }
            echo "Conquistas padrão criadas\n";
        }
        
        // Inserir recompensas padrão se não existirem
        $stmt = $pdo->query("SELECT COUNT(*) FROM recompensas");
        if ($stmt->fetchColumn() == 0) {
            $recompensas = [
                ['Vale Compras R$ 50', 'Vale compras no valor de R$ 50,00 para usar em estabelecimentos parceiros', '🛍️', 'Compras', '✓', 5000, 15],
                ['Café Grátis', 'Café grátis em estabelecimentos parceiros', '☕', 'Gastronomia', '☕', 500, 50],
                ['Ingresso Cinema', 'Ingresso para cinema em qualquer filme em cartaz', '🎬', 'Entretenimento', '🎬', 3000, 10],
                ['Kit Sustentável', 'Kit com produtos sustentáveis e ecológicos', '🌱', 'Eco', '🎁', 2000, 25],
                ['Vale Compras R$ 100', 'Vale compras no valor de R$ 100,00 para usar em estabelecimentos parceiros', '🛒', 'Compras', '✓', 10000, 8],
                ['Experiência Eco-Turismo', 'Passeio ecológico em parque natural com guia especializado', '🏔️', 'Turismo', '🧭', 15000, 5],
                ['Desconto 20% Loja Verde', 'Desconto de 20% em produtos sustentáveis na Loja Verde', '🌿', 'Desconto', '💰', 1500, 30],
                ['Livro Sustentabilidade', 'Livro sobre sustentabilidade e meio ambiente', '📚', 'Educação', '📖', 800, 20]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO recompensas (titulo, descricao, icone, categoria, categoria_icone, pontos, disponivel) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($recompensas as $recompensa) {
                $stmt->execute($recompensa);
            }
            echo "Recompensas padrão criadas\n";
        }
    }
    
    echo "Banco de dados inicializado com sucesso!\n";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
