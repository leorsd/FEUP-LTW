<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../templates/change_password_body.php');
require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');

if (!isset($_SESSION['username'])) {
  $_SESSION['error'] = "Please log in to change your password.";
  header('Location: login.php');
  exit();
}

draw_initial_common_header('Change Password');
draw_change_pasword_header();
draw_final_common_header();
draw_common_headbar($_SESSION['user_info']);
draw_change_password_body();
draw_common_footer();
?>
