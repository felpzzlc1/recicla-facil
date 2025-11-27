# Recicla Fácil - Branch javafx-main

> **Frontend Desktop (JavaFX) + Backend Laravel**

Sistema completo de gestão de reciclagem desenvolvido como aplicação desktop nativa, oferecendo uma experiência de usuário rica e integrada ao sistema operacional.

## 🎯 Visão Geral

O Recicla Fácil na branch **javafx-main** é uma aplicação desktop que roda nativamente no sistema operacional, proporcionando performance superior e integração com recursos do sistema.

### Arquitetura

```
┌──────────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Cliente Desktop      │    │ Backend REST    │    │ Database        │
│ JavaFX (Java 17)     │◄──►│ Laravel + PHP   │◄──►│ MySQL 8.0       │
│ Execução Manual      │    │ Docker:9161     │    │ Docker:33061    │
└──────────────────────┘    └─────────────────┘    └─────────────────┘
```

## 🛠️ Stack Tecnológica

### Frontend Desktop
- **Framework:** JavaFX 21
- **Linguagem:** Java 17
- **Build:** Maven + JavaFX Maven Plugin
- **HTTP Client:** java.net.http + Jackson (JSON)
- **Arquitetura:** MVVM simplificado (Services + Controllers)
- **UI:** FXML (XML) + CSS
- **Execução:** JVM (Java Virtual Machine)
- **Empacotamento:** JAR standalone

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
├── javafx-client/          # Aplicação JavaFX
│   ├── src/
│   │   └── main/
│   │       ├── java/
│   │       │   └── com/reciclafacil/desktop/
│   │       │       ├── controller/    # Controllers JavaFX
│   │       │       ├── service/       # Services para API
│   │       │       ├── model/         # Modelos de dados
│   │       │       ├── net/           # Cliente HTTP
│   │       │       └── util/           # Utilitários
│   │       └── resources/
│   │           ├── fxml/              # Interfaces FXML
│   │           ├── styles/            # Estilos CSS
│   │           └── app.properties     # Configurações
│   └── pom.xml                        # Configuração Maven
├── backend/                 # API Laravel
│   ├── app/
│   ├── routes/
│   ├── database/
│   └── public/
└── docker-compose.yml       # Configuração Docker
```

## 🚀 Como Executar

### Pré-requisitos

**Para Backend e Banco de Dados:**
- ✅ Docker Desktop instalado e rodando
- ✅ Docker Compose

**Para Frontend JavaFX:**
- ✅ Java 17 ou superior instalado
- ✅ Maven 3.6+ instalado (ou usar Maven Wrapper)

### Verificar Instalações

```bash
# Verificar Java
java -version  # Deve ser 17 ou superior

# Verificar Maven
mvn -version   # Deve ser 3.6 ou superior
```

### Passo a Passo

#### 1. Iniciar Backend e Banco de Dados

```bash
# Na raiz do projeto
docker compose up -d

# Verificar se está rodando
docker compose logs -f backend
curl http://localhost:9161/api/test
```

#### 2. Executar Frontend JavaFX

```bash
# Windows (PowerShell/CMD)
cd javafx-client
.\mvnw.cmd clean javafx:run

# Linux/Mac
cd javafx-client
./mvnw clean javafx:run

# Ou se Maven estiver instalado globalmente
cd javafx-client
mvn clean javafx:run
```

## 📍 Endpoints e Acessos

| Serviço | URL | Descrição |
|---------|-----|-----------|
| **API Backend** | http://localhost:9161/api | API REST principal |
| **Health Check** | http://localhost:9161/up | Verificar se backend está online |
| **Test API** | http://localhost:9161/api/test | Teste básico da API |
| **Database** | localhost:33061 | MySQL (acesso direto) |
| **Cliente JavaFX** | Executado via `mvn javafx:run` | Interface desktop |

## 🎨 Características do Frontend JavaFX

### Vantagens
- ✅ **Performance Nativa:** Execução direta na JVM, sem intermediários
- ✅ **Interface Rica:** Componentes nativos do sistema operacional
- ✅ **Offline Parcial:** Pode funcionar parcialmente sem conexão
- ✅ **Integração Sistema:** Acesso a recursos do SO (notificações, arquivos)
- ✅ **Experiência Desktop:** Look and feel nativo
- ✅ **Segurança:** Execução em ambiente controlado (JVM)

### Funcionalidades
- **FXML:** Interface declarativa em XML
- **CSS Customizado:** Estilização avançada
- **Binding:** Data binding bidirecional
- **Threading:** Operações assíncronas com CompletableFuture
- **Services:** Camada de abstração para comunicação com API

## 🔄 Fluxo de Dados

```
1. Usuário executa aplicação JavaFX
   ↓
