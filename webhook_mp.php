<?php
// webhook_mp.php
// Este archivo recibe las notificaciones de Mercado Pago sobre el pago de los pedidos.

require 'db.php';
require 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;

// ATENCIÓN: Misma configuración de acceso que en checkout.php
$access_token = 'APP_USR-2853066532590744-030520-430676cab96ef5043200ba6841f536b9-3246381517'; 
MercadoPagoConfig::setAccessToken($access_token);

// Leer los datos crudos del webhook
$body = file_get_contents("php://input");
$data = json_decode($body, true);

// MP envía dos tipos de notificaciones (IPN y Webhook).
// Nos interesa el tipo "payment" que se manda así: ?topic=payment&id=1234
$topic = isset($_GET['topic']) ? $_GET['topic'] : (isset($data['type']) ? $data['type'] : null);
$payment_id = isset($_GET['id']) ? $_GET['id'] : (isset($data['data']['id']) ? $data['data']['id'] : null);

if ($topic == 'payment' && $payment_id) {
    try {
        $client = new PaymentClient();
        $payment = $client->get($payment_id);

        // De este Payment nos importan 2 cosas:
        $status = $payment->status; // 'approved', 'pending', 'rejected', etc.
        $external_reference = $payment->external_reference; // Éste es nuestro 'pedido_id'
        
        if ($external_reference) {
            $pedido_id = (int)$external_reference;
            
            // Actualizar la base de datos
            $stmt = $conn->prepare("UPDATE pedidos SET estado_pago = ?, payment_id = ? WHERE id = ?");
            // Para mantener compatibilidad con español en nuestro panel
            $estado_local = 'pendiente';
            if ($status === 'approved') $estado_local = 'aprobado';
            if ($status === 'rejected') $estado_local = 'rechazado';
            
            $stmt->bind_param("ssi", $estado_local, $payment_id, $pedido_id);
            $stmt->execute();
            $stmt->close();
            
            http_response_code(200);
            echo "OK: Pedido $pedido_id actualizado a $estado_local";
            exit;
        }

    } catch (MPApiException $e) {
        error_log("Error MP Webhook: " . $e->getMessage());
        http_response_code(500);
        exit;
    } catch (\Exception $e) {
        error_log("Error MP Webhook General: " . $e->getMessage());
        http_response_code(500);
        exit;
    }
}

// Responde 200 siempre para que MP deje de enviar la notificación
http_response_code(200);
echo "OK";
?>
