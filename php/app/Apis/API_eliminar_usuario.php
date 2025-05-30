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

// Recibir datos del cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id']) || !is_numeric($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de usuario no proporcionado o no válido']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    $conexion->beginTransaction();
    
    // Verificar que el usuario no sea el administrador principal (ID 1)
    if ($data['id'] == 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No se puede eliminar el administrador principal']);
        exit;
    }
    
    // Eliminar usuario
    $sql = "DELETE FROM USUARIOS WHERE USU_USUARIO = :id";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $conexion->commit();
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    } else {
        $conexion->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    }
    
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
