<?php
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/status.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$status = new Status($db);

try {
    $statuses = $status->getAllStatuses();
    echo json_encode($statuses);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch statuses']);
}