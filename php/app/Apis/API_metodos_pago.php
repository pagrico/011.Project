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

// Verificar que se proporcione un userId
if (!isset($_GET['userId']) || !is_numeric($_GET['userId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de usuario no proporcionado o no válido']);
    exit;
}

$userId = (int)$_GET['userId'];

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    // Log para depuración
    error_log("Buscando métodos de pago para userId: " . $userId);
    
    // Construir la consulta SQL
    $sql = "SELECT MDP_PAGO, MDP_TIPO, MDP_NUMERO, MDP_FECHAEXP, MDP_TITULAR, 
            MDP_ACTIVO, MDP_FECHA_REGISTRO
            FROM METODOS_PAGO
            WHERE MDP_USUARIO = :userId
            ORDER BY MDP_FECHA_REGISTRO DESC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $metodosPago = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log de la cantidad de registros encontrados
    error_log("Métodos de pago encontrados: " . count($metodosPago));
    
    // Si no hay resultados, verificamos que la tabla y columnas existen
    if (empty($metodosPago)) {
        $checkTableSql = "SHOW TABLES LIKE 'METODOS_PAGO'";
        $checkTableStmt = $conexion->prepare($checkTableSql);
        $checkTableStmt->execute();
        $tableExists = $checkTableStmt->rowCount() > 0;
        
        if (!$tableExists) {
            error_log("La tabla METODOS_PAGO no existe en la base de datos");
        } else {
            // Verificar si hay registros para cualquier usuario
            $countSql = "SELECT COUNT(*) FROM METODOS_PAGO";
            $countStmt = $conexion->prepare($countSql);
            $countStmt->execute();
            $totalRecords = $countStmt->fetchColumn();
            error_log("Total de registros en METODOS_PAGO: " . $totalRecords);
        }
    }
    
    http_response_code(200);
    echo json_encode(['success' => true, 'metodosPago' => $metodosPago]);
    
} catch (PDOException $e) {
    error_log("Error en API_metodos_pago.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
