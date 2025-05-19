<?php
session_start();
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');
require_once(__DIR__ . '/../lib/review.php');
require_once(__DIR__ . '/../lib/user.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$service = new Service($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : null; // Service ID

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
// favorites owner ID, that is the user ID, but if this parameter is set, it means that it is only to return the favorite services of this user
$favorites_owner = isset($_GET['favorites_owner']) ? (int) $_GET['favorites_owner'] : null;

// It is used to always now the user ID and that way now if each service is favorited or not
$user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;

// When set, it will only return the services that have the title, description, owner or locatin like the value given
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

// When set, it will only return the services that have the provider name like the value given
$provider = isset($_GET['provider']) ? trim($_GET['provider']) : null;

// When set, it will only return the services that have the provider ID like the value given
$provider_id = isset($_GET['provider_id']) ? (int) $_GET['provider_id'] : null;

// When set, it will only return the services that have the category ID like one of the values given, as it can be a list of categories
$category = isset($_GET['category']) && $_GET['category'] !== '' ? array_map('intval', explode(',', $_GET['category'])) : null;

// When set, it will only return the services that have the location like the value given
$location = isset($_GET['location']) ? trim($_GET['location']) : null;

// When set, it will only return the services that have the status like one of the values given, as it can be a list of statuses
$status = isset($_GET['status']) && $_GET['status'] !== '' ? array_map('intval', explode(',', $_GET['status'])) : null;

// When set, it will only return the services that have the price bigger than the value given
$min_price = isset($_GET['min_price']) ? (int) $_GET['min_price'] : null;

// When set, it will only return the services that have the price smaller than the value given
$max_price = isset($_GET['max_price']) ? (int) $_GET['max_price'] : null;

// When set, it will only return the services that have the rating bigger than the value given
$min_rating = isset($_GET['min_rating']) ? (float) $_GET['min_rating'] : null;

// When set, it will only return the services that have the rating smaller than the value given
$max_rating = isset($_GET['max_rating']) ? (float) $_GET['max_rating'] : null;

// Represents the order in which the services will be returned
$orderby = $_GET['orderby'] ?? 'created_at-desc';

// Represents the page of the services that will be returned
$page = isset($_GET['page']) ? (int) $_GET['page'] : null;

// Represents the number of services that will be returned per page
$per_page = isset($_GET['per_page']) ? (int) $_GET['per_page'] : null;
$my_services = isset($_GET['my_services']) && $_GET['my_services'] == '1';

// Get user_id from session if available
$user_id = null;
if (isset($_SESSION['user_info']['username'])) {
    $userObj = new User($db);
    $userObj->setUserData($_SESSION['user_info']['username'], '', '');
    $userInfo = $userObj->getUserInfo();
    if ($userInfo && isset($userInfo['id'])) {
        $user_id = (int) $userInfo['id'];
    }
}

$filters = [
    'favorites_owner' => $favorites_owner,
    'search' => $search,
    'provider' => $provider,
    'provider_id' => $provider_id,
    'category' => $category,
    'location' => $location,
    'status' => $status,
    'min_price' => $min_price,
    'max_price' => $max_price,
    'min_rating' => $min_rating,
    'max_rating' => $max_rating
];

if ($my_services && $user_id !== null) {
    // Only show services the user bought (from service_customer)
    $services = $service->getServicesBoughtByUser($user_id, $filters, $orderby, $page, $per_page);
    $total = $service->countServicesBoughtByUser($user_id, $filters);
} else {
    $services = $service->getFilteredAndOrderedServices($filters, $orderby, $page, $per_page);
    $total = $service->countFilteredServices($filters);
}

if ($user_id) {
    foreach ($services as &$srv) {
        $srv['is_favorite'] = $service->isFavorite($user_id, $srv['id']);
    }
    unset($srv);
} else {
    foreach ($services as &$srv) {
        $srv['is_favorite'] = false;
    }
    unset($srv);
}

echo json_encode([
    'services' => $services,
    'total' => $total,
]);
