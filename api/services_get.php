<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/service.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$service = new Service($db);

$user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;
$orderby = $_GET['orderby'] ?? 'created_at-desc';
$page = isset($_GET['page']) ? (int) $_GET['page'] : null;
$per_page = isset($_GET['per_page']) ? (int) $_GET['per_page'] : null;

$filters = [
    'search' => $_GET['search'] ?? null,
    'provider' => $_GET['provider'] ?? null,
    'provider_id' => isset($_GET['provider_id']) ? (int)$_GET['provider_id'] : null,
    'category' => isset($_GET['category']) && $_GET['category'] !== '' ? array_map('intval', explode(',', $_GET['category'])) : null,
    'location' => $_GET['location'] ?? null,
    'exclude_provider_id' => isset($_GET['exclude_provider_id']) ? (int)$_GET['exclude_provider_id'] : null,
    'status' => isset($_GET['status']) && $_GET['status'] !== '' ? array_map('intval', explode(',', $_GET['status'])) : null,
    'min_price' => isset($_GET['min_price']) ? (int)$_GET['min_price'] : null,
    'max_price' => isset($_GET['max_price']) ? (int)$_GET['max_price'] : null,
    'min_rating' => isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : null,
    'max_rating' => isset($_GET['max_rating']) ? (float)$_GET['max_rating'] : null,
];

$result = $service->getServices('all', null, $filters, $orderby, $page, $per_page);

if ($user_id) {
    foreach ($result['services'] as &$srv) {
        $srv['is_favorite'] = $service->isFavorite($user_id, $srv['id']);
    }
    unset($srv);
}

echo json_encode($result);