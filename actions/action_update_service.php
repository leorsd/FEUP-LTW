<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}
// CSRF token check
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['update_error'] = 'Invalid CSRF token.';
    header("Location: ../pages/edit_service.php?service_id=$service_id");
    exit();
}

$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : null;
$user_id = $_SESSION['user_info']['id'] ?? null;

if (!$service_id || !$user_id) {
    $_SESSION['update_error'] = "Invalid service or user.";
    header("Location: ../pages/edit_service.php?service_id=$service_id");
    exit();
}

$db = getDatabaseConnection();
$serviceObj = new Service($db);

// Check if the user is the creator of the service
$stmt = $db->prepare("SELECT creator_id FROM service WHERE id = ?");
$stmt->execute([$service_id]);
$creator_id = $stmt->fetchColumn();

if ($creator_id != $user_id) {
    $_SESSION['update_error'] = "You are not authorized to update this service.";
    header("Location: ../pages/edit_service.php?service_id=$service_id");
    exit();
}

// Check for active orders (do not allow update if there are any)
$stmt = $db->prepare("SELECT COUNT(*) FROM service_order WHERE service_id = ? AND status IN (1,2,3)");
$stmt->execute([$service_id]);
$active_orders = $stmt->fetchColumn();

if ($active_orders > 0) {
    $_SESSION['update_error'] = "Cannot update service with active orders.";
    header("Location: ../pages/edit_service.php?service_id=$service_id");
    exit();
}

// Collect only allowed fields to update
$allowed_fields = ['title', 'description', 'category', 'price', 'location', 'image'];
$fields = [];
foreach ($allowed_fields as $field) {
    if (isset($_POST[$field]) && $_POST[$field] !== '') {
        $fields[$field] = $_POST[$field];
    }
}

if (empty($fields)) {
    $_SESSION['update_error'] = "No fields to update.";
    header("Location: ../pages/edit_service.php?service_id=$service_id");
    exit();
}

// Use the updateService method from Service class
$success = $serviceObj->updateService($service_id, $user_id, $fields);

if ($success) {
    $_SESSION['update_success'] = "Service updated successfully.";
    header("Location: ../pages/my_services.php?");
    exit();
} else {
    $_SESSION['update_error'] = "Failed to update service.";
    header("Location: ../pages/edit_service.php?service_id=$service_id");
    exit();
}