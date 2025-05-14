document.addEventListener('DOMContentLoaded', () => {
    const buttonsContainer = document.getElementById('form-category');

    // Fetch categories from the API
    fetch('../api/categories.php')
        .then(response => response.json())
        .then(categories => {
            categories.forEach(category => {

                const radio_button = document.createElement('input');
                radio_button.type = 'radio';
                radio_button.name = 'service_category';
                radio_button.id = category.id;
                radio_button.value = category.id;
                radio_button.required = true;

                const label = document.createElement('label');
                label.setAttribute('for', category.id);
                label.textContent = category.name;

                buttonsContainer.appendChild(radio_button);
                buttonsContainer.appendChild(label);
            });
        })
        .catch(error => {
            console.error('Error fetching categories:', error);
        });



    const slider = document.getElementById('price-slider');
    const priceInput = document.getElementById('service-price');

    slider.addEventListener('input', () => {
        priceInput.value = slider.value;
    });

    priceInput.addEventListener('input', () => {
        slider.value = priceInput.value;
    });
});