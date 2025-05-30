<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // Permitir solicitudes desde este origen
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Métodos permitidos (GET no se usa aquí)
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Encabezados permitidos
require_once __DIR__ . '/../conexion/db.php';

// Manejo de preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$estado = $data['estado'] ?? null;

if (!$id || !$estado) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

try {
    $sql = "UPDATE SOLICITUDES SET SOL_ESTADO = :estado WHERE SOL_ID = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
