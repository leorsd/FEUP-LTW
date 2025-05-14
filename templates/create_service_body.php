<?php
declare(strict_types=1);
function draw_create_service_body()
{
  ?>
  <main>
    <h2>Create a Service</h2>
    <form id="create-service-form" method="POST" enctype="multipart/form-data" action="../actions/action_create_service.php">
      <div id="form-title">
        <label for="service-title">Service Title</label>
        <input type="text" id="service-title" name="service_title" placeholder="Title" required>
      </div>

      <div id="form-description">
        <label for="service-description">Description</label>
        <textarea id="service-description" name="service_description" placeholder="Description" required></textarea>
      </div>

      <div id="form-location">
        <label for="service-location">Location</label>
        <input type="text" id="service-location" name="service_location" placeholder="Location" required>
      </div>

      <div id="form-price">
        <label for="service-price">Price</label> 
        <input type="number" id="service-price" name="service_price" min="0" max="1000" placeholder="Price" required>
        <input type="range" id="price-slider" min="0" max="1000" value="0">
      </div>

      <div id="form-category">
        <!-- Categories buttons will be dynamically loaded here --> 
      </div>

      <div id="form-image">
        <label for="service-image">Service Image</label>
        <input type="file" id="service-image" name="service_image">
      </div>

      <button type="submit">Create Service</button>
    </form> 
  </main>
  <?php
}
?>