<?php
// Mostrar errores en pantalla para facilitar la depuración durante el desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Reporta todos los errores de PHP

// Configuración de cabeceras para CORS (permite solicitudes desde cualquier origen)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Max-Age: 3600'); // Tiempo en segundos para que el preflight request sea cacheado
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Manejo de solicitudes "preflight" (solicitudes OPTIONS que envía el navegador antes del POST real)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit; // No se ejecuta más lógica, solo responde exitosamente a OPTIONS
}

// Verificación de que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Código HTTP: Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Registrar en el log que se recibió una solicitud para eliminar un método de pago
error_log("Recibiendo solicitud DELETE para método de pago");

// Leer y decodificar el cuerpo de la solicitud, se espera que venga en formato JSON
$data = json_decode(file_get_contents('php://input'), true);

// Mostrar los datos recibidos en el log para fines de depuración
error_log("Datos recibidos: " . print_r($data, true));

// Validar que se haya recibido un ID de método de pago
if (!$data || !isset($data['id'])) {
    http_response_code(400); // Código HTTP: Bad Request
    echo json_encode(['success' => false, 'error' => 'ID de método de pago no proporcionado']);
    exit;
}

// Incluir el archivo que contiene la conexión PDO a la base de datos
require_once '../conexion/db.php';

try {
    // Iniciar una transacción para asegurar la integridad en caso de error
    $conexion->beginTransaction();

    // Construir la consulta SQL para eliminar un método de pago por su ID
    $sql = "DELETE FROM METODOS_PAGO WHERE MDP_PAGO = :id";

    // Si también se recibió un ID de usuario, añadirlo a la cláusula WHERE
    if (isset($data['userId'])) {
        $sql .= " AND MDP_USUARIO = :userId"; // Protección adicional: solo el dueño puede eliminar su método
    }

    // Preparar la consulta usando PDO
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT); // Asociar el parámetro :id

    // Asociar el parámetro :userId si fue especificado
    if (isset($data['userId'])) {
        $stmt->bindParam(':userId', $data['userId'], PDO::PARAM_INT);
    }

    // Ejecutar la consulta preparada
    $stmt->execute();

    // Verificar si alguna fila fue eliminada
    if ($stmt->rowCount() > 0) {
        $conexion->commit(); // Confirmar los cambios si hubo eliminación exitosa
        error_log("Método de pago eliminado correctamente: ID " . $data['id']);
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Método de pago eliminado correctamente']);
    } else {
        // Si no se eliminó nada, revertir la transacción
        $conexion->rollBack();
        error_log("No se encontró el método de pago o no se tiene permiso: ID " . $data['id']);
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Método de pago no encontrado o no tienes permiso para eliminarlo']);
    }

} catch (PDOException $e) {
    // En caso de excepción, revertir cualquier cambio pendiente
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    // Registrar el error y responder con mensaje de error interno del servidor
    error_log("Error al eliminar método de pago: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
