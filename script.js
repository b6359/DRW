document.addEventListener("DOMContentLoaded", () => {
  fetch("sidebar.html")
    .then(res => res.text())
    .then(html => {
      document.getElementById("sidebar-placeholder").innerHTML = html;

      displayLoggedInUser();

      const sidebar = document.querySelector(".sidebar");
      const sidebarBtn = document.querySelector(".bx-menu");
      const mainBody = document.querySelector('.Main-body');

      // Initialize sidebar events (click on menu to toggle open/close)
      initSidebarEvents(sidebar, sidebarBtn, mainBody);

      // Load the last page visited (if any)
      const lastPage = localStorage.getItem("lastPage");
      if (lastPage) {
        fetchPage(lastPage);
      } else {
        const defaultPage = "pages/dashboard.html";
        localStorage.setItem("lastPage", defaultPage);
        fetchPage(defaultPage);
      }
    });
});

function initSidebarEvents(sidebar, sidebarBtn, mainBody) {
  // Ensure sidebar is always open initially on page load
  sidebar.classList.remove("close");
  mainBody.classList.remove("sidebar-closed");

  // Handle the sidebar button click to toggle the sidebar open/close
  if (sidebar && sidebarBtn) {
    sidebarBtn.addEventListener("click", () => {
      sidebar.classList.toggle("close");
      mainBody.classList.toggle('sidebar-closed');
      // Don't save the state in localStorage; it's only toggled based on click
    });
  }

  // Handle arrows for submenus
  document.querySelectorAll(".arrow").forEach(arrow => {
    arrow.addEventListener("click", e => {
      const arrowParent = e.target.closest("li");
      arrowParent.classList.toggle("showMenu");
    });
  });

  // Handle navigation links to load pages
  document.querySelectorAll(".nav-links a").forEach(link => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const page = this.getAttribute("href");
      localStorage.setItem("lastPage", page);
      fetchPage(page);
    });
  });
}

function displayLoggedInUser() {
  const userName = localStorage.getItem("loggedInUser");
  const userEl = document.getElementById("loggedInUser");
  if (userName && userEl) {
    userEl.textContent = userName;
  }
}


function fetchPage(page) {
  fetch(page)
    .then(response => {
      if (!response.ok) throw new Error("Page not found: " + page);
      return response.text();
    })
    .then(html => {
      const container = document.querySelector(".page-content");
      container.innerHTML = html;

      // Load corresponding script and initialize after load
      if (page.includes("item-master")) {
        loadScript("scripts/item-master.js", () => {
          if (typeof initializeItemMaster === "function") {
            initializeItemMaster();
          }
        });
      } else if (page.includes("product-master")) {
        loadScript("scripts/product-master.js", () => {
          if (typeof initializeProductMaster === "function") {
            initializeProductMaster();
          }
        });
      }
    })
    .catch(error => {
      document.querySelector(".page-content").innerHTML =
        `<p>Error loading page: ${error.message}</p>`;
    });
}

function loadScript(src, callback) {
  const existing = document.querySelector(`script[src="${src}"]`);
  if (existing) {
    existing.remove(); // prevent duplicate loading
  }

  const script = document.createElement("script");
  script.src = src;
  script.defer = true;
  script.onload = callback;
  document.body.appendChild(script);
}
