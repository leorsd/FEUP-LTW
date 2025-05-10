<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../templates/edit_profile_body.php');
require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');

if (!isset($_SESSION['username'])) {
  $_SESSION['error'] = "Please log in to edit your profile.";
  header('Location: login.php');
  exit();
}

draw_initial_common_header('Edit Profile');
draw_profile_header();
draw_final_common_header();
draw_edit_profile_body();
draw_common_footer();
?>
