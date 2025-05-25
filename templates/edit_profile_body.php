<?php
declare(strict_types=1);
function draw_edit_profile_body()
{
  $user_info = $_SESSION['user_info'] ?? [];
  ?>
  <main id="profile-main">
    <h2 class="profile-title">Edit Profile</h2>
    <section class="profile-section">
      <?php if (isset($_SESSION['profile_msg'])): ?>
        <p style="color:green; text-align:center;"> <?php echo $_SESSION['profile_msg']; unset($_SESSION['profile_msg']); ?> </p>
      <?php elseif (isset($_SESSION['profile_error'])): ?>
        <p style="color:red; text-align:center;"> <?php echo $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?> </p>
      <?php endif; ?>
      <form action="../actions/action_update_profile.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">
        <!-- <div class="edit-profile-row">
          <label>Email:</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars((string)($user_info['email'] ?? '')); ?>" required>
        </div> -->
        <div class="edit-profile-row">
          <label>Phone:</label>
          <input type="text" name="phone" value="<?php echo htmlspecialchars((string)($user_info['phone'] ?? '')); ?>">
        </div>
        <div class="edit-profile-row">
          <label>Age:</label>
          <input type="number" name="age" min="0" value="<?php echo isset($user_info['age']) ? htmlspecialchars((string)$user_info['age']) : ''; ?>">
        </div>
        <div class="edit-profile-row">
          <label>Location:</label>
          <input type="text" name="location" value="<?php echo htmlspecialchars((string)($user_info['location'] ?? '')); ?>">
        </div>
        <div class="edit-profile-row">
          <label>Bio:</label>
          <textarea name="bio"><?php echo htmlspecialchars((string)($user_info['bio'] ?? '')); ?></textarea>
        </div>
        <div class="edit-profile-row">
          <label>Profile Picture:</label>
          <input type="file" name="profile_picture" accept="image/*">
        </div>
        <button type="submit" class="profile-btn">Update Profile</button>
        <a href="profile.php">Back to Profile</a>
      </form>
    </section>
  </main>
  <?php
}
?>
