<?php
declare(strict_types=1);

function draw_service_body()
{
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
                    <!-- Order actions/status will be rendered by JS -->
                </div>
            </section>
        </div>
        <section id="provider-orders-block" style="display:none;">
            <h2>Orders for this Service</h2>
            <div id="provider-orders-list">
                <!-- Provider order management will be rendered by JS -->
            </div>
        </section>
        <section id="service-reviews">
            <h2>Reviews</h2>
            <div id="reviews-form" style="display:none;">
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

