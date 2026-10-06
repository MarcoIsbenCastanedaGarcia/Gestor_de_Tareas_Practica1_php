<?php
session_start();
require_once '../conections/conection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$pdo = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM Tareas WHERE id_usuarios = ?");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} elseif ($method === 'POST') {
    // Import / Create
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['action']) && $data['action'] === 'import') {
        foreach($data['tasks'] as $t) {
            $stmt = $pdo->prepare("INSERT INTO Tareas (NombreTarea, descripcion, Materia, Fecha, id_usuarios) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$t['NombreTarea'], $t['descripcion'], $t['Materia'], $t['Fecha'], $user_id]);
        }
        echo json_encode(["success" => true]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO Tareas (NombreTarea, descripcion, Materia, Fecha, id_usuarios) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['NombreTarea'], $data['descripcion'], $data['Materia'], $data['Fecha'], $user_id]);
        echo json_encode(["success" => true]);
    }
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("DELETE FROM Tareas WHERE id = ? AND id_usuarios = ?");
    $stmt->execute([$data['id'], $user_id]);
    echo json_encode(["success" => true]);
}
?>