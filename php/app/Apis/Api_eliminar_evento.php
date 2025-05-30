<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // O DELETE, OPTIONS
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

try {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validación básica
    if (!isset($input['id']) || !is_numeric($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID del evento no válido']);
        exit;
    }

    $sql = "DELETE FROM EVENTOS WHERE EVE_EVENTO = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $input['id'], PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'No se encontró el evento para eliminar']);
    } else {
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'detalle' => $e->getMessage()
    ]);
}
