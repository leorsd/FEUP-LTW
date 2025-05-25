<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');
require_once(__DIR__ . '/../lib/review.php');
require_once(__DIR__ . '/../lib/order.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$serviceObj = new Service($db);
$reviewObj = new Review($db);
$orderObj = new Order($db);

$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : null;
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

if (!$service_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing service_id']);
    exit;
}

$service = $serviceObj->getServiceById($service_id);

if (!$service) {
    http_response_code(404);
    echo json_encode(['error' => 'Service not found']);
    exit;
}

// Get reviews for this service
$reviews = $reviewObj->getReviewsService($service_id);

// Optionally, get order info for this user and service
$order_info = null;
if ($user_id) {
    $order_info = $orderObj->getUserOrderInfo($service_id, $user_id);
    $service['is_favorite'] = $serviceObj->isFavorite($user_id, $service_id);
}

echo json_encode([
    'service' => $service,
    'reviews' => $reviews,
    'order_info' => $order_info
]);