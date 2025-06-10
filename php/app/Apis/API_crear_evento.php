<?php
// Configuración de cabeceras para permitir peticiones CORS y definir el tipo de contenido
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Incluir archivo de conexión a la base de datos
require_once '../conexion/db.php'; // Ajusta la ruta según tu estructura

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

try {
    // Recoge los datos del cuerpo de la petición y los decodifica
    $input = json_decode(file_get_contents('php://input'), true);

    // Validación básica de campos requeridos
    if (
        !isset($input['titulo']) || trim($input['titulo']) === '' ||
        !isset($input['fecha_evento']) || trim($input['fecha_evento']) === '' ||
        !isset($input['ciudad']) || trim($input['ciudad']) === '' ||
        !isset($input['price'])  // nuevo campo obligatorio
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos requeridos']);
        exit;
    }

    // Obtener campos opcionales o asignar null/valor por defecto
    $descripcion = isset($input['descripcion']) ? $input['descripcion'] : null;
    $calle = isset($input['calle']) ? $input['calle'] : null;
    $capacidad_maxima = isset($input['capacidad_maxima']) ? $input['capacidad_maxima'] : null;
    $visibilidad = isset($input['visibilidad']) ? $input['visibilidad'] : 'Público';
    $precio = $input['price']; // obtener el valor del campo precio

    // Preparar la consulta SQL para insertar el evento
    $sql = "INSERT INTO EVENTOS (
                EVE_TITULO,
                EVE_DESCRIPCION,
                EVE_FECHA_EVENTO,
                EVE_CIUDAD,
                EVE_CALLE,
                EVE_CAPACIDAD_MAXIMA,
                EVE_VISIBILIDAD,
                EVE_PRECIO
            ) VALUES (
                :titulo,
                :descripcion,
                :fecha_evento,
                :ciudad,
                :calle,
                :capacidad_maxima,
                :visibilidad,
                :precio
            )";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':titulo', $input['titulo']);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha_evento', $input['fecha_evento']);
    $stmt->bindParam(':ciudad', $input['ciudad']);
    $stmt->bindParam(':calle', $calle);
    $stmt->bindParam(':capacidad_maxima', $capacidad_maxima);
    $stmt->bindParam(':visibilidad', $visibilidad);
    $stmt->bindParam(':precio', $precio); // enlazar el campo precio

    // Ejecutar la consulta y obtener el ID del evento insertado
    $stmt->execute();
    $evento_id = $conexion->lastInsertId();

    // Responder con éxito y el ID del evento creado
    echo json_encode(['success' => true, 'evento_id' => $evento_id]);

} catch (Exception $e) {
    // Manejo de errores generales
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'detalle' => $e->getMessage()
    ]);
}
