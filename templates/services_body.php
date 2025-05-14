<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.php');

function draw_services_body()
{
    ?>
    <main>
        <h2> Results for your search </h2>
        <?php draw_services_grid(); ?>
    </main>
    <?php
}
?>