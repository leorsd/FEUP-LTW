<?php
declare(strict_types=1);

function draw_profile_body()
{
  ?>
  <main id="profile-main">
    <section class="profile-section">
      <div class="profile-top-row">
        <img id="profile-image" src="../images/user.jpg" alt="Profile Picture" class="profile-image">
        <div class="profile-username-block">
          <span class="profile-username" id="profile-username"></span>
        </div>
      </div>
      <div class="profile-actions-row">
        <a href="edit_profile.php" class="profile-btn">Edit Profile</a>
        <a href="change_password.php" class="change-password-btn">Change Password</a>
      </div>
      <ul class="profile-attributes">
        <li><strong>Email:</strong> <span id="profile-email"></span></li>
        <li><strong>Phone:</strong> <span id="profile-phone"></span></li>
        <li><strong>Age:</strong> <span id="profile-age"></span></li>
        <li><strong>Location:</strong> <span id="profile-location"></span></li>
        <li><strong>Bio:</strong> <span id="profile-bio"></span></li>
      </ul>
      <div class="profile-bottom-row">
        <a href="../actions/action_logout.php" class="logout-btn">Logout</a>
      </div>
    </section>
    <div id="profile-messages"></div>
  </main>
  <?php
}
?>