FROM nginx:alpine

# Copiar arquivos da aplicação frontend
COPY . /usr/share/nginx/html

# Configuração do Nginx para servir o frontend e fazer proxy para o backend
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expor porta 80
EXPOSE 80

# Comando para iniciar o Nginx
CMD ["nginx", "-g", "daemon off;"]
