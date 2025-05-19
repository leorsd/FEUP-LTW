<?php
declare(strict_types=1);
session_start();

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

// Establish the database connection
$db = getDatabaseConnection(); // Get the PDO object from the function

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];

  // Basic validation
  if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($phone)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Check if passwords match
  if ($password !== $confirm_password) {
    $_SESSION['error'] = 'Passwords do not match.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Create a new User object
  $user = new User($db);

  // Set user data
  $user->setUserData($username, $email, $phone);

  if ($user->userExists()) {
    $_SESSION['error'] = 'Username is already taken. Please choose another one.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Check if email is already registered
  if ($user->emailExists()) {
    $_SESSION['error'] = 'Email is already registered. Please use another one.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Check if phone number is already registered
  if ($user->phoneExists()) {
    $_SESSION['error'] = 'Phone number is already registered. Please use another one.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Register user (password is passed directly)
  if ($user->register($password)) {
    // Auto-login after registration
    $_SESSION['user_info'] = $user->getUserInfo();
    $_SESSION['success'] = 'Registration successful! Welcome!';
    header('Location: ../pages/home.php');
  } else {
    $_SESSION['error'] = 'An error occurred. Please try again.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }

  exit();
}

?>

