document.addEventListener("DOMContentLoaded", () => {
  const searchForm = document.getElementById("search-bar");
  const clearFiltersButton = document.getElementById("clear-filters");
  const openFilterPopupButton = document.getElementById("open-filters");
  const closeFilterPopupButton = document.getElementById("close-filter-popup");  
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
  const status = "Open"; // Default status
  let min_price = urlParams.get("min_price") || null;
  let max_price = urlParams.get("max_price") || null;
  let min_rating = urlParams.get("min_rating") || null;
  let max_rating = urlParams.get("max_rating") || null;
  let orderby = urlParams.get("orderby") || "created_at-desc";

  function build_api_query() {
    let query = `/api/services.php?orderby=${orderby}&page=${page}&per_page=${per_page}&status=${status}`;

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
    if (min_price) {
      query += `&min_price=${encodeURIComponent(min_price)}`;
    }
    if (max_price) {
      query += `&max_price=${encodeURIComponent(max_price)}`;
    }
    if (min_rating) {
      query += `&min_rating=${encodeURIComponent(min_rating)}`;
    }
    if (max_rating) {
      query += `&max_rating=${encodeURIComponent(max_rating)}`;
    }

    return query;
  }

  async function fetchServices() {
    try {
      const response = await fetch(
        `${build_api_query()}`
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

        pageSpan.textContent = `Page 0 of 0`;
        prevPageButton.disabled = true;
        nextPageButton.disabled = true;
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
      categories.forEach((category) => {
        const listItem = document.createElement("li");
        listItem.innerHTML = `
          <button class="category-button" data-category="${category.id}">
            ${category.name}
          </button>
        `;
        categoriesList.appendChild(listItem);
      });

      document.querySelectorAll('.category-button').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          category = this.getAttribute('data-category');
          page = 1;
          fetchServices();
        });
      });

      categoriesOption.innerHTML = "";
      categories.forEach((category) => {
        const option = document.createElement("option");
        option.value = category.id;
        option.textContent = category.name;
        categoriesOption.appendChild(option);
      });


    } catch (error) {
      console.error("Error fetching categories:", error);
      categoriesList.innerHTML = "<li>Error loading categories</li>";
    }
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

  searchForm.addEventListener("submit", (event) => {
    event.preventDefault();
    search = searchForm.elements["search"].value;
    if (search === "") search = null;
    page = 1; 
    fetchServices();
    searchForm.reset(); 
  });

  openFilterPopupButton.addEventListener("click", () => {
    const filterPopup = document.getElementById("filter-popup");
    filterPopup.classList.remove("hidden");
    document.body.style.overflow = "hidden";
  });

  closeFilterPopupButton.addEventListener("click", () => {
    const filterPopup = document.getElementById("filter-popup");
    filterPopup.classList.add("hidden");
    document.body.style.overflow = "auto";
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
    fetchServices();
  });

  filterForm.addEventListener("submit", (event) => {
    event.preventDefault();
    category = filterForm.elements["category"].value;
    provider = filterForm.elements["provider"].value;
    location = filterForm.elements["location"].value;
    min_price = filterForm.elements["min-price"].value;
    max_price = filterForm.elements["max-price"].value;
    min_rating = filterForm.elements["min-rating"].value;
    max_rating = filterForm.elements["max-rating"].value;
    orderby = filterForm.elements["order-by"].value;
    if (category === "") category = null;
    if (min_price === "") min_price = null;
    if (max_price === "") max_price = null;
    if (min_rating === "") min_rating = null;
    if (max_rating === "") max_rating = null;
    page = 1;
    fetchServices();
    filterForm.reset();
    const filterPopup = document.getElementById("filter-popup");
    filterPopup.classList.add("hidden");
    document.body.style.overflow = "auto";
  });

  fetchCategories();
  fetchServices();
});
