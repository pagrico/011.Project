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

// Validar que se reciban los datos necesarios y que el userId sea numérico
if (!$data || !isset($data['userId']) || !is_numeric($data['userId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos o inválidos']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    // Iniciar transacción para asegurar atomicidad
    $conexion->beginTransaction();
    
    // Verificar si el usuario ya tiene una dirección asociada
    $sqlCheck = "SELECT DIR_DIRECCION FROM DIRECCIONES WHERE DIR_USUARIO = :userId";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    $stmtCheck->execute();
    
    $direccionId = $stmtCheck->fetchColumn();
    
    if ($direccionId) {
        // Si existe, actualizar la dirección existente
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
        // El campo 'numero' del frontend corresponde a 'puerta' en la base de datos
        $stmtUpdate->bindParam(':puerta', $data['numero'], PDO::PARAM_STR);
        $stmtUpdate->bindParam(':direccionId', $direccionId, PDO::PARAM_INT);
        $stmtUpdate->execute();
    } else {
        // Si no existe, crear una nueva dirección asociada al usuario
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
        // El campo 'numero' del frontend corresponde a 'puerta' en la base de datos
        $stmtInsert->bindParam(':puerta', $data['numero'], PDO::PARAM_STR);
        $stmtInsert->execute();
    }
    
    // Confirmar la transacción si todo fue correcto
    $conexion->commit();
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Dirección actualizada correctamente']);
    
} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
