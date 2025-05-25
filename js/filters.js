// Common filter state and listeners for all pages
let filters = {
  page: 1,
  per_page: 12,
  search: null,
  provider: null,
  status: null,
  category: null,
  location: null,
  min_price: null,
  max_price: null,
  min_rating: null,
  max_rating: null,
  orderby: "created_at-desc",
  favorites_owner: null,
};

function setupFilterListeners(filterForm, prevPageButton, nextPageButton, pageSpan, fetchCallback, clearFiltersButton) {
  if (!filterForm) return;

  // Search
  if (filterForm.elements["search"]) {
    filterForm.elements["search"].addEventListener("input", (e) => {
      filters.search = e.target.value || null;
      filters.page = 1;
      fetchCallback();
    });
  }

  // Provider
  if (filterForm.elements["provider"]) {
    filterForm.elements["provider"].addEventListener("input", (e) => {
      filters.provider = e.target.value || null;
      filters.page = 1;
      fetchCallback();
    });
  }

  // Location
  if (filterForm.elements["location"]) {
    filterForm.elements["location"].addEventListener("input", (e) => {
      filters.location = e.target.value || null;
      filters.page = 1;
      fetchCallback();
    });
  }

  // Order by
  if (filterForm.elements["order-by"]) {
    filterForm.elements["order-by"].addEventListener("change", (e) => {
      filters.orderby = e.target.value;
      filters.page = 1;
      fetchCallback();
    });
  }

  // Status checkboxes (like categories)
  const statusCheckboxes = filterForm.querySelectorAll('input[name="status"]');
  if (statusCheckboxes.length > 0) {
    statusCheckboxes.forEach((cb) => {
      cb.addEventListener("change", () => {
        filters.status = Array.from(
          filterForm.querySelectorAll('input[name="status"]:checked')
        )
          .map((cb) => cb.value)
          .join(",");
        if (filters.status === "") filters.status = null;
        filters.page = 1;
        fetchCallback();
      });
    });
  }

  // Category checkboxes
  const categoryCheckboxes = filterForm.querySelectorAll('input[name="categories"]');
  if (categoryCheckboxes.length > 0) {
    categoryCheckboxes.forEach((cb) => {
      cb.addEventListener("change", () => {
        filters.category = Array.from(
          filterForm.querySelectorAll('input[name="categories"]:checked')
        )
          .map((cb) => cb.value)
          .join(",");
        if (filters.category === "") filters.category = null;
        filters.page = 1;
        fetchCallback();
      });
    });
  }

  // Price range
  if (filterForm.elements["min-price"] && filterForm.elements["max-price"]) {
    const minPriceBar = filterForm.elements["min-price"];
    const maxPriceBar = filterForm.elements["max-price"];
    const minPriceNumber = filterForm.elements["min-price-number"];
    const maxPriceNumber = filterForm.elements["max-price-number"];

    minPriceBar.addEventListener("input", (e) => {
      let min = parseInt(e.target.value);
      let max = parseInt(maxPriceBar.value);
      if (min > max) min = max;
      minPriceBar.value = min;
      if (minPriceNumber) minPriceNumber.value = min;
      filters.min_price = min;
      filters.page = 1;
      fetchCallback();
    });

    if (minPriceNumber) {
      minPriceNumber.addEventListener("input", (e) => {
        const inputValue = e.target.value;
        if (inputValue === "") {
          minPriceBar.value = 0;
          filters.min_price = null;
        } else {
          let min = parseInt(inputValue);
          let max = parseInt(maxPriceBar.value);
          if (min > max) min = max;
          minPriceNumber.value = min;
          minPriceBar.value = min;
          filters.min_price = min;
        }
        filters.page = 1;
        fetchCallback();
      });
    }

    maxPriceBar.addEventListener("input", (e) => {
      let max = parseInt(e.target.value);
      let min = parseInt(minPriceBar.value);
      if (max < min) max = min;
      maxPriceBar.value = max;
      if (maxPriceNumber) maxPriceNumber.value = max;
      filters.max_price = max;
      filters.page = 1;
      fetchCallback();
    });

    if (maxPriceNumber) {
      maxPriceNumber.addEventListener("input", (e) => {
        const inputValue = e.target.value;
        if (inputValue === "") {
          maxPriceBar.value = 1000;
          filters.max_price = null;
        } else {
          let max = parseInt(inputValue);
          let min = parseInt(minPriceBar.value);
          if (max < min) max = min;
          maxPriceNumber.value = max;
          maxPriceBar.value = max;
          filters.max_price = max;
        }
        filters.page = 1;
        fetchCallback();
      });
    }
  }

  // Rating range
  if (filterForm.elements["min-rating"] && filterForm.elements["max-rating"]) {
    const minRatingBar = filterForm.elements["min-rating"];
    const maxRatingBar = filterForm.elements["max-rating"];
    const minRatingNumber = filterForm.elements["min-rating-number"];
    const maxRatingNumber = filterForm.elements["max-rating-number"];

    minRatingBar.addEventListener("input", (e) => {
      let min = parseFloat(e.target.value);
      let max = parseFloat(maxRatingBar.value);
      if (min > max) min = max;
      minRatingBar.value = min;
      if (minRatingNumber) minRatingNumber.value = min;
      filters.min_rating = min;
      filters.page = 1;
      fetchCallback();
    });

    if (minRatingNumber) {
      minRatingNumber.addEventListener("input", (e) => {
        const inputValue = e.target.value;
        if (inputValue === "") {
          minRatingBar.value = 1;
          filters.min_rating = null;
        } else {
          let min = parseFloat(inputValue);
          let max = parseFloat(maxRatingBar.value);
          if (min > max) min = max;
          minRatingNumber.value = min;
          minRatingBar.value = min;
          filters.min_rating = min;
        }
        filters.page = 1;
        fetchCallback();
      });
    }

    maxRatingBar.addEventListener("input", (e) => {
      let max = parseFloat(e.target.value);
      let min = parseFloat(minRatingBar.value);
      if (max < min) max = min;
      maxRatingBar.value = max;
      if (maxRatingNumber) maxRatingNumber.value = max;
      filters.max_rating = max;
      filters.page = 1;
      fetchCallback();
    });

    if (maxRatingNumber) {
      maxRatingNumber.addEventListener("input", (e) => {
        const inputValue = e.target.value;
        if (inputValue === "") {
          maxRatingBar.value = 5;
          filters.max_rating = null;
        } else {
          let max = parseFloat(inputValue);
          let min = parseFloat(minRatingBar.value);
          if (max < min) max = min;
          maxRatingNumber.value = max;
          maxRatingBar.value = max;
          filters.max_rating = max;
        }
        filters.page = 1;
        fetchCallback();
      });
    }
  }

  // Pagination buttons
  if (prevPageButton) {
    prevPageButton.addEventListener("click", () => {
      if (filters.page > 1) {
        filters.page--;
        window.scrollTo(0, 0);
        fetchCallback();
      }
    });
  }
  if (nextPageButton) {
    nextPageButton.addEventListener("click", () => {
      filters.page++;
      window.scrollTo(0, 0);
      fetchCallback();
    });
  }

  if (clearFiltersButton) {
    clearFiltersButton.addEventListener("click", () => {
      filters.search = null;
      filters.status = null;
      filters.category = null;
      filters.location = null;
      filters.provider = null;
      filters.min_price = null;
      filters.max_price = null;
      filters.min_rating = null;
      filters.max_rating = null;
      filters.orderby = "created_at-desc";
      filters.page = 1;

      if (filterForm.elements["provider"]) filterForm.elements["provider"].value = "";
      if (filterForm.elements["location"]) filterForm.elements["location"].value = "";

      if (filterForm.elements["min-price"]) filterForm.elements["min-price"].value = 0;
      if (filterForm.elements["max-price"]) filterForm.elements["max-price"].value = 1000;
      if (filterForm.elements["min-price-number"]) filterForm.elements["min-price-number"].value = "";
      if (filterForm.elements["max-price-number"]) filterForm.elements["max-price-number"].value = "";

      if (filterForm.elements["min-rating"]) filterForm.elements["min-rating"].value = 1;
      if (filterForm.elements["max-rating"]) filterForm.elements["max-rating"].value = 5;
      if (filterForm.elements["min-rating-number"]) filterForm.elements["min-rating-number"].value = "";
      if (filterForm.elements["max-rating-number"]) filterForm.elements["max-rating-number"].value = "";

      if (filterForm.elements["search"]) filterForm.elements["search"].value = "";
      if (filterForm.elements["order-by"]) filterForm.elements["order-by"].value = "created_at-desc";

      filterForm
        .querySelectorAll('input[name="status"]')
        .forEach((cb) => (cb.checked = false));
      filterForm
        .querySelectorAll('input[name="categories"]')
        .forEach((cb) => (cb.checked = false));

      fetchCallback();
    });
  }
}