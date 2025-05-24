document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const serviceId = urlParams.get("id");

  if (!serviceId) {
    document.getElementById("service-main").innerHTML =
      "<p>Service not found.</p>";
    return;
  }

  async function loadServiceInfo() {
    try {
      const response = await fetch(`/api/services.php?id=${serviceId}`);
      if (!response.ok) {
        let errorMsg = "Service not found.";
        try {
          const errorData = await response.json();
          if (errorData && errorData.error) errorMsg = errorData.error;
        } catch (e) {}
        document.getElementById(
          "service-main"
        ).innerHTML = `<p>${errorMsg}</p>`;
        return;
      }
      const service = await response.json();

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

      document.getElementById("service-image").src = service.image;
      document.getElementById(
        "service-title"
      ).innerHTML = `<h3>${service.title}</h3>`;
      document.getElementById(
        "service-description"
      ).innerHTML = `<p>${service.description}</p>`;
      document.getElementById("service-provider").innerHTML = `
      <img src="${service.provider_image}" alt="${service.provider_username}" class="provider-image">
      <p>${service.provider_username}</p>
      `;
      document.getElementById(
        "service-category"
      ).innerHTML = `<p>Category: ${service.category_name}</p>`;
      document.getElementById(
        "service-price"
      ).innerHTML = `<p>Price: ${service.price}€/hour</p>`;
      document.getElementById(
        "service-location"
      ).innerHTML = `<p>Location: ${service.location}</p>`;
      document.getElementById(
        "service-rating"
      ).innerHTML = `<p>Rating: ${service.rating}</p>`;

      // Render order status/actions
      renderServiceOrder(service);

      // Fill in reviews
      const reviewsList = document.getElementById("reviews-list");
      reviewsList.innerHTML = "";
      reviewsList.innerHTML = `<h3>All Reviews</h3>`;
      if (service.reviews && service.reviews.length > 0) {
        service.reviews.forEach((review) => {
          if (!review.reviewer_image) {
            review.reviewer_image = "../images/user.jpg";
          } else {
            review.reviewer_image = `../images/cache/${review.reviewer_image}`;
          }

          const reviewDiv = document.createElement("div");
          reviewDiv.className = "review-item";
          reviewDiv.innerHTML = `
            <strong>${review.reviewer_username}</strong>
            <img src="${review.reviewer_image}" alt="reviewer image" class="reviewer-image">
            <span>Rating: ${review.rating}</span>
            <p>${review.text}</p>
          `;
          reviewsList.appendChild(reviewDiv);
        });
      } else {
        reviewsList.innerHTML += "<p>No reviews yet.</p>";
      }

      // Show/hide reviews form based on context
      const reviewsForm = document.getElementById("reviews-form");
      const myServicesContext =
        localStorage.getItem("my_services") || "customer";
      if (reviewsForm) {
        if (myServicesContext === "customer") {
          reviewsForm.style.display = "block";
        } else {
          reviewsForm.style.display = "none";
        }
      }
    } catch (error) {
      document.getElementById(
        "service-main"
      ).innerHTML = `<p>Service not found.<br><span style='color:#b00;'>${error.message}</span></p>`;
    }
  }

  function renderServiceOrder(service) {
    const orderDiv = document.getElementById("service-order");
    orderDiv.innerHTML = "";
    const providerOrdersBlock = document.getElementById(
      "provider-orders-block"
    );
    const providerOrdersList = document.getElementById("provider-orders-list");
    if (providerOrdersBlock && providerOrdersList) {
      providerOrdersBlock.style.display = "none";
      providerOrdersList.innerHTML = "";
    }
    const myServicesContext = localStorage.getItem("my_services") || "customer";
    // CUSTOMER: default UI, do nothing special
    if (myServicesContext === "customer") {
      if (service.has_ordered) {
        orderDiv.innerHTML = `<p>Status: ${
          service.order_status || "Ordered"
        }</p>`;
      } else {
        orderDiv.innerHTML = "";
      }
      return;
    }
    // PROVIDER: show all orders for this service
    if (
      myServicesContext === "vendor_services" &&
      service.provider_id &&
      CURRENT_USER_ID == service.provider_id
    ) {
      providerOrdersBlock.style.display = "block";
      providerOrdersBlock.querySelector("h2").textContent =
        "Orders for this Service";
      if (Array.isArray(service.orders) && service.orders.length > 0) {
        providerOrdersList.innerHTML = "";
        service.orders.forEach((order, idx) => {
          let statusText = order.status_name || "Unknown";
          let actionsHtml = "";
          if (order.status_name === "Ordered") {
            actionsHtml = `
              <form class="status-update-form" data-idx="${idx}" style="margin-top:0.7em;">
                <input type="hidden" name="service_id" value="${service.id}">
                <input type="hidden" name="customer_id" value="${
                  order.customer_id
                }">
                <input type="hidden" name="status" value="2">
                <input type="hidden" name="csrf_token" value="${
                  service.csrf_token || ""
                }">
                <button type="submit">Mark as In Progress</button>
              </form>
              <form class="status-update-form" data-idx="${idx}" style="margin-top:0.7em; margin-left:0.7em;">
                <input type="hidden" name="service_id" value="${service.id}">
                <input type="hidden" name="customer_id" value="${
                  order.customer_id
                }">
                <input type="hidden" name="status" value="3">
                <input type="hidden" name="csrf_token" value="${
                  service.csrf_token || ""
                }">
                <button type="submit">Mark as Completed</button>
              </form>
            `;
          } else if (order.status_name === "In Progress") {
            actionsHtml = `
              <form class="status-update-form" data-idx="${idx}" style="margin-top:0.7em;">
                <input type="hidden" name="service_id" value="${service.id}">
                <input type="hidden" name="customer_id" value="${
                  order.customer_id
                }">
                <input type="hidden" name="status" value="3">
                <input type="hidden" name="csrf_token" value="${
                  service.csrf_token || ""
                }">
                <button type="submit">Mark as Completed</button>
              </form>
            `;
          }
          providerOrdersList.innerHTML += `
            <div class="provider-order-card">
              <div class="provider-order-header">
                <strong>Customer:</strong> ${order.customer_username}
              </div>
              <div class="provider-order-status">
                <span><strong>Status:</strong> ${statusText}</span>
              </div>
              <div class="provider-order-actions">
                ${actionsHtml}
              </div>
            </div>
          `;
        });
        // Attach event listeners for all forms
        const forms = providerOrdersList.querySelectorAll(
          ".status-update-form"
        );
        forms.forEach((form) => {
          form.addEventListener("submit", async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const response = await fetch(
              "../actions/action_update_status.php",
              {
                method: "POST",
                body: formData,
              }
            );
            if (response.ok) {
              await loadServiceInfo();
            } else {
              alert("Failed to update status.");
            }
          });
        });
      } else {
        providerOrdersBlock.style.display = "block";
        providerOrdersList.innerHTML = `<p>No orders for this service yet.</p>`;
      }
      return;
    }
    // PROVIDER: show only the order for a specific customer (vendor_orders)
    if (
      myServicesContext === "vendor_orders" &&
      service.provider_id &&
      CURRENT_USER_ID == service.provider_id
    ) {
      providerOrdersBlock.style.display = "block";
      providerOrdersBlock.querySelector("h2").textContent = "Order";
      // Get customer_id from localStorage (set by my_services.js)
      const customerId = localStorage.getItem("vendor_orders_customer_id");
      providerOrdersList.innerHTML = "";
      if (
        Array.isArray(service.orders) &&
        service.orders.length > 0 &&
        customerId
      ) {
        const order = service.orders.find(
          (o) => String(o.customer_id) === String(customerId)
        );
        if (order) {
          let statusText = order.status_name || "Unknown";
          let actionsHtml = "";
          if (order.status_name === "Ordered") {
            actionsHtml = `
              <form class="status-update-form" data-idx="0">
                <input type="hidden" name="service_id" value="${service.id}">
                <input type="hidden" name="customer_id" value="${
                  order.customer_id
                }">
                <input type="hidden" name="status" value="2">
                <input type="hidden" name="csrf_token" value="${
                  service.csrf_token || ""
                }">
                <button type="submit">Mark as In Progress</button>
              </form>
              <form class="status-update-form" data-idx="0">
                <input type="hidden" name="service_id" value="${service.id}">
                <input type="hidden" name="customer_id" value="${
                  order.customer_id
                }">
                <input type="hidden" name="status" value="3">
                <input type="hidden" name="csrf_token" value="${
                  service.csrf_token || ""
                }">
                <button type="submit">Mark as Completed</button>
              </form>
            `;
          } else if (order.status_name === "In Progress") {
            actionsHtml = `
              <form class="status-update-form" data-idx="0">
                <input type="hidden" name="service_id" value="${service.id}">
                <input type="hidden" name="customer_id" value="${
                  order.customer_id
                }">
                <input type="hidden" name="status" value="3">
                <input type="hidden" name="csrf_token" value="${
                  service.csrf_token || ""
                }">
                <button type="submit">Mark as Completed</button>
              </form>
            `;
          }
          providerOrdersList.innerHTML = `
            <div class="provider-order-card">
              <div class="provider-order-header">
                <strong>Customer:</strong> ${order.customer_username}
              </div>
              <div class="provider-order-status">
                <span><strong>Status:</strong> ${statusText}</span>
              </div>
              <div class="provider-order-actions">
                ${actionsHtml}
              </div>
            </div>
          `;
          // Attach event listeners for the forms (AJAX submit)
          const forms = providerOrdersList.querySelectorAll(
            ".status-update-form"
          );
          forms.forEach((form) => {
            form.addEventListener("submit", async function (e) {
              e.preventDefault();
              const formData = new FormData(this);
              const response = await fetch(
                "../actions/action_update_status.php",
                {
                  method: "POST",
                  body: formData,
                }
              );
              if (response.ok) {
                await loadServiceInfo();
              } else {
                alert("Failed to update status.");
              }
            });
          });
        } else {
          providerOrdersList.innerHTML = `<p>No order found for this customer.</p>`;
        }
      } else {
        providerOrdersList.innerHTML = `<p>No order found for this customer.</p>`;
      }
      return;
    }
    // If user has already ordered, show status and cancel option
    if (service.has_ordered) {
      let cancelForm = "";
      if (service.order_status === "Ordered") {
        cancelForm = `
        <form id="cancel-service-form" action="../actions/action_cancel_order.php" method="POST" style="margin-top:1em;">
          <input type="hidden" name="service_id" value="${service.id}">
          <input type="hidden" name="csrf_token" value="${
            service.csrf_token || ""
          }">
          <button type="submit">Cancel Order</button>
        </form>
        `;
      }
      orderDiv.innerHTML = `
        <p>Status: ${service.order_status || "Ordered"}</p>
        ${cancelForm}
      `;
    } else {
      // Not ordered yet
      orderDiv.innerHTML = `
        <form id="order-service-form" action="../actions/action_order_service.php" method="POST" style="margin-top:1em;">
          <input type="hidden" name="service_id" value="${service.id}">
          <input type="hidden" name="csrf_token" value="${
            service.csrf_token || ""
          }">
          <button type="submit">Order Service</button>
        </form>
      `;
    }
    // Add confirmation popups
    const orderServiceForm = document.getElementById("order-service-form");
    if (orderServiceForm) {
      orderServiceForm.addEventListener("submit", function (e) {
        if (!confirm("Are you sure you want to order this service?")) {
          e.preventDefault();
        }
      });
    }
    const cancelServiceForm = document.getElementById("cancel-service-form");
    if (cancelServiceForm) {
      cancelServiceForm.addEventListener("submit", function (e) {
        if (!confirm("Are you sure you want to cancel this order?")) {
          e.preventDefault();
        }
      });
    }
  }

  document
    .getElementById("review-form")
    .addEventListener("submit", async (e) => {
      e.preventDefault();

      const form = e.target;
      const payload = {
        service_id: serviceId,
        rating: form.elements["review-rating"].value,
        text: form.elements["review-text"].value,
        reviewer_id: CURRENT_USER_ID,
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

  loadServiceInfo();
});
