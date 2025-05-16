<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/review.php'); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$service_id = isset($data['service_id']) ? (int)$data['service_id'] : null;
$rating = isset($data['rating']) ? (int)$data['rating'] : null;
$text = isset($data['text']) ? trim($data['text']) : null;
$reviewer_id = isset($data['reviewer_id']) ? (int)$data['reviewer_id'] : null;

if (!$service_id || !$rating || $text === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$db = getDatabaseConnection();
$review = new Review($db);
$success = $review->addReview($service_id, $reviewer_id, $rating, $text);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add review']);
}