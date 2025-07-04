<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.php');

function draw_home_body()
{
    ?>
    <section id="create-service-section">
        <div id="create-service-text">
            <h2>Got car skills? Want to make some money?</h2>
            <h2>You are in the right place!</h2>
        </div>
        <a href="../pages/create_service.php" class="create-service-button">Create a Service</a>
    </section>
    
    <section id="default-searches">
        <ul id=categories-list>
            <!-- Categories will be dynamically loaded here -->
        </ul>
    </section>
    <section id="main-content">
        <h2 id="search-title">Services</h2>
        <?php draw_services_grid(
            [
                'search_filter' => false,
                'provider_filter' => true,
                'status_filter' => false,
                'category_filter' => true,
                'location_filter' => true,
                'price_filter' => true,
                'rating_filter' => true,
                'orderby_filter' => true,
            ]
        ); ?>
    </section>
    <?php
}
?>

