# Recicla Fácil

Sistema completo de gestão de reciclagem que conecta cidadãos, pontos de coleta e serviços de coleta domiciliar, promovendo a sustentabilidade através de uma plataforma gamificada com sistema de pontuação e recompensas.

## Visão Geral

O Recicla Fácil é composto por um backend Laravel + MySQL (containerizados) e um cliente desktop JavaFX responsável por toda a experiência do usuário. Assim, toda a lógica de gamificação, pontuação e gestão de coletas acontece em um aplicativo Java com layout inspirado na versão web original, mas sem dependências de AngularJS.

## Arquitetura (Monorepo)

```
┌──────────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Cliente Desktop      │    │ Backend REST    │    │ Database        │
│ JavaFX (Java 17)     │◄──►│ Laravel + PHP   │◄──►│ MySQL 8.0       │
│ UI completa + Docker │    │ Porta: 9161     │    │ Porta: 33061    │
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
- **Empacotamento:** JAR standalone + imagem Docker (Liberica OpenJFX)

### Infraestrutura
- **Orquestração:** Docker Compose
- **Proxy Reverso:** Nginx
- **Banco de Dados:** MySQL 8.0
- **Rede:** Bridge Network

## Como Rodar (Local)

### Pré-requisitos
- Docker e Docker Compose
- Git

### Instalação e Execução

```bash
# Clonar o repositório
git clone <repository-url>
cd recicla-facil

# Build e inicialização dos containers
docker compose build --no-cache
docker compose up -d

# Verificar status dos containers
docker ps
```

### Acessos
- **API Backend:** http://localhost:9161/api/*
- **Cliente JavaFX (local):** `mvn javafx:run` apontando para `http://localhost:9161/api`
- **Cliente JavaFX (Docker):** exposto via X11/VcXsrv (ver seção abaixo)
- **Database:** localhost:33061

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

### 1. Local (sem Docker)
```bash
cd javafx-client
mvn clean javafx:run
```
- Ajuste `javafx-client/src/main/resources/app.properties` ou exporte `API_BASE_URL` / `AUTH_TOKEN` para apontar para o backend em Docker.

### 2. Com Docker (X11 / VcXsrv)
O Dockerfile do cliente gera uma imagem com JavaFX. Para rodar a UI em um container é necessário encaminhar o display do host:

```bash
# Linux (X11)
xhost +local:docker
docker compose -f docker-compose.yml -f docker-compose.javafx.yml up --build

# Windows (WSL2 + VcXsrv)
# 1. Abra o VcXsrv permitindo acesso externo
# 2. Defina DISPLAY no PowerShell/WSL: $env:DISPLAY="host.docker.internal:0.0"
# 3. Execute:
docker compose -f docker-compose.yml -f docker-compose.javafx.yml up --build
```

Variáveis úteis:
- `API_BASE_URL`: override do endpoint (default `http://backend/api` dentro da rede Docker).
- `AUTH_TOKEN`: opcional para injetar um JWT enquanto o fluxo de login desktop não é implementado.
- `X11_SOCKET`: caminho do socket X11 (default `/tmp/.X11-unix`).

> Para sair, `docker compose down` e execute `xhost -local:docker` (Linux) se necessário.

## Scripts Úteis

| Comando | Descrição |
|---------|-----------|
| `docker compose up -d` | Inicia todos os serviços em background |
| `docker compose down` | Para e remove containers |
| `docker compose build --no-cache` | Rebuild completo das imagens |
| `docker compose logs -f` | Visualiza logs em tempo real |
| `docker exec -it recicla_facil_backend bash` | Acessa terminal do backend |
| `docker compose -f docker-compose.yml -f docker-compose.javafx.yml up` | Sobe backend + cliente JavaFX via Docker |

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