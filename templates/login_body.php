<?php
declare(strict_types=1);

function draw_login_body()
{
  ?>
  <header>
    <h1 id="login-title"><a href="../pages/welcome.php">CarLink</a></h1>
  </header>
  <div id="login-background"></div>
  <form action="../actions/action_login.php" method="POST" id="login-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <h2>Log In</h2>
    <div class="username">
      <label for="username" class="hide">Username:</label>
      <input type="text" name="username" placeholder="Username" required>
    </div>

    <div class="password">
      <label for="password" class="hide">Password:</label>
      <input type="password" name="password" placeholder="Password" required>
    </div>

    <div class="login-button">
      <button type="submit">Log In to CarLink</button>
    </div>

    <div class="bottom-question">
      <p>Don't have an account? <a href="register.php">Sign up here</a></p>
    </div>
  </form>
  <?php
}
?>

