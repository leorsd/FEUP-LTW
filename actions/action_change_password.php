<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

$db = getDatabaseConnection();

if (!isset($_SESSION['user_info']['username'])) {
  $_SESSION['change_password_error'] = 'You must be logged in.';
  header('Location: ../pages/change_password.php');
  exit();
}
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  $_SESSION['change_password_error'] = 'Invalid CSRF token.';
  header('Location: ../pages/change_password.php');
  exit();
}

$user = new User($db);
$user_id = $_SESSION['user_info']['id'];

if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_new_password'])) {
  if (!$user->verifyPasswordId($user_id, $_POST['current_password'])) {
    $_SESSION['change_password_error'] = 'Current password is incorrect.';
    header('Location: ../pages/change_password.php');
    exit();
  }

  if ($_POST['new_password'] !== $_POST['confirm_new_password']) {
    $_SESSION['change_password_error'] = 'New passwords do not match.';
    header('Location: ../pages/change_password.php');
    exit();
  }

  if (!User::validatePassword($_POST['new_password'])) {
    $_SESSION['change_password_error'] = 'Password must be at least 8 characters, contain at least one letter and one number.';
    header('Location: ../pages/change_password.php');
    exit();
  }

  if ($user->updatePassword($user_id, $_POST['current_password'], $_POST['new_password'])) {
    $_SESSION['change_password_msg'] = 'Password updated successfully!';
    header('Location: ../pages/profile.php');
    exit();
  } else {
    $_SESSION['change_password_error'] = 'Failed to update password. Please try again.';
    header('Location: ../pages/change_password.php');
    exit();
  }
} else {
  $_SESSION['change_password_error'] = 'Please fill in all fields.';
  header('Location: ../pages/change_password.php');
  exit();
}
?>