<?php
// Establece el tipo de contenido que se devuelve: JSON
header('Content-Type: application/json');

// Permite solicitudes CORS desde cualquier origen
header('Access-Control-Allow-Origin: *');

// Permite los métodos POST y OPTIONS (útil para preflight)
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Si planeas usar DELETE, cámbialo por: DELETE, OPTIONS

// Permite ciertas cabeceras específicas necesarias para peticiones CORS seguras
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Incluye el archivo de conexión a la base de datos
require_once '../conexion/db.php';

// Manejo del preflight request que envían los navegadores automáticamente con OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit; // Finaliza la ejecución si es solo un preflight
}

// Verifica que el método de la solicitud sea POST
// Si se espera DELETE, cambiar esta condición y la cabecera anterior
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido. Solo se acepta POST.' // Ajustar mensaje si se usa DELETE
    ]);
    exit;
}

// Decodifica los datos JSON recibidos desde el cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

// Extrae el ID del servicio, si está presente
$id = $data['id'] ?? null;

// Validación: si no se recibe un ID válido, se responde con error
if (!$id) {
    echo json_encode([
        'success' => false,
        'error' => 'ID requerido'
    ]);
    exit;
}

try {
    // Prepara la sentencia SQL para eliminar el servicio con el ID proporcionado
    $sql = "DELETE FROM SERVICIOS WHERE SER_ID = :id";
    $stmt = $conexion->prepare($sql);

    // Asocia el valor del ID a la consulta preparada
    $stmt->bindParam(':id', $id);

    // Ejecuta la sentencia
    if ($stmt->execute()) {
        // Si la ejecución fue exitosa, se responde con éxito
        echo json_encode(['success' => true]);
    } else {
        // Si la ejecución falló (por ejemplo, no existe el ID), se informa del error
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo eliminar el servicio'
        ]);
    }

} catch (PDOException $e) {
    // Si ocurre una excepción con la base de datos, se captura y se muestra el mensaje
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
