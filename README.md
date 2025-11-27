# Recicla Fácil

Sistema completo de gestão de reciclagem que conecta cidadãos, pontos de coleta e serviços de coleta domiciliar, promovendo a sustentabilidade através de uma plataforma gamificada com sistema de pontuação e recompensas.

## Visão Geral

O Recicla Fácil é composto por um **backend Laravel + MySQL** (containerizados via Docker) e um **cliente desktop JavaFX** que deve ser executado manualmente no ambiente local. O backend e banco de dados rodam em containers Docker, enquanto o frontend JavaFX é executado diretamente na máquina do desenvolvedor, conectando-se ao backend através da API REST.

### Como Funciona o Projeto

O sistema segue uma arquitetura **cliente-servidor** onde:

1. **Backend (Docker)**: API REST em PHP/Laravel que gerencia toda a lógica de negócio, autenticação, banco de dados e regras de pontuação
2. **Banco de Dados (Docker)**: MySQL 8.0 que armazena todos os dados (usuários, coletas, pontuações, recompensas, etc.)
3. **Frontend (Manual)**: Aplicação desktop JavaFX que se conecta ao backend via HTTP para exibir dados e permitir interação do usuário

**Fluxo de Dados:**
```
Cliente JavaFX → HTTP Request → Backend Laravel → MySQL Database
                ← JSON Response ←
```

O cliente JavaFX faz requisições HTTP para o backend na porta `9161`, que processa as requisições, consulta/atualiza o banco de dados MySQL e retorna respostas JSON. O backend também gerencia autenticação via tokens, CORS para permitir requisições do cliente, e toda a lógica de gamificação (pontos, níveis, conquistas, recompensas).

## Arquitetura (Monorepo)

```
┌──────────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Cliente Desktop      │    │ Backend REST    │    │ Database        │
│ JavaFX (Java 17)     │◄──►│ Laravel + PHP   │◄──►│ MySQL 8.0       │
│ Execução Manual      │    │ Docker:9161     │    │ Docker:33061    │
└──────────────────────┘    └─────────────────┘    └─────────────────┘
```

| Diretório | Propósito | Tecnologia |
|-----------|-----------|------------|
| `javafx-client/` | Cliente desktop oficial | Java 17 + JavaFX 21 |
| `backend/` | API REST e lógica de negócio | Laravel + PHP 8.2 |
| `database/` | Migrações e seeders | MySQL 8.0 |

## Stack & Principais Tecnologias

### Backend
- **Linguagem:** PHP 8.2
- **Framework:** Laravel (Illuminate Database)
- **Banco de Dados:** MySQL 8.0
- **Servidor:** Nginx + PHP-FPM
- **Containerização:** Docker

### Cliente Desktop (único frontend)
- **Framework:** JavaFX 21
- **Build:** Maven + JavaFX Maven Plugin
- **HTTP Client:** java.net.http + Jackson
- **Arquitetura:** MVVM simplificado (services + controllers)
- **Execução:** Manual (não containerizado)
- **Empacotamento:** JAR standalone

### Infraestrutura
- **Orquestração:** Docker Compose
- **Proxy Reverso:** Nginx
- **Banco de Dados:** MySQL 8.0
- **Rede:** Bridge Network

## Como Rodar o Projeto

### Pré-requisitos

**Para Backend e Banco de Dados (Docker):**
- ✅ Docker Desktop instalado e rodando
- ✅ Docker Compose (incluído no Docker Desktop)

**Para Frontend JavaFX (Execução Manual):**
- ✅ Java 17 ou superior instalado e no PATH
- ✅ Maven 3.6+ instalado e no PATH

**Verificar Instalações:**
```bash
# Verificar Docker
docker --version
docker compose version

# Verificar Java
java -version  # Deve ser 17 ou superior

# Verificar Maven
mvn -version   # Deve ser 3.6 ou superior
```

---

## 🚀 Comandos para Subir o Projeto

### 1️⃣ Iniciar Backend e Banco de Dados (Docker)

**Primeira vez (build + start):**
```bash
# Na raiz do projeto
docker compose build --no-cache
docker compose up -d
```

