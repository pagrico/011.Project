<?php
// Mostrar errores para depuración (no recomendado en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de cabeceras CORS para permitir peticiones desde cualquier origen
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Manejo de preflight (peticiones OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir el método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Recibir y decodificar los datos enviados en el cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

// Validar que se reciban los datos necesarios y que sean numéricos
if (!$data || !isset($data['id']) || !isset($data['rol']) || !is_numeric($data['id']) || !is_numeric($data['rol'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos o no válidos']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    // Verificar que el rol especificado exista en la tabla de roles
    $sqlCheckRol = "SELECT COUNT(*) FROM ROLES WHERE ROL_ROL = :rol";
    $stmtCheckRol = $conexion->prepare($sqlCheckRol);
    $stmtCheckRol->bindParam(':rol', $data['rol'], PDO::PARAM_INT);
    $stmtCheckRol->execute();
    
    if ($stmtCheckRol->fetchColumn() == 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'El rol especificado no existe']);
        exit;
    }
    
    // Actualizar el rol del usuario en la base de datos
    $sql = "UPDATE USUARIOS SET USU_ROL = :rol WHERE USU_USUARIO = :id";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':rol', $data['rol'], PDO::PARAM_INT);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Rol de usuario actualizado correctamente']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    }
    
} catch (PDOException $e) {
    // Manejo de errores de base de datos
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
