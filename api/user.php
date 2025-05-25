<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid user id']);
    exit();
}

$db = getDatabaseConnection();
$user = new User($db);

$id = (int)$_GET['id'];
$userInfo = $user->getUserInfo($id);

if ($userInfo) {
    // Only return basic info
    $basicInfo = [
        'id' => $userInfo['id'],
        'username' => $userInfo['username'],
        'email' => $userInfo['email'],
        'profile_picture' => $userInfo['profile_picture'] ?? null
    ];
    echo json_encode($basicInfo);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
?>