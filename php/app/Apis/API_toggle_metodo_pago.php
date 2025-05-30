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

// Log para depuración
error_log("Datos recibidos: " . print_r($data, true));

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de método de pago no proporcionado']);
    exit;
}

// Convertir el estado a entero para evitar problemas con valores booleanos
$activoInt = isset($data['activo']) ? (int)$data['activo'] : null;
if ($activoInt !== 0 && $activoInt !== 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El valor de activo debe ser 0 o 1']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    $conexion->beginTransaction();

    // Si se activa un método, desactivar todos los demás del mismo tipo
    if ($activoInt == 1) {
        // Primero, obtener información sobre el método de pago
        $sqlInfo = "SELECT MDP_TIPO, MDP_USUARIO FROM METODOS_PAGO WHERE MDP_PAGO = :id";
        $stmtInfo = $conexion->prepare($sqlInfo);
        $stmtInfo->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmtInfo->execute();
        
        $metodoPago = $stmtInfo->fetch(PDO::FETCH_ASSOC);
        
        if ($metodoPago) {
            // Desactivar todos los otros métodos del mismo tipo para este usuario
            $sqlDesactivar = "UPDATE METODOS_PAGO 
                           SET MDP_ACTIVO = 0 
                           WHERE MDP_USUARIO = :userId 
                           AND MDP_TIPO = :tipo 
                           AND MDP_PAGO <> :id";
            
            $stmtDesactivar = $conexion->prepare($sqlDesactivar);
            $stmtDesactivar->bindParam(':userId', $metodoPago['MDP_USUARIO'], PDO::PARAM_INT);
            $stmtDesactivar->bindParam(':tipo', $metodoPago['MDP_TIPO'], PDO::PARAM_STR);
            $stmtDesactivar->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $stmtDesactivar->execute();
        }
    }
    
    // Actualizar el estado del método de pago seleccionado
    $sql = "UPDATE METODOS_PAGO SET MDP_ACTIVO = :activo WHERE MDP_PAGO = :id";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':activo', $activoInt, PDO::PARAM_INT);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $conexion->commit();
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Estado del método de pago actualizado correctamente']);
    } else {
        // Si no hay filas afectadas, verificar si el método existe
        $checkSql = "SELECT COUNT(*) FROM METODOS_PAGO WHERE MDP_PAGO = :id";
        $checkStmt = $conexion->prepare($checkSql);
        $checkStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() > 0) {
            // El método existe, pero no se actualizó (puede que ya tuviera el mismo estado)
            $conexion->commit();
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'No hubo cambios en el estado del método de pago']);
        } else {
            // El método no existe
            $conexion->rollBack();
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Método de pago no encontrado']);
        }
    }
    
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
