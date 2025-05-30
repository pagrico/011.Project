<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';
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

// Verificar que se proporcione un userId
if (!isset($_GET['userId']) || !is_numeric($_GET['userId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de usuario no proporcionado o no válido']);
    exit;
}

$userId = (int)$_GET['userId'];



try {
    // En esta consulta, primero obtenemos el DIR_DIRECCION asociado al usuario
    // y luego utilizamos ese valor para consultar la tabla DIRECCIONES
    $sql = "SELECT d.* 
            FROM DIRECCIONES d
            WHERE d.DIR_USUARIO = :userId";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $direccion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Consulta alternativa si la primera no devuelve resultados
    // Es posible que hayamos actualizado la estructura y ahora DIR_USUARIO está en la tabla DIRECCIONES
    if (!$direccion) {
        $sqlAlternativo = "SELECT * FROM DIRECCIONES WHERE DIR_USUARIO = :userId";
        $stmtAlternativo = $conexion->prepare($sqlAlternativo);
        $stmtAlternativo->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtAlternativo->execute();
        $direccion = $stmtAlternativo->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($direccion) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'direccion' => [
                'id' => $direccion['DIR_DIRECCION'],
                'cp' => $direccion['DIR_CP'],
                'ciudad' => $direccion['DIR_CIUDAD'],
                'provincia' => $direccion['DIR_PROVINCIA'],
                'calle' => $direccion['DIR_CALLE'],
                'puerta' => $direccion['DIR_PUERTA'],
                'piso' => $direccion['DIR_PISO']
            ]
        ]);
    } else {
        http_response_code(200); // 200 porque no es un error, simplemente no hay dirección
        echo json_encode([
            'success' => true,
            'direccion' => null
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
