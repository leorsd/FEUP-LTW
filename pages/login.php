<?php
declare(strict_types=1);
session_start(); // Start the session to handle any session variables like errors
require_once(__DIR__ . '/../templates/login_body.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
</head>

<body>

  <h1>Welcome to Our Site</h1>

  <?php
  draw_login_body();
  ?>

</body>

</html>
