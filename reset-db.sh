#!/bin/bash

# Detener el script si ocurre algún error
set -e

echo "➡️ Limpiando y corriendo migraciones frescas..."
php artisan migrate:fresh

echo "➡️ Ejecutando seeder de Roles y Permisos..."
php artisan db:seed --class=RolesAndPermissionsSeeder

echo "➡️ Ejecutando migración legacy de SGEI..."
php artisan sgei:migrate-legacy-starting-bd

echo "➡️ Ejecutando seeder de Personas y Usuarios de prueba..."
php artisan db:seed --class=PersonasUsuariosPruebaSeeder

echo "➡️ Ejecutando seeder de Naciones..."
php artisan db:seed --class=NacionsTableSeeder

echo "➡️ Ejecutando seeder de Cargos..."
php artisan db:seed --class=CargoSeeder

echo "✅ ¡Base de datos restaurada y poblada con éxito!"