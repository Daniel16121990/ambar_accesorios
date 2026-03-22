<?php
session_start();
require 'db.php';

// Configuración: ¡CAMBIA ESTO POR TU ACCESS TOKEN DE PRODUCCIÓN O PRUEBA!
// Puedes obtenerlo en: https://www.mercadopago.com.mx/developers/panel
$access_token = 'APP_USR-2853066532590744-030520-430676cab96ef5043200ba6841f536b9-3246381517'; 

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit;
}

// 1. Inicializar el SDK de Mercado Pago y agregar autoloader
require 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

// Autenticar la configuración de Mercado Pago
MercadoPagoConfig::setAccessToken($access_token);

// 2. Construir el array de items para la Preferencia
$items = [];
$ids = array_keys($_SESSION['carrito']);
$ids_string = implode(',', array_map('intval', $ids));

if ($ids_string) {
    $sql = "SELECT * FROM productos WHERE id IN ($ids_string)";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        $cantidad = $_SESSION['carrito'][$row['id']];
        
        $items[] = [
            "id" => (string)$row['id'],
            "title" => $row['nombre'],
            "description" => substr($row['descripcion'], 0, 200),
            "picture_url" => "https://tusitio.com/" . $row['imagen'],
            "quantity" => (int)$cantidad,
            "currency_id" => "ARS",
            "unit_price" => (float)$row['precio']
        ];
    }
}

// 3. Evaluar el Método de Envío elegido por el usuario
$tipo_envio = isset($_POST['tipo_envio']) ? $_POST['tipo_envio'] : 'mercado_envios';
$costo_envio = 0;
$shipments_config = null;
$metadata_info = null;

if ($tipo_envio === 'correo_argentino') {
    $cp = isset($_POST['codigo_postal']) ? $_POST['codigo_postal'] : '';
    $dir_calle = isset($_POST['dir_calle']) ? $_POST['dir_calle'] : '';
    $dir_piso = isset($_POST['dir_piso']) ? $_POST['dir_piso'] : '';
    $dir_ciudad = isset($_POST['dir_ciudad']) ? $_POST['dir_ciudad'] : '';
    $dir_provincia = isset($_POST['dir_provincia']) ? $_POST['dir_provincia'] : '';
    $dir_nombre = isset($_POST['dir_nombre']) ? $_POST['dir_nombre'] : '';

    $costo_envio = 6500.00; // Tarifa Nacional Base
    
    if ($cp && substr((string)$cp, 0, 1) === '1') {
        $costo_envio = 4000.00; // Tarifa Regional CABA/GBA
    }
    
    // Guardar los datos de envío en metadata para que el vendedor los vea en MP
    $metadata_info = [
        "envio_modo" => "correo_argentino",
        "envio_nombre" => $dir_nombre,
        "envio_cp" => $cp,
        "envio_direccion" => $dir_calle,
        "envio_piso" => $dir_piso,
        "envio_ciudad" => $dir_ciudad,
        "envio_provincia" => $dir_provincia
    ];
    
    // Agregar envío como item
    if ($costo_envio > 0) {
        $items[] = [
            "id" => "envio_correo_argentino",
            "title" => "Costo de Envío (Correo Argentino)",
            "quantity" => 1,
            "currency_id" => "ARS",
            "unit_price" => (float)$costo_envio
        ];
    }
} else if ($tipo_envio === 'mercado_envios') {
    $shipments_config = [
        "mode" => "me2"
    ];
}

// 4. Calcular el Total
$total_pedido = 0;
foreach ($_SESSION['carrito'] as $id_prod => $qty) {
    $result_prod = $conn->query("SELECT precio FROM productos WHERE id = " . (int)$id_prod);
    if ($row_prod = $result_prod->fetch_assoc()) {
        $total_pedido += ($row_prod['precio'] * $qty);
    }
}
$total_pedido += $costo_envio;
$user_email = "test_user_123456@testuser.com"; // Temporal harcodeado, luego puede venir del login

// 5. Guardar Pedido en Base de Datos (Estado Pendiente)
$dir_json = ($metadata_info !== null) ? json_encode($metadata_info) : null;
$stmt = $conn->prepare("INSERT INTO pedidos (user_email, total, envio_costo, envio_modo, direccion_json, estado_pago) VALUES (?, ?, ?, ?, ?, 'pendiente')");
$stmt->bind_param("sddss", $user_email, $total_pedido, $costo_envio, $tipo_envio, $dir_json);
$stmt->execute();
$pedido_id = $conn->insert_id;
$stmt->close();

// Insertar Items del pedido
$stmt_items = $conn->prepare("INSERT INTO pedidos_items (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
foreach ($_SESSION['carrito'] as $id_prod => $qty) {
    $result_prod = $conn->query("SELECT precio FROM productos WHERE id = " . (int)$id_prod);
    if ($row_prod = $result_prod->fetch_assoc()) {
        $precio_u = $row_prod['precio'];
        $stmt_items->bind_param("iiid", $pedido_id, $id_prod, $qty, $precio_u);
        $stmt_items->execute();
    }
}
$stmt_items->close();

// Limpiar el carrito porque ya generamos un pedido
// unset($_SESSION['carrito']); // Lo comentamos hasta tener la confirmación de pago si se desea.

// 6. Instanciar el PreferenceClient del SDK y crear la petición
$client = new PreferenceClient();

try {
    $preference_data = [
        "items" => $items,
        "payer" => [
            "email" => $user_email
        ],
        "back_urls" => [
            "success" => "http://localhost/tienda_online/success.php",
            "failure" => "http://localhost/tienda_online/failure.php",
            "pending" => "http://localhost/tienda_online/failure.php"
        ],
        "notification_url" => "https://tusitio.com/webhook_mp.php", // Debería ser una URL pública accesible para MP
        "external_reference" => (string)$pedido_id, // SUPER IMPORTANTE: Vincula MP con nuestro pedido
        "binary_mode" => true
    ];
    
    if ($shipments_config !== null) {
        $preference_data["shipments"] = $shipments_config;
    }
    
    if ($metadata_info !== null) {
        $preference_data["metadata"] = $metadata_info;
    }

    $preference = $client->create($preference_data);
    
    // Guardar el MP Preference ID en nuestro pedido
    $stmt_update = $conn->prepare("UPDATE pedidos SET mp_preference_id = ? WHERE id = ?");
    $stmt_update->bind_param("si", $preference->id, $pedido_id);
    $stmt_update->execute();
    $stmt_update->close();
    
    // 7. Redirigir al usuario al Checkout
    header("Location: " . $preference->init_point);
    exit;

} catch (MPApiException $e) {
    echo "Error en la API de Mercado Pago: \n";
    $apiResponse = $e->getApiResponse();
    if ($apiResponse) {
        echo print_r($apiResponse->getContent(), true);
    } else {
        echo $e->getMessage();
    }
} catch (\Exception $e) {
    echo "Error general: " . $e->getMessage();
}
?>