**Iniciar (após primeira vez):**
```bash
docker compose up -d
```

**Verificar se está rodando:**
```bash
# Ver status dos containers
docker ps

# Ver logs do backend
docker compose logs -f backend

# Testar API
curl http://localhost:9161/api/test
```

**Parar os serviços:**
```bash
docker compose down
```

**Parar e remover volumes (limpar dados):**
```bash
docker compose down -v
```

---

### 2️⃣ Executar Frontend JavaFX (Manual)

**Windows (PowerShell ou CMD):**
```bash
cd javafx-client
mvn clean javafx:run
```

**Linux/Mac:**
```bash
cd javafx-client
mvn clean javafx:run
```

**Nota Importante:** 
- O frontend **deve ser executado manualmente** após o backend estar rodando
- O frontend se conecta automaticamente ao backend em `http://localhost:9161/api`
- A configuração está em `javafx-client/src/main/resources/app.properties`

---

## 📍 Endpoints e Acessos

| Serviço | URL | Descrição |
|---------|-----|-----------|
| **API Backend** | http://localhost:9161/api | API REST principal |
| **Health Check** | http://localhost:9161/up | Verificar se backend está online |
| **Test API** | http://localhost:9161/api/test | Teste básico da API |
| **Database** | localhost:33061 | MySQL (acesso direto, se necessário) |
| **Cliente JavaFX** | Executado via `mvn javafx:run` | Interface desktop |

---

## 🔄 Ordem de Execução Recomendada

1. **Primeiro:** Iniciar backend e banco de dados
   ```bash
   docker compose up -d
   ```

2. **Aguardar:** Verificar se backend está pronto (30-60 segundos)
   ```bash
   docker compose logs -f backend
   # Aguardar mensagem de sucesso ou testar:
   curl http://localhost:9161/api/test
   ```

3. **Depois:** Executar frontend JavaFX
   ```bash
   cd javafx-client
   mvn clean javafx:run
   ```

## 📋 Configuração do Projeto

### Variáveis de Ambiente (Backend)

O backend utiliza variáveis de ambiente definidas no `docker-compose.yml`:

```yaml
DB_HOST=db
DB_PORT=3306
DB_DATABASE=recicla_facil
DB_USERNAME=root
DB_PASSWORD=root
```

### Configuração do Frontend JavaFX

A URL da API está configurada em `javafx-client/src/main/resources/app.properties`:

```properties
api.baseUrl=http://localhost:9161/api
auth.token=
ui.locale=pt-BR
```

**Para alterar a URL da API:**
1. Edite o arquivo `javafx-client/src/main/resources/app.properties`
2. Ou defina variável de ambiente antes de executar:
   ```bash
   export API_BASE_URL=http://localhost:9161/api
   mvn javafx:run
   ```

---

## 🐳 Comandos Docker Úteis

```bash
# Inicializar serviços
docker compose up -d

# Parar serviços
docker compose down

# Parar e remover volumes (limpar dados)
docker compose down -v

# Rebuild completo
docker compose build --no-cache
docker compose up -d

# Ver logs em tempo real
docker compose logs -f

# Ver logs apenas do backend
docker compose logs -f backend

# Ver logs apenas do banco
docker compose logs -f db

# Acessar container do backend
docker exec -it recicla_facil_backend bash

# Acessar container do banco
docker exec -it recicla_facil_db mysql -u root -proot recicla_facil

# Ver status dos containers
docker ps

# Ver uso de recursos
docker stats
```

---

## 🔧 Troubleshooting

### Problemas com Backend

**Backend não inicia:**
```bash
# Verificar logs
docker compose logs backend

# Verificar se porta 9161 está livre
netstat -an | grep 9161  # Linux/Mac
netstat -an | findstr 9161  # Windows

# Rebuild completo
docker compose down
docker compose build --no-cache
docker compose up -d
```

**Erro de conexão com banco:**
```bash
# Verificar se banco está rodando
docker ps | grep recicla_facil_db

# Verificar logs do banco
docker compose logs db

# Testar conexão manual
docker exec -it recicla_facil_db mysql -u root -proot -e "SHOW DATABASES;"
```

