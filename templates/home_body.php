<?php
declare(strict_types=1);

function draw_home_body()
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
      <?php if (isset($_SESSION['username'])): ?>
        <a id="profile-button" href="../pages/profile.php">Profile</a>
      <?php else: ?>
        <a id="register-button" href="../pages/register.php">Sign Up</a>
        <a id="login-button" href="../pages/login.php">Log In</a>
      <?php endif; ?>
    </div>
  </header>
  <main>
    <?php if (!isset($_SESSION['username'])): ?>
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
    <?php else: ?>
      <section id="hire">
        <div class="user-panel">
          <h1 class="panel-title">Service Dashboard</h1>
          <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Here you can quickly access your car
            service needs.</p>
          <div class="quick-links">
            <a href="../pages/my_requests.php" class="panel-btn">My Requests</a>
            <a href="../pages/search_services.php" class="panel-btn">Search Services</a>
            <a href="../pages/new_request.php" class="panel-btn highlight">New Request</a>
          </div>
        </div>
      </section>
      <section id="offer" class="hide">
        <div class="user-panel">
          <h1 class="panel-title">Provider Hub</h1>
          <p>Manage your services, track jobs, and grow your business with CarLink.</p>
          <div class="quick-links">
            <a href="../pages/my_services.php" class="panel-btn">My Services</a>
            <a href="../pages/incoming_jobs.php" class="panel-btn">Incoming Jobs</a>
            <a href="../pages/create_service.php" class="panel-btn highlight">Create Service</a>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </main>
  <?php
}
?>

