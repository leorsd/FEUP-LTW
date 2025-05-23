<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.php');

function draw_my_services_body()
{
    ?>
    <main>
        <h2 id="title">My Services</h2>
        <div id="my-services-toggle">
            <button id="ordered-services-btn" class="selected">Ordered Services</button>
            <button id="provided-services-btn">Provided Services</button>
        </div>
        <?php draw_services_grid([
            'search_filter' => true,
            'provider_filter' => false, // Not needed for my services
            'status_filter' => true,
            'category_filter' => true,
            'location_filter' => true,
            'price_filter' => true,
            'rating_filter' => true,
            'orderby_filter' => true,
        ]); ?>
        <div id="ordered-services-section">
            <div id="ordered-services-list"></div>
        </div>
        <div id="provided-services-section" class="hide">
            <div id="provided-services-list"></div>
        </div>
    </main>
    <?php
}
?>

