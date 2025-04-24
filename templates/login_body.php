<?php
declare(strict_types=1);

function draw_login_body()
{
  ?>
  <header>
    <h1 id="login-title">CarLink</h1>
  </header>
  <div id="login-image">
    <img src="../images/login_register.jpg" alt="Login Image" width="50%" height="100%">
  </div>
  <form action="../actions/action_login.php" method="POST" id="login-form">
    <h2>Log in</h2>
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

    <div class="username">
      <label for="username" class="hide">Username:</label>
      <input type="text" name="username" placeholder="Username" required>
    </div>

    <div class="password">
      <label for="password" class="hide">Password:</label>
      <input type="password" name="password" placeholder="Password" required>
    </div>

    <div class="login-button">
      <button type="submit">Log in to CarLink</button>
    </div>

    <div class="bottom-question">
      <p>Don't have an account? <a href="register.php">Sign up here</a></p>
    </div>
  </form>
  <?php
}
?>

