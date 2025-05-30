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

try {
    $input = json_decode(file_get_contents('php://input'), true);

    // Log de entrada para depuración
    file_put_contents('php://stderr', "Datos recibidos: " . print_r($input, true));

    // Validación de campos requeridos, se añade validación para el campo 'price'
    if (
        !isset($input['id']) || !is_numeric($input['id']) ||
        !isset($input['titulo']) || trim($input['titulo']) === '' ||
        !isset($input['fecha_evento']) || trim($input['fecha_evento']) === '' ||
        !isset($input['ciudad']) || trim($input['ciudad']) === '' ||
        !isset($input['price']) || !is_numeric($input['price'])
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos requeridos o el precio es inválido']);
        exit;
    }

    // Validar formato de fecha
    $fechaEvento = DateTime::createFromFormat('Y-m-d H:i:s', $input['fecha_evento']);
    if (!$fechaEvento) {
        error_log("Fecha evento parse error: " . $input['fecha_evento']); // Debug: registrar valor recibido
        http_response_code(400);
        echo json_encode(['error' => 'Formato de fecha inválido para EVE_FECHA_EVENTO: ' . $input['fecha_evento']]);
        exit;
    }

    // Validar visibilidad
    $visibilidad = isset($input['visibilidad']) && in_array($input['visibilidad'], ['Público', 'Privado'])
        ? $input['visibilidad']
        : 'Público';

    // Campos opcionales
    $descripcion = isset($input['descripcion']) && $input['descripcion'] !== '' ? $input['descripcion'] : null;
    $address = isset($input['address']) && $input['address'] !== '' ? $input['address'] : null;
    $capacidad_maxima = isset($input['capacidad_maxima']) && $input['capacidad_maxima'] !== '' ? (int)$input['capacidad_maxima'] : null;

    // Consulta SQL modificada para incluir el campo precio
    $sql = "UPDATE EVENTOS SET
                EVE_TITULO = :titulo,
                EVE_DESCRIPCION = :descripcion,
                EVE_FECHA_EVENTO = :fecha_evento,
                EVE_CIUDAD = :ciudad,
                EVE_PRECIO = :precio,
                EVE_CALLE = :direccion, 
                EVE_CAPACIDAD_MAXIMA = :capacidad_maxima,
                EVE_VISIBILIDAD = :visibilidad
            WHERE EVE_EVENTO = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':titulo', $input['titulo']);
    $stmt->bindValue(':descripcion', $descripcion, $descripcion === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':fecha_evento', $input['fecha_evento']);
    $stmt->bindParam(':ciudad', $input['ciudad']);
    $stmt->bindParam(':precio', $input['price']);
    // Se cambia la vinculación a :direccion
    $stmt->bindValue(':direccion', isset($input['address']) && $input['address'] !== '' ? $input['address'] : null, isset($input['address']) && $input['address'] !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':capacidad_maxima', $capacidad_maxima, $capacidad_maxima === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindParam(':visibilidad', $visibilidad);
    $stmt->bindParam(':id', $input['id'], PDO::PARAM_INT);

    $stmt->execute();

    // Verifica si se modificó alguna fila
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'No se modificó ningún evento (ID no encontrado o sin cambios)']);
    } else {
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    // Log del error para depuración
    file_put_contents('php://stderr', "Error en el servidor: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'detalle' => $e->getMessage()
    ]);
}
