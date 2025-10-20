# Recicla Fácil - Docker Setup

Este projeto contém a aplicação completa Recicla Fácil com frontend (AngularJS) e backend (Laravel) rodando em containers Docker.

## Arquitetura

- **Frontend**: AngularJS servido via Nginx na porta 9160
- **Backend**: Laravel API com PHP-FPM
- **Database**: MySQL 8.0
- **Proxy**: Nginx faz proxy reverso de `/api` para o backend

## Comandos para Executar

### Limpar ambiente anterior
```powershell
# parar e remover tudo do compose atual
docker compose down --remove-orphans

# remover imagens antigas (ignorar erro se não existirem)
docker rmi recicla-facil-backend; if ($?) { echo "Imagem backend removida" } else { echo "Imagem backend não existia" }
docker rmi recicla-facil-frontend; if ($?) { echo "Imagem frontend removida" } else { echo "Imagem frontend não existia" }
```

### Build e subida com nomes e tags exigidos
```powershell
# build e subida com nomes e tags exigidos
docker compose build --no-cache
docker compose up -d
```

### Verificar se está funcionando
```powershell
# verificar containers
docker ps

# testar frontend
curl -I http://localhost:9160

# testar API (se houver endpoint de health)
curl -I http://localhost:9160/api/health
```

## Endpoints

- **Frontend**: http://localhost:9160
- **API Backend**: http://localhost:9160/api/*
- **Database**: localhost:33061 (MySQL)

## Estrutura dos Containers

### Imagens (exatas conforme solicitado)
- `recicla-facil-backend`
- `recicla-facil-frontend`

### Containers (exatos conforme solicitado)
- `recicla_facil_backend`
- `recicla_facil_frontend`
- `recicla_facil_db`

## Configuração

O frontend está configurado para consumir a API em `http://localhost:9160/api` (configurado no `window.APP_CONFIG` do `index.html`).

O Nginx no frontend faz proxy reverso de todas as requisições `/api/*` para o container do backend.

## Troubleshooting

### Ver logs dos containers
```powershell
docker compose logs -f
```

### Acessar container do backend
```powershell
docker exec -it recicla_facil_backend bash
```

### Acessar container do frontend
```powershell
docker exec -it recicla_facil_frontend sh
```

### Verificar conectividade entre containers
```powershell
docker exec -it recicla_facil_frontend ping recicla_facil_backend
```