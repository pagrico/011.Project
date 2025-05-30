<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once '../conexion/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

// Recoger datos del POST
$data = json_decode(file_get_contents('php://input'), true);

$nombre = $data['nombre'] ?? '';
$email = $data['email'] ?? '';
$mensaje = $data['mensaje'] ?? '';
$servicio = $data['servicio'] ?? '';

if (!$nombre || !$email || !$mensaje) {
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    // Añadir el nombre del servicio al mensaje si está presente
    $mensajeFinal = $servicio ? "[Servicio: $servicio]\n$mensaje" : $mensaje;

    // Quitar SOL_FECHA de la consulta y del bindParam
    $sql = "INSERT INTO SOLICITUDES (SOL_NOMBRE, SOL_CORREO, SOL_MENSAJE)
            VALUES (:nombre, :correo, :mensaje)";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $email);
    $stmt->bindParam(':mensaje', $mensajeFinal);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar la solicitud']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
