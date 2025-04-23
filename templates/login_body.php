<?php
declare(strict_types=1);

function draw_login_body()
{
  ?>
  <form action="../actions/action_login.php" method="POST">
    <h2>Login</h2>
<?php
  // Display error message if it exists
  if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">' . $_SESSION['error'] . '</p>';
    unset($_SESSION['error']);  // Unset the error message after displaying it
  }

  // Display success message if it exists
  if (isset($_SESSION['success'])) {
    echo '<p style="color:green;">' . $_SESSION['success'] . '</p>';
    unset($_SESSION['success']);  // Unset the success message after displaying it
  }
  ?>

<div>
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" required>
    </div>

    <div>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required>
    </div>

    <div>
      <button type="submit">Login</button>
    </div>

    <p>Don't have an account? <a href="register.php">Sign up here</a></p>
  </form>
  <?php
}
?>

