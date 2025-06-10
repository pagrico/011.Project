<?php
// Mostrar errores para depuración (no recomendado en producción)
ini_set('display_errors', 1); // Solo para desarrollo
ini_set('display_startup_errors', 1); // Solo para desarrollo
error_reporting(E_ALL); // Solo para desarrollo

// --- Enhanced Debug Logging ---
// Archivo de log para depuración de la edición de servicios
$debug_log_file = __DIR__ . '/editar_servicio_debug.log';
$log_entry = "Timestamp: " . date('Y-m-d H:i:s') . "\n";
$log_entry .= "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
$log_entry .= "Content-Type Header: " . ($_SERVER['CONTENT_TYPE'] ?? 'N/A') . "\n";
// $log_entry .= "All Headers: " . print_r(getallheaders(), true) . "\n"; // Descomenta para log detallado

// CABECERAS CORS – aplicar SIEMPRE al principio del script para permitir peticiones desde cualquier origen
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Permitir POST y OPTIONS
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Permitir cabeceras necesarias

// Manejo de preflight OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir el método POST para editar servicios
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

// Incluir archivo de conexión a la base de datos (asegúrate que $conexion (PDO) esté disponible aquí)
require_once '../conexion/db.php';

// --- Helper Functions ---
// Devuelve la ruta web base a la carpeta de uploads de servicios
function getUploadsWebPath() {
    $scriptDir = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))); // Sube un nivel desde /Apis
    return rtrim($scriptDir, '/') . '/uploads/servicios/';
}

// Devuelve la URL completa de una imagen a partir del nombre de archivo
function getFullImageUrl($filename) {
    if (!$filename) return null;
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host . getUploadsWebPath() . $filename;
}

// Guarda una nueva imagen en el servidor y devuelve la URL o un error
function guardarNuevaImagen($fileInfo) {
    $uploadDir = __DIR__ . '/../uploads/servicios/'; // Ruta física
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) { // Crear directorio si no existe
            error_log("Error al crear directorio de subida: " . $uploadDir);
            return ['success' => false, 'error' => 'Error interno al crear directorio para imágenes.'];
        }
    }

    // Validar que la información del archivo esté completa
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

    // Validar errores de subida
    if ($fileError !== UPLOAD_ERR_OK) {
        // Mensajes de error detallados según el código de error
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

    // Validar extensión y tamaño
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['success' => false, 'error' => 'Extensión no permitida para ' . htmlspecialchars($originalName)];
    }
    if ($fileSize > $maxFileSize) {
        return ['success' => false, 'error' => 'Archivo ' . htmlspecialchars($originalName) . ' excede el tamaño máximo.'];
    }

    // Generar nombre único y mover archivo
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

// Verificar si la solicitud es multipart/form-data (para subida de archivos)
$isMultipart = false;
if (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
    $isMultipart = true;
    
    // Log detallado de lo que llegó por POST y archivos
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

// Inicializar arrays para URLs de imágenes y errores de subida
$imagenes_final_urls = []; // URLs completas para la BD
$upload_errors = [];

// --- MANEJO DE DATOS SEGÚN EL TIPO DE SOLICITUD ---
// Si es multipart/form-data, procesar archivos y campos del formulario
if ($isMultipart) {
    // Obtener el ID del servicio a editar
    $id = isset($_POST['id']) ? trim($_POST['id']) : null;
    // Log para depuración del ID recibido
    file_put_contents($debug_log_file, "ID recibido: " . var_export($id, true) . "\n", FILE_APPEND);
    file_put_contents($debug_log_file, "Tipo de dato del ID: " . gettype($id) . "\n", FILE_APPEND);
    
    // Obtener otros campos del formulario
    $icono = $_POST['icono'] ?? 'fa-camera-retro';
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
    $titulo = $_POST['titulo'] ?? '';
    $desc_corta = $_POST['descripcion_corta'] ?? '';
    $desc_larga = $_POST['descripcion_larga'] ?? '';
    $incluye_json = $_POST['incluye'] ?? '[]'; 
    $estado = $_POST['estado'] ?? 'Oculto';
    
    // Procesar imágenes nuevas enviadas como imagenesNuevas_0, imagenesNuevas_1, etc.
    $imagenesCount = isset($_POST['imagenesNuevasCount']) ? intval($_POST['imagenesNuevasCount']) : 0;
    file_put_contents($debug_log_file, "Procesando $imagenesCount imágenes nuevas\n", FILE_APPEND);
    for ($i = 0; $i < $imagenesCount; $i++) {
        $fieldName = "imagenesNuevas_$i";
        if (isset($_FILES[$fieldName])) {
            file_put_contents($debug_log_file, "Detalles de archivo $fieldName: " . print_r($_FILES[$fieldName], true) . "\n", FILE_APPEND);
            if ($_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
                $fileInfo = $_FILES[$fieldName];
                $uploadResult = guardarNuevaImagen($fileInfo);
                if ($uploadResult['success']) {
                    $imagenes_final_urls[] = $uploadResult['url'];
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

    // Procesar imágenes existentes (no eliminadas por el usuario)
    $imagenes_existentes_urls = [];
    if (isset($_POST['imagenesExistentes'])) {
        $decoded_existentes = json_decode($_POST['imagenesExistentes'], true);
        if (is_array($decoded_existentes)) {
            foreach ($decoded_existentes as $url) {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $imagenes_existentes_urls[] = $url;
                }
            }
        }
        file_put_contents($debug_log_file, "Imágenes existentes: " . count($imagenes_existentes_urls) . "\n", FILE_APPEND);
    }
    // Combinar imágenes existentes y nuevas, eliminando duplicados
    $imagenes_final_urls = array_merge($imagenes_existentes_urls, $imagenes_final_urls);
    $imagenes_final_urls = array_values(array_unique($imagenes_final_urls));

} else { // Si es JSON puro (sin archivos nuevos)
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'JSON malformado.']);
        exit;
    }
    
    $id = isset($data['id']) ? trim($data['id']) : null;
    file_put_contents($debug_log_file, "ID recibido (JSON): " . var_export($id, true) . "\n", FILE_APPEND);
    file_put_contents($debug_log_file, "Tipo de dato del ID (JSON): " . gettype($id) . "\n", FILE_APPEND);
    
    $icono = $data['icono'] ?? 'fa-camera-retro';
    $precio = isset($data['precio']) ? floatval($data['precio']) : 0;
    $titulo = $data['titulo'] ?? '';
    $desc_corta = $data['descripcion_corta'] ?? '';
    $desc_larga = $data['descripcion_larga'] ?? '';
    $incluye_json = $data['incluye'] ?? '[]'; 
    $estado = $data['estado'] ?? 'Oculto';
    
    // Procesar imágenes enviadas como URLs en el JSON
    $imagenes_json_str = $data['imagenes'] ?? '[]'; 
    $decoded_urls = json_decode($imagenes_json_str, true);
    if (is_array($decoded_urls)) {
        foreach ($decoded_urls as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                 $imagenes_final_urls[] = $url;
            }
        }
    }
}

