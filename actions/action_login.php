<?php
declare(strict_types=1);

session_start();

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

// Establish the database connection
$db = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // CSRF token check
  if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Invalid CSRF token.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Basic validation
  if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Please fill in both fields.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  $user = new User($db);

  // Verify password and get user ID if correct
  $user_id = $user->verifyPassword($username, $password);

  if ($user_id !== null) {
    $_SESSION['user_info'] = $user->getUserInfo($user_id);
    $_SESSION['success'] = 'Login successful! Welcome back.';
    header('Location: ../pages/home.php');
  } else {
    $_SESSION['error'] = 'Invalid username or password.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
  exit();
}
?>
