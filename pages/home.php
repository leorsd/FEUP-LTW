<?php
declare(strict_types=1);
session_start(); // Start the session if needed for user state

require_once(__DIR__ . '/../templates/home_page.php');
require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');

draw_initial_common_header('Home');
draw_home_header();
draw_final_common_header();
draw_home_body();
draw_common_footer();
?>