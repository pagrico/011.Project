<?php
ini_set('display_errors', 1); // For development only
ini_set('display_startup_errors', 1); // For development only
error_reporting(E_ALL); // For development only

// --- Debug Logging ---
$debug_log_file = __DIR__ . '/crear_servicio_debug.log';
$log_entry = "Timestamp: " . date('Y-m-d H:i:s') . "\n";
$log_entry .= "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
$log_entry .= "Content-Type Header: " . ($_SERVER['CONTENT_TYPE'] ?? 'N/A') . "\n";
// $log_entry .= "All Headers: " . print_r(getallheaders(), true) . "\n"; // Uncomment for very detailed header logging

// CABECERAS CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejo de preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

require_once '../conexion/db.php'; // Asegúrate que $conexion (PDO) esté disponible

// --- Helper Functions (Consistent with API_editar_servicio.php) ---
function getUploadsWebPath() {
    // Define tu ruta web base a la carpeta de uploads.
    // AJUSTA ESTA RUTA SEGÚN TU ESTRUCTURA DE SERVIDOR
    // Ejemplo: /proyecto/uploads/servicios/  o /uploads/servicios/
    $scriptDir = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))); // Sube un nivel desde /Apis
    return rtrim($scriptDir, '/') . '/uploads/servicios/';
}

function getFullImageUrl($filename) {
    if (!$filename) return null;
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host . getUploadsWebPath() . $filename;
}

function guardarNuevaImagen($fileInfo) {
    $uploadDir = __DIR__ . '/../uploads/servicios/'; // Ruta física
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) { // Usar 0755
            error_log("Error al crear directorio de subida: " . $uploadDir);
            return ['success' => false, 'error' => 'Error interno al crear directorio para imágenes.'];
        }
    }

    // Verificar si el archivo es válido
    if (!isset($fileInfo['name']) || !isset($fileInfo['tmp_name']) || !isset($fileInfo['size']) || !isset($fileInfo['error'])) {
        error_log("Información de archivo incompleta: " . print_r($fileInfo, true));
        return ['success' => false, 'error' => 'Información de archivo incompleta'];
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    $originalName = $fileInfo['name'];
    $tmpName = $fileInfo['tmp_name'];
    $fileSize = $fileInfo['size'];
    $fileError = $fileInfo['error'];

    if ($fileError !== UPLOAD_ERR_OK) {
        // Provide more detailed error messages based on UPLOAD_ERR_* constants
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido en php.ini',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido en el formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta un directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión PHP detuvo la subida del archivo'
        ];
        
        $errorMsg = isset($errorMessages[$fileError]) ? $errorMessages[$fileError] : "Error desconocido ($fileError)";
        
        error_log("Error en la subida del archivo " . htmlspecialchars($originalName) . ": " . $errorMsg);
        return ['success' => false, 'error' => 'Error en la subida del archivo ' . htmlspecialchars($originalName) . ': ' . $errorMsg];
    }

    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['success' => false, 'error' => 'Extensión no permitida para ' . htmlspecialchars($originalName)];
    }
    if ($fileSize > $maxFileSize) {
        return ['success' => false, 'error' => 'Archivo ' . htmlspecialchars($originalName) . ' excede el tamaño máximo.'];
    }

    $uniqueFilename = uniqid('serv_', true) . '.' . $fileExtension;
    $destination = $uploadDir . $uniqueFilename;

    if (move_uploaded_file($tmpName, $destination)) {
        return ['success' => true, 'url' => getFullImageUrl($uniqueFilename), 'filename' => $uniqueFilename];
    } else {
        error_log("Error al mover archivo subido: " . $tmpName . " a " . $destination);
        return ['success' => false, 'error' => 'Error interno al guardar la imagen ' . htmlspecialchars($originalName)];
    }
}
// --- End Helper Functions ---

// Verificar si es multipart/form-data
$isMultipart = false;
if (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
    $isMultipart = true;
    
    // Log detallado de lo que llegó
    $log_entry .= "POST Data: " . print_r($_POST, true) . "\n";
    if (!empty($_FILES)) {
        $log_entry .= "FILES Keys: " . print_r(array_keys($_FILES), true) . "\n";
        foreach ($_FILES as $fieldName => $fileInfo) {
            if (is_array($fileInfo['name'])) {
                $log_entry .= "Campo $fieldName: " . count($fileInfo['name']) . " archivos\n";
                for ($i = 0; $i < count($fileInfo['name']); $i++) {
                    $log_entry .= "  - " . $fileInfo['name'][$i] . " (" . $fileInfo['size'][$i] . " bytes)\n";
                }
            } else {
                $log_entry .= "Campo $fieldName: " . $fileInfo['name'] . " (" . $fileInfo['size'] . " bytes)\n";
            }
        }
    } else {
        $log_entry .= "No se recibieron archivos\n";
    }
}
$log_entry .= "Raw Input (first 1KB): " . substr(file_get_contents('php://input'), 0, 1024) . "\n";
$log_entry .= "----------------------------------------------------------\n";
file_put_contents($debug_log_file, $log_entry, FILE_APPEND);

$final_image_urls = [];
$upload_errors = [];

