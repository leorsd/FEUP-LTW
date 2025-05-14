document.addEventListener("DOMContentLoaded", () => {
  const servicesList = document.getElementById("services-list");
  const prevPageButton = document.getElementById("prev-page");
  const nextPageButton = document.getElementById("next-page");
  const pageSpan = document.getElementById("current-page");

  let page = 1; // Start with page 1
  const per_page = 12; // Number of services per page
  const orderby = "created_at-desc"; // Default ordering
  const status = "Open"; // Default status


  function buildApiUrl(page, per_page) {
    const params = new URLSearchParams(window.location.search);

    params.set('page', page);
    params.set('per_page', per_page);
    params.set('status', status); // Only access open services

    return `/api/services.php?${params.toString()}`;
  }

  // Function to fetch and display services
  async function fetchServices(page) {
    try {
      const response = await fetch(
        buildApiUrl(page, per_page)
      );
      const data = await response.json();
      const services = data.services;
      const total = data.total;
      const totalPages = Math.ceil(total / per_page);

      servicesList.innerHTML = "";

      if (services.length === 0) {
        const noServicesMsg = document.createElement("div");
        noServicesMsg.className = "no-services";
        noServicesMsg.textContent = "No services found for your search...";
        servicesList.appendChild(noServicesMsg);

        prevPageButton.disabled = true;
        nextPageButton.disabled = true;

        pageSpan.textContent = `Page 0 of 0`;
        return;
      }

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

      pageSpan.textContent = `Page ${page} of ${totalPages}`;

      prevPageButton.disabled = page === 1;
      nextPageButton.disabled = page >= totalPages;
    } catch (error) {
      console.error("Error fetching services:", error);
    }
  }

  prevPageButton.addEventListener("click", () => {
    if (page > 1) {
      page--;
      window.scrollTo(0, 0);
      fetchServices(page);
    }
  });

  nextPageButton.addEventListener("click", () => {
    page++;
    window.scrollTo(0, 0);
    fetchServices(page);
  });

  fetchServices(page);
});