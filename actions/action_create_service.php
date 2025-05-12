<?php
declare(strict_types=1);

session_start();

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');

if (!isset($_SESSION['user_info'])) {
    $_SESSION['error'] = "You must be logged in to create a service.";
    header('Location: ../pages/login.php');
    exit();
}

$serviceTitle = trim($_POST['service_title'] ?? '');
$serviceDescription = trim($_POST['service_description'] ?? '');
$servicePrice = floatval($_POST['service_price'] ?? 0);
$serviceCategory = intval($_POST['service_category'] ?? 0);
$serviceLocation = trim($_POST['service_location'] ?? '');
$serviceStatus = 1; //Default status when created is open

if (empty($serviceTitle) || empty($serviceDescription) || $servicePrice <= 0 || $serviceCategory <= 0 || empty($serviceLocation)) {
    $_SESSION['error'] = "All fields are required, and price must be greater than 0.";
    header('Location: ../pages/create_service.php');
    exit();
}

$imagePath = null; // Default to null if no image is provided
if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['service_image']['tmp_name'];
    $name = basename($_FILES['service_image']['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed_exts)) {
        $new_name = $_SESSION['username'] . '_' . time() . '.' . $ext;
        $dest = __DIR__ . '/../images/cache/' . $new_name;
        if (move_uploaded_file($tmp_name, $dest)) {
            $imagePath = $new_name; // Save the image path for the database
        }
    }
}

$userId = $_SESSION['user_info']['id'];

try {

    $db = getDatabaseConnection();
    $service = new Service($db);

    $service->createService($serviceTitle, $serviceDescription, $servicePrice, $serviceLocation, $userId, $serviceStatus, $serviceCategory, $imagePath);

    $_SESSION['success'] = "Service created successfully!";
    header('Location: ../pages/home.php');
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = "Failed to create service: " . $e->getMessage();
    header('Location: ../pages/create_service.php');
    exit();
}