<?php
// api_envios.php
// Este script recibe un código postal y devuelve un costo estimado
// basado en una lógica simple (CABA vs. Interior) para la opción de Correo Argentino.

header('Content-Type: application/json');

// Recibir el CP por POST o GET
$data = json_decode(file_get_contents('php://input'), true);
$cp = isset($data['cp']) ? $data['cp'] : (isset($_GET['cp']) ? $_GET['cp'] : null);

if (!$cp || strlen((string)$cp) < 4) {
    echo json_serialize([
        'status' => 'error',
        'message' => 'Código Postal no válido.'
    ]);
    exit;
}

// Lógica de tarifas estáticas (Aproximadas 2026 para Correo Argentino Clásico)
// Si empieza con 1 (ej. 1xxx), asumimos AMBA/CABA/GBA: Envío Regional
// Si empieza con cualquier otro, asumimos Interior: Envío Nacional

$costo = 6500.00; // Tarifa Nacional Base
$tiempo_estimado = "3 a 6 días hábiles"; // Tiempo estimado para nacionales

if (substr((string)$cp, 0, 1) === '1') {
    $costo = 4000.00; // Tarifa Regional CABA/GBA
    $tiempo_estimado = "1 a 3 días hábiles"; // Tiempo estimado para regionales
}

echo json_encode([
    'status' => 'success',
    'proveedor' => 'Correo Argentino',
    'costo' => $costo,
    'tiempo_estimado' => $tiempo_estimado,
    'cp' => $cp
]);
?>
