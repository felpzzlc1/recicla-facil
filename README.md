# Recicla Fácil - Branch Main

> **Frontend Web (AngularJS) + Backend Laravel**

Sistema completo de gestão de reciclagem desenvolvido como aplicação web, conectando cidadãos, pontos de coleta e serviços de coleta domiciliar através de uma interface web moderna e responsiva.

## 🎯 Visão Geral

O Recicla Fácil na branch **main** é uma aplicação web completa que funciona diretamente no navegador, permitindo acesso de qualquer dispositivo (computador, tablet, smartphone) sem necessidade de instalação.

### Arquitetura

```
┌──────────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Frontend Web         │    │ Backend REST    │    │ Database        │
│ AngularJS + HTML/CSS  │◄──►│ Laravel + PHP   │◄──►│ MySQL 8.0       │
│ Navegador Web        │    │ Docker:9161     │    │ Docker:33061    │
└──────────────────────┘    └─────────────────┘    └─────────────────┘
```

## 🛠️ Stack Tecnológica

### Frontend Web
- **Framework:** AngularJS 1.x
- **Linguagem:** JavaScript (ES5/ES6)
- **Estilização:** CSS3, Bootstrap (ou framework CSS customizado)
- **HTTP Client:** AngularJS $http service
- **Build:** Gulp/Grunt ou Webpack (opcional)
- **Execução:** Navegador web (Chrome, Firefox, Safari, Edge)

### Backend
- **Linguagem:** PHP 8.2
- **Framework:** Laravel (Illuminate Database)
- **Banco de Dados:** MySQL 8.0
- **Servidor:** Nginx + PHP-FPM
- **Containerização:** Docker + Docker Compose
- **API:** RESTful JSON

### Infraestrutura
- **Orquestração:** Docker Compose
- **Proxy Reverso:** Nginx
- **Banco de Dados:** MySQL 8.0
- **Rede:** Bridge Network

## 📁 Estrutura do Projeto

```
recicla-facil/
├── frontend/              # Aplicação AngularJS
│   ├── app/
│   │   ├── controllers/   # Controllers AngularJS
│   │   ├── services/      # Services para comunicação com API
│   │   ├── directives/    # Directives customizadas
│   │   └── views/         # Templates HTML
│   ├── assets/
│   │   ├── css/           # Estilos CSS
│   │   ├── js/            # JavaScript adicional
│   │   └── images/        # Imagens e ícones
│   ├── index.html         # Página principal
│   └── package.json        # Dependências Node.js
├── backend/                # API Laravel
│   ├── app/
│   ├── routes/
│   ├── database/
│   └── public/
└── docker-compose.yml      # Configuração Docker
```

## 🚀 Como Executar

### Pré-requisitos

**Para Backend e Banco de Dados:**
- ✅ Docker Desktop instalado e rodando
- ✅ Docker Compose

**Para Frontend:**
- ✅ Navegador web moderno (Chrome, Firefox, Safari, Edge)
- ✅ Node.js e npm (opcional, para build)

### Passo a Passo

#### 1. Iniciar Backend e Banco de Dados

```bash
# Na raiz do projeto
docker compose up -d
```

#### 2. Verificar se Backend está Rodando

```bash
# Testar API
curl http://localhost:9161/api/test

# Ver logs
docker compose logs -f backend
```

#### 3. Acessar Frontend Web

**Opção 1: Servidor de Desenvolvimento (se configurado)**
```bash
cd frontend
npm install
npm start
# Acessar: http://localhost:4200 ou porta configurada
```

**Opção 2: Servir Arquivos Estáticos**
```bash
# Usando Python
cd frontend
python -m http.server 8000
# Acessar: http://localhost:8000

# Ou usando Node.js http-server
npx http-server -p 8000
```

**Opção 3: Integrado com Backend**
- Se o frontend está servido pelo próprio backend Laravel, acesse:
- `http://localhost:9161` (ou porta configurada)

## 📍 Endpoints e Acessos

| Serviço | URL | Descrição |
|---------|-----|-----------|
| **Frontend Web** | http://localhost:9161 | Interface web AngularJS |
| **API Backend** | http://localhost:9161/api | API REST principal |
| **Health Check** | http://localhost:9161/up | Verificar se backend está online |
| **Database** | localhost:33061 | MySQL (acesso direto) |

## 🎨 Características do Frontend AngularJS

### Vantagens
- ✅ **Acessibilidade:** Funciona em qualquer navegador moderno
- ✅ **Responsivo:** Adapta-se a diferentes tamanhos de tela
- ✅ **Sem Instalação:** Não requer instalação no dispositivo do usuário
- ✅ **Atualização Automática:** Mudanças no servidor refletem imediatamente
- ✅ **Multiplataforma:** Windows, Mac, Linux, Android, iOS

### Funcionalidades
- **SPA (Single Page Application):** Navegação fluida sem recarregar página
- **Two-Way Data Binding:** Atualização automática da interface
- **Roteamento:** Navegação entre diferentes views
- **Serviços HTTP:** Comunicação assíncrona com API REST
- **Filtros e Directives:** Manipulação de dados e criação de componentes reutilizáveis

## 🔄 Fluxo de Dados

