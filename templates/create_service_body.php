<?php
declare(strict_types=1);
function draw_create_service_body()
{
  ?>
  <main>
    <h2>Create a Service</h2>
    <form id="create-service-form" method="POST" enctype="multipart/form-data" action="../actions/action_create_service.php">
      <div class="form-line">
        <label for="service-title">Service Title:</label>
        <input type="text" id="service-title" name="service_title" required>
      </div>

      <div class="form-line">
        <label for="service-description">Description:</label>
        <textarea id="service-description" name="service_description" required></textarea>
      </div>

      <div class="form-line">
        <label for="service-price">Price:</label>
        <input type="number" id="service-price" name="service_price" required>
      </div>

      <div class="form-line">
        <label for="service-location">Location:</label>
        <input type="text" id="service-location" name="service_location" required>
      </div>

      <div class="form-line">
        <label for="service-category">Category:</label>
        <select id="service-category" name="service_category" required>
          <!-- Categories will be dynamically loaded here -->
        </select>
      </div>

      <div class="form-line">
        <label for="service-image">Service Image:</label>
        <input type="file" id="service-image" name="service_image" required>
      </div>

      <button type="submit">Create Service</button>
    </form>  
  </main>
  <?php
}
?>