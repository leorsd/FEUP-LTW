<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/message.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$sender_id = isset($data['sender_id']) ? (int)$data['sender_id'] : null;
$receiver_id = isset($data['receiver_id']) ? (int)$data['receiver_id'] : null;
$content = isset($data['content']) ? trim($data['content']) : '';

if (!$sender_id || !$receiver_id || $content === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$db = getDatabaseConnection();
$messageObj = new Message($db);

$message_id = $messageObj->sendMessage($sender_id, $receiver_id, $content);

if ($message_id) {
    echo json_encode(['success' => true, 'message_id' => $message_id]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send message']);
}