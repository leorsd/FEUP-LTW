document.addEventListener("DOMContentLoaded", () => {
  const searchForm = document.getElementById("search-bar");
  const clearFiltersButton = document.getElementById("clear-filters");
  const filterForm = document.getElementById("filter-form");
  const servicesList = document.getElementById("services-list");
  const prevPageButton = document.getElementById("prev-page");
  const nextPageButton = document.getElementById("next-page");
  const pageSpan = document.getElementById("current-page");
  const categoriesOption = document.getElementById("form-categories");

  let page = 1; // Start with page 1
  const per_page = 12; // Number of services per page

  let search = null;
  let provider_id = CURRENT_USER_ID;
  let category = null;
  let location = null;
  let status = null;
  let min_price = null;
  let max_price = null;
  let min_rating = null;
  let max_rating = null;
  let orderby = "created_at-desc";

  let categoriesLoaded = false;
  let statusesLoaded = false;

  function trySetupLiveFilters() {
    if (categoriesLoaded && statusesLoaded) {
      setupLiveFilters();
    }
  }

  function build_api_query() {
    // Only fetch services the user bought (from service_customer table)
    let query = `/api/services.php?my_services=1&page=${page}&per_page=${per_page}&orderby=${orderby}`;

    if (search) {
      query += `&search=${encodeURIComponent(search)}`;
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
    if (status) {
      query += `&status=${encodeURIComponent(status)}`;
    }

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
                  currentTab === "ordered" && service.status_name !== undefined
                    ? `<p>Status: ${service.status_name}</p>`
                    : ""
                }
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

      categoriesLoaded = true;
      trySetupLiveFilters();
    } catch (error) {
      console.error("Error fetching categories:", error);
    }
  }

  async function fetchStatuses() {
    try {
      const response = await fetch("../api/statuses.php");
      const statuses = await response.json();
      if (statuses.error) {
        console.error("Error fetching statuses:", statuses.error);
        return;
      }
      const statusesOption = document.getElementById("form-statuses");
      statusesOption.innerHTML = "";
      statuses.forEach((status) => {
        const option = document.createElement("label");
        option.innerHTML = `
                <input type="checkbox" name="status" value="${status.id}"> ${status.name}
                `;
        statusesOption.appendChild(option);
      });

      statusesLoaded = true;
      trySetupLiveFilters();
    } catch (error) {
      console.error("Error fetching statuses:", error);
    }
  }

  function setupLiveFilters() {
    filterForm.elements["search"].addEventListener("input", (e) => {
      search = e.target.value;
      if (search === "") search = null;
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

    // Status checkboxes
    filterForm.querySelectorAll('input[name="status"]').forEach((cb) => {
      cb.addEventListener("change", () => {
        status = Array.from(
          filterForm.querySelectorAll('input[name="status"]:checked')
        )
          .map((cb) => cb.value)
          .join(",");
        if (status === "") status = null;
        page = 1;
        fetchServices();
      });
    });

    // Location input
    if (filterForm.elements["location"]) {
      filterForm.elements["location"].addEventListener("input", (e) => {
        location = e.target.value;
        if (location === "") location = null;
        page = 1;
        fetchServices();
      });
    }

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

  searchForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const value = searchForm.elements["search"].value.trim();
    if (value !== "") {
      window.location.href = `../pages/home.php?search=${encodeURIComponent(
        value
      )}`;
    }
  });

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
    category = null;
    min_price = null;
    max_price = null;
    min_rating = null;
    max_rating = null;
    status = null;
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

    filterForm.elements["search"].value = "";
    filterForm.elements["order-by"].value = "created_at-desc";

    filterForm
      .querySelectorAll('input[name="categories"]')
      .forEach((cb) => (cb.checked = false));
    filterForm
      .querySelectorAll('input[name="status"]')
      .forEach((cb) => (cb.checked = false));
    fetchServices();
  });

  // --- TOGGLE LOGIC FOR ORDERED/PROVIDED SERVICES ---
  const orderedBtn = document.getElementById("ordered-services-btn");
  const providedBtn = document.getElementById("provided-services-btn");
  const orderedSection = document.getElementById("ordered-services-section");
  const providedSection = document.getElementById("provided-services-section");

  let currentTab = "ordered"; // default

  function showProvidedServices() {
    providedBtn.classList.add("selected");
    orderedBtn.classList.remove("selected");
    providedSection.classList.remove("hide");
    orderedSection.classList.add("hide");
    currentTab = "provided";
    // Hide only the status filter and its label
    const statusLabel = document.querySelector('label[for="status"]');
    if (statusLabel) statusLabel.style.display = "none";
    const statusFilter = document.getElementById("form-statuses");
    if (statusFilter) statusFilter.style.display = "none";
    // Switch API to fetch provided (created) services
    build_api_query = function () {
      let query = `/api/services.php?created_services=1&page=${page}&per_page=${per_page}&orderby=${orderby}`;
      if (search) query += `&search=${encodeURIComponent(search)}`;
      if (category) query += `&category=${encodeURIComponent(category)}`;
      if (location) query += `&location=${encodeURIComponent(location)}`;
      if (min_price !== null && min_price !== "" && min_price !== undefined)
        query += `&min_price=${encodeURIComponent(min_price)}`;
      if (max_price !== null && max_price !== "" && max_price !== undefined)
        query += `&max_price=${encodeURIComponent(max_price)}`;
      if (min_rating) query += `&min_rating=${encodeURIComponent(min_rating)}`;
      if (max_rating) query += `&max_rating=${encodeURIComponent(max_rating)}`;
      // status filter is not used for provided services
      return query;
    };
    fetchServices();
  }

  function showOrderedServices() {
    orderedBtn.classList.add("selected");
    providedBtn.classList.remove("selected");
    orderedSection.classList.remove("hide");
    providedSection.classList.add("hide");
    currentTab = "ordered";
    // Show only the status filter and its label
    const statusLabel = document.querySelector('label[for="status"]');
    if (statusLabel) statusLabel.style.display = "";
    const statusFilter = document.getElementById("form-statuses");
    if (statusFilter) statusFilter.style.display = "";
    // Switch API to fetch ordered services
    build_api_query = function () {
      let query = `/api/services.php?my_services=1&page=${page}&per_page=${per_page}&orderby=${orderby}`;
      if (search) query += `&search=${encodeURIComponent(search)}`;
      if (category) query += `&category=${encodeURIComponent(category)}`;
      if (location) query += `&location=${encodeURIComponent(location)}`;
      if (min_price !== null && min_price !== "" && min_price !== undefined)
        query += `&min_price=${encodeURIComponent(min_price)}`;
      if (max_price !== null && max_price !== "" && max_price !== undefined)
        query += `&max_price=${encodeURIComponent(max_price)}`;
      if (min_rating) query += `&min_rating=${encodeURIComponent(min_rating)}`;
      if (max_rating) query += `&max_rating=${encodeURIComponent(max_rating)}`;
      if (status) query += `&status=${encodeURIComponent(status)}`;
      return query;
    };
    fetchServices();
  }

  orderedBtn.addEventListener("click", showOrderedServices);
  providedBtn.addEventListener("click", showProvidedServices);

  fetchCategories();
  fetchStatuses();
  fetchServices();
});
