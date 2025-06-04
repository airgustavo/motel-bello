# 🏨 Sistema de Control de Motel

Este proyecto es un sistema web desarrollado en **Laravel** utilizando **FilamentPHP** para la gestión de un motel. Permite controlar el estado de las habitaciones (ocupadas o disponibles), mostrar el tiempo restante de uso, y realizar operaciones administrativas básicas a través de una interfaz moderna y fácil de usar.

## ✨ Características

- 🔐 **Login seguro** para el acceso al sistema.
- 🛏️ **Visualización de habitaciones** con su estado (Disponible / Ocupada / Fuera de Servicio).
- ⏱️ **Control del tiempo de uso** por habitación ocupada.
- 📊 **Dashboard amigable** gracias a FilamentPHP.
- ⚙️ Panel administrativo para la gestión de habitaciones (en desarrollo o ya implementado).
- 📱 Interfaz responsiva y moderna.

## 🛠️ Tecnologías utilizadas

- **Laravel** (versión 11 o superior)
- **FilamentPHP** (v3)
- **PHP** 8.2+
- **TailwindCSS** (integrado con Filament)
- **SQLite / MySQL** como base de datos (según configuración)
- **Vite** para compilación de assets

## 🚀 Instalación

1. Clona el repositorio:
   
   git clone https://gustavo_orbezo@bitbucket.org/gustavo_orbezo/motel-bello.git
   cd motel-bello

2. Intala las dependencias:
    composer install
    npm install && npm run dev

3. Configura tu archivo .env:
    cp .env.example .env
    php artisan key:generate

4. Configura la base de datos en .env y ejecuta las migraciones:
    php artisan migrate

5. Opcional: seed inicial con habitaciones
    php artisan db:seed

6. Inicia el servidor:
    php artisan serve


📄 Licencia
Este proyecto está bajo la licencia MIT.