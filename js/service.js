document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const serviceId = urlParams.get("id");
  const serviceMain = document.getElementById("service-main");

  if (!serviceId) {
    serviceMain.innerHTML = "<p>Service not found.</p>";
    return;
  }

  async function loadServiceInfo() {
    try {
      const response = await fetch(`/api/service_details.php?service_id=${serviceId}&user_id=${CURRENT_USER_ID}`);
      const data = await response.json();
      if (!response.ok || !data.service) {
        serviceMain.innerHTML = `<p>${data.error || "Service not found."}</p>`;
        return;
      }
      const service = data.service;
      const reviews = data.reviews || [];
      const orderInfo = data.order_info || {};
      const allOrders = data.orders || [];

      // Set images
      service.image = service.image ? `../images/cache/${service.image}` : "../images/service.png";
      service.provider_image = service.provider_image ? `../images/cache/${service.provider_image}` : "../images/user.jpg";
      service.rating = service.rating ? Number(service.rating).toFixed(1) : "Not rated yet";

      // Render service info
      document.getElementById("service-image").src = service.image;
      document.getElementById("service-title").innerHTML = `<h3>${service.title}</h3>`;
      document.getElementById("service-description").innerHTML = `<p>${service.description}</p>`;
      document.getElementById("service-provider").innerHTML = `
        <img src="${service.provider_image}" alt="${service.provider_username}" class="provider-image">
        <p>${service.provider_username}</p>`;
      document.getElementById("service-category").innerHTML = `<p>Category: ${service.category_name}</p>`;
      document.getElementById("service-price").innerHTML = `<p>Price: ${service.price}€/hour</p>`;
      document.getElementById("service-location").innerHTML = `<p>Location: ${service.location}</p>`;
      document.getElementById("service-rating").innerHTML = `<p>Rating: ${service.rating}</p>`;

      // If current user is the provider, show all orders and allow status updates
      if (service.creator_id == CURRENT_USER_ID) {
        renderProviderOrders(allOrders, service);
      } else {
        renderOrderSection(service, orderInfo);
      }

      renderReviews(reviews);

      const reviewsForm = document.getElementById("reviews-form");
      if (reviewsForm) {
        reviewsForm.style.display = (service.creator_id != CURRENT_USER_ID) ? "block" : "none";
      }
    } catch (error) {
      serviceMain.innerHTML = `<p>Service not found.<br><span style='color:#b00;'>${error.message}</span></p>`;
    }
  }

  function renderOrderSection(service, orderInfo) {
    const orderDiv = document.getElementById("service-order");
    orderDiv.innerHTML = "";

    if (orderInfo.hasOrdered) {
      let cancelBtn = "";
      if (orderInfo.status === "Ordered") {
        cancelBtn = `<button id="cancel-order-btn">Cancel Order</button>`;
      }
      orderDiv.innerHTML = `<p>Status: ${orderInfo.status || "Ordered"}</p>${cancelBtn}`;
      if (cancelBtn) {
        document.getElementById("cancel-order-btn").onclick = async () => {
          await updateOrCreateOrder(service.id, CURRENT_USER_ID, 0); // 0 or a status for "cancelled"
          await loadServiceInfo();
        };
      }
    } else {
      orderDiv.innerHTML = `<button id="order-service-btn">Order Service</button>`;
      document.getElementById("order-service-btn").onclick = async () => {
        await updateOrCreateOrder(service.id, CURRENT_USER_ID, 1); // 1 = Ordered
        await loadServiceInfo();
      };
    }
  }

  function renderProviderOrders(orders, service) {
    const providerOrdersBlock = document.getElementById("provider-orders-block");
    const providerOrdersList = document.getElementById("provider-orders-list");
    if (providerOrdersBlock) providerOrdersBlock.style.display = "block";
    if (providerOrdersList) providerOrdersList.innerHTML = "";

    if (!orders || orders.length === 0) {
      if (providerOrdersList) providerOrdersList.innerHTML = `<p>No orders for this service yet.</p>`;
      return;
    }

    orders.forEach((order) => {
      let statusText = order.status_name || "Unknown";
      let actionsHtml = "";
      if (order.status_name === "Ordered") {
        actionsHtml = `
          <button class="status-update-btn" data-customer="${order.customer_id}" data-status="2">Mark as In Progress</button>
          <button class="status-update-btn" data-customer="${order.customer_id}" data-status="3">Mark as Completed</button>
        `;
      } else if (order.status_name === "In Progress") {
        actionsHtml = `
          <button class="status-update-btn" data-customer="${order.customer_id}" data-status="3">Mark as Completed</button>
        `;
      }
      if (providerOrdersList) providerOrdersList.innerHTML += `
        <div class="provider-order-card">
          <div class="provider-order-header"><strong>Customer:</strong> ${order.customer_username}</div>
          <div class="provider-order-status"><span><strong>Status:</strong> ${statusText}</span></div>
          <div class="provider-order-actions">${actionsHtml}</div>
        </div>
      `;
    });

    // Attach event listeners for all status update buttons
    if (providerOrdersList) {
      providerOrdersList.querySelectorAll(".status-update-btn").forEach(btn => {
        btn.addEventListener("click", async function () {
          const customerId = this.getAttribute("data-customer");
          const statusId = this.getAttribute("data-status");
          await updateOrCreateOrder(service.id, customerId, statusId);
          await loadServiceInfo();
        });
      });
    }
  }

  async function updateOrCreateOrder(service_id, customer_id, status_id) {
    const payload = {
      service_id,
      customer_id,
      status_id,
    };
    const response = await fetch("../api/orders_post.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    if (!response.ok) {
      const errorText = await response.text();
      alert(`Error: ${errorText}`);
    }
  }

  function renderReviews(reviews) {
    const reviewsList = document.getElementById("reviews-list");
    reviewsList.innerHTML = "<h3>All Reviews</h3>";
    if (reviews && reviews.length > 0) {
      reviews.forEach(review => {
        const reviewerImage = review.reviewer_image ? `../images/cache/${review.reviewer_image}` : "../images/user.jpg";
        const reviewDiv = document.createElement("div");
        reviewDiv.className = "review-item";
        reviewDiv.innerHTML = `
          <strong>${review.reviewer_username}</strong>
          <img src="${reviewerImage}" alt="reviewer image" class="reviewer-image">
          <span>Rating: ${review.rating}</span>
          <p>${review.text}</p>
        `;
        reviewsList.appendChild(reviewDiv);
      });
    } else {
      reviewsList.innerHTML += "<p>No reviews yet.</p>";
    }
  }

  // Review form submit
  const reviewForm = document.getElementById("review-form");
  if (reviewForm) {
    reviewForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const form = e.target;
      const payload = {
        service_id: serviceId,
        rating: form.elements["review-rating"].value,
        text: form.elements["review-text"].value,
        reviewer_id: CURRENT_USER_ID,
        csrf_token: document.getElementById("csrf_token")?.value || ""
      };
      const response = await fetch("../api/reviews.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      if (response.ok) {
        form.reset();
        await loadServiceInfo();
      } else {
        const errorText = await response.text();
        alert(`Error: ${errorText}`);
      }
    });
  }

  loadServiceInfo();
});