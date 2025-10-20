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
        pontuacao INT DEFAULT 0,
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
    
    // Criar tabela pontos_coleta se não existir
    $sql = "CREATE TABLE IF NOT EXISTS pontos_coleta (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        tipo VARCHAR(255) NOT NULL,
        endereco VARCHAR(500) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabela pontos_coleta criada/verificada\n";
    
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
    
    // Inserir dados de exemplo se não existirem
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        // Inserir usuário demo
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, telefone, senha, pontuacao) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            'Usuário Demo',
            'demo@recicla.com',
            '(11) 99999-9999',
            password_hash('123456', PASSWORD_DEFAULT),
            150
        ]);
        echo "Usuário demo criado\n";
        
        // Inserir pontos de coleta
        $pontos = [
            ['Eco Ponto Centro', 'Papel/Plástico', 'Av. Central, 100'],
            ['Coleta Verde', 'Vidro/Metal', 'Rua das Flores, 200'],
            ['Recicla Bairro', 'Eletrônicos', 'Praça da Matriz, 50']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO pontos_coleta (nome, tipo, endereco) VALUES (?, ?, ?)");
        foreach ($pontos as $ponto) {
            $stmt->execute($ponto);
        }
        echo "Pontos de coleta criados\n";
    }
    
    echo "Banco de dados inicializado com sucesso!\n";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
