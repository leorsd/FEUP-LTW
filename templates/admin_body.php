<?php
// filepath: /home/leandro/Uni/LTW/Project/templates/admin_body.php
declare(strict_types=1);

function draw_admin_body()
{
    ?>
    <main>
        <h2>Admin Panel</h2>

        <section>
            <h3>Categories</h3>
            <form id="admin-add-category-form" style="margin-bottom:1em;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="text" name="category_name" placeholder="New category name" required>
                <button type="submit">Add Category</button>
            </form>
            <ul id="admin-categories-list"></ul>
        </section>

        <section>
            <h3>Services</h3>
            <input type="text" id="admin-service-search" placeholder="Search services by title..." style="margin-bottom:1em;">
            <ul id="admin-services-list"></ul>
        </section>

        <section>
            <h3>Users</h3>
            <input type="text" id="admin-user-search" placeholder="Search users by username..." style="margin-bottom:1em;">
            <ul id="admin-users-list"></ul>
        </section>
    </main>
    <?php
}
?>