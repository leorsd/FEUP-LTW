<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');
require_once(__DIR__ . '/../lib/category.php');
require_once(__DIR__ . '/../lib/service.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$user = new User($db);

$adminId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$adminId || !$user->isAdmin($adminId)) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Admins only']);
    exit();
}

$action = $_GET['action'] ?? null;

if ($action === 'add_category') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['category_name'] ?? '');
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Category name required']);
        exit();
    }
    $category = new Category($db);
    $result = $category->createCategory($name);
    echo json_encode(['success' => (bool)$result]);
    exit();
}

if ($action === 'delete_category') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['category_id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Category ID required']);
        exit();
    }
    $category = new Category($db);
    $result = $category->deleteCategory($id);
    echo json_encode(['success' => (bool)$result]);
    exit();
}

if ($action === 'delete_user') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['user_id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID required']);
        exit();
    }
    $result = $user->deleteUser($id);
    echo json_encode(['success' => (bool)$result]);
    exit();
}

if ($action === 'promote_admin') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['user_id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID required']);
        exit();
    }
    $result = $user->promoteToAdmin($id);
    echo json_encode(['success' => (bool)$result]);
    exit();
}

if ($action === 'delete_service') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['service_id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Service ID required']);
        exit();
    }
    $service = new Service($db);
    $result = $service->deleteService($id);
    echo json_encode(['success' => (bool)$result]);
    exit();
}

http_response_code(404);
echo json_encode(['error' => 'Invalid action or method']);
exit();