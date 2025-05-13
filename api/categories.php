<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/category.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$category = new Category($db);

try {
    $categories = $category->getAllCategories();
    echo json_encode($categories);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch categories']);
}