<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../conexion/db.php';

try {
    $sql = "SELECT SOL_ID, SOL_NOMBRE, SOL_CORREO, SOL_MENSAJE, SOL_FECHA, SOL_ESTADO FROM SOLICITUDES ORDER BY SOL_FECHA DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Normaliza SOL_FECHA: si es null, ponlo como ''
    foreach ($solicitudes as &$sol) {
        if (!isset($sol['SOL_FECHA']) || $sol['SOL_FECHA'] === null) {
            $sol['SOL_FECHA'] = '';
        }
    }
    echo json_encode(['success' => true, 'solicitudes' => $solicitudes]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
