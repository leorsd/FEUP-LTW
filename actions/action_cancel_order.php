<?php
// filepath: /home/naldo/college/2.2/LTW/proj/actions/action_cancel_order.php
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

$db = getDatabaseConnection();
$service_id = (int)$_POST['service_id'];
$user_id = (int)$_SESSION['user_info']['id'];

// Remove the order from service_customer
$stmt = $db->prepare('DELETE FROM service_customer WHERE service_id = ? AND customer_id = ?');
if ($stmt->execute([$service_id, $user_id])) {
    $_SESSION['order_msg'] = 'Order canceled successfully!';
} else {
    $_SESSION['order_error'] = 'Could not cancel order. Please try again.';
}
header('Location: ../pages/service.php?id=' . $service_id);
exit();
