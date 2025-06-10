<?php
// Configuración de cabeceras para permitir peticiones CORS y definir el tipo de contenido
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // O DELETE, OPTIONS
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

// Manejo de preflight (peticiones OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir el método POST (ajusta a DELETE si lo necesitas)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Cambiar a 'DELETE' si se usa el método DELETE
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']); // Ajustar mensaje si se usa DELETE
    exit;
}

try {
    // Recoger y decodificar los datos enviados en el cuerpo de la petición
    $input = json_decode(file_get_contents('php://input'), true);

    // Validación básica del ID del evento
    if (!isset($input['id']) || !is_numeric($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID del evento no válido']);
        exit;
    }

    // Preparar y ejecutar la consulta para eliminar el evento
    $sql = "DELETE FROM EVENTOS WHERE EVE_EVENTO = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $input['id'], PDO::PARAM_INT);
    $stmt->execute();

    // Comprobar si se eliminó algún registro
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'No se encontró el evento para eliminar']);
    } else {
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    // Manejo de errores generales
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'detalle' => $e->getMessage()
    ]);
}
