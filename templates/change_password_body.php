<?php
declare(strict_types=1);
function draw_change_password_body()
{
  ?>
  <main id="profile-main">
    <h2 class="profile-title">Change Password</h2>
    <section class="profile-section">
      <?php if (isset($_SESSION['change_password_msg'])): ?>
        <p style="color:green; text-align:center;"> <?php echo $_SESSION['change_password_msg']; unset($_SESSION['change_password_msg']); ?> </p>
      <?php elseif (isset($_SESSION['change_password_error'])): ?>
        <p style="color:red; text-align:center;"> <?php echo $_SESSION['change_password_error']; unset($_SESSION['change_password_error']); ?> </p>
      <?php endif; ?>
      <form action="../actions/action_change_password.php" method="POST" class="profile-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
        <label>
          Current Password:
          <input type="password" name="current_password" required>
        </label>
        <label>
          New Password:
          <input type="password" name="new_password" required>
        </label>
        <label>
          Confirm New Password:
          <input type="password" name="confirm_new_password" required>
        </label>
        <button type="submit" class="profile-btn">Change Password</button>
        <a href="profile.php">Back to Profile</a>
      </form>
    </section>
  </main>
  <?php
}
?>
