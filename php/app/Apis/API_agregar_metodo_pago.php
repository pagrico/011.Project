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

// Validar que se reciban los datos necesarios
if (!$data || !isset($data['userId']) || !isset($data['tipo']) || !isset($data['titular'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Validar tipo de método de pago permitido
$tiposPermitidos = ['TARJETA', 'PAYPAL', 'TRANSFERENCIA', 'OTRO'];
if (!in_array($data['tipo'], $tiposPermitidos)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo de método de pago no válido']);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

try {
    $conexion->beginTransaction();
    
    // Formatear la fecha de expiración si está presente
    if (isset($data['fechaExp']) && $data['fechaExp'] !== '') {
        // Convertir '2025-08' a '2025-08-01' (primer día del mes)
        if (preg_match('/^\d{4}-\d{2}$/', $data['fechaExp'])) {
            $data['fechaExp'] = $data['fechaExp'] . '-01';
        }
    }
    
    // Preparar la consulta base para insertar el método de pago
    $sql = "INSERT INTO METODOS_PAGO 
            (MDP_USUARIO, MDP_TIPO, MDP_TITULAR, MDP_ACTIVO";
    
    // Añadir campos opcionales a la consulta si están presentes
    if (isset($data['numero']) && $data['numero'] !== '') {
        $sql .= ", MDP_NUMERO";
    }
    
    if (isset($data['fechaExp']) && $data['fechaExp'] !== '') {
        $sql .= ", MDP_FECHAEXP";
    }
    
    $sql .= ") VALUES (:userId, :tipo, :titular, :activo";
    
    // Añadir valores para campos opcionales
    if (isset($data['numero']) && $data['numero'] !== '') {
        $sql .= ", :numero";
    }
    
    if (isset($data['fechaExp']) && $data['fechaExp'] !== '') {
        $sql .= ", :fechaExp";
    }
    
    $sql .= ")";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    $stmt->bindParam(':tipo', $data['tipo'], PDO::PARAM_STR);
    $stmt->bindParam(':titular', $data['titular'], PDO::PARAM_STR);
    
    // El estado activo por defecto es true si no se especifica lo contrario
    $activo = isset($data['activo']) ? (bool)$data['activo'] : true;
    $activoInt = $activo ? 1 : 0;
    $stmt->bindParam(':activo', $activoInt, PDO::PARAM_INT);
    
    // Bind de parámetros opcionales
    if (isset($data['numero']) && $data['numero'] !== '') {
        $stmt->bindParam(':numero', $data['numero'], PDO::PARAM_STR);
    }
    
    if (isset($data['fechaExp']) && $data['fechaExp'] !== '') {
        $stmt->bindParam(':fechaExp', $data['fechaExp'], PDO::PARAM_STR);
    }
    
    $stmt->execute();
    
    $conexion->commit();
    
    http_response_code(201); // Created
    echo json_encode(['success' => true, 'message' => 'Método de pago agregado correctamente', 'id' => $conexion->lastInsertId()]);
    
} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
