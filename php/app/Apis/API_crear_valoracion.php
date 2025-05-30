<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once '../conexion/db.php'; // Usar la conexión PDO $conexion

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
$valoracion = $data['valoracion'] ?? '';
$estrellas = $data['estrellas'] ?? 0;
$servicio = $data['servicio'] ?? 0;

if (!$nombre || !$valoracion || !$estrellas || !$servicio) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios']);
    exit;
}

try {
    $sql = "INSERT INTO VALORACIONES (VAL_NOMBRE_USUARIO, VAL_VALORACION, VAL_ESTRELLAS, VAL_SERVICIO)
            VALUES (:nombre, :valoracion, :estrellas, :servicio)";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':valoracion', $valoracion);
    $stmt->bindParam(':estrellas', $estrellas);
    $stmt->bindParam(':servicio', $servicio);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conexion->lastInsertId()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo insertar la valoración']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
