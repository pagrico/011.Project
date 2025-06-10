<?php
// Configuración de cabeceras para permitir peticiones CORS y definir el tipo de contenido
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php'; // Usar la conexión PDO $conexion

// Manejo de preflight (peticiones OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir el método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

// Recoger y decodificar los datos enviados en el cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

// Obtener los campos del formulario, usando valores por defecto si no existen
$nombre = $data['nombre'] ?? '';
$valoracion = $data['valoracion'] ?? '';
$estrellas = $data['estrellas'] ?? 0;
$servicio = $data['servicio'] ?? 0;

// Validar que los campos obligatorios estén presentes
if (!$nombre || !$valoracion || !$estrellas || !$servicio) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios']);
    exit;
}

try {
    // Preparar la consulta SQL para insertar la valoración
    $sql = "INSERT INTO VALORACIONES (VAL_NOMBRE_USUARIO, VAL_VALORACION, VAL_ESTRELLAS, VAL_SERVICIO)
            VALUES (:nombre, :valoracion, :estrellas, :servicio)";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':valoracion', $valoracion);
    $stmt->bindParam(':estrellas', $estrellas);
    $stmt->bindParam(':servicio', $servicio);

    // Ejecutar la consulta y responder según el resultado
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conexion->lastInsertId()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo insertar la valoración']);
    }
} catch (PDOException $e) {
    // Manejo de errores de base de datos
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
