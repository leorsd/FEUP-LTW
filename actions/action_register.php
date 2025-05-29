<?php
declare(strict_types=1);
session_start();

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

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
  $confirm_password = $_POST['confirm_password'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];

  if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($phone)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  if ($password !== $confirm_password) {
    $_SESSION['error'] = 'Passwords do not match.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  $user = new User($db);

  if (!User::validatePassword($password)) {
    $_SESSION['error'] = 'Password must be at least 8 characters, contain at least one letter and one number.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  if ($user->usernameExists($username)) {
    $_SESSION['error'] = 'Username is already taken. Please choose another one.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }
  if ($user->emailExists($email)) {
    $_SESSION['error'] = 'Email is already registered. Please use another one.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }
  if ($user->phoneExists($phone)) {
    $_SESSION['error'] = 'Phone number is already registered. Please use another one.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  $user_id = $user->register($username, $email, $phone, $password);
  if ($user_id !== null) {
    $_SESSION['user_info'] = $user->getUserInfo($user_id);
    $_SESSION['success'] = 'Registration successful! Welcome!';
    header('Location: ../pages/home.php');
  } else {
    $_SESSION['error'] = 'An error occurred. Please try again.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }

  exit();
}
?>
