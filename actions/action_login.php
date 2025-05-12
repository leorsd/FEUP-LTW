<?php
declare(strict_types=1);

session_start();

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

// Establish the database connection
$db = getDatabaseConnection(); // Get the PDO object from the function

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Basic validation
  if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Please fill in both fields.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Create a new User object
  $user = new User($db);

  // Set user data for authentication
  $user->setUserData($username, "", "");

  // Check if user exists and verify the password (password is passed directly)
  if ($user->userExists() && $user->verifyPassword($password)) {
    // User is authenticated, store in session
    $_SESSION['username'] = $username;

    // EXPERIMENTAL
    $_SESSION['user_info'] = $user->getUserInfo(); // Store additional user info if needed

    // Set a success message
    $_SESSION['success'] = 'Login successful! Welcome back.';

    // Redirect to home page
    header('Location: ../pages/home.php');
  } else {
    // Authentication failed, set the error and redirect back to login page
    $_SESSION['error'] = 'Invalid username or password.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
  exit();
}

?>

