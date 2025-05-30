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
    echo json_encode(['success' => false, 'error' => 'Datos de usuario no válidos']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    $conexion->beginTransaction();
    
    // Preparar la consulta base
    $sql = "UPDATE USUARIOS SET 
            USU_NOMBRE = :nombre,
            USU_APELLIDOS = :apellidos,
            USU_EMAIL = :email";
    
    // Agregar campos opcionales si están presentes
    if (isset($data['telefono']) && $data['telefono'] !== '') {
        $sql .= ", USU_TELEFONO = :telefono";
    }
    
    // Agregar campo de rol si está presente y es un administrador quien lo cambia
    if (isset($data['rol']) && is_numeric($data['rol'])) {
        $sql .= ", USU_ROL = :rol";
    }
    
    $sql .= " WHERE USU_USUARIO = :id";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':apellidos', $data['apellidos'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    
    if (isset($data['telefono']) && $data['telefono'] !== '') {
        $stmt->bindParam(':telefono', $data['telefono'], PDO::PARAM_STR);
    }
    
    if (isset($data['rol']) && is_numeric($data['rol'])) {
        $stmt->bindParam(':rol', $data['rol'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    // Si se solicita cambiar la contraseña
    if (isset($data['resetPassword']) && $data['resetPassword'] === true && !empty($data['nuevaPassword'])) {
        $passwordHash = password_hash($data['nuevaPassword'], PASSWORD_DEFAULT);
        
        $sqlPassword = "UPDATE USUARIOS SET USU_PASSWORD = :password WHERE USU_USUARIO = :id";
        $stmtPassword = $conexion->prepare($sqlPassword);
        $stmtPassword->bindParam(':password', $passwordHash, PDO::PARAM_STR);
        $stmtPassword->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmtPassword->execute();
    }
    
    $conexion->commit();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
    
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
?>
