<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/message.php');
require_once(__DIR__ . '/../lib/user.php');

header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$db = getDatabaseConnection();
$message = new Message($db);

try {
    $chats = $message->getChatsForUser($user_id);
    echo json_encode($chats);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch chats']);
    exit;
}