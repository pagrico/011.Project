<?php
// ==========================
// CABECERAS HTTP INICIALES
// ==========================

// Permitir acceso desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");
// Métodos permitidos
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// Cabeceras que pueden ser enviadas por el cliente
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ==========================
// MANEJO DE PETICIONES OPTIONS (Preflight)
// ==========================

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Si es una solicitud preflight, simplemente respondemos con 200 OK
    http_response_code(200);
    exit();
}

// ==========================
// VALIDACIÓN DEL MÉTODO HTTP
// ==========================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Repetimos las cabeceras CORS por compatibilidad
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // Método no permitido
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido. Solo se acepta POST.'
    ]);
    exit;
}

// ==========================
// VALIDACIÓN DEL ARCHIVO
// ==========================

// Verificamos que se haya enviado un archivo con el nombre 'imagen' y que no tenga errores
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'No se recibió ninguna imagen válida con el nombre "imagen".'
    ]);
    exit;
}

// ==========================
// CONFIGURACIÓN DEL SERVICIO
// ==========================

// Ruta física donde se guardarán las imágenes (relativa al sistema de archivos del servidor)
$uploadDir = __DIR__ . '/../uploads/servicios/';

// Extensiones de archivo permitidas
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Tamaño máximo permitido (en bytes)
$maxFileSize = 5 * 1024 * 1024; // 5MB

// ==========================
// CREACIÓN DEL DIRECTORIO (si no existe)
// ==========================

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo crear el directorio de subida. Verifique permisos en ' . dirname($uploadDir)
        ]);
        exit;
    }
}

// ==========================
// VALIDACIONES DEL ARCHIVO
// ==========================

$file = $_FILES['imagen'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Validar extensión
if (!in_array($fileExtension, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Extensión de archivo no permitida: ' . htmlspecialchars($fileExtension)
    ]);
    exit;
}

// Validar tamaño
if ($fileSize > $maxFileSize) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'El archivo excede el tamaño máximo permitido (' . ($maxFileSize / 1024 / 1024) . ' MB)'
    ]);
    exit;
}

// ==========================
// GENERAR NOMBRE ÚNICO Y GUARDAR IMAGEN
// ==========================

$uniqueName = uniqid('serv_', true) . '.' . $fileExtension;
$destination = $uploadDir . $uniqueName;

if (!move_uploaded_file($fileTmpName, $destination)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al guardar la imagen. Verifique permisos en el directorio de subida.'
    ]);
    exit;
}

// ==========================
// CONSTRUCCIÓN DE LA URL PÚBLICA DE LA IMAGEN
// ==========================

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Determinar el directorio base a partir del SCRIPT_NAME para construir correctamente la URL
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseDir = preg_replace('#/Apis/?$#i', '', $scriptDir); // Quitar "/Apis" si está

// Construir la URL pública hacia la imagen subida
$imageUrl = rtrim($protocol . $host . $baseDir, '/') . '/uploads/servicios/' . $uniqueName;

// ==========================
// RESPUESTA EXITOSA
// ==========================

http_response_code(200);
echo json_encode([
    'success' => true,
    'url' => $imageUrl,
    'filename' => $uniqueName
]);
?>
