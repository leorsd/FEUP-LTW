<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../templates/register_body.php');
require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/headers.php');

draw_initial_common_header('Register');
draw_register_header();
draw_final_common_header();
draw_register_body();
draw_common_footer();
?>

