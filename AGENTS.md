# AGENTS.md

## Proyecto

Este proyecto se llama PeruAsoCebu.

Es una base Laravel que ya tiene:
- Login
- Registro
- Usuarios
- Roles y permisos
- AdminLTE
- Dashboard
- Layout administrativo
- Vite
- Laravel 12

No crear otro login.
No reemplazar AdminLTE.
No eliminar el dashboard.
No modificar autenticación sin autorización.

## Objetivo

Convertir esta base en un sistema para ganadería bovina.

Debe tener:
- Página web pública institucional
- Panel administrativo
- Gestión de ganado vacuno
- Registro de razas
- Registro de fundos o haciendas
- Registro de padre y madre
- Árbol genealógico bovino
- Control sanitario
- Control de pesos
- Reportes

## Reglas

- Respetar la estructura actual.
- Usar Laravel, Blade, Bootstrap, AdminLTE y Vite.
- Textos visibles en español.
- Tablas y modelos en inglés.
- No borrar archivos sin explicar primero.
- Antes de cambios grandes, proponer plan.
- Después de cada cambio, indicar archivos modificados y comandos a ejecutar.

## Comandos útiles

composer install
npm install
npm run dev
npm run build
php artisan migrate
php artisan optimize:clear
php artisan route:list