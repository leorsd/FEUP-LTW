<?php
declare(strict_types=1);


function draw_register_body()
{
  ?>
  <header>
    <h1 id="register-title">CarLink</h1>
  </header>
  <div id="register-image">
    <img src="../images/login_register.jpg" alt="Register Image" width="50%" height="100%">
  </div>
  <form action="../actions/action_register.php" method="POST" id="register-form">
    <h2>Sign up</h2>
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

    <div class="username">
      <label for="username" class="hide">Username:</label>
      <input type="text" name="username" placeholder="Username" required>
    </div>

    <div class="password">
      <label for="password" class="hide">Password:</label>
      <input type="password" name="password" placeholder="Password" required>
    </div>

    <div class="confirm-password">
      <label for="confirm_password" class="hide">Confirm Password:</label>
      <input type="password" name="confirm_password" placeholder="Confirm password" required>
    </div>

    <div class="email">
      <label for="email" class="hide">Email:</label>
      <input type="email" name="email" placeholder="Email" required>
    </div>

    <div class="phone">
      <label for="phone" class="hide">Phone:</label>
      <input type="text" name="phone" placeholder="Phone number" required>
    </div>

    <div class="register-button">
      <button type="submit">Register in CarLink</button>
    </div>

    <div class="botttom-question">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>  
  </form>
  <?php
}
?>

