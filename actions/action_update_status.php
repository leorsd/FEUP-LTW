<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');
require_once(__DIR__ . '/../lib/user.php');

// Debug log helper
function debug_log($msg) {
    file_put_contents(__DIR__ . '/../debug_update_status.log', date('c') . ' ' . $msg . "\n", FILE_APPEND);
}

// debug_log('--- Update status action called ---');
// debug_log('POST: ' . json_encode($_POST));
// debug_log('SESSION: ' . json_encode($_SESSION));

if (!isset($_SESSION['user_info']['username'])) {
    debug_log('Not logged in');
    $_SESSION['order_error'] = 'You must be logged in to update order status.';
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['service_id'], $_POST['customer_id'], $_POST['status'])) {
    debug_log('Invalid request: missing POST fields');
    $_SESSION['order_error'] = 'Invalid request.';
    header('Location: ../pages/home.php');
    exit();
}

$db = getDatabaseConnection();
$service_id = (int)$_POST['service_id'];
$customer_id = (int)$_POST['customer_id'];
$status_id = (int)$_POST['status'];

$userObj = new User($db);
$userObj->setUserData($_SESSION['user_info']['username'], '', '');
$userInfo = $userObj->getUserInfo();
$current_user_id = (int)$userInfo['id'];

$serviceObj = new Service($db);
$service = $serviceObj->getServiceById($service_id);
// debug_log('Service: ' . json_encode($service));

if (!$service || $service['creator_id'] != $current_user_id) {
    debug_log('Not authorized: user ' . $current_user_id . ' is not creator of service ' . $service_id);
    $_SESSION['order_error'] = 'You are not authorized to update this order.';
    header('Location: ../pages/service.php?id=' . $service_id);
    exit();
}

// CSRF protection
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    debug_log('Invalid CSRF token');
    exit('Invalid CSRF token');
}

// Use Service class for update logic for better encapsulation
if (!$service || $service['creator_id'] != $current_user_id) {
    debug_log('Not authorized: user ' . $current_user_id . ' is not creator of service ' . $service_id);
    $_SESSION['order_error'] = 'You are not authorized to update this order.';
    header('Location: ../pages/service.php?id=' . $service_id);
    exit();
}

// Get current order status ID
$currentStatusId = $serviceObj->getOrderStatusId($service_id, $customer_id);
if (!$serviceObj->isValidStatusTransition($currentStatusId, $status_id)) {
  debug_log('Invalid status transition: ' . $currentStatusId . ' -> ' . $status_id);
  $_SESSION['order_error'] = 'Invalid status transition.';
  header('Location: ../pages/service.php?id=' . $service_id);
  exit();
}

// Refactor: move update logic to Service class
$updated = $serviceObj->updateOrderStatus($service_id, $customer_id, $status_id);
if ($updated) {
  // debug_log('Status updated successfully');
    $_SESSION['order_msg'] = 'Order status updated successfully!';
} else {
    debug_log('Failed to update status');
    $_SESSION['order_error'] = 'Could not update order status. Please try again.';
}
header('Location: ../pages/service.php?id=' . $service_id);
exit();
?>
