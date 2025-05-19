<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.php');

function draw_favorites_body()
{
    ?>
    <main>
        <h2 id="title">Favorites</h2>
        <?php draw_services_grid(
            [
                'search_filter' => true,
                'provider_filter' => true,
                'status_filter' => false,
                'category_filter' => true,
                'location_filter' => true,
                'price_filter' => true,
                'rating_filter' => true,
                'orderby_filter' => true,
            ]
        ); ?>
    </main>
    <?php
}
?>

