<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../templates/edit_service_body.php');
require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');

if (!isset($_SESSION['user_info']['id'])) {
  $_SESSION['error'] = "Please log in to change your password.";
  header('Location: login.php');
  exit();
}

draw_initial_common_header('Edit Service');
draw_edit_service_header();
draw_final_common_header();
draw_common_headbar($_SESSION['user_info']);
draw_edit_service_body();
draw_common_footer();
?>