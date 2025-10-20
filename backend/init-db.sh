#!/bin/bash

echo "Aguardando banco de dados..."
sleep 10

echo "Executando migrations..."
php artisan migrate --force

echo "Executando seeders..."
php artisan db:seed --force

echo "Banco de dados inicializado!"
