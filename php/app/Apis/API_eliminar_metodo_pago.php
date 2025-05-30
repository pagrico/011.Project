<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Manejo de preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Log para depuración
error_log("Recibiendo solicitud DELETE para método de pago");

// Recibir datos del cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

// Log de datos recibidos
error_log("Datos recibidos: " . print_r($data, true));

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de método de pago no proporcionado']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    $conexion->beginTransaction();
    
    // Preparar la consulta
    $sql = "DELETE FROM METODOS_PAGO WHERE MDP_PAGO = :id";
    
    // Si se especifica un userId, añadir la cláusula de seguridad
    if (isset($data['userId'])) {
        $sql .= " AND MDP_USUARIO = :userId";
    }
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    
    // Si se especifica un userId, hacer el bind
    if (isset($data['userId'])) {
        $stmt->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $conexion->commit();
        error_log("Método de pago eliminado correctamente: ID " . $data['id']);
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Método de pago eliminado correctamente']);
    } else {
        $conexion->rollBack();
        error_log("No se encontró el método de pago o no se tiene permiso: ID " . $data['id']);
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Método de pago no encontrado o no tienes permiso para eliminarlo']);
    }
    
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    error_log("Error al eliminar método de pago: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
