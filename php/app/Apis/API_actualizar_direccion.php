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

if (!$data || !isset($data['userId']) || !is_numeric($data['userId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos o inválidos']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    $conexion->beginTransaction();
    
    // Verificar si el usuario ya tiene una dirección asociada
    $sqlCheck = "SELECT DIR_DIRECCION FROM DIRECCIONES WHERE DIR_USUARIO = :userId";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    $stmtCheck->execute();
    
    $direccionId = $stmtCheck->fetchColumn();
    
    if ($direccionId) {
        // Actualizar dirección existente
        $sqlUpdate = "UPDATE DIRECCIONES SET 
                     DIR_CP = :cp,
                     DIR_CIUDAD = :ciudad,
                     DIR_PROVINCIA = :provincia,
                     DIR_CALLE = :calle,
                     DIR_PISO = :piso,
                     DIR_PUERTA = :puerta
                     WHERE DIR_DIRECCION = :direccionId";
        
        $stmtUpdate = $conexion->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':cp', $data['cp'], PDO::PARAM_STR);
        $stmtUpdate->bindParam(':ciudad', $data['ciudad'], PDO::PARAM_STR);
        $stmtUpdate->bindParam(':provincia', $data['provincia'], PDO::PARAM_STR);
        $stmtUpdate->bindParam(':calle', $data['calle'], PDO::PARAM_STR);
        $stmtUpdate->bindParam(':piso', $data['piso'], PDO::PARAM_STR);
        $stmtUpdate->bindParam(':puerta', $data['numero'], PDO::PARAM_STR); // El 'numero' del frontend es realmente la puerta
        $stmtUpdate->bindParam(':direccionId', $direccionId, PDO::PARAM_INT);
        $stmtUpdate->execute();
    } else {
        // Crear nueva dirección con el DIR_USUARIO directamente
        $sqlInsert = "INSERT INTO DIRECCIONES 
                     (DIR_USUARIO, DIR_CP, DIR_CIUDAD, DIR_PROVINCIA, DIR_CALLE, DIR_PISO, DIR_PUERTA) 
                     VALUES 
                     (:userId, :cp, :ciudad, :provincia, :calle, :piso, :puerta)";
        
        $stmtInsert = $conexion->prepare($sqlInsert);
        $stmtInsert->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
        $stmtInsert->bindParam(':cp', $data['cp'], PDO::PARAM_STR);
        $stmtInsert->bindParam(':ciudad', $data['ciudad'], PDO::PARAM_STR);
        $stmtInsert->bindParam(':provincia', $data['provincia'], PDO::PARAM_STR);
        $stmtInsert->bindParam(':calle', $data['calle'], PDO::PARAM_STR);
        $stmtInsert->bindParam(':piso', $data['piso'], PDO::PARAM_STR);
        $stmtInsert->bindParam(':puerta', $data['numero'], PDO::PARAM_STR); // El 'numero' del frontend es realmente la puerta
        $stmtInsert->execute();
    }
    
    $conexion->commit();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Dirección actualizada correctamente']);
    
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
