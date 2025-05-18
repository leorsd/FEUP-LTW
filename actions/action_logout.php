<?php
declare(strict_types=1);

session_start();

// Unset all session variables
$_SESSION = [];

session_destroy();

header('Location: ../pages/welcome.php');

exit();
?>

