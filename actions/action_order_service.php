<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');
require_once(__DIR__ . '/../lib/user.php');

// CSRF token check (if you use CSRF tokens, add check here)

if (!isset($_SESSION['user_info']['username'])) {
  $_SESSION['order_error'] = 'You must be logged in to order a service.';
  header('Location: ../pages/login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['service_id'])) {
  $_SESSION['order_error'] = 'Invalid request.';
  header('Location: ../pages/home.php');
  exit();
}

$db = getDatabaseConnection();
$service = new Service($db);
$userObj = new User($db);
$userObj->setUserData($_SESSION['user_info']['username'], '', '');
$userInfo = $userObj->getUserInfo();
$service_id = (int)$_POST['service_id'];
$customer_id = (int)$userInfo['id'];

// Check if already ordered
$stmt = $db->prepare('SELECT COUNT(*) FROM service_customer WHERE service_id = ? AND customer_id = ?');
$stmt->execute([$service_id, $customer_id]);
if ($stmt->fetchColumn() > 0) {
  $_SESSION['order_error'] = 'You have already ordered this service.';
  header('Location: ../pages/service.php?id=' . $service_id);
  exit();
}

// Insert with status 'Ordered' (id=1)
$stmt = $db->prepare('INSERT INTO service_customer (service_id, customer_id, status, created_at) VALUES (?, ?, 1, CURRENT_TIMESTAMP)');
if ($stmt->execute([$service_id, $customer_id])) {
  $_SESSION['order_msg'] = 'Service ordered successfully!';
} else {
  $_SESSION['order_error'] = 'Could not order service. Please try again.';
}
header('Location: ../pages/service.php?id=' . $service_id);
exit();
?>
