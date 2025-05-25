document.addEventListener("DOMContentLoaded", async () => {
  async function adminApi(action, data) {
    // Attach CSRF token from the page to all admin API requests
    data = { ...data, csrf_token: document.querySelector('input[name="csrf_token"]')?.value || '' };
    const res = await fetch(`../api/admin.php?action=${action}&id=${encodeURIComponent(CURRENT_USER_ID)}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
    return res.json();
  }

  async function loadCategories() {
    const res = await fetch("../api/categories.php");
    const categories = await res.json();
    const list = document.getElementById("admin-categories-list");
    list.innerHTML = "";
    categories.forEach(category => {
      const li = document.createElement("li");
      li.textContent = category.name + " ";
      const delBtn = document.createElement("button");
      delBtn.textContent = "Delete";
      delBtn.onclick = async () => {
        if (confirm("Delete this category?")) {
          await adminApi("delete_category", { category_id: category.id });
          loadCategories();
        }
      };
      li.appendChild(delBtn);
      list.appendChild(li);
    });
  }

  document.getElementById("admin-add-category-form").addEventListener("submit", async e => {
    e.preventDefault();
    const input = e.target.category_name;
    await adminApi("add_category", { category_name: input.value });
    input.value = "";
    loadCategories();
  });

  async function loadServices(search = "") {
    let url = "../api/services_get.php";
    if (search) url += `?search=${encodeURIComponent(search)}`;
    const res = await fetch(url);
    const data = await res.json();
    const list = document.getElementById("admin-services-list");
    list.innerHTML = "";
    data.services.forEach(service => {
      const li = document.createElement("li");
      li.textContent = `${service.title} (${service.provider_username}) `;
      const delBtn = document.createElement("button");
      delBtn.textContent = "Delete";
      delBtn.onclick = async () => {
        if (confirm("Delete this service? It will also delete all related data.")) {
          await adminApi("delete_service", { service_id: service.id });
          loadServices(search);
        }
      };
      li.appendChild(delBtn);
      list.appendChild(li);
    });
  }

  async function loadUsers(search = "") {
    let url = "../api/user.php";
    if (search) url += `?search=${encodeURIComponent(search)}`;
    const res = await fetch(url);
    const users = await res.json();
    const list = document.getElementById("admin-users-list");
    list.innerHTML = "";
    users.forEach(user => {
      const li = document.createElement("li");
      li.textContent = `${user.username} (${user.is_admin ? "Admin" : "User"}) `;
      if (!user.is_admin) {
        const promoteBtn = document.createElement("button");
        promoteBtn.textContent = "Promote to Admin";
        promoteBtn.onclick = async () => {
          if (confirm("Promote this user to admin? This action cannot be undone.")) {
            await adminApi("promote_admin", { user_id: user.id });
            loadUsers(search);
          }
        };
        li.appendChild(promoteBtn);
      }
      if (user.id !== Number(CURRENT_USER_ID) && !user.is_admin) {
        const delBtn = document.createElement("button");
        delBtn.textContent = "Delete";
        delBtn.onclick = async () => {
          if (confirm("Delete this user? It will also delete their services and all related data.")) {
            await adminApi("delete_user", { user_id: user.id });
            loadUsers(search);
            loadServices();
          }
        };
        li.appendChild(delBtn);
      }
      list.appendChild(li);
    });
  }

  document.getElementById("admin-service-search").addEventListener("input", e => {
    loadServices(e.target.value);
  });

  document.getElementById("admin-user-search").addEventListener("input", e => {
    loadUsers(e.target.value);
  });

  loadCategories();
  loadServices();
  loadUsers();
});