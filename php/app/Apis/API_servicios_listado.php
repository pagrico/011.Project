<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // Permitir solicitudes desde este origen
header('Access-Control-Allow-Methods: GET, OPTIONS'); // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Encabezados permitidos
require_once __DIR__ . '/../conexion/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta GET.']);
    exit;
}

try {
    // Usar la conexión existente
    // Obtener todos los servicios
    $stmt = $conexion->query("SELECT * FROM SERVICIOS");
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todas las valoraciones (ahora incluyen estrellas y servicio)
    $stmt2 = $conexion->query("SELECT * FROM VALORACIONES");
    $valoraciones = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Indexar valoraciones por VAL_SERVICIO
    $valoracionesByServicio = [];
    foreach ($valoraciones as $val) {
        $valoracionesByServicio[$val['VAL_SERVICIO']][] = $val;
    }

    // Añadir valoraciones a cada servicio según su SER_ID
    foreach ($servicios as &$servicio) {
        $servicioId = $servicio['SER_ID'];
        if (isset($valoracionesByServicio[$servicioId])) {
            $servicio['valoraciones'] = $valoracionesByServicio[$servicioId];
        } else {
            $servicio['valoraciones'] = [];
        }
        // Procesar imágenes: decodifica el JSON de SER_IMAGENES
        if (isset($servicio['SER_IMAGENES']) && is_string($servicio['SER_IMAGENES']) && strlen($servicio['SER_IMAGENES'])) {
            $imgs = json_decode($servicio['SER_IMAGENES'], true);
            $servicio['imagenes'] = is_array($imgs) ? $imgs : [];
        } else {
            $servicio['imagenes'] = [];
        }
    }

    echo json_encode([
        'servicios' => $servicios,
        'valoraciones' => $valoraciones
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
