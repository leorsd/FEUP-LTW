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
// CSRF token check
session_start();
if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

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