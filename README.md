# Recicla Fácil

Sistema completo de gestão de reciclagem que conecta cidadãos, pontos de coleta e serviços de coleta domiciliar, promovendo a sustentabilidade através de uma plataforma gamificada com sistema de pontuação e recompensas.

## Visão Geral

O Recicla Fácil é composto por um backend Laravel + MySQL (containerizados via Docker) e um cliente desktop JavaFX que deve ser executado manualmente no ambiente local. O backend e banco de dados rodam em containers Docker, enquanto o frontend JavaFX é executado diretamente na máquina do desenvolvedor, conectando-se ao backend através da API REST.

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

## Como Rodar (Local)

### Pré-requisitos

**Para Backend e Banco de Dados:**
- Docker Desktop instalado e rodando
- Docker Compose (incluído no Docker Desktop)

**Para Frontend JavaFX:**
- Java 17 ou superior instalado e no PATH
- Maven 3.6+ instalado e no PATH

**📖 Guia Completo de Instalação:**
- **Windows:** Veja `INSTALACAO_WINDOWS.md` para instruções detalhadas
- **Linux/Mac:** Instale via gerenciador de pacotes (apt, brew, etc.)

**🔍 Verificar Dependências:**
- **Windows (PowerShell):** Execute `.\verificar-dependencias.ps1`
- **Windows (CMD):** Execute `verificar-dependencias.bat`
- **Linux/Mac:** Execute `./verificar-dependencias.sh` (se disponível)

**⚡ Scripts de Ajuda (Windows):**
- `verificar-dependencias.bat` ou `.ps1` - Verifica se todas as dependências estão instaladas
- `iniciar-backend.bat` - Inicia o backend e banco de dados automaticamente
- `javafx-client/executar.bat` - Executa o frontend JavaFX com verificações

### Instalação e Execução

#### 1. Iniciar Backend e Banco de Dados (Docker)

**Windows (Script Automatizado):**
```powershell
.\iniciar-backend.bat
```

**Windows/Linux/Mac (Manual):**
```bash
# Clonar o repositório (se ainda não tiver)
git clone <repository-url>
cd recicla-facil

# Build e inicialização dos containers (backend + banco)
docker compose build --no-cache
docker compose up -d

# Verificar status dos containers
docker ps

# Verificar logs do backend
docker compose logs -f backend
```

#### 2. Executar Frontend JavaFX (Manual)

**Windows:**
```powershell
# Opção 1: Usar script automatizado (recomendado)
cd javafx-client
.\executar.bat

# Opção 2: Executar manualmente
cd javafx-client
mvn clean javafx:run
```

**Linux/Mac:**
```bash
cd javafx-client
mvn clean javafx:run
```

**Nota:** O frontend está configurado para se conectar automaticamente ao backend em `http://localhost:9161/api`. Esta configuração está em `javafx-client/src/main/resources/app.properties`.

### Acessos
- **API Backend:** http://localhost:9161/api/*
- **Cliente JavaFX:** Executado manualmente via `mvn javafx:run`
- **Database:** localhost:33061 (apenas para acesso direto, se necessário)

## Configuração de Ambiente (resumo, sem nomes/valores)

O sistema utiliza variáveis de ambiente para configuração do banco de dados e aplicação. Crie um arquivo `.env` com credenciais locais e endpoints adequados ao seu ambiente. Evite commitar segredos.

> TODO: Ajustar variáveis de ambiente conforme documentação interna.

## Docker

### Comandos Principais

```bash
# Inicializar todos os serviços
docker compose up -d

# Parar todos os serviços
docker compose down

# Rebuild completo
docker compose build --no-cache
docker compose up -d

# Ver logs
docker compose logs -f

# Acessar container do backend
docker exec -it recicla_facil_backend bash
```

## Executando o Cliente JavaFX

O frontend JavaFX **deve ser executado manualmente** na máquina local, não em Docker.

### Pré-requisitos
- Java 17 ou superior instalado
- Maven 3.6+ instalado
- Backend e banco de dados rodando em Docker (ver seção anterior)

### Execução

```bash
# Navegar para o diretório do cliente
cd javafx-client

# Executar o aplicativo
mvn clean javafx:run
```

### Configuração

A URL da API está configurada em `javafx-client/src/main/resources/app.properties`:

```properties
api.baseUrl=http://localhost:9161/api
```

Para alterar a URL da API, você pode:
1. Editar o arquivo `app.properties` diretamente, ou
2. Definir a variável de ambiente `API_BASE_URL` antes de executar:
   ```bash
   export API_BASE_URL=http://localhost:9161/api
   mvn javafx:run
   ```

### Troubleshooting

**Erro de conexão com o backend:**
- Verifique se o backend está rodando: `docker ps`
- Verifique os logs do backend: `docker compose logs backend`
- Teste a API manualmente: `curl http://localhost:9161/api/test`

**Erro ao executar o Maven:**
- Verifique se o Java 17+ está instalado: `java -version`
- Verifique se o Maven está instalado: `mvn -version`
- Limpe e recompile: `mvn clean install`

## Scripts Úteis

| Comando | Descrição |
|---------|-----------|
| `docker compose up -d` | Inicia backend e banco em background |
| `docker compose down` | Para e remove containers |
| `docker compose build --no-cache` | Rebuild completo das imagens |
| `docker compose logs -f` | Visualiza logs em tempo real |
| `docker exec -it recicla_facil_backend bash` | Acessa terminal do backend |
| `cd javafx-client && mvn javafx:run` | Executa o frontend JavaFX manualmente |

## Funcionalidades Principais

### Para Usuários
- **Autenticação:** Registro e login de usuários
- **Solicitação de Coleta:** Agendamento de coleta domiciliar
- **Pontos de Coleta:** Visualização de pontos próximos no mapa
- **Cronograma:** Consulta de datas de coleta
- **Pontuação:** Sistema de pontos por ações sustentáveis
- **Recompensas:** Resgate de benefícios com pontos
- **Perfil:** Gerenciamento de dados pessoais

### Para Administradores
- **Gestão de Coletas:** Aprovação e controle de solicitações
- **Pontos de Coleta:** Cadastro e manutenção de locais
- **Cronograma:** Definição de datas de coleta
- **Recompensas:** Criação e gestão de benefícios
- **Relatórios:** Acompanhamento de métricas

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