// Validación del ID del servicio
if (empty($id)) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID del servicio es requerido.']);
    exit;
}
if (!is_numeric($id)) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID del servicio debe ser numérico.']);
    exit;
}

try {
    $conexion->beginTransaction();

    // Obtener imágenes actuales de la BD para comparar y borrar las que ya no están
    $stmt_get_images = $conexion->prepare("SELECT SER_IMAGENES FROM SERVICIOS WHERE SER_ID = :id");
    $stmt_get_images->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_get_images->execute();
    $current_images_json = $stmt_get_images->fetchColumn();
    $current_image_urls_db = $current_images_json ? json_decode($current_images_json, true) : [];
    if (!is_array($current_image_urls_db)) $current_image_urls_db = [];

    // Actualizar los datos del servicio en la base de datos
    $sql = "UPDATE SERVICIOS SET 
            SER_ICONO = :icono,
            SER_PRECIO = :precio,
            SER_TITULO = :titulo,
            SER_DESCRIPCION_CORTA = :desc_corta,
            SER_DESCRIPCION_LARGA = :desc_larga,
            SER_INCLUYE = :incluye,
            SER_ESTADO = :estado,
            SER_IMAGENES = :imagenes
            WHERE SER_ID = :id";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':icono', $icono);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':desc_corta', $desc_corta);
    $stmt->bindParam(':desc_larga', $desc_larga);
    $stmt->bindParam(':incluye', $incluye_json); // Guardar como string JSON
    $stmt->bindParam(':estado', $estado);
    $imagenes_final_json_str = json_encode($imagenes_final_urls);
    $stmt->bindParam(':imagenes', $imagenes_final_json_str);
    $idInt = intval($id);
    $stmt->bindParam(':id', $idInt, PDO::PARAM_INT);

    $success = $stmt->execute();
    
    if ($success) {
        $conexion->commit();

        // Lógica para borrar imágenes antiguas del servidor que ya no están asociadas al servicio
        $uploadDirPhysical = __DIR__ . '/../uploads/servicios/';
        $urls_to_keep = $imagenes_final_urls; // URLs que deben permanecer

        foreach ($current_image_urls_db as $url_in_db) {
            if (!in_array($url_in_db, $urls_to_keep)) {
                // Extraer el nombre del archivo de la URL para borrarlo físicamente
                $filename_to_delete = basename(parse_url($url_in_db, PHP_URL_PATH));
                if ($filename_to_delete) {
                    $filepath_to_delete = $uploadDirPhysical . $filename_to_delete;
                    if (file_exists($filepath_to_delete)) {
                        unlink($filepath_to_delete);
                        file_put_contents($debug_log_file, "Deleted old image: " . $filepath_to_delete . "\n", FILE_APPEND);
                    } else {
                        file_put_contents($debug_log_file, "Old image not found for deletion: " . $filepath_to_delete . "\n", FILE_APPEND);
                    }
                }
            }
        }

        // Respuesta de éxito
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        http_response_code(200);
        $response = ['success' => true, 'message' => 'Servicio actualizado correctamente.'];
        if (!empty($upload_errors)) {
            $response['upload_warnings'] = $upload_errors; // Informar sobre errores de subida parciales
        }
        echo json_encode($response);
    } else {
        $conexion->rollBack();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el servicio en la base de datos.', 'details' => $stmt->errorInfo()]);
    }

} catch (PDOException $e) {
    // Revertir la transacción en caso de error de base de datos
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    http_response_code(500);
    error_log("Error de BD al actualizar servicio: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Revertir la transacción en caso de cualquier otro error
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    http_response_code(500);
    error_log("Error general al actualizar servicio: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error inesperado: ' . $e->getMessage()]);
}
?>