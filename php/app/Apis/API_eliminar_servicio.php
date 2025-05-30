<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // O DELETE, OPTIONS si se usa el método DELETE
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once '../conexion/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Cambiar a 'DELETE' si se usa el método DELETE
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']); // Ajustar mensaje si se usa DELETE
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID requerido']);
    exit;
}

try {
    $sql = "DELETE FROM SERVICIOS WHERE SER_ID = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el servicio']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
