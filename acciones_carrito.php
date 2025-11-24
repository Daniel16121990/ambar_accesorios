<?php
session_start();

header('Content-Type: application/json'); // Responderemos siempre con JSON

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$response = ['ok' => false, 'mensaje' => 'Error desconocido', 'total_items' => 0];

// Recibir datos JSON (fetch) o POST normal
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST; // Fallback si no es JSON puro
if (!$input) $input = $_GET;  // Fallback si es GET

$accion = $input['accion'] ?? '';
$id = isset($input['id']) ? (int)$input['id'] : 0;

// --- 1. AGREGAR ---
if ($accion === 'agregar') {
    $cantidad = isset($input['cantidad']) ? (int)$input['cantidad'] : 1;
    if ($cantidad < 1) $cantidad = 1;

    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] += $cantidad;
    } else {
        $_SESSION['carrito'][$id] = $cantidad;
    }
    $response['mensaje'] = '¡Agregado al carrito!';
    $response['ok'] = true;
}

// --- 2. ELIMINAR ---
if ($accion === 'eliminar') {
    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
        $response['ok'] = true;
    }
}

// --- 3. SUMAR / RESTAR (Para la vista de carrito) ---
if ($accion === 'sumar' || $accion === 'restar') {
    if (isset($_SESSION['carrito'][$id])) {
        if ($accion === 'sumar') {
            $_SESSION['carrito'][$id]++;
        } elseif ($accion === 'restar') {
            $_SESSION['carrito'][$id]--;
            if ($_SESSION['carrito'][$id] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }
        $response['ok'] = true;
    }
}

// CALCULAR TOTALES PARA ACTUALIZAR EL DOM
require 'db.php';
$ids = array_keys($_SESSION['carrito']);
$gran_total = 0;
$total_items = 0;
$row_subtotal = 0; // Subtotal específico del producto modificado

if (!empty($ids)) {
    $ids_string = implode(',', array_map('intval', $ids));
    $result = $conn->query("SELECT id, precio FROM productos WHERE id IN ($ids_string)");
    
    while($row = $result->fetch_assoc()) {
        $qty = $_SESSION['carrito'][$row['id']];
        $subtotal = $row['precio'] * $qty;
        $gran_total += $subtotal;
        $total_items += $qty;

        if ($row['id'] == $id) {
            $row_subtotal = $subtotal;
        }
    }
}

$response['total_items'] = $total_items;
$response['gran_total'] = number_format($gran_total, 2);
$response['row_subtotal'] = number_format($row_subtotal, 2);
$response['row_qty'] = isset($_SESSION['carrito'][$id]) ? $_SESSION['carrito'][$id] : 0;

echo json_encode($response);
exit;
?>