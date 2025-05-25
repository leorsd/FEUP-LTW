<?php
declare(strict_types=1);
session_start();

require_once(__DIR__ . '/../templates/admin_body.php');
require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');

$user_info = $_SESSION['user_info'] ?? null;

if (!$user_info) {
    $_SESSION['error'] = "You must be logged in to acces the admin page.";
    header('Location: login.php');
    exit();
}

draw_initial_common_header('Log in');
draw_admin_header();
draw_final_common_header();
draw_common_headbar($user_info);
draw_admin_body();
draw_common_footer();
?>