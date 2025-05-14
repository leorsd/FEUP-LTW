<?php
declare(strict_types=1);


function draw_register_body()
{
  ?>
  <header>
    <h1 id="register-title"><a href="../pages/welcome.php">CarLink</a></h1>
  </header>
  <div id="register-background"></div>
  <form action="../actions/action_register.php" method="POST" id="register-form">
    <h2>Sign Up</h2>
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
      <button type="submit">Sign Up for CarLink</button>
    </div>

    <div class="bottom-question">
      <p>Already have an account? <a href="login.php">Log in here</a></p>
    </div>  
  </form>
  <?php
}
?>

