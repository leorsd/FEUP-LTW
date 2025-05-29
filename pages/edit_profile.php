<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../templates/edit_profile_body.php');
require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');

if (!isset($_SESSION['user_info']['username'])) {
  $_SESSION['error'] = "Please log in to change your password.";
  header('Location: login.php');
  exit();
}

draw_initial_common_header('CarLink');
draw_edit_profile_header();
draw_final_common_header();
draw_common_headbar($_SESSION['user_info']);
draw_edit_profile_body();
draw_common_footer();
?>
