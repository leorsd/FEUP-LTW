<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');
require_once(__DIR__ . '/../lib/user.php');

if (!isset($_SESSION['user_info']['username'])) {
    $_SESSION['order_error'] = 'You must be logged in to cancel an order.';
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['service_id'])) {
    $_SESSION['order_error'] = 'Invalid request.';
    header('Location: ../pages/home.php');
    exit();
}

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$db = getDatabaseConnection();
$service_id = (int) $_POST['service_id'];
$user_id = (int) $_SESSION['user_info']['id'];

$service = new Service($db);
$statusName = $service->getUserOrderStatus($service_id, $user_id);

if ($statusName !== 'Ordered') {
    $_SESSION['order_error'] = 'You can only cancel orders with status "Ordered".';
    header('Location: ../pages/service.php?id=' . $service_id);
    exit();
}

// Remove the order from service_customer
$stmt = $db->prepare('DELETE FROM service_customer WHERE service_id = ? AND customer_id = ?');
if ($stmt->execute([$service_id, $user_id])) {
    $_SESSION['order_msg'] = 'Order canceled successfully!';
} else {
    $_SESSION['order_error'] = 'Could not cancel order. Please try again.';
}
header('Location: ../pages/service.php?id=' . $service_id);
exit();