2. JavaFX carrega MainApp e inicializa interface
   ↓
3. AppContext inicializa serviços (AuthService, PontuacaoService, etc.)
   ↓
4. Controller faz requisição via Service: GET /api/pontuacao/estatisticas
   ↓
5. ApiClient envia HTTP Request com token (se autenticado)
   ↓
6. Backend processa e retorna JSON
   ↓
7. Jackson deserializa JSON para objetos Java
   ↓
8. Controller atualiza interface via Platform.runLater()
   ↓
9. JavaFX atualiza UI na thread principal
```

## 📋 Principais Funcionalidades

### Para Usuários
- ✅ **Autenticação:** Login automático e gerenciamento de sessão
- ✅ **Dashboard:** Visualização completa de estatísticas e progresso
- ✅ **Pontos de Coleta:** Lista com informações detalhadas
- ✅ **Cronograma:** Visualização de datas de coleta
- ✅ **Pontuação:** Sistema gamificado com níveis e conquistas
- ✅ **Recompensas:** Catálogo e resgate de benefícios
- ✅ **Ranking:** Visualização de posição entre usuários
- ✅ **Registro de Descarte:** Interface para registrar materiais

### Arquitetura do Cliente

```
MainApp (Entry Point)
    ↓
MainViewController (Navegação Principal)
    ├── PontuacaoViewController (Dashboard)
    ├── PontosColetaViewController (Pontos de Coleta)
    ├── CronogramaViewController (Cronograma)
    └── RecompensasViewController (Recompensas)
         ↓
    Services (Camada de Negócio)
         ├── PontuacaoService
         ├── PontoColetaService
         ├── CronogramaService
         └── RecompensaService
              ↓
    ApiClient (Comunicação HTTP)
         ↓
    Backend Laravel (API REST)
```

## 🔧 Desenvolvimento

### Estrutura JavaFX Típica

```java
// Controller
public class PontuacaoViewController {
    @FXML private Label pontosLabel;
    
    private final PontuacaoService pontuacaoService;
    
    public void initialize() {
        pontuacaoService = AppContext.get().getPontuacaoService();
        carregarEstatisticas();
    }
    
    private void carregarEstatisticas() {
        pontuacaoService.obterEstatisticas()
            .thenAccept(estatisticas -> {
                Platform.runLater(() -> {
                    pontosLabel.setText(estatisticas.getPontuacaoTotal() + " pontos");
                });
            });
    }
}
```

### Build e Empacotamento

```bash
# Compilar
cd javafx-client
mvn clean compile

# Executar
mvn javafx:run

# Criar JAR executável (se configurado)
mvn clean package
java -jar target/recicla-facil-javafx.jar
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
- **JavaFX 21** - Framework UI para Java
- **Java 17** - Linguagem de programação
- **Maven** - Gerenciador de dependências e build
- **Jackson** - Serialização/Deserialização JSON
- **java.net.http** - Cliente HTTP nativo do Java

### Backend
- **Laravel** - Framework PHP
- **MySQL** - Banco de dados relacional
- **Docker** - Containerização

## 🔍 Diferenças da Branch Main

| Aspecto | Branch javafx-main (Desktop) | Branch Main (Web) |
|---------|------------------------------|-------------------|
| **Frontend** | JavaFX (Desktop) | AngularJS (Web) |
| **Acesso** | Aplicativo instalado | Navegador web |
| **Plataforma** | Windows/Mac/Linux (nativo) | Multiplataforma (web) |
| **Instalação** | Requer instalação | Não requer |
| **Atualização** | Manual (nova versão) | Automática (servidor) |
| **Offline** | Pode funcionar parcialmente | Não funciona |
| **Performance** | Nativa do sistema | Depende do navegador |
| **Experiência** | Desktop nativo | Web moderna |

## 📝 Notas

- Esta branch utiliza JavaFX para criar uma experiência desktop nativa
- Requer Java instalado no sistema do usuário
- Ideal para usuários que preferem aplicativos desktop tradicionais
- Oferece melhor performance e integração com o sistema operacional
- Pode ser empacotado como JAR executável para distribuição

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

