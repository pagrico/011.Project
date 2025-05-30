<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS'); // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../conexion/db.php'; // Ajusta la ruta según tu estructura

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
    // Usa la conexión existente

    $sql = "SELECT 
                EVE_EVENTO,
                EVE_TITULO,
                EVE_DESCRIPCION,
                EVE_FECHA_EVENTO,
                EVE_CAPACIDAD_MAXIMA,
                EVE_VISIBILIDAD,
                EVE_CIUDAD,
                EVE_PRECIO,
                EVE_CALLE   
            FROM EVENTOS
            ORDER BY EVE_CIUDAD, EVE_FECHA_EVENTO";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Transformar cada registro a un arreglo plano con el nuevo campo "address"
    $resultado = [];
    foreach ($eventos as $evento) {
        $resultado[] = [
            'id' => $evento['EVE_EVENTO'],
            'titulo' => $evento['EVE_TITULO'],
            'descripcion' => $evento['EVE_DESCRIPCION'],
            'fecha_evento' => $evento['EVE_FECHA_EVENTO'],
            'capacidad_maxima' => $evento['EVE_CAPACIDAD_MAXIMA'],
            'visibilidad' => $evento['EVE_VISIBILIDAD'],
            'ciudad' => $evento['EVE_CIUDAD'],
            'precio' => $evento['EVE_PRECIO'],
            'address' => $evento['EVE_CALLE']   // nuevo mapeo
        ];
    }

    echo json_encode($resultado);
// Si no hay eventos, $resultado será un array vacío []

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor']);
}
