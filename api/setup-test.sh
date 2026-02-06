#!/bin/bash

echo "Iniciando Setup de Entorno de Pruebas..."

# 1. Limpiar caches para evitar conflictos
php artisan config:clear --env=testing

# 2. Ejecutar migraciones y seeders específicos de test
# Usamos el flag --force porque en testing se considera un entorno "protected"
php artisan migrate --env=testing

echo "Base de datos de test lista."

echo "Ejecutando seeders"
php artisan db:seed DatabaseSeeder --env=testing

# 3. Opcional: Ejecutar los tests inmediatamente
# php artisan test