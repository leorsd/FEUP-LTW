document.addEventListener("DOMContentLoaded", () => {
  const clearFiltersButton = document.getElementById("clear-filters");
  const filterForm = document.getElementById("filter-form");
  const servicesList = document.getElementById("services-list");
  const prevPageButton = document.getElementById("prev-page");
  const nextPageButton = document.getElementById("next-page");
  const pageSpan = document.getElementById("current-page");
  const categoriesList = document.getElementById("categories-list");
  const categoriesOption = document.getElementById("form-categories");

  const urlParams = new URLSearchParams(window.location.search);
  const searchParam = urlParams.get("search");
  if (searchParam) {
    filters.search = searchParam;
  }

  // Use the shared filters object from filters.js
  filters.page = 1;
  filters.per_page = 12;

  function build_api_query() {
    const params = new URLSearchParams({
      orderby: filters.orderby,
      page: filters.page,
      per_page: filters.per_page,
      user_id: CURRENT_USER_ID,
      exclude_provider_id: CURRENT_USER_ID,
    });
    if (filters.search) params.append("search", filters.search);
    if (filters.provider) params.append("provider", filters.provider);
    if (filters.category) params.append("category", filters.category);
    if (filters.location) params.append("location", filters.location);
    if (filters.min_price !== null && filters.min_price !== "")
      params.append("min_price", filters.min_price);
    if (filters.max_price !== null && filters.max_price !== "")
      params.append("max_price", filters.max_price);
    if (filters.min_rating) params.append("min_rating", filters.min_rating);
    if (filters.max_rating) params.append("max_rating", filters.max_rating);
    return `/api/services_get.php?${params.toString()}`;
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
        servicesList.innerHTML = `<div class="no-services">No services found for your search...</div>`;
        pageSpan.textContent = `Page 0 of 0`;
        prevPageButton.disabled = true;
        nextPageButton.disabled = true;
        return;
      }

      services.forEach((service) => {
        service.image = service.image
          ? `../images/cache/${service.image}`
          : "../images/service.png";
        service.provider_image = service.provider_image
          ? `../images/cache/${service.provider_image}`
          : "../images/user.jpg";
        service.rating = service.rating
          ? service.rating.toFixed(1)
          : "Not rated yet";

        const serviceItem = document.createElement("a");
        serviceItem.href = `service.php?id=${service.id}&user_id=${CURRENT_USER_ID}`;
        serviceItem.classList.add("service-item");
        serviceItem.innerHTML = `
          <img src="${service.image}" alt="${
          service.title
        }" class="service-image">
          <h4>${service.title}</h4>
          <p>Description: ${service.description}</p>
          <img src="${
            service.provider_image
          }" alt="profile image" class="provider-image">
          <p>Provider: ${service.provider_username}</p>
          <p>Category: ${service.category_name}</p>
          <p>Location: ${service.location}</p>
          ${
            service.status_name !== undefined
              ? `<p>Status: ${service.status_name}</p>`
              : ""
          }
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
            } catch {
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
        categoriesList.innerHTML = "<li>Error loading categories</li>";
        return;
      }

      categoriesList.innerHTML = "";
      categoriesOption.innerHTML = "";
      categories.forEach((category) => {
        const listItem = document.createElement("li");
        listItem.innerHTML = `
          <button class="category-button" data-category="${category.id}">
            ${category.name}
          </button>
        `;
        categoriesList.appendChild(listItem);

        const option = document.createElement("label");
        option.innerHTML = `
        <input type="checkbox" name="categories" value="${category.id}"> ${category.name}
        `;
        categoriesOption.appendChild(option);
      });

      document.querySelectorAll(".category-button").forEach((btn) => {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          document
            .querySelectorAll(".category-button")
            .forEach((b) => b.classList.remove("selected"));
          this.classList.add("selected");
          filterForm
            .querySelectorAll('input[name="categories"]')
            .forEach((cb) => (cb.checked = false));
          const catId = this.getAttribute("data-category");
          filterForm.querySelector(
            `input[name="categories"][value="${catId}"]`
          ).checked = true;
          filters.category = catId;
          filters.page = 1;
          fetchServices();
        });
      });

      setupFilterListeners(
        filterForm,
        prevPageButton,
        nextPageButton,
        pageSpan,
        fetchServices,
        clearFiltersButton
      );
    } catch {
      categoriesList.innerHTML = "<li>Error loading categories</li>";
    }
  }

  fetchCategories();
  fetchServices();
});
