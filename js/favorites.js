document.addEventListener("DOMContentLoaded", () => {
  const clearFiltersButton = document.getElementById("clear-filters");
  const filterForm = document.getElementById("filter-form");
  const servicesList = document.getElementById("services-list");
  const prevPageButton = document.getElementById("prev-page");
  const nextPageButton = document.getElementById("next-page");
  const pageSpan = document.getElementById("current-page");
  const categoriesOption = document.getElementById("form-categories");

  filters.page = 1;
  filters.per_page = 12;
  filters.favorites_owner = CURRENT_USER_ID;

  function build_api_query() {
    const params = new URLSearchParams({
      favorites_owner: filters.favorites_owner,
      page: filters.page,
      per_page: filters.per_page,
      orderby: filters.orderby,
      user_id: CURRENT_USER_ID,
    });
    if (filters.search) params.append("search", filters.search);
    if (filters.category) params.append("category", filters.category);
    if (filters.provider) params.append("provider", filters.provider);
    if (filters.location) params.append("location", filters.location);
    if (filters.min_price !== null && filters.min_price !== "") params.append("min_price", filters.min_price);
    if (filters.max_price !== null && filters.max_price !== "") params.append("max_price", filters.max_price);
    if (filters.min_rating) params.append("min_rating", filters.min_rating);
    if (filters.max_rating) params.append("max_rating", filters.max_rating);
    return `/api/services_favorites.php?${params.toString()}`;
  }

  async function fetchServices() {
    try {
      const response = await fetch(build_api_query());
      const data = await response.json();
      const services = data.services || [];
      const total = data.total || 0;
      const totalPages = Math.ceil(total / filters.per_page);

      servicesList.innerHTML = "";

      if (services.length === 0) {
        const noServicesMsg = document.createElement("div");
        noServicesMsg.className = "no-services";
        noServicesMsg.textContent = "No services found for your search...";
        servicesList.appendChild(noServicesMsg);

        pageSpan.textContent = `Page 0 of 0`;
        prevPageButton.disabled = true;
        nextPageButton.disabled = true;
        return;
      }

      services.forEach((service) => {
        service.image = service.image ? `../images/cache/${service.image}` : "../images/service.png";
        service.provider_image = service.provider_image ? `../images/cache/${service.provider_image}` : "../images/user.jpg";
        service.rating = service.rating ? service.rating.toFixed(1) : "Not rated yet";

        const serviceItem = document.createElement("a");
        serviceItem.href = `service.php?id=${service.id}`;
        serviceItem.classList.add("service-item");
        serviceItem.innerHTML = `
          <img src="${service.image}" alt="${service.title}" class="service-image">
          <h4>${service.title}</h4>
          <p>Description: ${service.description}</p>
          <img src="${service.provider_image}" alt="profile image" class="provider-image">
          <p>Provider: ${service.provider_username}</p>
          <p>Category: ${service.category_name}</p>
          <p>Location: ${service.location}</p>
          <p>Price: $${service.price}</p>
          <p>Rating: ${service.rating}</p>
        `;

        if (service.creator_id !== CURRENT_USER_ID) {
          const favBtn = document.createElement("button");
          favBtn.className = "favorite-btn";
          favBtn.dataset.serviceId = service.id;
          favBtn.textContent = service.is_favorite
            ? "💔 Remove from Favorites"
            : "❤️ Add to Favorites";
          serviceItem.appendChild(favBtn);

          favBtn.addEventListener("click", async function (e) {
            e.preventDefault();
            e.stopPropagation();
            try {
              const response = await fetch("../api/favorites.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  user_id: CURRENT_USER_ID,
                  service_id: service.id,
                }),
              });
              const result = await response.json();
              favBtn.textContent = result.favorited
                ? "💔 Remove from Favorites"
                : "❤️ Add to Favorites";
              fetchServices();
            } catch (error) {
              alert("Failed to toggle favorite.");
            }
          });

          const contactBtn = document.createElement("button");
          contactBtn.className = "contact-btn";
          contactBtn.textContent = "Contact Provider";
          serviceItem.appendChild(contactBtn);

          contactBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = `chat.php?user=${service.creator_id}`;
          });
        }

        servicesList.appendChild(serviceItem);
      });

      pageSpan.textContent = `Page ${filters.page} of ${totalPages}`;
      prevPageButton.disabled = filters.page === 1;
      nextPageButton.disabled = filters.page >= totalPages;
    } catch (error) {
      servicesList.innerHTML = `<div class="no-services">Error fetching services.</div>`;
    }
  }

  async function fetchCategories() {
    try {
      const response = await fetch("../api/categories.php");
      const categories = await response.json();

      if (categories.error) {
        categoriesOption.innerHTML = "<li>Error loading categories</li>";
        return;
      }

      categoriesOption.innerHTML = "";
      categories.forEach((category) => {
        const option = document.createElement("label");
        option.innerHTML = `
          <input type="checkbox" name="categories" value="${category.id}"> ${category.name}
        `;
        categoriesOption.appendChild(option);
      });

      setupFilterListeners(
        filterForm,
        prevPageButton,
        nextPageButton,
        pageSpan,
        fetchServices,
        clearFiltersButton
      );

    } catch (error) {
      categoriesOption.innerHTML = "<li>Error loading categories</li>";
    }
  }

  fetchCategories();
  fetchServices();
});