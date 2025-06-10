<?php
// Configuración de cabeceras para permitir peticiones CORS y definir el tipo de contenido
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php';

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
$email = $data['email'] ?? '';
$mensaje = $data['mensaje'] ?? '';
$servicio = $data['servicio'] ?? '';

// Validar que los campos obligatorios estén presentes
if (!$nombre || !$email || !$mensaje) {
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    // Añadir el nombre del servicio al mensaje si está presente
    $mensajeFinal = $servicio ? "[Servicio: $servicio]\n$mensaje" : $mensaje;

    // Insertar la solicitud en la base de datos (sin SOL_FECHA, que se asume por defecto en la tabla)
    $sql = "INSERT INTO SOLICITUDES (SOL_NOMBRE, SOL_CORREO, SOL_MENSAJE)
            VALUES (:nombre, :correo, :mensaje)";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $email);
    $stmt->bindParam(':mensaje', $mensajeFinal);

    // Ejecutar la consulta y responder según el resultado
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar la solicitud']);
    }
} catch (PDOException $e) {
    // Manejo de errores de base de datos
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
