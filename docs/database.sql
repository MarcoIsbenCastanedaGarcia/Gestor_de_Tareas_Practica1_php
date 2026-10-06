-- Script para la creación de la base de datos (MariaDB)
CREATE DATABASE IF NOT EXISTS task_manager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Creación de usuario exclusivo para el proyecto (NO root)
CREATE USER IF NOT EXISTS 'task_admin'@'localhost' IDENTIFIED BY 'Admin123!';
GRANT ALL PRIVILEGES ON task_manager.* TO 'task_admin'@'localhost';
FLUSH PRIVILEGES;

USE task_manager;

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL
);

-- Tabla de Tareas
CREATE TABLE IF NOT EXISTS Tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    NombreTarea VARCHAR(150) NOT NULL,
    descripcion TEXT,
    Materia VARCHAR(100),
    Fecha DATE,
    id_usuarios INT,
    FOREIGN KEY (id_usuarios) REFERENCES Usuarios(id) ON DELETE CASCADE
);

-- Insertar usuario preestablecido (Contraseña almacenada en texto plano para simplificar el prototipo)
INSERT INTO Usuarios (Nombre, Contrasena) VALUES ('Juan_Perez', '12345');