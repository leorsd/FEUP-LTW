document.addEventListener("DOMContentLoaded", () => {
  const searchForm = document.getElementById("search-bar");
  const targetPage = "../pages/home.php";
  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const value = searchForm.elements["search"].value.trim();
      if (value !== "") {
        window.location.href = `${targetPage}?search=${encodeURIComponent(value)}`;
      }
    });
  }
});