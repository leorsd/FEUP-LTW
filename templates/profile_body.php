<?php
declare(strict_types=1);

function draw_profile_body()
{
  $user_info = $_SESSION['user_info'] ?? [];
  $profile_pic = isset($user_info['profile_picture']) ? $user_info['profile_picture'] : 'user.jpg';
  if ($profile_pic === 'user.jpg' || $profile_pic === '') {
    $profile_pic_path = "../images/user.jpg";
  } else {
    $profile_pic_path = "../images/cache/" . htmlspecialchars((string) $profile_pic);
  }

  ?>
  <main id="profile-main">
    <section class="profile-section">
      <div class="profile-top-row">
        <img src="<?php echo $profile_pic_path; ?>" alt="Profile Picture" class="profile-image">
        <div class="profile-username-block">
          <span class="profile-username"><?php echo htmlspecialchars((string) ($user_info['username'] ?? '')); ?></span>
        </div>
      </div>
      <div class="profile-actions-row">
        <a href="edit_profile.php" class="profile-btn">Edit Profile</a>
        <a href="change_password.php" class="change-password-btn">Change Password</a>
      </div>
      <ul class="profile-attributes">
        <li><strong>Email:</strong> <?php echo htmlspecialchars((string) ($user_info['email'] ?? '')); ?></li>
        <li><strong>Phone:</strong> <?php echo htmlspecialchars((string) ($user_info['phone'] ?? '')); ?></li>
        <li><strong>Age:</strong> <?php echo htmlspecialchars((string) ($user_info['age'] ?? '')); ?></li>
        <li><strong>Location:</strong> <?php echo htmlspecialchars((string) ($user_info['location'] ?? '')); ?></li>
        <li><strong>Bio:</strong> <?php echo htmlspecialchars((string) ($user_info['bio'] ?? '')); ?></li>
      </ul>
      <div class="profile-bottom-row">
        <a href="../actions/action_logout.php" class="logout-btn">Logout</a>
      </div>
    </section>
  </main>

  <?php
  // Show all possible flash messages (profile and password)
  if (isset($_SESSION['profile_msg'])) {
    echo '<p style="color:green; text-align:center;">' . $_SESSION['profile_msg'] . '</p>';
    unset($_SESSION['profile_msg']);
  }
  if (isset($_SESSION['profile_error'])) {
    echo '<p style="color:red; text-align:center;">' . $_SESSION['profile_error'] . '</p>';
    unset($_SESSION['profile_error']);
  }
  if (isset($_SESSION['change_password_msg'])) {
    echo '<p style="color:green; text-align:center;">' . $_SESSION['change_password_msg'] . '</p>';
    unset($_SESSION['change_password_msg']);
  }
  if (isset($_SESSION['change_password_error'])) {
    echo '<p style="color:red; text-align:center;">' . $_SESSION['change_password_error'] . '</p>';
    unset($_SESSION['change_password_error']);
  }
}
?>

