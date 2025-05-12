document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('service-category');

    // Fetch categories from the API
    fetch('../api/categories.php')
        .then(response => response.json())
        .then(categories => {
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching categories:', error);
        });
});