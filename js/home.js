document.addEventListener('DOMContentLoaded', () => {
    const servicesList = document.getElementById('services-list');
    const prevPageButton = document.getElementById('prev-page');
    const nextPageButton = document.getElementById('next-page');
    const currentPageSpan = document.getElementById('current-page');

    let currentPage = 1; // Start with page 1
    const limit = 3; // Number of services per page
    const orderby = 'created_at-desc'; // Default ordering

    // Function to fetch and display services
    async function fetchServices(page) {
        try {
            const response = await fetch(`/api/services_api.php?paginated=true&page=${page}&limit=${limit}&orderby=${orderby}`);
            const services = await response.json();

            // Clear the current services list
            servicesList.innerHTML = '';

            // Add the new services to the list
            services.forEach(service => {
                const serviceItem = document.createElement('div');
                serviceItem.classList.add('service-item');
                serviceItem.innerHTML = `
                    <h4>${service.title}</h4>
                    <p>${service.description}</p>
                    <p>Price: $${service.price}</p>
                    <p>Rating: ${service.rating}</p>
                `;
                servicesList.appendChild(serviceItem);
            });

            // Update the current page display
            currentPageSpan.textContent = page;

            // Enable or disable pagination buttons
            prevPageButton.disabled = page === 1;
            nextPageButton.disabled = services.length < limit; // Disable "Next" if fewer than `limit` services are returned
        } catch (error) {
            console.error('Error fetching services:', error);
        }
    }

    // Event listener for the "Previous" button
    prevPageButton.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            fetchServices(currentPage);
        }
    });

    // Event listener for the "Next" button
    nextPageButton.addEventListener('click', () => {
        currentPage++;
        fetchServices(currentPage);
    });

    // Initial fetch for page 1
    fetchServices(currentPage);
});