<?php
declare(strict_types=1);

function draw_welcome_body()
{
  ?>
  <header>
    <div class="left">
      <h2 class="logo">CarLink</h2>
    </div>
    <div class="center">
      <button id="hire-button" class="selected">Hire a Service</button>
      <p id="bar">|</p>
      <button id="offer-button">Offer a Service</button>
    </div>
    <div class="right">
      <a id="register-button" href="../pages/register.php">Sign Up</a>
      <a id="login-button" href="../pages/login.php">Log In</a>
    </div>
  </header>
  <main>
    <section id="hire">
      <h1>Connecting You to the Best Car Services - Hire or Offer with Ease</h1>
      <p>Whether you need a mechanic, a detailer, or any other car service, we have you covered. Our platform connects you
        with trusted service providers in your area.</p>
      <p>Find the perfect service provider for your needs today!</p>
      <a href="../pages/login.php" class="btn">Get Help Now!</a>
    </section>
    <section id="offer" class="hide">
      <h1>Connecting You to the Best Car Services - Hire or Offer with Ease</h1>
      <p>Start earning now by offering your skills to those who need them. Connect with clients and grow your business
        today!</p>
      <p>Find the perfect client for your skills today!</p>
      <a href="../pages/login.php" class="btn">Start Earning Now!</a>
    </section>
  </main>
  <?php
}
?>

