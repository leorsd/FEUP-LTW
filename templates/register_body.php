<?php
declare(strict_types=1);
function draw_register_body()
{
  ?>
  <form action="../actions/action_register.php" method="POST">
    <h2>Register</h2>
    <?php
    if (isset($_SESSION['error'])) {
      echo '<p style="color:red;">' . $_SESSION['error'] . '</p>';
      unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
      echo '<p style="color:green;">' . $_SESSION['success'] . '</p>';
      unset($_SESSION['success']);
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
      <label for="confirm_password">Confirm Password:</label>
      <input type="password" name="confirm_password" id="confirm_password" required>
    </div>

    <div>
      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required>
    </div>

    <div>
      <label for="phone">Phone:</label>
      <input type="text" name="phone" id="phone" required>
    </div>

    <div>
      <button type="submit">Register</button>
    </div>

    <p>Already have an account? <a href="login.php">Login here</a></p>
  </form>
  <?php
}
?>