**API não responde:**
```bash
# Testar endpoint
curl http://localhost:9161/api/test

# Verificar se container está saudável
docker ps

# Reiniciar backend
docker compose restart backend
```

### Problemas com Frontend JavaFX

**Erro de conexão com backend:**
- ✅ Verifique se backend está rodando: `docker ps`
- ✅ Teste a API: `curl http://localhost:9161/api/test`
- ✅ Verifique a URL em `javafx-client/src/main/resources/app.properties`
- ✅ Verifique logs do backend: `docker compose logs backend`

**Erro ao executar Maven:**
- ✅ Verifique Java: `java -version` (deve ser 17+)
- ✅ Verifique Maven: `mvn -version` (deve ser 3.6+)
- ✅ Limpe e recompile: `mvn clean install`
- ✅ Verifique se está no diretório correto: `cd javafx-client`

**Erro de dependências Maven:**
```bash
cd javafx-client
mvn clean install -U  # -U atualiza dependências
```

**JavaFX não inicia:**
- ✅ Verifique se JavaFX está instalado corretamente
- ✅ Tente executar com: `mvn clean javafx:run`
- ✅ Verifique logs de erro no console

## 📚 Estrutura e Funcionamento Detalhado

### Arquitetura do Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENTE JAVAFX (Manual)                  │
│  - Interface gráfica desktop                                │
│  - Comunicação HTTP com backend                            │
│  - Gerenciamento de sessão/autenticação                     │
└───────────────────────┬─────────────────────────────────────┘
                        │ HTTP/REST (JSON)
                        │ http://localhost:9161/api
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              BACKEND LARAVEL (Docker)                       │
│  - API REST (PHP 8.2 + Laravel)                            │
│  - Nginx + PHP-FPM                                          │
│  - Autenticação via tokens                                  │
│  - CORS habilitado                                          │
│  - Lógica de negócio e validações                           │
└───────────────────────┬─────────────────────────────────────┘
                        │ SQL Queries
                        │ MySQL Protocol
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              BANCO DE DADOS MYSQL (Docker)                  │
│  - MySQL 8.0                                                │
│  - Porta: 33061 (host) / 3306 (container)                   │
│  - Dados persistentes em volume Docker                      │
└─────────────────────────────────────────────────────────────┘
```

### Fluxo de Requisições

1. **Usuário interage com JavaFX** → Clique em botão, preenche formulário, etc.
2. **JavaFX faz requisição HTTP** → `POST http://localhost:9161/api/auth/login`
3. **Backend processa requisição** → Valida dados, consulta banco, aplica regras
4. **Backend retorna JSON** → `{"success": true, "data": {...}}`
5. **JavaFX atualiza interface** → Exibe dados, mostra mensagens, etc.

### Principais Funcionalidades da API

#### Autenticação
- `POST /api/auth/register` - Registrar novo usuário
- `POST /api/auth/login` - Fazer login (retorna token)
- `GET /api/auth/profile` - Obter perfil do usuário
- `PUT /api/auth/profile` - Atualizar perfil

#### Pontuação e Gamificação
- `GET /api/pontuacao/estatisticas` - Estatísticas do usuário (pontos, nível, etc.)
- `POST /api/pontuacao/registrar-descarte` - Registrar descarte e ganhar pontos
- `GET /api/pontuacao/ranking` - Ranking de usuários
- `GET /api/pontuacao/conquistas` - Conquistas do usuário
- `GET /api/pontuacao/estatisticas-gerais` - Estatísticas gerais do sistema

#### Recompensas
- `GET /api/recompensas` - Listar recompensas disponíveis
- `POST /api/recompensas/resgatar` - Resgatar recompensa com pontos
- `GET /api/recompensas/meus-resgates` - Histórico de resgates

#### Coletas e Pontos de Coleta
- `GET /api/pontos` - Listar pontos de coleta
- `GET /api/pontos/proximos` - Pontos próximos por localização
- `POST /api/coletas` - Solicitar coleta domiciliar
- `GET /api/coletas` - Listar coletas

