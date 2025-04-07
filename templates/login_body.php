<?php
function draw_login_body()
{
  ?>
  <section class="">
    <h2>Login</h2>
    <form action="actions/action_login.php" method="post">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Login</button>
    </form>
  </section>
  <?php
}
?>

