<?php
// Configuração de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-User-ID');

// Incluir classes necessárias
require_once __DIR__ . '/../app/Repositories/PontuacaoRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuração de resposta JSON
header('Content-Type: application/json');

// Configuração do banco de dados
$host = 'db';
$dbname = 'recicla_facil';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar tabelas se não existirem
    $pdo->exec("CREATE TABLE IF NOT EXISTS pontuacoes (
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
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS conquistas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        conquista_id INT NOT NULL,
        conquistada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        progresso INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_conquista (user_id, conquista_id)
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS tipos_conquistas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        icone VARCHAR(10),
        requisito INT NOT NULL,
        pontos_bonus INT DEFAULT 0,
        ativo BOOLEAN DEFAULT TRUE
    )");
    
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
    }
    
    // Criar tabelas de recompensas se não existirem
    $pdo->exec("CREATE TABLE IF NOT EXISTS recompensas (
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
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS resgate_recompensas (
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
    )");
    
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
    }
    
    // Inserir pontuação inicial para usuário padrão se não existir
    $stmt = $pdo->query("SELECT COUNT(*) FROM pontuacoes WHERE user_id = 1");
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO pontuacoes (user_id, pontos, nivel, nivel_nome, descartes, sequencia_dias, badges_conquistadas, pontos_semana_atual, total_pontos_ganhos, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([1, 8350, 84, 'Campeão da Terra', 50, 7, 8, 500, 8350, date('Y-m-d H:i:s')]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão com o banco de dados']);
    exit();
}

// Função para resposta de sucesso
function success($data = null, $message = 'Sucesso', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Função para resposta de erro
function error($message = 'Erro', $code = 400, $errors = null) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ]);
    exit();
}

// Obter método e URL
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);

