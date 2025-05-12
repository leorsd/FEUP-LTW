<?php
declare(strict_types=1);

function draw_home_body(array $categories)
{
    ?>
    <section id="create-service-section">
        <h2>Got car skills? Want to make some money?</h2>
        <h2>You are in the right place!</h2>
        <p>Create your service, receive messages from customers, start working and earning money!</p>
        <p>It's that simple!</p>
        <a href="../pages/create_service.php" class="create-service-button">Create a Service</a>
    </section>
    
    <section id="default-searches">
        <ul>
            <?php
            foreach ($categories as $category) {

                if (!isset($category['id']) || !isset($category['name'])) {
                    continue;
                }
                
                $categoryId = htmlspecialchars((string)$category['id']);
                $categoryName = htmlspecialchars($category['name']);
                ?>
                <li>
                    <a href="../pages/category.php?id=<?= $categoryId ?>" class="category-link">
                        <?= $categoryName ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </section>

    <section id="home-services">
        <h3>Recommended Services</h3>

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
?>

