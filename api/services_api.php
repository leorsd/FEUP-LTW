<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$service = new Service($db);

// Get query parameters
$paginated = isset($_GET['paginated']) ? filter_var($_GET['paginated'], FILTER_VALIDATE_BOOLEAN) : false; // Default to not paginated
$orderby = $_GET['orderby'] ?? 'created_at-desc'; // Default ordering
$category = isset($_GET['category']) ? (int)$_GET['category'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to page 1
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12; // Default limit
$offset = ($page - 1) * $limit;

if ($paginated) {
    // Fetch paginated services
    $services = $service->getFilteredAndOrderedServices($limit, $offset, $category, $orderby);
} else {
    // Fetch all services without pagination
    $services = $service->getAllFilteredAndOrderedServices($category, $orderby);
}

// Return the services as JSON
echo json_encode($services);