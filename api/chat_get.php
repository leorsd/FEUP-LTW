<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/message.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$message = new Message($db);

$user1 = isset($_GET['user1']) ? (int) $_GET['user1'] : null;
$user2 = isset($_GET['user2']) ? (int) $_GET['user2'] : null;
$msg_after_id = isset($_GET['msg_after_id']) ? (int) $_GET['msg_after_id'] : 0;

if ($user1 === null || $user2 === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user IDs']);
    exit;
}

try {
    $messages = $message->getMessagesBetween($user1, $user2, $msg_after_id); 
    echo json_encode($messages);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch messages']);
    exit;
}