<?php
declare(strict_types=1);

function draw_profile_body()
{
  $username = $_SESSION['username'] ?? 'User';
  $user_info = $_SESSION['user_info'] ?? [];
  ?>
  <header>
    <h1>Welcome to your Profile, <?php echo htmlspecialchars($username); ?>!</h1>
    <nav>
      <a href="home.php">Home</a> |
      <a href="../actions/action_logout.php">Logout</a>
      <!-- Optionally add more profile-related links here -->
    </nav>
  </header>
  <main id="profile-mas" <section class="profile-section">
    <h2>Account Details</h2>
    <ul>
      <li>Email: <?php echo htmlspecialchars($user_info['email'] ?? 'Not available'); ?></li>
      <!-- Add more profile/account info as needed -->
    </ul>
    </>
    <section class="profile-section">
      <h2>Recent Activity</h2>
      <p>Here you could display a summary of recent actions, messages, or notifications.</p>
    </section>
    <!-- Add more sections for profile widgets, etc. -->
  </main>
  <?php
}
?>

