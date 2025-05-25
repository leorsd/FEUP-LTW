<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$user = new User($db);

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

if ($id === null) {
    $userInfo = $user->getAllUsers($search);
} else {
    $userInfo = $user->getUserInfo($id);
}

if ($userInfo) {
    echo json_encode($userInfo);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
?>