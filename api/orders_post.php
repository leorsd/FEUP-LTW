<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/order.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$service_id  = isset($data['service_id'])  ? (int)$data['service_id']  : null;
$customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : null;
$status_id   = isset($data['status_id'])   ? (int)$data['status_id']   : 1;

if (!$service_id || !$customer_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
    exit();
}

$db = getDatabaseConnection();
$orderObj = new Order($db);

$exists = $orderObj->getOrder($service_id, $customer_id);

if ($exists) {
    $ok = $orderObj->updateOrderStatus($service_id, $customer_id, $status_id);
    $msg = $ok ? ['success' => true, 'message' => 'Order updated.'] : ['error' => 'Could not update order.'];
    http_response_code($ok ? 200 : 500);
    echo json_encode($msg);
} else {
    $created = $orderObj->createOrder($service_id, $customer_id, $status_id);
    $msg = $created ? ['success' => true, 'message' => 'Order created.', 'order_id' => $created] : ['error' => 'Could not create order.'];
    http_response_code($created ? 200 : 500);
    echo json_encode($msg);
}
exit();