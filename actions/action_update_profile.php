<?php
declare(strict_types=1);
session_start();
require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

$db = getDatabaseConnection();

if (!isset($_SESSION['user_info']['id'])) {
  $_SESSION['profile_error'] = 'You must be logged in.';
  header('Location: ../pages/edit_profile.php');
  exit();
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  $_SESSION['profile_error'] = 'Invalid CSRF token.';
  header('Location: ../pages/edit_profile.php');
  exit();
}

$user_id = $_SESSION['user_info']['id'];
$user_info = $_SESSION['user_info'];
$user = new User($db);

$fields = [];
$allowed = ['phone', 'age', 'location', 'bio', 'email', 'username'];
foreach ($allowed as $field) {
  if (isset($_POST[$field])) {
    $fields[$field] = trim($_POST[$field]);
  }
}

if (isset($fields['email']) && $fields['email'] !== '') {
  if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION['profile_error'] = 'Invalid email address.';
    header('Location: ../pages/edit_profile.php');
    exit();
  }
  if ($fields['email'] !== $user_info['email'] && $user->emailExists($fields['email'], $user_id)) {
    $_SESSION['profile_error'] = 'Email is already in use.';
    header('Location: ../pages/edit_profile.php');
    exit();
  }
}

if (isset($fields['username']) && $fields['username'] !== '') {
  if (!User::validateUsername($fields['username'])) {
    $_SESSION['profile_error'] = 'Invalid username.';
    header('Location: ../pages/edit_profile.php');
    exit();
  }
  if ($fields['username'] !== $user_info['username'] && $user->usernameExists($fields['username'], $user_id)) {
    $_SESSION['profile_error'] = 'Username is already in use.';
    header('Location: ../pages/edit_profile.php');
    exit();
  }
}

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
  $tmp_name = $_FILES['profile_picture']['tmp_name'];
  $name = basename($_FILES['profile_picture']['name']);
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
  if (in_array($ext, $allowed_exts)) {
    $new_name = $_SESSION['user_info']['username'] . '_' . time() . '.' . $ext;
    $dest = __DIR__ . '/../images/cache/' . $new_name;
    if (move_uploaded_file($tmp_name, $dest)) {
      $user->updateProfilePicture($user_id, $new_name);
    }
  }
}

if (!empty($fields['phone']) && !User::validatePhone($fields['phone'])) {
  $_SESSION['profile_error'] = 'Invalid phone number.';
  header('Location: ../pages/edit_profile.php');
  exit();
}

if (isset($fields['age']) && $fields['age'] !== '' && (!is_numeric($fields['age']) || intval($fields['age']) < 13)) {
  $_SESSION['profile_error'] = 'Age must be a number and at least 13.';
  header('Location: ../pages/edit_profile.php');
  exit();
}

if (isset($fields['location']) && strlen($fields['location']) > 100) {
  $_SESSION['profile_error'] = 'Location must be at most 100 characters.';
  header('Location: ../pages/edit_profile.php');
  exit();
}

if (isset($fields['bio']) && strlen($fields['bio']) > 1000) {
  $_SESSION['profile_error'] = 'Bio must be at most 1000 characters.';
  header('Location: ../pages/edit_profile.php');
  exit();
}

if (!empty($fields)) {
  $user->updateProfile($user_id, $fields);
}

$_SESSION['user_info'] = $user->getUserInfo($user_id);
header('Location: ../pages/profile.php');
exit();