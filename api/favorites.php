<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/favorites.php'); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id = isset($data['user_id']) ? (int)$data['user_id'] : null;
$service_id = isset($data['service_id']) ? (int)$data['service_id'] : null;


if (!isset($user_id) || !isset($service_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$db = getDatabaseConnection();
$favorites = new Favorites($db);

$success = $favorites->toggleFavorite($user_id, $service_id);

if ($success === true || $success === false) {
    echo json_encode(['favorited' => $success]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to toggle favorite']);
}