<?php
function getConnection() {
    // Busca el archivo .env en la raíz del proyecto
    $env_path = dirname(__DIR__, 2) . '/.env';

    if (!file_exists($env_path)) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Error: No se encontró el archivo .env en " . $env_path]);
        exit;
    }

    $env_vars = [];
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            // Limpia espacios, comillas y caracteres invisibles como \r y \n
            $env_vars[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
        }
    }

    // Carga de credenciales desde el archivo .env sin valores hardcodeados en el código
    $host = $env_vars['DB_HOST'] ?? null;
    $port = $env_vars['DB_PORT'] ?? '3307';
    $db   = $env_vars['DB_NAME'] ?? null;
    $user = $env_vars['DB_USER'] ?? null;
    $pass = $env_vars['DB_PASS'] ?? null;

    if (!$host || !$db || !$user || $pass === null) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Error: Faltan credenciales requeridas en el archivo .env"]);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false, 
            "message" => "Error de conexión: " . $e->getMessage()
        ]);
        exit;
    }
}
?>