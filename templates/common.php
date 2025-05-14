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
    <div class="search-bar">
      <form action="../actions/action_search.php" method="GET">
        <input type="text" name="search" placeholder="Search for a service..." required>
        <button type="submit">Search</button>
      </form>
    </div>
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
  <section id="services-container">

        <button id="open-filters" class="filter-button">Filters</button>

        <div id="services-list">
            <!-- Services will be dynamically loaded here -->
        </div>
        <div id="pagination">
            <button id="prev-page" disabled>Previous</button>
            <span id="current-page">1</span>
            <button id="next-page">Next</button>
        </div>
    </section>
  <?php
}