```
1. Usuário acessa http://localhost:9161 no navegador
   ↓
2. Navegador carrega index.html e AngularJS
   ↓
3. AngularJS inicializa e faz requisição: GET /api/auth/profile
   ↓
4. Backend valida token (se existir) e retorna dados do usuário
   ↓
5. AngularJS atualiza interface com dados recebidos
   ↓
6. Usuário interage (clica, preenche formulário)
   ↓
7. AngularJS envia requisição HTTP para API
   ↓
8. Backend processa e retorna JSON
   ↓
9. AngularJS atualiza view automaticamente
```

## 📋 Principais Funcionalidades

### Para Usuários
- ✅ **Autenticação:** Login e registro via interface web
- ✅ **Dashboard:** Visualização de estatísticas e progresso
- ✅ **Pontos de Coleta:** Mapa e lista de pontos próximos
- ✅ **Cronograma:** Consulta de datas de coleta
- ✅ **Pontuação:** Sistema gamificado com níveis e conquistas
- ✅ **Recompensas:** Catálogo e resgate de benefícios
- ✅ **Ranking:** Visualização de posição entre usuários
- ✅ **Perfil:** Gerenciamento de dados pessoais

### Para Administradores
- ✅ **Painel Administrativo:** Interface de gestão
- ✅ **Gestão de Coletas:** Aprovação e controle
- ✅ **CRUD de Pontos:** Cadastro e manutenção
- ✅ **Gestão de Recompensas:** Criação e edição
- ✅ **Relatórios:** Métricas e estatísticas

## 🔧 Desenvolvimento

### Estrutura AngularJS Típica

```javascript
// app.js - Módulo principal
var app = angular.module('reciclaFacil', ['ngRoute']);

// Controller
app.controller('DashboardController', function($scope, $http) {
    $http.get('/api/pontuacao/estatisticas')
        .then(function(response) {
            $scope.estatisticas = response.data.data;
        });
});

// Service
app.service('PontuacaoService', function($http) {
    this.getEstatisticas = function() {
        return $http.get('/api/pontuacao/estatisticas');
    };
});
```

### Build e Deploy

```bash
# Instalar dependências
cd frontend
npm install

# Build para produção (se configurado)
npm run build

# Os arquivos compilados podem ser servidos pelo backend
# ou por qualquer servidor web estático
```

## 🐳 Docker

```bash
# Iniciar serviços
docker compose up -d

# Ver logs
docker compose logs -f backend

# Parar serviços
docker compose down
```

## 📚 Tecnologias e Bibliotecas

### Frontend
- **AngularJS 1.x** - Framework JavaScript MVC
- **Bootstrap** - Framework CSS (opcional)
- **jQuery** - Biblioteca JavaScript (se necessário)
- **Chart.js** - Gráficos e visualizações (opcional)

### Backend
- **Laravel** - Framework PHP
- **MySQL** - Banco de dados relacional
- **Docker** - Containerização

## 🔍 Diferenças da Branch javafx-main

| Aspecto | Branch Main (Web) | Branch javafx-main (Desktop) |
|---------|-------------------|------------------------------|
| **Frontend** | AngularJS (Web) | JavaFX (Desktop) |
| **Acesso** | Navegador web | Aplicativo instalado |
| **Plataforma** | Multiplataforma (web) | Windows/Mac/Linux (nativo) |
| **Instalação** | Não requer | Requer instalação |
| **Atualização** | Automática (servidor) | Manual (nova versão) |
| **Offline** | Não funciona | Pode funcionar parcialmente |
| **Performance** | Depende do navegador | Nativa do sistema |

## 📝 Notas

- Esta branch utiliza AngularJS para criar uma experiência web completa
- O frontend pode ser servido pelo próprio backend Laravel ou por um servidor web separado
- Ideal para usuários que preferem acessar via navegador sem instalar software
- Compatível com dispositivos móveis através de design responsivo

## 🗄️ Estrutura do Banco de Dados

O sistema utiliza um banco de dados MySQL com **11 tabelas principais**:

### Tabelas Principais

1. **`users`** - Cadastro de usuários do sistema
2. **`sessions`** - Controle de sessões e autenticação
3. **`coletas`** - Solicitações de coleta domiciliar
4. **`ponto_coletas`** - Pontos de coleta cadastrados
5. **`cronograma_coletas`** - Cronograma de coletas programadas
6. **`doacoes`** - Registro de doações de materiais
7. **`pontuacoes`** - Sistema de pontuação e estatísticas dos usuários
8. **`tipos_conquistas`** - Catálogo de conquistas disponíveis
9. **`conquistas`** - Conquistas desbloqueadas pelos usuários
10. **`recompensas`** - Catálogo de recompensas disponíveis
11. **`resgate_recompensas`** - Histórico de resgates de recompensas

### Funções das Tabelas

- **Usuários e Autenticação:** `users`, `sessions`
- **Coleta e Reciclagem:** `coletas`, `ponto_coletas`, `cronograma_coletas`, `doacoes`
- **Gamificação:** `pontuacoes`, `tipos_conquistas`, `conquistas`
- **Recompensas:** `recompensas`, `resgate_recompensas`

Todas as tabelas são criadas automaticamente pelo script `init-database.php` quando o backend é iniciado pela primeira vez.

---

## 📄 Licença

> TODO: Definir licença do projeto.