### Estrutura de Dados (Banco de Dados)

**Tabelas Principais:**
- `users` - Usuários do sistema
- `pontuacoes` - Pontuação e estatísticas dos usuários
- `conquistas` - Conquistas desbloqueadas pelos usuários
- `tipos_conquistas` - Tipos de conquistas disponíveis
- `recompensas` - Recompensas disponíveis para resgate
- `resgate_recompensas` - Histórico de resgates
- `coletas` - Solicitações de coleta
- `ponto_coletas` - Pontos de coleta cadastrados
- `doacoes` - Registro de doações
- `cronograma_coletas` - Cronograma de coletas

### Sistema de Pontuação

O sistema calcula pontos baseado em:
- **Material reciclado**: Papel (10 pts/kg), Plástico (15 pts/kg), Vidro (20 pts/kg), Metal (25 pts/kg)
- **Níveis**: Baseados em pontos totais (100 pontos por nível)
- **Conquistas**: Badges desbloqueadas por quantidade de descartes
- **Sequência de dias**: Bônus por descartes consecutivos
- **Pontos semanais**: Resetados semanalmente

---

## 📝 Scripts Úteis - Resumo Rápido

| Comando | Descrição |
|---------|-----------|
| `docker compose up -d` | Inicia backend e banco em background |
| `docker compose down` | Para e remove containers |
| `docker compose logs -f backend` | Ver logs do backend em tempo real |
| `docker compose build --no-cache` | Rebuild completo das imagens |
| `cd javafx-client && mvn javafx:run` | Executa o frontend JavaFX |
| `curl http://localhost:9161/api/test` | Testa se API está funcionando |
| `docker ps` | Lista containers em execução |

## 🎯 Funcionalidades Principais

### Para Usuários
- ✅ **Autenticação:** Registro e login de usuários com tokens de sessão
- ✅ **Solicitação de Coleta:** Agendamento de coleta domiciliar
- ✅ **Pontos de Coleta:** Visualização de pontos próximos (com cálculo de distância)
- ✅ **Cronograma:** Consulta de datas de coleta por material/cidade
- ✅ **Pontuação:** Sistema gamificado de pontos por ações sustentáveis
- ✅ **Níveis e Progresso:** Sistema de níveis baseado em pontos acumulados
- ✅ **Conquistas:** Badges desbloqueadas por quantidade de descartes
- ✅ **Recompensas:** Resgate de benefícios utilizando pontos acumulados
- ✅ **Ranking:** Visualização de ranking de usuários
- ✅ **Estatísticas:** Dashboard com estatísticas pessoais e gerais
- ✅ **Perfil:** Gerenciamento de dados pessoais

### Para Administradores
- ✅ **Gestão de Coletas:** Aprovação e controle de solicitações
- ✅ **Pontos de Coleta:** Cadastro e manutenção de locais
- ✅ **Cronograma:** Definição de datas de coleta
- ✅ **Recompensas:** Criação e gestão de benefícios
- ✅ **Relatórios:** Acompanhamento de métricas do sistema

---

## 🔍 Como o Projeto Funciona - Explicação Detalhada

### 1. Inicialização do Sistema

**Backend (Docker):**
1. Docker Compose inicia dois containers: `backend` e `db`
2. Container `db` (MySQL) inicializa e cria banco `recicla_facil`
3. Container `backend` (PHP-FPM + Nginx) inicia servidor web na porta 80 (mapeada para 9161 no host)
4. Script `init-database.php` cria tabelas automaticamente se não existirem
5. Backend fica pronto para receber requisições HTTP

**Frontend (Manual):**
1. Maven compila o projeto JavaFX
2. JavaFX inicia aplicação desktop
3. Aplicação carrega configuração de `app.properties` (URL da API)
4. Aplicação tenta conectar ao backend em `http://localhost:9161/api`

### 2. Fluxo de Autenticação

