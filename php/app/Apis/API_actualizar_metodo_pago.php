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
if (!$data || !isset($data['id']) || !isset($data['tipo']) || !isset($data['titular'])) {
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
    
    // Preparar la consulta base para actualizar el método de pago
    $sql = "UPDATE METODOS_PAGO SET 
            MDP_TIPO = :tipo,
            MDP_TITULAR = :titular,
            MDP_ACTIVO = :activo";
    
    // Agregar campos opcionales si están presentes
    if (isset($data['numero'])) {
        $sql .= ", MDP_NUMERO = :numero";
    }
    
    if (isset($data['fechaExp'])) {
        $sql .= ", MDP_FECHAEXP = :fechaExp";
    }
    
    $sql .= " WHERE MDP_PAGO = :id";
    
    // Si se especifica un userId, añadir la cláusula de seguridad
    if (isset($data['userId'])) {
        $sql .= " AND MDP_USUARIO = :userId";
    }
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':tipo', $data['tipo'], PDO::PARAM_STR);
    $stmt->bindParam(':titular', $data['titular'], PDO::PARAM_STR);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    
    // El estado activo por defecto es true si no se especifica lo contrario
    $activo = isset($data['activo']) ? (bool)$data['activo'] : true;
    $activoInt = $activo ? 1 : 0;
    $stmt->bindParam(':activo', $activoInt, PDO::PARAM_INT);
    
    // Bind de parámetros opcionales
    if (isset($data['numero'])) {
        $stmt->bindParam(':numero', $data['numero'], PDO::PARAM_STR);
    }
    
    if (isset($data['fechaExp'])) {
        $stmt->bindParam(':fechaExp', $data['fechaExp'], PDO::PARAM_STR);
    }
    
    // Si se especifica un userId, hacer el bind
    if (isset($data['userId'])) {
        $stmt->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    $conexion->commit();
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Método de pago actualizado correctamente']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Método de pago no encontrado o no tienes permiso para modificarlo']);
    }
    
} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
