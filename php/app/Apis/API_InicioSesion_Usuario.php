<?php
// Configuración de cabeceras
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Inclusión de la base de datos
require_once '../conexion/db.php';

// Definición del path para el log de errores
$logPath = __DIR__ . '/error.log';

// Validar conexión a la base de datos
if (!isset($conexion) || !$conexion) {
    error_log("Error: Conexión a la base de datos no inicializada.\n", 3, $logPath);
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión al sistema.']);
    exit;
}

// Manejo de preflight request de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    // Validar JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error al decodificar JSON: " . json_last_error_msg() . "\n", 3, $logPath);
        http_response_code(400);
        echo json_encode(['error' => 'Datos enviados no válidos.']);
        exit;
    }

    // Validar existencia de campos necesarios
    if (empty($input['identifier']) || empty($input['password'])) {
        error_log("Error: Campos faltantes o vacíos en la solicitud. Datos recibidos: " . print_r($input, true) . "\n", 3, $logPath);
        http_response_code(400);
        echo json_encode(['error' => 'Por favor, proporciona tu usuario/correo y contraseña.']);
        exit;
    }

    $identifier = trim($input['identifier']);
    $password = trim($input['password']);

    // Validar formato de identifier
    if (!is_string($identifier) || strlen($identifier) === 0) {
        error_log("Error: El parámetro 'identifier' no es válido. Valor: " . print_r($identifier, true) . "\n", 3, $logPath);
        http_response_code(400);
        echo json_encode(['error' => 'Identificador inválido.']);
        exit;
    }

    // Preparar consulta SQL (cambiando los placeholders correctamente)
    $query = "
        SELECT 
            USU_USUARIO, USU_EMAIL, USU_LOGIN, USU_PASSWORD, 
            USU_ROL, USU_NOMBRE, USU_APELLIDOS, USU_TELEFONO
        FROM USUARIOS
        WHERE USU_EMAIL = :email OR USU_LOGIN = :login
    ";

    $stmt = $conexion->prepare($query);

    // Ejecutar consulta pasando parámetros correctamente
    $stmt->execute([
        ':email' => $identifier,
        ':login' => $identifier
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log("Usuario no encontrado. Identifier: $identifier\n", 3, $logPath);
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado.']);
        exit;
    }

    // Verificar contraseña
    if (!password_verify($password, $user['USU_PASSWORD'])) {
        error_log("Contraseña incorrecta para el usuario: $identifier\n", 3, $logPath);
        http_response_code(401);
        echo json_encode(['error' => 'Contraseña incorrecta.']);
        exit;
    }

    // Respuesta exitosa
    echo json_encode([
        'success'   => true,
        'message'   => 'Inicio de sesión exitoso.',
        'nombre'    => $user['USU_NOMBRE'],
        'apellidos' => $user['USU_APELLIDOS'],
        'user_id'   => $user['USU_USUARIO'],
        'rol'       => $user['USU_ROL']
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage() . "\n", 3, $logPath);
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos.']);
    exit;
} catch (Exception $e) {
    error_log("Error inesperado: " . $e->getMessage() . "\n", 3, $logPath);
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error inesperado.']);
    exit;
}
?>