```
1. Usuário preenche login no JavaFX
   ↓
2. JavaFX envia: POST /api/auth/login {email, senha}
   ↓
3. Backend valida credenciais no MySQL
   ↓
4. Backend gera token de sessão e salva em `sessions`
   ↓
5. Backend retorna: {success: true, data: {user, token}}
   ↓
6. JavaFX armazena token e envia em requisições futuras
   ↓
7. Backend valida token via header Authorization: Bearer <token>
```

### 3. Fluxo de Pontuação

```
1. Usuário registra descarte no JavaFX
   ↓
2. JavaFX envia: POST /api/pontuacao/registrar-descarte {material, peso}
   ↓
3. Backend calcula pontos: peso × pontos_por_kg[material]
   ↓
4. Backend atualiza tabela `pontuacoes`:
   - Adiciona pontos ao total
   - Incrementa contador de descartes
   - Atualiza sequência de dias
   - Calcula novo nível
   ↓
5. Backend verifica conquistas desbloqueadas
   ↓
6. Backend retorna: {pontos_ganhos, pontuacao, novas_conquistas}
   ↓
7. JavaFX atualiza interface com novos dados
```

### 4. Sistema de Níveis

Os níveis são calculados automaticamente:
- **Nível 1 (Iniciante)**: 0-99 pontos
- **Nível 2 (Reciclador)**: 100-199 pontos
- **Nível 3 (Eco-amigo)**: 200-299 pontos
- E assim por diante...

Cada nível requer 100 pontos adicionais. O progresso é calculado como: `(pontos % 100) / 100 * 100`

### 5. Sistema de Conquistas

Conquistas são desbloqueadas automaticamente quando:
- Usuário atinge quantidade de descartes (ex: 1, 5, 10, 25, 50, 100, 250, 500)
- Cada conquista desbloqueada adiciona pontos bônus
- Progresso é calculado em tempo real

### 6. Sistema de Recompensas

```
1. Usuário visualiza recompensas disponíveis
   ↓
2. Usuário seleciona recompensa para resgatar
   ↓
3. JavaFX envia: POST /api/recompensas/resgatar {recompensa_id}
   ↓
4. Backend valida:
   - Usuário tem pontos suficientes?
   - Recompensa está disponível?
   ↓
5. Backend executa transação:
   - Cria registro em `resgate_recompensas`
   - Subtrai pontos do usuário
   - Decrementa disponibilidade da recompensa
   ↓
6. Backend retorna confirmação
   ↓
7. JavaFX atualiza interface
```

### 7. Comunicação Cliente-Servidor

**Protocolo:**
- **Método:** HTTP/HTTPS
- **Formato:** JSON (request e response)
- **Autenticação:** Bearer Token no header `Authorization`
- **CORS:** Habilitado para permitir requisições do cliente

**Exemplo de Requisição:**
```http
POST /api/pontuacao/registrar-descarte HTTP/1.1
Host: localhost:9161
Authorization: Bearer abc123token456
Content-Type: application/json

{
  "material": "plastico",
  "peso": 2.5
}
```

**Exemplo de Resposta:**
```json
{
  "success": true,
  "message": "Descarte registrado com sucesso",
  "data": {
    "pontos_ganhos": 37,
    "material": "plastico",
    "peso": 2.5,
    "pontuacao": {
      "pontos": 837,
      "nivel": 9,
      "nivel_nome": "Mestre da Reciclagem"
    },
    "novas_conquistas": []
  }
}
```

## Estrutura do Banco de Dados

O sistema inclui as seguintes entidades principais:
- **Users:** Usuários do sistema
- **Coletas:** Solicitações de coleta
- **PontoColetas:** Pontos de coleta cadastrados
- **CronogramaColetas:** Datas programadas de coleta
- **Pontuacoes:** Histórico de pontos dos usuários
- **Recompensas:** Benefícios disponíveis
- **Conquistas:** Sistema de badges
- **Doacoes:** Registro de doações

## Licença

> TODO: Definir licença do projeto.

## Créditos

Sistema desenvolvido para promoção da sustentabilidade e gestão eficiente de resíduos recicláveis.
Sistema desenvolvido para promoção da sustentabilidade e gestão eficiente de resíduos recicláveis.