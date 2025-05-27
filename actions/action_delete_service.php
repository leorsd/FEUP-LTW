<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');
require_once(__DIR__ . '/../lib/order.php');

session_start();

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    $_SESSION['delete_error'] = "Invalid CSRF token.";
    header("Location: ../pages/my_services.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['delete_error'] = "Method Not Allowed.";
    header("Location: ../pages/my_services.php");
    exit('Method Not Allowed');
}

$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : null;
$user_id = $_SESSION['user_info']['id'] ?? null;

if (!$service_id || !$user_id) {
    $_SESSION['delete_error'] = "Invalid service or user.";
    header("Location: ../pages/my_services.php");
    exit();
}

$db = getDatabaseConnection();
$service = new Service($db);

// Get service info using the Service class
$serviceData = $service->getServiceById($service_id);

if (!$serviceData) {
    $_SESSION['delete_error'] = "Service not found.";
    header("Location: ../pages/my_services.php");
    exit();
}

// Check if the user is the creator of the service
if ($serviceData['creator_id'] != $user_id) {
    $_SESSION['delete_error'] = "You are not authorized to delete this service.";
    header("Location: ../pages/my_services.php");
    exit();
}

// Check for active orders (using getServices with context 'sold' and status filter)
$orders = new Order($db);
$orders = $orders->getOrdersByService($service_id, [1, 2, 3]); // Only active orders

if (!empty($orders)) {
    $_SESSION['delete_error'] = "Cannot delete service with active orders.";
    header("Location: ../pages/my_services.php");
    exit();
}

// Delete the service using the Service class method
if ($service->deleteService($service_id)) {
    $_SESSION['delete_success'] = "Service deleted successfully.";
} else {
    $_SESSION['delete_error'] = "Failed to delete service.";
}
header("Location: ../pages/my_services.php");
exit();
