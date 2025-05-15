<?php
declare(strict_types=1);

function draw_initial_common_header(string $title)
{
  ?>
  <!DOCTYPE html>
  <html lang="en">
  
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
<?php }

function draw_final_common_header()
{
  ?>
  </head>
  <body>
<?php
}

function draw_common_footer()
{
  ?>
      <footer>
        <p>&copy; CarLink 2025 - LTWT2G2 - All Rights Reserved. Made by Arnaldo Lopes, António Braga e Leandro Resende</p>
      </footer>
    </body>
  </html>
  <?php
}

function draw_common_headbar($user_info)
{
  $profile_pic = $user_info['profile_picture'];
  if (!isset($profile_pic) || $profile_pic === '') {
    $profile_pic_path = "../images/user.jpg";
  } else {
    $profile_pic_path = "../images/cache/" . htmlspecialchars((string) $profile_pic);
  }
  ?>
  
  <header>
    <h1 id="logo"><a href="../pages/home.php">CarLink</a></h1>
    <form id="search-bar">
      <input type="text" id="search-input" name="search" placeholder="Search for a service..." required>
      <button type="submit">Search</button>
    </form>
    <nav>
      <ul>
        <li><a href="../pages/home.php">Home</a></li>
        <li><a href="../pages/favorites.php">Favorites</a></li>
        <li><a href="../pages/my_services.php">Services</a></li>
        <li><a href="../pages/profile.php"><img src="<?php echo $profile_pic_path; ?>" alt="Profile Picture" class="profile-avatar"></a></li>
      </ul>
    </nav>
  </header>
  <?php
}


function draw_services_grid()
{
  ?>
  <div id="services-grid-filters">
    <div id="filters">
      <h3>Filters</h3>
      <form id="filter-form">
        <label for="category">Category</label>
        <div id="form-categories">
          <!-- Categories will be dynamically loaded here -->
        </div>
        <label for="location">Location</label>
        <input type="text" id="location" name="location" placeholder="Enter location">
        <label for="provider">Provider</label>
        <input type="text" id="provider" name="provider" placeholder="Enter provider username">
        <label for="price-range">Price</label>
        <div id="price-range-boxes">
          <input type="number" id="min-price" name="min-price" min="0" max="1000" step="1" placeholder="Min price">
          <input type="number" id="max-price" name="max-price" min="0" max="1000" step="1" placeholder="Max price">
        </div>
        <label for="rating-range">Rating</label>
        <div id="rating-range-boxes">
          <input type="number" id="min-rating" name="min-rating" min="0" max="5" step="0.1" placeholder="Min rating">
          <input type="number" id="max-rating" name="max-rating" min="0" max="5" step="0.1" placeholder="Max rating">
        </div>
        <label for="sort-by">Order By</label>
        <select id="order-by" name="order-by">
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="rating-asc">Rating: Low to High</option>
          <option value="rating-desc">Rating: High to Low</option>
          <option value="created_at-asc">Date: Oldest First</option>
          <option value="created_at-desc" selected>Date: Newest First</option>
        </select>
        <button type="submit">Apply Filters</button>
      </form>
      <button id="clear-filters">Clear Filters</button>
    </div>
    <div id="services-container">
      <div id="services-list">
          <!-- Services will be dynamically loaded here -->
      </div>
      <div id="pagination">
          <button id="prev-page" disabled>Previous</button>
          <span id="current-page">1</span>
          <button id="next-page">Next</button>
      </div>
    </div>
  </div>
  <?php
}