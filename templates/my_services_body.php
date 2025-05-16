<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.php');

function draw_my_services_body()
{
    ?>
    <main>
        <h2 id="title">My Services</h2>
        <?php draw_services_grid(
            [
                'search_filter' => true,
                'provider_filter' => false,
                'status_filter' => true,
                'category_filter' => true,
                'location_filter' => false,
                'price_filter' => true,
                'rating_filter' => true,
                'orderby_filter' => true,
            ]
        ); ?>
    </main>
    <?php
}
?>

