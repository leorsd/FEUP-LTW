<?php
declare(strict_types=1);
session_start();

require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');
require_once(__DIR__ . '/../templates/create_service_body.php');

$user_info = $_SESSION['user_info'] ?? null;

if (!$user_info) {
    $_SESSION['error'] = "You must be logged in to create a service.";
    header('Location: login.php');
    exit();
}

draw_initial_common_header('CarLink');
draw_create_service_header();
draw_final_common_header();
draw_common_headbar($user_info);
draw_create_service_body();
draw_common_footer();
?>