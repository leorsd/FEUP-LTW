<?php
declare(strict_types=1);

function draw_service_body()
{
    require_once(__DIR__ . '/../includes/db/connection.php');
    require_once(__DIR__ . '/../lib/service.php');
    $db = getDatabaseConnection();
    $serviceId = isset($_GET['id']) ? (int) $_GET['id'] : null;
    $userId = $_SESSION['user_info']['id'] ?? null;
    $hasOrdered = false;
    $orderStatus = null;
    if ($serviceId && $userId) {
        $service = new Service($db);
        $orderInfo = $service->getUserOrderInfo($serviceId, $userId);
        $hasOrdered = $orderInfo['hasOrdered'];
        $orderStatus = $orderInfo['status'];
    }
    ?>
    <main id="service-main">
        <h2>Service Details</h2>
        <div id="service-info">
            <img id="service-image" src="../images/service.png" alt="Service Image">
            <section id="service-atributes">
                <div id="service-title">
                    <!-- Title will be loaded from the database -->
                </div>
                <div id="service-description">
                    <!-- Description will be loaded from the database -->
                </div>
                <div id="service-provider">
                    <!-- Provider will be loaded from the database -->
                </div>
                <div id="service-category">
                    <!-- Category will be loaded from the database -->
                </div>
                <div id="service-price">
                    <!-- Price will be loaded from the database -->
                </div>
                <div id="service-location">
                    <!-- Location will be loaded from the database -->
                </div>
                <div id="service-rating">
                    <!-- Rating will be loaded from the database -->
                </div>
                <div id="service-order">
                    <?php if (isset($_SESSION['user_info']['username']) && !$hasOrdered): ?>
                        <form action="../actions/action_order_service.php" method="POST" style="margin-top:1em;">
                            <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            <button type="submit">Order Service</button>
                        </form>
                    <?php elseif (isset($_SESSION['user_info']['username']) && $hasOrdered): ?>
                                                        <p>Status: <?php echo htmlspecialchars($orderStatus ?? 'Ordered'); ?></p>
                                                <?php else: ?>
                        <p><a href="../pages/login.php">Log in</a> to order this service.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <section id="service-reviews">
            <h2>Reviews</h2>
            <div id="reviews-form">
                <h3>Leave a Review</h3>
                <form id="review-form">
                    <label for="review-text">Review:</label>
                    <textarea id="review-text" name="review-text" required></textarea>
                    <label for="review-rating">Rating:</label>
                    <select id="review-rating" name="review-rating" required>
                        <option value="" disabled selected>Select a rating</option>
                        <option value="1">1 Star</option>
                        <option value="2">2 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="5">5 Stars</option>
                    </select>
                    <button type="submit">Submit Review</button>
                </form>
            </div>
            <div id="reviews-list">
                <!-- Reviews will be loaded from the database -->
            </div>
        </section>
    </main>
    <?php
}
?>

