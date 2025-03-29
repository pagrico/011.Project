<?php
// filepath: c:\Users\Pablo\Desktop\Projecto\backend\api.php

// Configuración básica
header("Access-Control-Allow-Origin: *"); // Permitir solicitudes desde cualquier origen
header("Content-Type: application/json; charset=UTF-8");

// Ejemplo de respuesta JSON
$response = [
    "status" => "success",
    "message" => "Conexión exitosa con el backend PHP"
];

// Enviar respuesta
echo json_encode($response);
?>