// MANEJO MEJORADO PARA MÚLTIPLES ARCHIVOS - CORREGIDO
if ($isMultipart) {
    // El frontend ahora envía cada imagen como imagenesNuevas_0, imagenesNuevas_1, etc.
    $imagenesCount = isset($_POST['imagenesNuevasCount']) ? intval($_POST['imagenesNuevasCount']) : 0;
    
    // Log para depuración
    file_put_contents($debug_log_file, "Procesando $imagenesCount imágenes nuevas\n", FILE_APPEND);
    
    // Procesar cada imagen
    for ($i = 0; $i < $imagenesCount; $i++) {
        $fieldName = "imagenesNuevas_$i";
        if (isset($_FILES[$fieldName])) {
            // Log detallado del archivo recibido
            file_put_contents($debug_log_file, "Detalles de archivo $fieldName: " . print_r($_FILES[$fieldName], true) . "\n", FILE_APPEND);
            
            if ($_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
                $fileInfo = $_FILES[$fieldName];
                $uploadResult = guardarNuevaImagen($fileInfo);
                if ($uploadResult['success']) {
                    $final_image_urls[] = $uploadResult['url'];
                    file_put_contents($debug_log_file, "Imagen $i subida exitosamente: " . $uploadResult['url'] . "\n", FILE_APPEND);
                } else {
                    $upload_errors[] = $uploadResult['error'];
                    file_put_contents($debug_log_file, "Error al subir imagen $i: " . $uploadResult['error'] . "\n", FILE_APPEND);
                }
            } else {
                $errorCode = $_FILES[$fieldName]['error'];
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido en php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido en el formulario',
                    UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
                    UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                    UPLOAD_ERR_NO_TMP_DIR => 'Falta un directorio temporal',
                    UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
                    UPLOAD_ERR_EXTENSION => 'Una extensión PHP detuvo la subida del archivo'
                ];
                $errorMsg = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : "Error desconocido ($errorCode)";
                $upload_errors[] = "Error $errorCode al subir imagen $i: $errorMsg";
                file_put_contents($debug_log_file, "Error $errorCode al subir imagen $i: $errorMsg\n", FILE_APPEND);
            }
        } else {
            file_put_contents($debug_log_file, "No se encontró el campo $fieldName para imagen $i\n", FILE_APPEND);
        }
    }

    // Obtener otros campos del formulario
    $icono = $_POST['icono'] ?? 'fa-camera-retro';
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
    $titulo = $_POST['titulo'] ?? '';
    $desc_corta = $_POST['descripcion_corta'] ?? '';
    $desc_larga = $_POST['descripcion_larga'] ?? '';
    $incluye_json = $_POST['incluye'] ?? '[]'; // Vue envía esto como un string JSON
    $estado = $_POST['estado'] ?? 'Oculto';

} else { // Solicitud JSON (sin archivos nuevos)
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'JSON malformado.']);
        exit;
    }
    
    $icono = $data['icono'] ?? 'fa-camera-retro';
    $precio = isset($data['precio']) ? floatval($data['precio']) : 0;
    $titulo = $data['titulo'] ?? '';
    $desc_corta = $data['descripcion_corta'] ?? '';
    $desc_larga = $data['descripcion_larga'] ?? '';
    $incluye_json = $data['incluye'] ?? '[]'; // Espera un string JSON
    $estado = $data['estado'] ?? 'Oculto';
    
    // 'imagenes' en el JSON body es un string JSON de un array de URLs
    $imagenes_json_from_payload = $data['imagenes'] ?? '[]'; 
    $decoded_urls = json_decode($imagenes_json_from_payload, true);
    if (is_array($decoded_urls)) {
        foreach ($decoded_urls as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) { // Validar que sea una URL
                 $final_image_urls[] = $url;
            }
        }
    }
}

// Validación de campos obligatorios
if (empty($titulo)) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El título del servicio es requerido.']);
    exit;
}
// Agrega más validaciones según sea necesario (precio, descripciones, etc.)


try {
    $sql = "INSERT INTO SERVICIOS (
                SER_ICONO, SER_PRECIO, SER_TITULO, 
                SER_DESCRIPCION_CORTA, SER_DESCRIPCION_LARGA, 
                SER_INCLUYE, SER_ESTADO, SER_IMAGENES
            ) VALUES (
                :icono, :precio, :titulo, 
                :desc_corta, :desc_larga, 
                :incluye, :estado, :imagenes
            )";
    
    $stmt = $conexion->prepare($sql);

    $final_imagenes_json_str = json_encode($final_image_urls); // Convert array of URLs to JSON string for DB

    $stmt->bindParam(':icono', $icono);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':desc_corta', $desc_corta);
    $stmt->bindParam(':desc_larga', $desc_larga);
    $stmt->bindParam(':incluye', $incluye_json); // $incluye_json ya es un string JSON
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':imagenes', $final_imagenes_json_str); // Guardar el string JSON de URLs

    if ($stmt->execute()) {
        $lastInsertId = $conexion->lastInsertId();
        header('Content-Type: application/json');
        http_response_code(201); // 201 Creado
        $response = [
            'success' => true, 
            'id' => $lastInsertId, 
            'message' => 'Servicio creado correctamente.',
            'imagenes' => $final_image_urls // Devolver las URLs de las imágenes subidas
        ];
        if (!empty($upload_errors)) {
            $response['upload_warnings'] = $upload_errors;
        }
        echo json_encode($response);
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo insertar el servicio en la base de datos.', 'details' => $stmt->errorInfo()]);
    }

} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    error_log("Error de BD al crear servicio: " . $e->getMessage()); // Log real error
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) { // Capturar otras excepciones generales
    header('Content-Type: application/json');
    http_response_code(500);
    error_log("Error general al crear servicio: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error inesperado: ' . $e->getMessage()]);
}
?>