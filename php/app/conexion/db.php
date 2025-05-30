<?php



$host = 'mysql_server';
$db = 'my_database';
$user = 'root';
$pass = 'rootpassword';

$DSN = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$USUARIO = $user;
$PASSWORD = $pass;

try {
    // Opciones adicionales para PDO
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Manejo de errores mediante excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH, // Devuelve resultados como arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false, // Desactiva la emulación de consultas preparadas
    ];

    // Crear conexión PDO
    $conexion = new PDO($DSN, $USUARIO, $PASSWORD, $opciones);

    // Registrar mensaje de éxito en el log (opcional)
    error_log("Conexión a la base de datos exitosa.", 0);

} catch (PDOException $e) {
    $logPath = __DIR__ . "/error.log";

    // Verificar si el archivo de log es escribible
    if (!is_writable(__DIR__)) {
        echo "Error: No se puede escribir en el directorio de logs.";
        exit;
    }

    error_log("Error al conectar a la base de datos: " . $e->getMessage(), 3, $logPath);
    echo "Error al conectar a la base de datos. Por favor, revisa el archivo de log en: $logPath";
    exit; // Detener ejecución en caso de error fatal
}

?>