# Recicla Fácil

Sistema completo de gestão de reciclagem que conecta cidadãos, pontos de coleta e serviços de coleta domiciliar, promovendo a sustentabilidade através de uma plataforma gamificada com sistema de pontuação e recompensas.

## Visão Geral

O Recicla Fácil é uma aplicação web que facilita o processo de reciclagem ao conectar usuários com pontos de coleta próximos e permitir solicitações de coleta domiciliar. O sistema inclui funcionalidades de gamificação com pontuação por ações sustentáveis, cronograma de coletas, sistema de recompensas e conquistas, incentivando a participação ativa dos usuários na preservação ambiental.

## Arquitetura (Monorepo)

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (AngularJS)   │◄──►│   (Laravel)     │◄──►│   (MySQL 8.0)  │
│   Porta: 9160   │    │   Porta: 9161   │    │   Porta: 33061  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

| Diretório | Propósito | Tecnologia |
|-----------|-----------|------------|
| `frontend/` | Interface do usuário SPA | AngularJS 1.8.3 + Nginx |
| `backend/` | API REST e lógica de negócio | Laravel + PHP 8.2 |
| `database/` | Migrações e seeders | MySQL 8.0 |

## Stack & Principais Tecnologias

### Backend
- **Linguagem:** PHP 8.2
- **Framework:** Laravel (Illuminate Database)
- **Banco de Dados:** MySQL 8.0
- **Servidor:** Nginx + PHP-FPM
- **Containerização:** Docker

### Frontend
- **Framework:** AngularJS 1.8.3
- **Servidor:** Nginx Alpine
- **Roteamento:** Angular Route
- **HTTP Client:** Angular $http
- **Containerização:** Docker

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
- **Frontend:** http://localhost:9160
- **API Backend:** http://localhost:9160/api/*
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

# Acessar container do frontend
docker exec -it recicla_facil_frontend sh
```

## Scripts Úteis

| Comando | Descrição |
|---------|-----------|
| `docker compose up -d` | Inicia todos os serviços em background |
| `docker compose down` | Para e remove containers |
| `docker compose build --no-cache` | Rebuild completo das imagens |
| `docker compose logs -f` | Visualiza logs em tempo real |
| `docker exec -it recicla_facil_backend bash` | Acessa terminal do backend |
| `docker exec -it recicla_facil_frontend sh` | Acessa terminal do frontend |

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

## Créditos

Sistema desenvolvido para promoção da sustentabilidade e gestão eficiente de resíduos recicláveis.
