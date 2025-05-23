<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.php');

function draw_my_services_body()
{
    ?>
    <main>
        <h2 id="title">My Services</h2>
        <div id="main-role-toggle">
            <button id="user-section-btn" class="selected">Customer</button>
            <button id="vendor-section-btn">Vendor</button>
        </div>
        <div id="user-section">
            <div class="section-header">
                <h3>My Orders</h3>
            </div>
        </div>
        <div id="vendor-section" class="hide">
            <div id="my-services-toggle">
                <button id="provided-services-btn" class="selected">Services</button>
                <button id="sold-services-btn">Orders</button>
            </div>
        </div>
        <?php draw_services_grid([
            'search_filter' => true,
            'provider_filter' => false,
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
        <div id="sold-services-section" class="hide">
            <div id="sold-services-list"></div>
        </div>
    </main>
    <?php
}
?>

