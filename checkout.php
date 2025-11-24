<?php
session_start();
require 'db.php';

// Configuración: ¡CAMBIA ESTO POR TU ACCESS TOKEN DE PRODUCCIÓN O PRUEBA!
// Puedes obtenerlo en: https://www.mercadopago.com.mx/developers/panel
$access_token = 'TEST-8366462776626564-091813-1b63456789abcdef123456789abcdef-123456789'; 

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit;
}

// 1. Construir el array de items para Mercado Pago
$items = [];
$ids = array_keys($_SESSION['carrito']);
$ids_string = implode(',', array_map('intval', $ids));

if ($ids_string) {
    $sql = "SELECT * FROM productos WHERE id IN ($ids_string)";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        $cantidad = $_SESSION['carrito'][$row['id']];
        
        $items[] = [
            "id" => $row['id'],
            "title" => $row['nombre'],
            "description" => substr($row['descripcion'], 0, 200),
            "picture_url" => "https://tusitio.com/" . $row['imagen'], // Idealmente usa URL absoluta real
            "quantity" => (int)$cantidad,
            "currency_id" => "MXN", // Peso Mexicano
            "unit_price" => (float)$row['precio']
        ];
    }
}

// 2. Calcular costo de envío (Simulado o desde API de Mercado Envíos)
$costo_envio = 0;
if (isset($_POST['codigo_postal'])) {
    // Lógica simple de ejemplo:
    // Si el CP inicia con '0', es CDMX (ejemplo) -> $50
    // Si no, $150
    // Aquí conectarías con la API real de Mercado Envíos si tuvieras credenciales
    $cp = $_POST['codigo_postal'];
    if (substr($cp, 0, 1) === '0' || substr($cp, 0, 1) === '1') {
        $costo_envio = 50;
    } else {
        $costo_envio = 150;
    }
}

// Agregar envío como un item más (o usar shipments.cost si usas modo Marketplace)
if ($costo_envio > 0) {
    $items[] = [
        "id" => "envio",
        "title" => "Costo de Envío",
        "quantity" => 1,
        "currency_id" => "MXN",
        "unit_price" => (float)$costo_envio
    ];
}

// 3. Crear la Preferencia (Payload)
$datos_preferencia = [
    "items" => $items,
    "payer" => [
        "email" => "test_user_123456@testuser.com" // Email de prueba o del usuario logueado
    ],
    "back_urls" => [
        "success" => "http://localhost/tienda_online/success.php",
        "failure" => "http://localhost/tienda_online/failure.php",
        "pending" => "http://localhost/tienda_online/failure.php"
    ],
    "auto_return" => "approved",
    "binary_mode" => true
];

// 4. Enviar solicitud a Mercado Pago via cURL
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.mercadopago.com/checkout/preferences",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($datos_preferencia),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $access_token,
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $data = json_decode($response, true);
    
    // 5. Redirigir al usuario al Checkout de Mercado Pago
    if (isset($data['init_point'])) {
        header("Location: " . $data['init_point']);
        exit;
    } else {
        echo "Error al crear preferencia: ";
        print_r($data);
    }
}
?>
