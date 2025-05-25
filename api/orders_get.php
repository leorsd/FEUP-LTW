<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/order.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$orderObj = new Order($db);

$provider_id = isset($_GET['provider_id']) ? (int)$_GET['provider_id'] : null;
$service_id  = isset($_GET['service_id'])  ? (int)$_GET['service_id']  : null;
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;

// Get all orders for a provider related to a certain service
if ($provider_id && $service_id) {
    $sql = "SELECT o.*, u.username AS customer_username, s.name AS status_name
            FROM service_order o
            JOIN service sv ON o.service_id = sv.id
            JOIN user u ON o.customer_id = u.id
            LEFT JOIN service_status s ON o.status = s.id
            WHERE sv.creator_id = :provider_id AND o.service_id = :service_id
            ORDER BY o.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([':provider_id' => $provider_id, ':service_id' => $service_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['orders' => $orders]);
    exit();
}

// Get all orders for a customer
if ($customer_id) {
    $orders = $orderObj->getOrdersByCustomer($customer_id);
    echo json_encode(['orders' => $orders]);
    exit();
}

http_response_code(400);
echo json_encode(['error' => 'Missing or invalid parameters.']);
exit();