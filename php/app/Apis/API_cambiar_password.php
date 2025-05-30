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

if (!$data || !isset($data['userId']) || !isset($data['passwordActual']) || !isset($data['passwordNueva'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    // Verificar la contraseña actual
    $sql = "SELECT USU_PASSWORD FROM USUARIOS WHERE USU_USUARIO = :userId";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar si la contraseña actual es correcta
    if (!password_verify($data['passwordActual'], $usuario['USU_PASSWORD'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Contraseña actual incorrecta']);
        exit;
    }
    
    // Actualizar la contraseña
    $passwordHash = password_hash($data['passwordNueva'], PASSWORD_DEFAULT);
    
    $sqlUpdate = "UPDATE USUARIOS SET USU_PASSWORD = :password WHERE USU_USUARIO = :userId";
    $stmtUpdate = $conexion->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':password', $passwordHash, PDO::PARAM_STR);
    $stmtUpdate->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    $stmtUpdate->execute();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
