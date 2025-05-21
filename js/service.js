document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const serviceId = urlParams.get("id");

  if (!serviceId) {
    document.getElementById("service-main").innerHTML = "<p>Service not found.</p>";
    return;
  }

  async function loadServiceInfo() {
    try {
      const response = await fetch(`/api/services.php?id=${serviceId}`);
      if (!response.ok) throw new Error("Service not found");
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
        "service-status"
      ).innerHTML = `<p>${service.status_name}</p>`;
      document.getElementById(
        "service-rating"
      ).innerHTML = `<p>Rating: ${service.rating}</p>`;

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
    } catch (error) {
      document.getElementById("service-main").innerHTML =
        "<p>Service not found.</p>";
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

  // Confirmation popup for order form
  const orderForm = document.querySelector("#service-order form");
  if (orderForm) {
    orderForm.addEventListener("submit", function (e) {
      if (!confirm("Are you sure you want to order this service?")) {
        e.preventDefault();
      }
    });
  }

    loadServiceInfo();
});
