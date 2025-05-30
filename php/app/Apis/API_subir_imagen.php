<?php
// Cabeceras CORS SIEMPRE al principio
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejo de preflight OPTIONS SIEMPRE antes de cualquier salida
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Cabeceras CORS también aquí
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(405); // Método no permitido
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

// Verificar si se envió un archivo con el nombre 'imagen'
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(['success' => false, 'error' => 'No se recibió ninguna imagen válida con el nombre "imagen".']);
    exit;
}

// Configuración
$uploadDir = __DIR__ . '/../uploads/servicios/'; // Ruta al directorio de subidas
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxFileSize = 5 * 1024 * 1024; // 5 MB

// Crear el directorio de subida si no existe
if (!is_dir($uploadDir)) {
    // Intentar crear el directorio recursivamente con permisos 0755 (más seguro que 0777)
    if (!mkdir($uploadDir, 0755, true)) { // Changed 0777 to 0755
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo crear el directorio de subida. Verifique los permisos para ' . dirname($uploadDir) . ' o créelo manualmente (' . $uploadDir . ') y dé permisos de escritura al usuario del servidor web (ej: www-data).'
        ]);
        exit;
    }
}

// Validar el archivo
$file = $_FILES['imagen'];
$fileName = $file['name']; // It's good practice to sanitize the original filename if used directly
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error']; // Already checked but can be used for more specific errors

$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
if (!in_array($fileExtension, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Extensión de archivo no permitida: ' . htmlspecialchars($fileExtension)]);
    exit;
}
if ($fileSize > $maxFileSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El archivo excede el tamaño máximo permitido (' . ($maxFileSize / 1024 / 1024) . ' MB)']);
    exit;
}

// Generar un nombre único para el archivo para evitar colisiones y problemas con caracteres especiales
$uniqueName = uniqid('serv_', true) . '.' . $fileExtension;
$destination = $uploadDir . $uniqueName;

// Mover el archivo al directorio de subida
if (!move_uploaded_file($fileTmpName, $destination)) {
    http_response_code(500); // Error interno del servidor
    // Log the error for server-side debugging if possible
    // error_log("Error moving uploaded file: " . $fileTmpName . " to " . $destination);
    echo json_encode(['success' => false, 'error' => 'Error al guardar la imagen. Revise los permisos de escritura en ' . $uploadDir]);
    exit;
}

// --- Construir la URL pública de la imagen subida ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Esto asume que 'uploads' está en el directorio raíz web o un nivel por encima de 'Apis'
// Si 'Apis' está en la raíz web, y 'uploads' también, la URL base sería diferente.
// Ejemplo: si la estructura es /var/www/html/Apis y /var/www/html/uploads
// SCRIPT_NAME podría ser /Apis/API_subir_imagen.php
// dirname($_SERVER['SCRIPT_NAME']) sería /Apis
// Si uploads está en /var/www/html/uploads, entonces necesitas subir un nivel desde /Apis.

// Una forma más robusta podría ser definir una constante o configuración para la URL base de los uploads.
// Por ejemplo, define('UPLOADS_WEB_PATH', '/uploads/servicios/');
// $imageUrl = $protocol . $host . UPLOADS_WEB_PATH . $uniqueName;

// Actual cálculo de $baseDir:
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); // e.g., /proyecto/Apis or /Apis
// Si $scriptDir es /Apis, $baseDir se vuelve "" (raíz web).
// Si $scriptDir es /proyecto/Apis, $baseDir se vuelve "/proyecto".
$baseDir = preg_replace('#/Apis/?$#i', '', $scriptDir); 

$imageUrl = rtrim($protocol . $host . $baseDir, '/') . '/uploads/servicios/' . $uniqueName;

// Respuesta exitosa
http_response_code(200);
echo json_encode(['success' => true, 'url' => $imageUrl, 'filename' => $uniqueName]);

?>