<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../templates/register_body.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
</head>

<body>

  <h1>Register a New Account</h1>

  <?php
  draw_register_body();
  ?>

</body>

</html>
