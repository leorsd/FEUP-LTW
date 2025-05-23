document.addEventListener("DOMContentLoaded", () => {
  const searchForm = document.getElementById("search-bar");
  const clearFiltersButton = document.getElementById("clear-filters");
  const filterForm = document.getElementById("filter-form");
  const servicesList = document.getElementById("services-list");
  const prevPageButton = document.getElementById("prev-page");
  const nextPageButton = document.getElementById("next-page");
  const pageSpan = document.getElementById("current-page");
  const categoriesList = document.getElementById("categories-list");
  const categoriesOption = document.getElementById("form-categories");

  let page = 1; // Start with page 1
  const per_page = 12; // Number of services per page

  const urlParams = new URLSearchParams(window.location.search);

  let search = urlParams.get("search") || null;
  let provider = urlParams.get("provider") || null;
  let category = urlParams.get("category") || null;
  let location = urlParams.get("location") || null;
  let min_price = urlParams.get("min_price") || null;
  let max_price = urlParams.get("max_price") || null;
  let min_rating = urlParams.get("min_rating") || null;
  let max_rating = urlParams.get("max_rating") || null;
  let orderby = urlParams.get("orderby") || "created_at-desc";

  // Remove status from default query for home page
  function build_api_query() {
    let query = `/api/services.php?orderby=${orderby}&page=${page}&per_page=${per_page}&user_id=${CURRENT_USER_ID}`;

    if (search) {
      query += `&search=${encodeURIComponent(search)}`;
    }
    if (provider) {
      query += `&provider=${encodeURIComponent(provider)}`;
    }
    if (category) {
      query += `&category=${encodeURIComponent(category)}`;
    }
    if (location) {
      query += `&location=${encodeURIComponent(location)}`;
    }
    if (min_price !== null && min_price !== "" && min_price !== undefined) {
      query += `&min_price=${encodeURIComponent(min_price)}`;
    }
    if (max_price !== null && max_price !== "" && max_price !== undefined) {
      query += `&max_price=${encodeURIComponent(max_price)}`;
    }
    if (min_rating) {
      query += `&min_rating=${encodeURIComponent(min_rating)}`;
    }
    if (max_rating) {
      query += `&max_rating=${encodeURIComponent(max_rating)}`;
    }
    // Exclude services where provider is the current user
    query += `&exclude_provider_id=${CURRENT_USER_ID}`;
    return query;
  }

  async function fetchServices() {
    try {
      const response = await fetch(`${build_api_query()}`);

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

        pageSpan.textContent = `Page 0 of 0`;
        prevPageButton.disabled = true;
        nextPageButton.disabled = true;
        return;
      }

      services.forEach((service) => {
        if (!service.image) {
          service.image = "../images/service.png";
        } else {
          service.image = `../images/cache/${service.image}`;
        }

        if (!service.provider_image) {
          service.provider_image = "../images/user.jpg";
        } else {
          service.provider_image = `../images/cache/${service.provider_image}`;
        }

        if (!service.rating) {
          service.rating = "Not rated yet";
        } else {
          service.rating = service.rating.toFixed(1);
        }

        const serviceItem = document.createElement("a");
        serviceItem.href = `service.php?id=${service.id}`;
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
              if (result.favorited) {
                favBtn.textContent = "💔 Remove from Favorites";
                fetchServices();
              } else {
                favBtn.textContent = "❤️ Add to Favorites";
                fetchServices();
              }
            } catch (error) {
              alert("Failed to toggle favorite.");
            }
          });
        }

        servicesList.appendChild(serviceItem);
      });

      pageSpan.textContent = `Page ${page} of ${totalPages}`;

      prevPageButton.disabled = page === 1;
      nextPageButton.disabled = page >= totalPages;
    } catch (error) {
      console.error("Error fetching services:", error);
    }
  }

  async function fetchCategories() {
    try {
      const response = await fetch("../api/categories.php");
      const categories = await response.json();

      if (categories.error) {
        console.error("Error fetching categories:", categories.error);
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

          filterForm
            .querySelectorAll('input[name="categories"]')
            .forEach((cb) => (cb.checked = false));
          const catId = this.getAttribute("data-category");
          filterForm.querySelector(
            `input[name="categories"][value="${catId}"]`
          ).checked = true;
          category = catId;
          page = 1;
          fetchServices();
        });
      });

      setupLiveFilters();
    } catch (error) {
      console.error("Error fetching categories:", error);
      categoriesList.innerHTML = "<li>Error loading categories</li>";
    }
  }

  function setupLiveFilters() {
    searchForm.elements["search"].addEventListener("input", (e) => {
      search = e.target.value || null;
      page = 1;
      fetchServices();
    });

    // Category checkboxes
    filterForm.querySelectorAll('input[name="categories"]').forEach((cb) => {
      cb.addEventListener("change", () => {
        category = Array.from(
          filterForm.querySelectorAll('input[name="categories"]:checked')
        )
          .map((cb) => cb.value)
          .join(",");
        if (category === "") category = null;
        page = 1;
        fetchServices();
      });
    });

    // Provider input
    filterForm.elements["provider"].addEventListener("input", (e) => {
      provider = e.target.value || null;
      page = 1;
      fetchServices();
    });

    // Location input
    filterForm.elements["location"].addEventListener("input", (e) => {
      location = e.target.value || null;
      page = 1;
      fetchServices();
    });

    // --- Price Range Sync ---
    const minPriceBar = filterForm.elements["min-price"];
    const maxPriceBar = filterForm.elements["max-price"];
    const minPriceNumber = filterForm.elements["min-price-number"];
    const maxPriceNumber = filterForm.elements["max-price-number"];

    minPriceBar.addEventListener("input", (e) => {
      let min = parseInt(e.target.value);
      let max = parseInt(maxPriceBar.value);
      if (min > max) {
        min = max;
        minPriceBar.value = min;
      }
      minPriceNumber.value = min;
      min_price = min;
      page = 1;
      fetchServices();
    });

    minPriceNumber.addEventListener("input", (e) => {
      const inputValue = e.target.value;
      if (inputValue === "") {
        minPriceBar.value = 0;
        min_price = null;
      } else {
        let min = parseInt(inputValue);
        let max = parseInt(maxPriceBar.value);
        if (min > max) {
          min = max;
          minPriceNumber.value = min;
        }
        minPriceBar.value = min;
        min_price = min;
      }
      page = 1;
      fetchServices();
    });

    maxPriceBar.addEventListener("input", (e) => {
      let max = parseInt(e.target.value);
      let min = parseInt(minPriceBar.value);
      if (max < min) {
        max = min;
        maxPriceBar.value = max;
      }
      maxPriceNumber.value = max;
      max_price = max;
      page = 1;
      fetchServices();
    });

    maxPriceNumber.addEventListener("input", (e) => {
      const inputValue = e.target.value;
      if (inputValue === "") {
        maxPriceBar.value = 1000;
        max_price = null;
      } else {
        let max = parseInt(inputValue);
        let min = parseInt(minPriceBar.value);
        if (max < min) {
          max = min;
          maxPriceNumber.value = max;
        }
        maxPriceBar.value = max;
        max_price = max;
      }
      page = 1;
      fetchServices();
    });

    // --- Rating Range Sync ---
    const minRatingBar = filterForm.elements["min-rating"];
    const maxRatingBar = filterForm.elements["max-rating"];
    const minRatingNumber = filterForm.elements["min-rating-number"];
    const maxRatingNumber = filterForm.elements["max-rating-number"];

    minRatingBar.addEventListener("input", (e) => {
      let min = parseFloat(e.target.value);
      let max = parseFloat(maxRatingBar.value);
      if (min > max) {
        min = max;
        minRatingBar.value = min;
      }
      minRatingNumber.value = min;
      min_rating = min;
      page = 1;
      fetchServices();
    });

    minRatingNumber.addEventListener("input", (e) => {
      const inputValue = e.target.value;
      if (inputValue === "") {
        minRatingBar.value = 1;
        min_rating = null;
      } else {
        let min = parseFloat(inputValue);
        let max = parseFloat(maxRatingBar.value);
        if (min > max) {
          min = max;
          minRatingNumber.value = min;
        }
        minRatingBar.value = min;
        min_rating = min;
      }
      page = 1;
      fetchServices();
    });

    maxRatingBar.addEventListener("input", (e) => {
      let max = parseFloat(e.target.value);
      let min = parseFloat(minRatingBar.value);
      if (max < min) {
        max = min;
        maxRatingBar.value = max;
      }
      maxRatingNumber.value = max;
      max_rating = max;
      page = 1;
      fetchServices();
    });

    maxRatingNumber.addEventListener("input", (e) => {
      const inputValue = e.target.value;
      if (inputValue === "") {
        maxRatingBar.value = 5;
        max_rating = null;
      } else {
        let max = parseFloat(inputValue);
        let min = parseFloat(minRatingBar.value);
        if (max < min) {
          max = min;
          maxRatingNumber.value = max;
        }
        maxRatingBar.value = max;
        max_rating = max;
      }
      page = 1;
      fetchServices();
    });

    // Order by select
    filterForm.elements["order-by"].addEventListener("change", (e) => {
      orderby = e.target.value;
      page = 1;
      fetchServices();
    });
  }

  prevPageButton.addEventListener("click", () => {
    if (page > 1) {
      page--;
      window.scrollTo(0, 0);
      fetchServices();
    }
  });

  nextPageButton.addEventListener("click", () => {
    page++;
    window.scrollTo(0, 0);
    fetchServices();
  });

  clearFiltersButton.addEventListener("click", () => {
    search = null;
    provider = null;
    category = null;
    location = null;
    min_price = null;
    max_price = null;
    min_rating = null;
    max_rating = null;
    orderby = "created_at-desc";
    page = 1;

    filterForm.elements["min-price"].value = 0;
    filterForm.elements["max-price"].value = 1000;
    filterForm.elements["min-price-number"].value = "";
    filterForm.elements["max-price-number"].value = "";

    filterForm.elements["min-rating"].value = 1;
    filterForm.elements["max-rating"].value = 5;
    filterForm.elements["min-rating-number"].value = "";
    filterForm.elements["max-rating-number"].value = "";

    filterForm.elements["provider"].value = "";
    filterForm.elements["location"].value = "";
    filterForm.elements["order-by"].value = "created_at-desc";

    filterForm
      .querySelectorAll('input[name="categories"]')
      .forEach((cb) => (cb.checked = false));
    fetchServices();
  });

  fetchCategories();
  fetchServices();
});
