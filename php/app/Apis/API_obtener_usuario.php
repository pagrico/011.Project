<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Manejo de preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Verificar que se proporcione un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de usuario no proporcionado o no válido']);
    exit;
}

$userId = (int)$_GET['id'];

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    // Obtener datos del usuario
    $sql = "SELECT USU_USUARIO, USU_LOGIN, USU_NOMBRE, USU_APELLIDOS, USU_EMAIL, 
            USU_TELEFONO, USU_ROL, USU_FECHAREGISTRO
            FROM USUARIOS
            WHERE USU_USUARIO = :id";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Obtener dirección del usuario
    $sqlDireccion = "SELECT DIR_DIRECCION, DIR_CP, DIR_CIUDAD, DIR_PROVINCIA, DIR_CALLE, DIR_PUERTA, DIR_PISO
                     FROM DIRECCIONES
                     WHERE DIR_USUARIO = :id";
    
    $stmtDireccion = $conexion->prepare($sqlDireccion);
    $stmtDireccion->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmtDireccion->execute();
    
    $direccion = $stmtDireccion->fetch(PDO::FETCH_ASSOC);
    
    // Preparar respuesta
    $respuesta = [
        'success' => true,
        'usuario' => $usuario
    ];
    
    if ($direccion) {
        $respuesta['direccion'] = $direccion;
    }
    
    http_response_code(200);
    echo json_encode($respuesta);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
