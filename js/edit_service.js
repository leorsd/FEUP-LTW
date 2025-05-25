document.addEventListener("DOMContentLoaded", async () => {
  const params = new URLSearchParams(window.location.search);
  const serviceId = params.get("service_id");
  if (!serviceId) return;

  try {
    const response = await fetch(`../api/service_details.php?service_id=${encodeURIComponent(serviceId)}`);
    if (!response.ok) throw new Error("Failed to load service data.");
    const data = await response.json();
    const service = data.service;

    document.getElementById("service-title").value = service.title || "";
    document.getElementById("service-description").value = service.description || "";
    document.getElementById("service-location").value = service.location || "";
    document.getElementById("service-price").value = service.price || "";

    const priceInput = document.getElementById("service-price");
    const priceSlider = document.getElementById("price-slider");
    if (priceSlider) {
      priceSlider.value = priceInput.value;
      priceInput.addEventListener("input", () => {
        priceSlider.value = priceInput.value;
      });
      priceSlider.addEventListener("input", () => {
        priceInput.value = priceSlider.value;
      });
    }

    const catRes = await fetch("../api/categories.php");
    if (!catRes.ok) throw new Error("Failed to load categories.");
    const categories = await catRes.json();

    const formCategory = document.getElementById("form-category");
    formCategory.innerHTML = ""; // Clear previous

    categories.forEach(category => {
      const label = document.createElement("label");
      label.textContent = category.name;
      const radio = document.createElement("input");
      radio.type = "radio";
      radio.name = "service_category";
      radio.value = category.id;
      if (service.category === category.id) radio.checked = true;
      label.prepend(radio);
      formCategory.appendChild(label);
    });

    const form = document.getElementById("create-service-form");
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const categoryInput = form.querySelector('input[name="service_category"]:checked');
      const category = categoryInput ? categoryInput.value : "";

      const payload = new URLSearchParams();
      payload.append("service_id", serviceId);
      payload.append("title", document.getElementById("service-title").value);
      payload.append("description", document.getElementById("service-description").value);
      payload.append("location", document.getElementById("service-location").value);
      payload.append("price", document.getElementById("service-price").value);
      payload.append("category", category);

      const resp = await fetch("../actions/action_update_service.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: payload.toString(),
      });

      if (resp.redirected) {
        window.location.href = resp.url;
      } else if (resp.ok) {
        window.location.href = `service.php?id=${serviceId}`;
      } else {
        alert("Failed to update service.");
      }
    });

  } catch (err) {
    alert("Could not load service data: " + err.message);
  }
});