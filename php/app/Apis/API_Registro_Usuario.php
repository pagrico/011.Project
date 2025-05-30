<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // Permitir solicitudes desde este origen
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Solo POST y OPTIONS son relevantes aquí
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Encabezados permitidos
require_once '../conexion/db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respuesta simple para OPTIONS
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

// Registro temporal para depuración de encabezados
file_put_contents('php://stderr', "Encabezados recibidos: " . print_r(getallheaders(), true) . PHP_EOL);

$rawInput = file_get_contents('php://input');

// Registro temporal para depuración del cuerpo de la solicitud
file_put_contents('php://stderr', "Contenido recibido: " . $rawInput . PHP_EOL);

if (empty($rawInput)) {
    echo json_encode(['success' => false, 'message' => 'El cuerpo de la solicitud está vacío.']);
    exit;
}

$input = json_decode($rawInput, true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'El cuerpo de la solicitud no contiene datos válidos.']);
    exit;
}

$nombre = trim($input['nombre'] ?? '');
$apellidos = trim($input['apellidos'] ?? '');
$email = trim($input['email'] ?? '');
$telefono = trim($input['telefono'] ?? '');
$password = trim($input['password'] ?? '');
$login = trim($input['login'] ?? '');
$rol_id = 2;

if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || empty($password) || empty($login)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
    exit;
}

if (!preg_match('/^\d{9,15}$/', $telefono)) {
    echo json_encode(['success' => false, 'message' => 'El número de teléfono no es válido.']);
    exit;
}

try {
    if (!$conexion) {
        throw new PDOException('Conexión a la base de datos no inicializada.');
    }

    $stmt = $conexion->prepare('SELECT 1 FROM USUARIOS WHERE USU_EMAIL = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado.']);
        exit;
    }

    $stmt = $conexion->prepare('SELECT 1 FROM USUARIOS WHERE USU_LOGIN = :login');
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está registrado.']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conexion->prepare('INSERT INTO USUARIOS (USU_LOGIN, USU_NOMBRE, USU_APELLIDOS, USU_EMAIL, USU_TELEFONO, USU_PASSWORD, USU_ROL) VALUES (:login, :nombre, :apellidos, :email, :telefono, :password, :rol_id)');
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':rol_id', $rol_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión a la base de datos: ' . $e->getMessage()]);
}
exit;
?>
