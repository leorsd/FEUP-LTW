<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/order.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$service_id  = isset($_POST['service_id']) ? (int)$_POST['service_id'] : null;
$customer_id = $_SESSION['user_info']['id'] ?? null;

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['payment_error'] = 'Invalid CSRF token.';
    header("Location: ../pages/payment.php?service_id=$service_id");
    exit();
}

$cardholder = trim($_POST['cardholder'] ?? '');
$cardnumber = preg_replace('/\D/', '', $_POST['cardnumber'] ?? '');
$expiry    = trim($_POST['expiry'] ?? '');
$cvv       = trim($_POST['cvv'] ?? '');

$errors = [];
if (!$service_id || !$customer_id) $errors[] = "Invalid service or user.";
if ($cardholder === '') $errors[] = "Cardholder name required.";
if (strlen($cardnumber) < 16 || strlen($cardnumber) > 19) $errors[] = "Invalid card number.";
if (!preg_match('/^\d{2}\/\d{2}$/', $expiry)) $errors[] = "Invalid expiry date format.";
if (!preg_match('/^\d{3,4}$/', $cvv)) $errors[] = "Invalid CVV.";

if ($errors) {
    $_SESSION['payment_error'] = implode(' ', $errors);
    header("Location: ../pages/payment.php?service_id=$service_id");
    exit();
}

$db = getDatabaseConnection();
$orderObj = new Order($db);

$order = $orderObj->getOrder($service_id, $customer_id);
if ($order) {
    $orderObj->updateOrderStatus($service_id, $customer_id, 3);
} else {
    $_SESSION['payment_error'] = "Order not found.";
    header("Location: ../pages/payment.php?service_id=$service_id");
    exit();
}

header("Location: ../pages/service.php?id=$service_id");
exit();
