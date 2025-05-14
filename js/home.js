document.addEventListener("DOMContentLoaded", () => {
  const servicesList = document.getElementById("services-list");
  const prevPageButton = document.getElementById("prev-page");
  const nextPageButton = document.getElementById("next-page");
  const currentPageSpan = document.getElementById("current-page");
  const categoriesList = document.getElementById("categories-list");

  let currentPage = 1; // Start with page 1
  const limit = 12; // Number of services per page
  const orderby = "created_at-desc"; // Default ordering

  // Function to fetch and display services
  async function fetchServices(page) {
    try {
      const response = await fetch(
        `/api/services.php?paginated=true&page=${page}&limit=${limit}&orderby=${orderby}`
      );
      const data = await response.json();
      const services = data.services;
      const total = data.total;
      const totalPages = Math.ceil(total / limit);

      servicesList.innerHTML = "";

      services.forEach((service) => {
        if (!service.image) {
          service.image = "../images/service.png"; // Default image if none provided
        }else {
          service.image = `../images/cache/${service.image}`; // Use the provided image
        }
        const serviceItem = document.createElement("div");
        serviceItem.classList.add("service-item");
        serviceItem.innerHTML = `
                <img src="${service.image}" alt="${service.title}" class="service-image">
                <h4>${service.title}</h4>
                <p>Description: ${service.description}</p>
                <p>Provider: ${service.provider_username}</p>
                <p>Category: ${service.category_name}</p>
                <p>Location: ${service.location}</p>
                <p>Status: ${service.status_name}</p>
                <p>Price: $${service.price}</p>
                <p>Rating: ${service.rating}</p>
            `;
        servicesList.appendChild(serviceItem);
      });

      currentPageSpan.textContent = page;

      prevPageButton.disabled = page === 1;
      nextPageButton.disabled = page >= totalPages;
    } catch (error) {
      console.error("Error fetching services:", error);
    }
  }

  // Fetch categories from the API
  async function fetchCategories() {
    try {
        const response = await fetch("../api/categories.php");
        const categories = await response.json();

        // Check for errors in the response
        if (categories.error) {
            console.error("Error fetching categories:", categories.error);
            categoriesList.innerHTML = "<li>Error loading categories</li>";
            return;
        }

        // Render categories
        categoriesList.innerHTML = ""; // Clear any existing content
        categories.forEach((category) => {
            const listItem = document.createElement("li");
            listItem.innerHTML = `
                <li><a href="../pages/category.php?id=${category.id}" class="category-link">
                    ${category.name}
                </a></li>
            `;
            categoriesList.appendChild(listItem);
        });
    } catch (error) {
        console.error("Error fetching categories:", error);
        categoriesList.innerHTML = "<li>Error loading categories</li>";
    }
}

  // Event listener for the "Previous" button
  prevPageButton.addEventListener("click", () => {
    if (currentPage > 1) {
      currentPage--;
      fetchServices(currentPage);
    }
  });

  // Event listener for the "Next" button
  nextPageButton.addEventListener("click", () => {
    currentPage++;
    fetchServices(currentPage);
  });

  // Initial fetch for page 1
  fetchServices(currentPage);
  fetchCategories();
});
