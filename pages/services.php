<?php
declare(strict_types=1);

session_start();

require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');
require_once(__DIR__ . '/../templates/services_body.php');

$user_info = $_SESSION['user_info'] ?? null;

if (!isset($user_info)) {
    $_SESSION['error'] = "Please log in to access this page.";
    header('Location: login.php');
    exit();
}

draw_initial_common_header('CarLink');
draw_services_header();
draw_final_common_header();
draw_common_headbar($user_info);
draw_services_body();
draw_common_footer();
?>