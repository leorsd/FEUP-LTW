<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
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

// Check if the user is the creator of the service
$stmt = $db->prepare("SELECT creator_id FROM service WHERE id = ?");
$stmt->execute([$service_id]);
$creator_id = $stmt->fetchColumn();

if ($creator_id != $user_id) {
    $_SESSION['delete_error'] = "You are not authorized to delete this service.";
    header("Location: ../pages/my_services.php");
    exit();
}

// Check for active orders
$stmt = $db->prepare("SELECT COUNT(*) FROM service_order WHERE service_id = ? AND status IN (1,2,3)");
$stmt->execute([$service_id]);
$active_orders = $stmt->fetchColumn();

if ($active_orders > 0) {
    $_SESSION['delete_error'] = "Cannot delete service with active orders.";
    header("Location: ../pages/my_services.php");
    exit();
}

// Delete the service
$stmt = $db->prepare("DELETE FROM service WHERE id = ?");
$stmt->execute([$service_id]);

$_SESSION['delete_success'] = "Service deleted successfully.";
header("Location: ../pages/my_services.php");
exit();