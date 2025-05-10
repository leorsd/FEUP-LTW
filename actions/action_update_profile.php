<?php
declare(strict_types=1);
session_start();

require_once(__DIR__ . '/../includes/db/connection.php');
require_once(__DIR__ . '/../lib/user.php');

$db = getDatabaseConnection();

if (!isset($_SESSION['username'])) {
  $_SESSION['profile_error'] = 'You must be logged in.';
  header('Location: ../pages/profile.php');
  exit();
}

$user = new User($db);
$user->setUserData($_SESSION['username'], '', '');

$fields = [];
// $allowed = ['email', 'phone', 'age', 'location', 'bio'];
$allowed = ['phone', 'age', 'location', 'bio'];
foreach ($allowed as $field) {
  if (isset($_POST[$field])) {
    $fields[$field] = trim($_POST[$field]);
  }
}

// Profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
  $tmp_name = $_FILES['profile_picture']['tmp_name'];
  $name = basename($_FILES['profile_picture']['name']);
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
  if (in_array($ext, $allowed_exts)) {
    $new_name = $_SESSION['username'] . '_' . time() . '.' . $ext;
    $dest = __DIR__ . '/../images/cache/' . $new_name;
    if (move_uploaded_file($tmp_name, $dest)) {
      $user->updateProfilePicture($new_name);
    }
  }
}

// Update profile fields
if (!empty($fields)) {
  $user->updateProfile($fields);
}

// Refresh session user info
$_SESSION['user_info'] = $user->getUserInfo();
if (!isset($_SESSION['profile_msg']) && !isset($_SESSION['profile_error'])) {
  $_SESSION['profile_msg'] = 'Profile updated successfully!';
}

header('Location: ../pages/profile.php');
exit();
?>

