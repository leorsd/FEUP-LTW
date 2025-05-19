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

$user = new User($db);
$user->setUserData($_SESSION['user_info']['username'], '', '');

if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_new_password'])) {
  if ($_POST['new_password'] === $_POST['confirm_new_password']) {
    if ($user->updatePassword($_POST['current_password'], $_POST['new_password'])) {
      $_SESSION['change_password_msg'] = 'Password updated successfully!';
    } else {
      $_SESSION['change_password_error'] = 'Current password is incorrect.';
    }
  } else {
    $_SESSION['change_password_error'] = 'New passwords do not match.';
  }
}

header('Location: ../pages/profile.php');
exit();
?>
