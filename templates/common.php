<?php
declare(strict_types=1);

function draw_initial_common_header(string $title)
{
  $user_id = $_SESSION['user_info']['id'] ?? null;
  ?>
  <!DOCTYPE html>
  <html lang="en">
  
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <script>
      const CURRENT_USER_ID = <?= json_encode($user_id) ?>; // This will be used in the JavaScript code to identify the current user
    </script>
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
        <li><a href="../pages/my_services.php?">My Services</a></li>
        <li><a href="../pages/profile.php"><img src="<?php echo $profile_pic_path; ?>" alt="Profile Picture" class="profile-avatar"></a></li>
      </ul>
    </nav>
  </header>
  <?php
}

function draw_services_grid(array $options = []) {
  $search_filter    = $options['search_filter']    ?? true;
  $provider_filter  = $options['provider_filter']  ?? true;
  $status_filter    = $options['status_filter']    ?? true;
  $category_filter  = $options['category_filter']  ?? true;
  $location_filter  = $options['location_filter']  ?? true;
  $price_filter     = $options['price_filter']     ?? true;
  $rating_filter    = $options['rating_filter']    ?? true;
  $orderby_filter   = $options['orderby_filter']   ?? true;
?>
  <div id="services-grid-filters">
    <div id="filters">
      <h3>Filters</h3>
      <form id="filter-form">
        <?php if ($search_filter): ?>
          <label for="search">Search</label>
          <input type="text" name="search" placeholder="Search my services..." />
        <?php endif; ?>
        <?php if ($category_filter): ?>
          <label for="category">Category</label>
          <div id="form-categories" name="category"></div>
        <?php endif; ?>
        <?php if ($location_filter): ?>
          <label for="location">Location</label>
          <input type="text" id="location" name="location" placeholder="Enter location">
        <?php endif; ?>
        <?php if ($provider_filter): ?>
          <label for="provider">Provider</label>
          <input type="text" id="provider" name="provider" placeholder="Enter provider username">
        <?php endif; ?>
        <?php if ($status_filter): ?>
          <label for="status" id="form-statuses-title">Status</label>
          <div id="form-statuses" name="status"></div>
        <?php endif; ?>
        <?php if ($price_filter): ?>
          <label for="price-range">Price</label>
          <div id="price-range-boxes">
            <div id="min-price-row">
              <input type="number" id="min-price-number" name="min-price-number" min="0" max="1000" step="1" placeholder="Min Price">
              <input type="range" id="min-price" name="min-price" min="0" max="1000" step="1" value="0">
            </div>
            <div id="max-price-row">
              <input type="number" id="max-price-number" name="max-price-number" min="0" max="1000" step="1" placeholder="Max Price">
              <input type="range" id="max-price" name="max-price" min="0" max="1000" step="1" value="1000">
            </div>
          </div>
        <?php endif; ?>
        <?php if ($rating_filter): ?>
          <label for="rating-range">Rating</label>
          <div id="rating-range-boxes">
            <div id="min-rating-row">
              <input type="number" id="min-rating-number" name="min-rating-number" min="1" max="5" step="0.1" placeholder="Min Rating">
              <input type="range" id="min-rating" name="min-rating" min="1" max="5" step="0.1" value="1">
            </div>
            <div id="max-rating-row">
              <input type="number" id="max-rating-number" name="max-rating-number" min="1" max="5" step="0.1" placeholder="Max Rating">
              <input type="range" id="max-rating" name="max-rating" min="1" max="5" step="0.1" value="5">
            </div>
          </div>
        <?php endif; ?>
        <?php if ($orderby_filter): ?>
          <label for="sort-by">Order By</label>
          <select id="order-by" name="order-by">
            <option value="price-asc">Price: Low to High</option>
            <option value="price-desc">Price: High to Low</option>
            <option value="rating-asc">Rating: Low to High</option>
            <option value="rating-desc">Rating: High to Low</option>
            <option value="created_at-asc">Date: Oldest First</option>
            <option value="created_at-desc" selected>Date: Newest First</option>
          </select>
        <?php endif; ?>
      </form>
      <button id="clear-filters">Clear Filters</button>
    </div>
    <div id="services-container">
      <div id="services-list"></div>
      <div id="pagination">
        <button id="prev-page" disabled>Previous</button>
        <span id="current-page">1</span>
        <button id="next-page">Next</button>
      </div>
    </div>
  </div>
<?php
}