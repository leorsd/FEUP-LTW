<?php
declare(strict_types=1);

function draw_payment_body()
{
  ?>
  <main>
    <h2>Payment</h2>
    <div id="service-details">
      <h3>Service Details</h3>
      <p>Service Title: <span id="service-title">Example Service</span></p>
      <p>Price: <span id="service-price">$100</span></p>
      <p>Location: <span id="service-location">123 Example St</span></p>
      <p>Description: <span id="service-description">This is an example service description.</span></p>
    </div>
    <?php if (isset($_SESSION['payment_error'])): ?>
      <div class="error-message"><?= htmlspecialchars($_SESSION['payment_error']) ?></div>
      <?php unset($_SESSION['payment_error']); ?>
    <?php endif; ?>
    <form id="payment-form" class="payment-section" action="../actions/action_payment.php" method="POST">
      <input type="hidden" name="service_id" value="<?= htmlspecialchars($_GET['service_id'] ?? '') ?>">
      <h3>Payment Details</h3>
      <label>
        Cardholder Name:
        <input type="text" name="cardholder" required>
      </label>
      <label>
        Card Number:
        <input type="text" name="cardnumber" maxlength="19" pattern="\d{16,19}" required placeholder="1234 5678 9012 3456">
      </label>
      <label>
        Expiry Date:
        <input type="text" name="expiry" maxlength="5" pattern="\d{2}/\d{2}" required placeholder="MM/YY">
      </label>
      <label>
        CVV:
        <input type="password" name="cvv" maxlength="4" pattern="\d{3,4}" required placeholder="123">
      </label>
      <button type="submit">Pay Now</button>
    </form>
  </main>
  <?php
}
?>