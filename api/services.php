<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');
require_once(__DIR__ . '/../lib/review.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$service = new Service($db);


$id = isset($_GET['id']) ? (int)$_GET['id'] : null; // Service ID

if ($id !== null) {

    $review = new Review($db);

    $serviceInfo = $service->getServiceById($id); 
    if ($serviceInfo) {
        $serviceInfo['reviews'] = $review->getReviewsService($id); 
        echo json_encode($serviceInfo);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Service not found']);
    }
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$provider = isset($_GET['provider']) ? trim($_GET['provider']) : null;
$category = isset($_GET['category']) && $_GET['category'] !== '' ? array_map('intval', explode(',', $_GET['category'])) : null;
$location = isset($_GET['location']) ? trim($_GET['location']) : null;
$status = isset($_GET['status']) ? trim($_GET['status']) : null;
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;
$min_rating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : null;
$max_rating = isset($_GET['max_rating']) ? (float)$_GET['max_rating'] : null;
$orderby = $_GET['orderby'] ?? 'created_at-desc';
$page = isset($_GET['page']) ? (int)$_GET['page'] : null;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : null;

$filters = [
    'search' => $search,
    'provider' => $provider,
    'category' => $category,
    'location' => $location,
    'status' => $status,
    'min_price' => $min_price,
    'max_price' => $max_price,
    'min_rating' => $min_rating,
    'max_rating' => $max_rating
];

$services = $service->getFilteredAndOrderedServices($filters, $orderby, $page, $per_page);
$total = $service->countFilteredServices($filters);

echo json_encode([
    'services' => $services,
    'total' => $total,
]);