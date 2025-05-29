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
      const orderInfo = data.order_info || null;
      const allOrders = data.orders || [];

      service.image = service.image ? `../images/cache/${service.image}` : "../images/service.png";
      service.provider_image = service.provider_image ? `../images/cache/${service.provider_image}` : "../images/user.jpg";
      service.rating = service.rating ? Number(service.rating).toFixed(1) : "Not rated yet";

      document.getElementById("service-image").src = service.image;
      document.getElementById("service-title").innerHTML = `<h3>${service.title}</h3>`;
      document.getElementById("service-description").innerHTML = `<p>Description: ${service.description}</p>`;
      document.getElementById("service-provider").innerHTML = `
        <img src="${service.provider_image}" alt="${service.provider_username}" class="provider-image">
        <p>${service.provider_username}</p>`;
      document.getElementById("service-category").innerHTML = `<p>Category: ${service.category_name}</p>`;
      document.getElementById("service-price").innerHTML = `<p>Price: $${service.price}</p>`;
      document.getElementById("service-location").innerHTML = `<p>Location: ${service.location}</p>`;
      document.getElementById("service-rating").innerHTML = `<p>Rating: ${service.rating}</p>`;

      if (service.creator_id == CURRENT_USER_ID) {

        const hasActiveOrders = allOrders.some(order =>
          order.status_name === "Ordered" ||
          order.status_name === "In Progress" ||
          order.status_name === "Waiting for Payment"
        );

        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit Service";
        editBtn.className = "edit-service-btn";
        editBtn.onclick = function () {
          if (hasActiveOrders) {
            alert("You cannot edit the service while there are active orders.");
          }else{
            window.location.href = `edit_service.php?service_id=${service.id}`;
          }
        };
        document.getElementById("service-buttons").innerHTML = "";
        document.getElementById("service-buttons").appendChild(editBtn);

        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete Service";
        deleteBtn.className = "delete-service-btn";
        deleteBtn.onclick = async function () {
          if (confirm("Are you sure you want to delete this service? This action cannot be undone.")) {
            const formData = new URLSearchParams();
            formData.append("service_id", service.id);
            formData.append("csrf_token", typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : (document.getElementById('csrf_token')?.value || ''));
            const response = await fetch("../actions/action_delete_service.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: formData.toString(),
            });
            if (response.redirected) {
              window.location.href = response.url;
            } else if (response.ok) {
              window.location.href = "my_services.php"; // fallback
            } else {
              alert("Failed to delete service.");
            }
          }
        };
        document.getElementById("service-buttons").appendChild(deleteBtn);


        renderProviderOrders(allOrders, service);

      } else {
        const serviceButtons = document.getElementById("service-buttons");
        serviceButtons.innerHTML = "";
        const favBtn = document.createElement("button");
        favBtn.className = "favorite-btn";
        favBtn.dataset.serviceId = service.id;
        favBtn.textContent = service.is_favorite
          ? "💔 Remove from Favorites"
          : "❤️ Add to Favorites";
        serviceButtons.appendChild(favBtn);

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
            loadServiceInfo();
          } catch {
            alert("Failed to toggle favorite.");
          }
        });

        const contactBtn = document.createElement("button");
        contactBtn.className = "contact-btn";
        contactBtn.textContent = "Contact Provider";
        serviceButtons.appendChild(contactBtn);

        contactBtn.addEventListener("click", function (e) {
          e.preventDefault();
          e.stopPropagation();
          window.location.href = `chat.php?user=${service.creator_id}`;
        });
        renderOrderSection(service, orderInfo);
      }

      renderReviews(reviews);

      const reviewsForm = document.getElementById("reviews-form");
      if (reviewsForm) {

        if (service.creator_id != CURRENT_USER_ID && orderInfo === "Completed") {
          reviewsForm.classList.remove("hide");
        } else {
          reviewsForm.classList.add("hide");
        }
      }
    } catch (error) {
      serviceMain.innerHTML = `<p>Service not found.<br><span style='color:#b00;'>${error.message}</span></p>`;
    }
  }

  function renderOrderSection(service, orderInfo) {
    const orderDiv = document.getElementById("service-order");
    orderDiv.innerHTML = "";


    if (!orderInfo) {
      orderDiv.innerHTML = `<button id="order-service-btn">Order Service</button>`;
      document.getElementById("order-service-btn").onclick = async () => {
        await updateOrCreateOrder(service.id, CURRENT_USER_ID, 1); // 1 = Ordered
        await loadServiceInfo();
      };
      return;
    }

    let buttons = "";

    if (orderInfo === "Ordered") {
      buttons = `<button id="cancel-order-btn">Cancel Order</button>`;
    } else if (orderInfo === "Waiting for Payment") {
      buttons = `
        <button id="pay-order-btn">Pay</button>
        <button id="cancel-order-btn">Cancel Order</button>
      `;
    }else if (orderInfo === "In Progress") {
      buttons = `<button id="cancel-order-btn">Cancel Order</button>`;
    } else if (orderInfo === "Completed" || orderInfo === "Cancelled" || orderInfo === "Rejected") {
      buttons = `<button id="cancel-order-btn">Delete Order</button>`;
    }

    orderDiv.innerHTML = `<p>Status: ${orderInfo}</p>${buttons}`;

    if (orderInfo === "Waiting for Payment") {
      const payBtn = document.getElementById("pay-order-btn");
      if (payBtn) {
        payBtn.onclick = async () => {
          window.location.href = `payment.php?service_id=${service.id}`;
        };
      }
    }

    if (orderInfo) {
      const cancelBtn = document.getElementById("cancel-order-btn");
      if (cancelBtn) {
        cancelBtn.onclick = async () => {
          await updateOrCreateOrder(service.id, CURRENT_USER_ID, -1); // -1 to delete the order
          await loadServiceInfo();
        };
      }
    }
  }

  function renderProviderOrders(orders, service) {
    const providerOrdersBlock = document.getElementById("provider-orders-block");
    const providerOrdersList = document.getElementById("provider-orders-list");
    if (providerOrdersBlock) providerOrdersBlock.style.display = "block";
    if (providerOrdersList) providerOrdersList.innerHTML = "";

    if (!orders || orders.length === 0) {
      if (providerOrdersList) providerOrdersList.innerHTML = `<p id='no-orders'>No orders for this service yet.</p>`;
      return;
    }

    orders.forEach((order) => {
      let statusText = order.status_name || "Unknown";
      let actionsHtml = "";
      if (order.status_name === "Ordered") {
        actionsHtml = `
          <button class="status-update-btn" data-customer="${order.customer_id}" data-status="2">Accept order</button>
          <button class="status-update-btn" data-customer="${order.customer_id}" data-status="5">Reject order</button>
        `;
      } else if (order.status_name === "In Progress") {
        actionsHtml = `
          <button class="status-update-btn" data-customer="${order.customer_id}" data-status="4">Mark as Completed</button>
          <button class="status-update-btn" data-customer="${order.customer_id}" data-status="5">Mark as Cancelled</button>
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
      reviewsList.innerHTML += "<p id='no-reviews'>No reviews for this service yet.</p>";
    }
  }

  const reviewForm = document.getElementById("reviews-form");
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
      }else {
        const errorText = await response.text();
        alert(`Error submitting review: ${errorText}`);
      }
    });
  }

  loadServiceInfo();
});