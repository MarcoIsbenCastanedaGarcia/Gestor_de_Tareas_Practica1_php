# Proyecto: Sistema Web de Gestión de Tareas

## Descripción
Este proyecto es un sistema web con un CRUD de tareas, inicio de sesión, calendario y sitio web público. 
Desarrollado en HTML, CSS, JavaScript (Frontend) y PHP, MariaDB (Backend).

## Estructura
Sigue la estructura solicitada de carpetas basada en el modelo MVC (apis, conections, controllers, models, docs, frontend...).

## Instalación y Ejecución
1. Importar la base de datos usando MariaDB con el archivo `docs/database.sql`.
2. Asegurar que las credenciales en `.env` coinciden con las de la base de datos (usuario `task_admin`).
3. Abrir la terminal en la carpeta raíz `proyecto`.
4. Ejecutar el servidor PHP embebido:
   ```bash
   php -S localhost:8000
   ```
5. Acceder a `http://localhost:8000` en el navegador.

## Prompt original adjunto
Se incluye el cumplimiento de: Lector TXT, formato JSON, interfaz completa, diseño con variables CSS específicas, base de datos MariaDB, usuario no root, etc.