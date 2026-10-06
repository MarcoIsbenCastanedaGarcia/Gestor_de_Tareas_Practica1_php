<?php
session_start();
require_once '../conections/conection.php';

header('Content-Type: application/json');

// Capturar datos enviados como JSON o FormData
$input = json_decode(file_get_contents('php://input'), true) ?? [];

$action = $_POST['action'] ?? $input['action'] ?? '';
$nombre = $_POST['usuario'] ?? $_POST['Nombre'] ?? $input['usuario'] ?? $input['Nombre'] ?? '';
$pass   = $_POST['contrasena'] ?? $_POST['Contrasena'] ?? $input['contrasena'] ?? $input['Contrasena'] ?? '';

if ($action === 'login') {
    if (empty($nombre) || empty($pass)) {
        echo json_encode(["success" => false, "message" => "Por favor completa usuario y contraseña."]);
        exit;
    }

    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, Contrasena FROM Usuarios WHERE Nombre = ?");
    $stmt->execute([$nombre]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && (password_verify($pass, $user['Contrasena']) || $pass === $user['Contrasena'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(["success" => true, "message" => "Inicio de sesión exitoso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Datos incorrectos o usuario no existe."]);
    }

} elseif ($action === 'register') {
    if (empty($nombre) || empty($pass)) {
        echo json_encode(["success" => false, "message" => "Por favor completa todos los campos."]);
        exit;
    }

    $pdo = getConnection();
    $hashPass = password_hash($pass, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO Usuarios (Nombre, Contrasena) VALUES (?, ?)");
    try {
        $stmt->execute([$nombre, $hashPass]);
        echo json_encode(["success" => true, "message" => "Usuario registrado correctamente."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error al registrar (el usuario ya podría existir)."]);
    }

} elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(["success" => true, "message" => "Sesión cerrada."]);
} else {
    echo json_encode(["success" => false, "message" => "Acción no válida."]);
}
?>