// Rotas
switch ($path) {
    case '/test':
        if ($method === 'GET') {
            success(['message' => 'API funcionando!', 'timestamp' => date('Y-m-d H:i:s')]);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            success(['message' => 'POST funcionando!', 'data' => $input]);
        }
        break;
        
    case '/auth/register':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validações
            if (empty($input['nome']) || empty($input['email']) || empty($input['telefone']) || empty($input['senha'])) {
                error('Todos os campos são obrigatórios', 422);
            }
            
            if (strlen($input['senha']) < 6) {
                error('A senha deve ter pelo menos 6 caracteres', 422);
            }
            
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$input['email']]);
            if ($stmt->fetch()) {
                error('Email já cadastrado', 422);
            }
            
            // Criar usuário
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $input['nome'],
                $input['email'],
                $input['telefone'],
                password_hash($input['senha'], PASSWORD_DEFAULT),
                0
            ]);
            
            $userId = $pdo->lastInsertId();
            $user = [
                'id' => $userId,
                'nome' => $input['nome'],
                'email' => $input['email'],
                'telefone' => $input['telefone'],
                'pontuacao' => 0
            ];
            
            success($user, 'Usuário criado com sucesso', 201);
        }
        break;
        
    case '/auth/login':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['email']) || empty($input['senha'])) {
                error('Email e senha são obrigatórios', 422);
            }
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$input['email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !password_verify($input['senha'], $user['senha'])) {
                error('Credenciais inválidas', 401);
            }
            
            // Gerar token de sessão
            $token = bin2hex(random_bytes(32));
            
            // Salvar sessão no banco (opcional, para controle de sessões)
            try {
                $stmt = $pdo->prepare("INSERT INTO sessions (id, user_id, ip_address, user_agent, payload, last_activity) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $token,
                    $user['id'],
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                    json_encode(['user_id' => $user['id'], 'token' => $token, 'created_at' => time()]),
                    time()
                ]);
            } catch (PDOException $e) {
                // Se a tabela sessions não existir, apenas logar o erro mas continuar
                error_log("Erro ao salvar sessão: " . $e->getMessage());
            }
            
            unset($user['senha']);
            $user['token'] = $token;
            success($user, 'Login realizado com sucesso');
        }
        break;
        
    case '/auth/profile':
        if ($method === 'GET') {
            $userId = $_SERVER['HTTP_X_USER_ID'] ?? 1;
            $stmt = $pdo->prepare("SELECT id, nome, email, telefone FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                error('Usuário não encontrado', 404);
            }
            
            success($user);
        } elseif ($method === 'PUT') {
            $userId = $_SERVER['HTTP_X_USER_ID'] ?? 1;
            $input = json_decode(file_get_contents('php://input'), true);
            
            $fields = [];
            $values = [];
            
            if (isset($input['nome'])) {
                $fields[] = 'nome = ?';
                $values[] = $input['nome'];
            }
            if (isset($input['telefone'])) {
                $fields[] = 'telefone = ?';
                $values[] = $input['telefone'];
            }
            if (isset($input['senha'])) {
                $fields[] = 'senha = ?';
                $values[] = password_hash($input['senha'], PASSWORD_DEFAULT);
            }
            
            if (empty($fields)) {
                error('Nenhum campo para atualizar', 422);
            }
            
            $values[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            
            $stmt = $pdo->prepare("SELECT id, nome, email, telefone FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            success($user, 'Perfil atualizado com sucesso');
        }
        break;
        
    case '/coletas':
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM coletas ORDER BY created_at DESC");
            $coletas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            success($coletas);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_SERVER['HTTP_X_USER_ID'] ?? 1;
            
            $stmt = $pdo->prepare("INSERT INTO coletas (user_id, material, quantidade, endereco, data_preferida, obs, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $input['material'],
                $input['quantidade'],
                $input['endereco'],
                $input['data_preferida'],
                $input['obs'] ?? null,
                'ABERTA'
            ]);
            
            $coletaId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM coletas WHERE id = ?");
            $stmt->execute([$coletaId]);
            $coleta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            success($coleta, 'Coleta criada com sucesso', 201);
        }
        break;
        
    case '/doacoes':
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM doacoes ORDER BY created_at DESC");
            $doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            success($doacoes);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_SERVER['HTTP_X_USER_ID'] ?? 1;
            
            $stmt = $pdo->prepare("INSERT INTO doacoes (user_id, material, qtd, contato, entregue) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $input['material'],
                $input['qtd'],
                $input['contato'],
                false
            ]);
            
            $doacaoId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM doacoes WHERE id = ?");
            $stmt->execute([$doacaoId]);
            $doacao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            success($doacao, 'Doação criada com sucesso', 201);
        }
        break;
        
    case '/pontos':
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM ponto_coletas WHERE ativo = 1 ORDER BY nome");
            $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            success($pontos);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validações
            if (empty($input['nome']) || empty($input['endereco'])) {
                error('Nome e endereço são obrigatórios', 422);
            }
            
            // Validar latitude e longitude apenas se fornecidos
            if (!empty($input['latitude']) && !is_numeric($input['latitude'])) {
                error('Latitude deve ser um número válido', 422);
            }
            
            if (!empty($input['longitude']) && !is_numeric($input['longitude'])) {
                error('Longitude deve ser um número válido', 422);
            }
            
            // Inserir ponto de coleta
            $stmt = $pdo->prepare("INSERT INTO ponto_coletas (nome, tipo, endereco, latitude, longitude, telefone, horario, materiais_aceitos, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['nome'],
                $input['tipo'] ?? 'Ponto de Coleta',
                $input['endereco'],
                $input['latitude'],
                $input['longitude'],
                $input['telefone'] ?? null,
                $input['horario'] ?? null,
                json_encode($input['materiais'] ?? []),
                true
            ]);
            
            $pontoId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM ponto_coletas WHERE id = ?");
            $stmt->execute([$pontoId]);
            $ponto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            success($ponto, 'Ponto de coleta cadastrado com sucesso', 201);
        }
        break;
        
    case '/pontos/proximos':
        if ($method === 'GET') {
            $lat = $_GET['lat'] ?? null;
            $lng = $_GET['lng'] ?? null;
            
            if (!$lat || !$lng) {
                error('Latitude e longitude são obrigatórios', 400);
            }
            
            // Buscar pontos cadastrados pelos usuários
            $stmt = $pdo->prepare("SELECT * FROM ponto_coletas WHERE ativo = 1");
            $stmt->execute();
            $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular distância para cada ponto e agrupar por cidade
            $pontosPorCidade = [];
            $pontosComDistancia = [];
            
            foreach ($pontos as $ponto) {
                // Calcular distância apenas se o ponto tiver coordenadas
                if ($ponto['latitude'] && $ponto['longitude']) {
                    $distancia = calcularDistancia($lat, $lng, $ponto['latitude'], $ponto['longitude']);
                    $ponto['distancia'] = round($distancia, 2) . ' km';
                    $ponto['distancia_km'] = round($distancia, 2);
                } else {
                    $ponto['distancia'] = 'Distância não disponível';
                    $ponto['distancia_km'] = 999999; // Valor alto para ordenar por último
                }
                $ponto['materiais'] = json_decode($ponto['materiais_aceitos'] ?? '[]', true);
                
                // Extrair cidade do endereço (assumindo formato: "Rua, Bairro, Cidade - Estado")
                $endereco = $ponto['endereco'];
                $cidade = extrairCidade($endereco);
                
                if (!isset($pontosPorCidade[$cidade])) {
                    $pontosPorCidade[$cidade] = [];
                }
                $pontosPorCidade[$cidade][] = $ponto;
            }
            
            // Ordenar cidades por distância média
            $cidadesOrdenadas = [];
            foreach ($pontosPorCidade as $cidade => $pontosCidade) {
                $distanciaMedia = array_sum(array_column($pontosCidade, 'distancia_km')) / count($pontosCidade);
                $cidadesOrdenadas[$cidade] = $distanciaMedia;
            }
            asort($cidadesOrdenadas);
            
            // Retornar pontos da cidade mais próxima
            $cidadeMaisProxima = array_key_first($cidadesOrdenadas);
            $pontosComDistancia = $pontosPorCidade[$cidadeMaisProxima] ?? [];
            
            // Ordenar pontos da cidade por distância
            usort($pontosComDistancia, function($a, $b) {
                return $a['distancia_km'] <=> $b['distancia_km'];
            });
            
            success([
                'pontos' => $pontosComDistancia,
                'cidade' => $cidadeMaisProxima,
                'localizacao' => [
                    'latitude' => $lat,
                    'longitude' => $lng
                ],
                'total' => count($pontosComDistancia)
            ]);
        }
        break;
        
    case '/pontos/raio':
        if ($method === 'GET') {
            $lat = $_GET['lat'] ?? null;
            $lng = $_GET['lng'] ?? null;
            $raio = $_GET['raio'] ?? 10000;
            
            if (!$lat || !$lng) {
                error('Latitude e longitude são obrigatórios', 400);
            }
            
            $stmt = $pdo->query("SELECT * FROM ponto_coletas WHERE ativo = 1 LIMIT 20");
            $pontos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular distância para cada ponto
            $pontosComDistancia = [];
            foreach ($pontos as $ponto) {
                // Calcular distância apenas se o ponto tiver coordenadas
                if ($ponto['latitude'] && $ponto['longitude']) {
                    $distancia = calcularDistancia($lat, $lng, $ponto['latitude'], $ponto['longitude']);
                    $ponto['distancia'] = round($distancia, 2) . ' km';
                    $ponto['distancia_km'] = round($distancia, 2);
                } else {
                    $ponto['distancia'] = 'Distância não disponível';
                    $ponto['distancia_km'] = 999999; // Valor alto para ordenar por último
                }
                $ponto['materiais'] = json_decode($ponto['materiais_aceitos'] ?? '[]', true);
                $pontosComDistancia[] = $ponto;
            }
            
            // Ordenar por distância
            usort($pontosComDistancia, function($a, $b) {
                return $a['distancia_km'] <=> $b['distancia_km'];
            });
            
            success([
                'pontos' => $pontosComDistancia,
                'localizacao' => ['latitude' => $lat, 'longitude' => $lng],
                'raio_km' => $raio,
                'total' => count($pontosComDistancia)
            ]);
        }
        break;
        
    case '/pontuacao/estatisticas':
        if ($method === 'GET') {
            // Obter userId do header Authorization
            $userId = null;
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                // Buscar usuário pelo token na tabela sessions
                try {
                    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE id = ? AND last_activity > ?");
                    $stmt->execute([$token, time() - 86400]); // Token válido por 24 horas
                    $session = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($session) {
                        $userId = $session['user_id'];
                    }
                } catch (PDOException $e) {
                    // Se tabela sessions não existir, tentar buscar por token no localStorage
                    error_log("Erro ao buscar sessão: " . $e->getMessage());
                }
            }
            
            if (!$userId) {
                // Para desenvolvimento, usar usuário padrão se não autenticado
                $userId = 1; // ID do usuário padrão para desenvolvimento
            }
            
            // Obter estatísticas do usuário da tabela pontuacoes
            $stmt = $pdo->prepare("SELECT pontos FROM pontuacoes WHERE user_id = ?");
            $stmt->execute([$userId]);
            $pontuacao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pontuacao) {
                // Se não existe registro, criar um inicial
                $stmt = $pdo->prepare("INSERT INTO pontuacoes (user_id, pontos, nivel, nivel_nome, descartes, sequencia_dias, badges_conquistadas, pontos_semana_atual, total_pontos_ganhos, ultima_atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, 0, 1, 'Iniciante', 0, 0, 0, 0, 0, date('Y-m-d H:i:s')]);
                $pontos = 0;
            } else {
                $pontos = $pontuacao['pontos'] ?? 0;
            }
            $nivel = min(10, max(1, floor($pontos / 100) + 1));
            $nivelNome = ['Iniciante', 'Reciclador', 'Eco-amigo', 'Guardião Verde', 'Defensor do Planeta', 'Herói Ambiental', 'Mestre da Reciclagem', 'Lenda Verde', 'Ícone Ecológico', 'Campeão da Terra'][$nivel - 1] ?? 'Mestre';
            $pontosProximoNivel = $nivel * 100;
            $pontosRestantes = max(0, $pontosProximoNivel - $pontos);
            $progressoNivel = min(100, ($pontos % 100) / 100 * 100);
            
            // Obter estatísticas adicionais da tabela pontuacoes
            try {
                $stmt = $pdo->prepare("SELECT descartes, sequencia_dias, badges_conquistadas, pontos_semana_atual FROM pontuacoes WHERE user_id = ?");
                $stmt->execute([$userId]);
                $estatisticas = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($estatisticas) {
                    $descartes = $estatisticas['descartes'] ?? 0;
                    $sequenciaDias = $estatisticas['sequencia_dias'] ?? 0;
                    $badges = $estatisticas['badges_conquistadas'] ?? 0;
                    $pontosSemanaAtual = $estatisticas['pontos_semana_atual'] ?? 0;
                } else {
                    $descartes = 0;
                    $sequenciaDias = 0;
                    $badges = 0;
                    $pontosSemanaAtual = 0;
                }
            } catch (PDOException $e) {
                $descartes = 0;
                $sequenciaDias = 0;
                $badges = 0;
                $pontosSemanaAtual = 0;
                error_log("Erro ao buscar estatísticas: " . $e->getMessage());
            }
            
            // Badges já obtidos da tabela pontuacoes acima
            
            $estatisticas = [
                'pontos' => $pontos,
                'nivel' => $nivel,
                'nivel_nome' => $nivelNome,
                'pontos_para_proximo_nivel' => $pontosRestantes,
                'progresso_nivel' => $progressoNivel,
                'pontos_semana_atual' => $pontosSemanaAtual,
                'descartes' => $descartes,
                'sequencia_dias' => $sequenciaDias,
                'badges_conquistadas' => $badges
            ];
            
            success($estatisticas);
        }
        break;
        
    case '/pontuacao/ranking':
        if ($method === 'GET') {
            $limite = $_GET['limite'] ?? 10;
            
            try {
                $stmt = $pdo->query("SELECT u.id, u.nome, p.pontos as pontuacao FROM users u LEFT JOIN pontuacoes p ON u.id = p.user_id ORDER BY p.pontos DESC LIMIT " . intval($limite));
                $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Adicionar posição no ranking
                foreach ($ranking as $index => &$user) {
                    $user['posicao'] = $index + 1;
                }
                
                success($ranking);
            } catch (PDOException $e) {
                error('Erro ao buscar ranking: ' . $e->getMessage(), 500);
            }
        }
        break;
        
    case '/pontuacao/conquistas':
        if ($method === 'GET') {
            // Obter userId do header Authorization
            $userId = null;
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                try {
                    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE id = ? AND last_activity > ?");
                    $stmt->execute([$token, time() - 86400]);
                    $session = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($session) {
                        $userId = $session['user_id'];
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao buscar sessão: " . $e->getMessage());
                }
            }
            
            if (!$userId) {
                error('Usuário não autenticado', 401);
            }
            
            // Buscar tipos de conquistas
            $stmt = $pdo->query("SELECT DISTINCT * FROM tipos_conquistas WHERE ativo = 1 ORDER BY requisito ASC");
            $tiposConquistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar conquistas do usuário
            $stmt = $pdo->prepare("SELECT conquista_id, conquistada_em, progresso FROM conquistas WHERE user_id = ?");
            $stmt->execute([$userId]);
            $conquistasUsuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Criar array de conquistas do usuário
            $conquistasUsuarioMap = [];
            foreach ($conquistasUsuario as $conquista) {
                $conquistasUsuarioMap[$conquista['conquista_id']] = $conquista;
            }
            
            // Obter total de descartes do usuário da tabela pontuacoes
            try {
                $stmt = $pdo->prepare("SELECT descartes FROM pontuacoes WHERE user_id = ?");
                $stmt->execute([$userId]);
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalDescartes = $resultado ? $resultado['descartes'] : 0;
            } catch (PDOException $e) {
                $totalDescartes = 0;
                error_log("Erro ao buscar descartes para conquistas: " . $e->getMessage());
            }
            
            // Obter pontos do usuário para conquistas baseadas em pontos
            $stmt = $pdo->prepare("SELECT pontos FROM pontuacoes WHERE user_id = ?");
            $stmt->execute([$userId]);
            $pontosUsuario = $stmt->fetch(PDO::FETCH_ASSOC)['pontos'] ?? 0;
            
            // Processar conquistas
            $conquistas = [];
            foreach ($tiposConquistas as $tipo) {
                $conquistaUsuario = $conquistasUsuarioMap[$tipo['id']] ?? null;
                
                // Determinar se a conquista foi desbloqueada baseado no requisito
                $desbloqueada = false;
                $progresso = 0;
                
                // Determinar tipo de conquista baseado no requisito
                // Conquistas de descartes: 1, 5, 10, 25, 50, 100, 250, 500
                // Conquistas de pontos: 100, 250, 500, 1000, 2500, 5000, 10000, 25000, 50000, 100000
                if (in_array($tipo['requisito'], [1, 5, 10, 25, 50, 100, 250, 500])) {
                    // Conquistas baseadas em descartes
                    $desbloqueada = $totalDescartes >= $tipo['requisito'];
                    $progresso = min(100, ($totalDescartes / $tipo['requisito']) * 100);
                } else {
                    // Conquistas baseadas em pontos
                    $desbloqueada = $pontosUsuario >= $tipo['requisito'];
                    $progresso = min(100, ($pontosUsuario / $tipo['requisito']) * 100);
                }
                
                // Se a conquista foi desbloqueada mas não está na tabela, inserir
                if ($desbloqueada && !$conquistaUsuario) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO conquistas (user_id, conquista_id, progresso) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE progresso = ?");
                        $stmt->execute([$userId, $tipo['id'], $progresso, $progresso]);
                        
                        // Buscar novamente para obter a data
                        $stmt = $pdo->prepare("SELECT conquistada_em FROM conquistas WHERE user_id = ? AND conquista_id = ?");
                        $stmt->execute([$userId, $tipo['id']]);
                        $conquistaUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        error_log("Erro ao inserir conquista: " . $e->getMessage());
                    }
                }
                
                $conquistas[] = [
                    'id' => $tipo['id'],
                    'nome' => $tipo['nome'],
                    'descricao' => $tipo['descricao'],
                    'icone' => $tipo['icone'],
                    'desbloqueada' => $desbloqueada,
                    'progresso' => $progresso,
                    'conquistada_em' => $conquistaUsuario['conquistada_em'] ?? null,
                    'pontos_bonus' => $tipo['pontos_bonus']
                ];
            }
            
            success($conquistas);
        }
        break;
        
    case '/pontuacao/estatisticas-gerais':
        if ($method === 'GET') {
            // Estatísticas gerais do sistema
            $stmt = $pdo->query("SELECT COUNT(*) as total_usuarios FROM users");
            $totalUsuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total_usuarios'] ?? 0;
            
            $stmt = $pdo->query("SELECT COUNT(*) as total_coletas FROM coletas WHERE status = 'CONCLUIDA'");
            $totalColetas = $stmt->fetch(PDO::FETCH_ASSOC)['total_coletas'] ?? 0;
            
            $stmt = $pdo->query("SELECT SUM(pontos) as total_pontos FROM pontuacoes");
            $totalPontos = $stmt->fetch(PDO::FETCH_ASSOC)['total_pontos'] ?? 0;
            
            $estatisticas = [
                'total_usuarios' => $totalUsuarios,
                'total_coletas' => $totalColetas,
                'total_pontos' => $totalPontos,
                'coletas_por_usuario' => $totalUsuarios > 0 ? round($totalColetas / $totalUsuarios, 2) : 0,
                'pontos_por_usuario' => $totalUsuarios > 0 ? round($totalPontos / $totalUsuarios, 2) : 0
            ];
            
            success($estatisticas);
        }
        break;
        
    case '/pontuacao/simular-descarte':
        if ($method === 'POST') {
            // Obter userId do header Authorization
            $userId = null;
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                try {
                    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE id = ? AND last_activity > ?");
                    $stmt->execute([$token, time() - 86400]);
                    $session = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($session) {
                        $userId = $session['user_id'];
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao buscar sessão: " . $e->getMessage());
                }
            }
            
            if (!$userId) {
                error('Usuário não autenticado', 401);
            }
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['material']) || empty($input['peso'])) {
                error('Material e peso são obrigatórios', 422);
            }
            
            // Calcular pontos baseado no material e peso
            $pontosPorKg = [
                'papel' => 10,
                'plastico' => 15,
                'vidro' => 20,
                'metal' => 25,
                'organico' => 5
            ];
            
            $pontosGanhos = round($input['peso'] * ($pontosPorKg[$input['material']] ?? 10));
            
            // Usar o sistema de pontuação completo
            $pontuacaoRepo = new \App\Repositories\PontuacaoRepository();
            $resultado = $pontuacaoRepo->adicionarPontos($userId, $pontosGanhos, 'simular-descarte');
            
            $response = [
                'pontos_ganhos' => $pontosGanhos,
                'pontuacao_total' => $resultado['pontuacao']->pontos,
                'novas_conquistas' => $resultado['novas_conquistas']
            ];
            
            success($response, 'Descarte simulado com sucesso');
        }
        break;
        
    case '/pontuacao/registrar-descarte':
        if ($method === 'POST') {
            // Obter userId do header Authorization
            $userId = null;
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                try {
                    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE id = ? AND last_activity > ?");
                    $stmt->execute([$token, time() - 86400]);
                    $session = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($session) {
                        $userId = $session['user_id'];
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao buscar sessão: " . $e->getMessage());
                }
            }
            
            if (!$userId) {
                error('Usuário não autenticado', 401);
            }
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['material']) || empty($input['peso'])) {
                error('Material e peso são obrigatórios', 422);
            }
            
            // Calcular pontos baseado no material e peso
            $pontosPorKg = [
                'papel' => 10,
                'plastico' => 15,
                'vidro' => 20,
                'metal' => 25,
                'organico' => 5
            ];
            
            $pontosGanhos = round($input['peso'] * ($pontosPorKg[$input['material']] ?? 10));
            
            // Usar o sistema de pontuação completo
            $pontuacaoRepo = new \App\Repositories\PontuacaoRepository();
            $resultado = $pontuacaoRepo->adicionarPontos($userId, $pontosGanhos, 'descarte');
            
            $response = [
                'pontos_ganhos' => $pontosGanhos,
                'material' => $input['material'],
                'peso' => $input['peso'],
                'pontuacao' => $resultado['pontuacao'],
                'novas_conquistas' => $resultado['novas_conquistas']
            ];
            
            success($response, 'Descarte registrado com sucesso');
        }
        break;
        
    case '/recompensas':
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM recompensas WHERE ativo = 1 AND disponivel > 0 ORDER BY pontos ASC");
            $recompensas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            success($recompensas);
        }
        break;
        
    case (preg_match('/^\/recompensas\/\d+$/', $path) ? $path : false):
        if ($method === 'GET') {
            $id = basename($path);
            $stmt = $pdo->prepare("SELECT * FROM recompensas WHERE id = ? AND ativo = 1");
            $stmt->execute([$id]);
            $recompensa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$recompensa) {
                error('Recompensa não encontrada', 404);
            }
            
            success($recompensa);
        }
        break;
        
    case '/recompensas/resgatar':
        if ($method === 'POST') {
            // Obter userId do header Authorization
            $userId = null;
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                try {
                    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE id = ? AND last_activity > ?");
                    $stmt->execute([$token, time() - 86400]);
                    $session = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($session) {
                        $userId = $session['user_id'];
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao buscar sessão: " . $e->getMessage());
                }
            }
            
            if (!$userId) {
                error('Usuário não autenticado', 401);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['recompensa_id'])) {
                error('ID da recompensa é obrigatório', 422);
            }
            
            // Verificar se a recompensa existe e está disponível
            $stmt = $pdo->prepare("SELECT * FROM recompensas WHERE id = ? AND ativo = 1 AND disponivel > 0");
            $stmt->execute([$input['recompensa_id']]);
            $recompensa = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$recompensa) {
                error('Recompensa não disponível', 400);
            }
            
            // Verificar se o usuário tem pontos suficientes
            $stmt = $pdo->prepare("SELECT pontos FROM pontuacoes WHERE user_id = ?");
            $stmt->execute([$userId]);
            $pontuacao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $pontosUsuario = $pontuacao ? $pontuacao['pontos'] : 0;
            
            if ($pontosUsuario < $recompensa['pontos']) {
                error('Pontos insuficientes', 400);
            }
            
            // Iniciar transação
            $pdo->beginTransaction();
            
            try {
                // Criar o resgate
                $stmt = $pdo->prepare("INSERT INTO resgate_recompensas (user_id, recompensa_id, pontos_gastos, status, data_resgate) VALUES (?, ?, ?, 'PENDENTE', NOW())");
                $stmt->execute([$userId, $recompensa['id'], $recompensa['pontos']]);
                
                $resgateId = $pdo->lastInsertId();
                
                // Decrementar disponibilidade da recompensa
                $stmt = $pdo->prepare("UPDATE recompensas SET disponivel = disponivel - 1 WHERE id = ?");
                $stmt->execute([$recompensa['id']]);
                
                // Subtrair pontos do usuário
                $stmt = $pdo->prepare("UPDATE pontuacoes SET pontos = pontos - ? WHERE user_id = ?");
                $stmt->execute([$recompensa['pontos'], $userId]);
                
                $pdo->commit();
                
                // Buscar o resgate criado
                $stmt = $pdo->prepare("SELECT rr.*, r.titulo, r.icone FROM resgate_recompensas rr JOIN recompensas r ON rr.recompensa_id = r.id WHERE rr.id = ?");
                $stmt->execute([$resgateId]);
                $resgate = $stmt->fetch(PDO::FETCH_ASSOC);
                
                success($resgate, 'Recompensa resgatada com sucesso!');
                
            } catch (Exception $e) {
                $pdo->rollBack();
                error('Erro ao resgatar recompensa: ' . $e->getMessage(), 500);
            }
        }
        break;
        
    case '/recompensas/meus-resgates':
        if ($method === 'GET') {
            // Obter userId do header Authorization
            $userId = null;
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                try {
                    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE id = ? AND last_activity > ?");
                    $stmt->execute([$token, time() - 86400]);
                    $session = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($session) {
                        $userId = $session['user_id'];
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao buscar sessão: " . $e->getMessage());
                }
            }
            
            if (!$userId) {
                error('Usuário não autenticado', 401);
            }
            
            $stmt = $pdo->prepare("
                SELECT rr.*, r.titulo, r.icone, r.categoria 
                FROM resgate_recompensas rr 
                JOIN recompensas r ON rr.recompensa_id = r.id 
                WHERE rr.user_id = ? 
                ORDER BY rr.created_at DESC
            ");
            $stmt->execute([$userId]);
            $resgates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            success($resgates);
        }
        break;
        
    case (preg_match('/^\/recompensas\/resgate\/\d+\/status$/', $path) ? $path : false):
        if ($method === 'PUT') {
            $id = basename(dirname($path));
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['status'])) {
                error('Status é obrigatório', 422);
            }
            
            $statusValidos = ['PENDENTE', 'APROVADO', 'REJEITADO', 'ENTREGUE'];
            if (!in_array($input['status'], $statusValidos)) {
                error('Status inválido', 422);
            }
            
            $stmt = $pdo->prepare("UPDATE resgate_recompensas SET status = ?, observacoes = ? WHERE id = ?");
            $stmt->execute([$input['status'], $input['observacoes'] ?? null, $id]);
            
            $stmt = $pdo->prepare("SELECT * FROM resgate_recompensas WHERE id = ?");
            $stmt->execute([$id]);
            $resgate = $stmt->fetch(PDO::FETCH_ASSOC);
            
            success($resgate, 'Status atualizado com sucesso!');
        }
        break;
        
        
    default:
        error('Rota não encontrada', 404);
}

// Função para calcular distância entre dois pontos (Haversine)
function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Raio da Terra em km
    
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    
    $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earthRadius * $c;
}

// Função para extrair cidade do endereço
function extrairCidade($endereco) {
    // Assumindo formato: "Rua, Bairro, Cidade - Estado" ou "Rua, Cidade - Estado"
    $partes = explode(',', $endereco);
    
    if (count($partes) >= 2) {
        $ultimaParte = trim(end($partes));
        
        // Se contém " - ", pegar a parte antes do " - "
        if (strpos($ultimaParte, ' - ') !== false) {
            $cidade = explode(' - ', $ultimaParte)[0];
            return trim($cidade);
        }
        
        return $ultimaParte;
    }
    
    // Fallback: retornar o endereço completo se não conseguir extrair
    return $endereco;
}

