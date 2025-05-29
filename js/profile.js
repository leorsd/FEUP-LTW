document.addEventListener("DOMContentLoaded", async () => {
  try {
    const response = await fetch(`../api/user.php?id=${CURRENT_USER_ID}`);
    if (!response.ok) throw new Error("Failed to load profile data.");
    const user = await response.json();

    document.getElementById("profile-image").src = user.profile_picture
      ? `../images/cache/${user.profile_picture}`
      : "../images/user.jpg";
    document.getElementById("profile-username").textContent = user.username || "";
    document.getElementById("profile-email").textContent = user.email || "";
    document.getElementById("profile-phone").textContent = user.phone || "";
    document.getElementById("profile-age").textContent = user.age || "";
    document.getElementById("profile-location").textContent = user.location || "";
    document.getElementById("profile-bio").textContent = user.bio || "";

    if (user.is_admin) {
      const adminBtn = document.createElement("a");
      adminBtn.href = "admin.php";
      adminBtn.className = "profile-btn admin-panel-btn";
      adminBtn.textContent = "Admin Panel";
      document.querySelector(".profile-bottom-row").appendChild(adminBtn);
    }
  } catch (err) {
    document.getElementById("profile-messages").textContent =
      "Could not load profile: " + err.message;
  }
});