<?php

// Configuração de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-User-ID');

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
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, telefone, senha, pontuacao) VALUES (?, ?, ?, ?, ?)");
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
            
            unset($user['senha']);
            success($user, 'Login realizado com sucesso');
        }
        break;
        
    case '/auth/profile':
        if ($method === 'GET') {
            $userId = $_SERVER['HTTP_X_USER_ID'] ?? 1;
            $stmt = $pdo->prepare("SELECT id, nome, email, telefone, pontuacao FROM users WHERE id = ?");
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
            
            $stmt = $pdo->prepare("SELECT id, nome, email, telefone, pontuacao FROM users WHERE id = ?");
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

