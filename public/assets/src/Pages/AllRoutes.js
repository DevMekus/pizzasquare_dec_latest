import Order from "../Classes/Order.js";
import Utility from "../Classes/Utility.js";
import AuthHelper from "./AuthPage.js";

class AllRoutes {
  constructor() {
    this.initialize();
  }

  initialize() {
    Utility.runClassMethods(this, ["initialize"]);
  }

  themeToggle() {
    const themeToggle = document.getElementById("themeToggle");
    const body = document.body;
    if (!themeToggle) return;

    function loadTheme() {
      const theme = localStorage.getItem("theme") || "theme-light";
      body.classList.remove("theme-light", "dark-theme");
      body.classList.add(theme);
      themeToggle.textContent = theme === "dark-theme" ? "☀️" : "🌙";
    }

    function toggleTheme() {
      const isDark = body.classList.contains("dark-theme");
      const newTheme = isDark ? "theme-light" : "dark-theme";
      body.classList.remove("dark-theme", "theme-light");
      body.classList.add(newTheme);
      localStorage.setItem("theme", newTheme);
      themeToggle.textContent = newTheme === "dark-theme" ? "☀️" : "🌙";
    }
    themeToggle.addEventListener("click", toggleTheme);
    loadTheme();
  }

  locationDemo() {
    const el = document.getElementById("userLocation");
    if (!el) return;
    if (!navigator.geolocation) {
      el.textContent = "Delivery near you";
      return;
    }
    navigator.geolocation.getCurrentPosition(
      () => (el.textContent = "Near you"),
      () => (el.textContent = "Delivery near you")
    );
  }

  logoutFunction() {
    const logout = document.querySelector(".logout");
    if (!logout) return;
    AuthHelper.logout();
  }

  displayFooterYear() {
    // year
    const domEl = document.getElementById("year");
    if (domEl) domEl.textContent = new Date().getFullYear();
  }

  newsletterConfetti() {
    // ---------- Newsletter confetti (tiny) ----------
    const domEl = document.getElementById("newsLetterConfetti");
    if (!domEl) return;
    document.getElementById("newsForm").addEventListener("submit", (e) => {
      e.preventDefault();
      // micro-confetti
      for (let i = 0; i < 25; i++) {
        const s = document.createElement("span");
        s.style.position = "fixed";
        s.style.left = Math.random() * 100 + "%";
        s.style.top = "-10px";
        s.style.width = s.style.height = 8 + Math.random() * 6 + "px";
        s.style.background = `hsl(${Math.random() * 360},90%,60%)`;
        s.style.borderRadius = "2px";
        s.style.transform = `rotate(${Math.random() * 360}deg)`;
        s.style.zIndex = 9999;
        s.style.transition =
          "transform 1.2s linear, top 1.2s ease-out, opacity 1.2s";
        document.body.appendChild(s);
        setTimeout(() => {
          s.style.top = "100vh";
          s.style.opacity = 0.8;
          s.style.transform = `translateY(${
            50 + Math.random() * 50
          }vh) rotate(${Math.random() * 720}deg)`;
        }, 10);
        setTimeout(() => s.remove(), 1400);
      }
    });
  }

  checkNetworkConnection() {
    if ("connection" in navigator) {
      const connection = navigator.connection;
      Utility.toast(
        `Effective network type: ${connection.effectiveType}`,
        "info"
      );
      Utility.toast(`Downlink speed:${connection.downlink} Mbps`, "info");

      connection.addEventListener("change", () => {
        if (
          connection.effectiveType.includes("2g") ||
          connection.downlink < 0.5
        ) {
          Utility.toast("⚠️ Your connection is very slow");
        } else {
          Utility.toast("✅ Network looks good");
        }
      });
    }

    window.addEventListener("online", () => {
      Utility.toast("Network is back ✅");
    });

    window.addEventListener("offline", () => {
      Utility.toast("No internet connection ❌");
    });
  }

  KPIBounceCards() {
    function bounceCard() {
      const cards = document.querySelectorAll(".bounce-card");
      cards.forEach((card, idx) => {
        setTimeout(() => {
          card.classList.add("bounce");

          // remove class after animation ends so it can re-trigger
          card.addEventListener(
            "animationend",
            () => card.classList.remove("bounce"),
            { once: true }
          );
        }, idx * 200); // small stagger effect between cards
      });
    }

    setInterval(bounceCard, 5000);
  }

  checkPageLoading() {
    const loader = document.getElementById("pageLoader");

    // Hide loader when everything finishes loading
    window.addEventListener("load", () => {
      loader.style.display = "none";
    });

    // Fallback: hide loader after 10 seconds max
    setTimeout(() => {
      if (loader.style.display !== "none") {
        loader.style.display = "none";
        console.warn("Loader hidden by timeout fallback (10s)");
      }
    }, 10000);
  }

  dashboardSidebarToggle() {
    const sidebar = document.getElementById("sidebar");
    const menuBtn = document.getElementById("menuBtn");
    const overlay = document.getElementById("overlay");
    if (!sidebar || !menuBtn) return;

    menuBtn.addEventListener("click", () => {
      overlay.classList.add("active");
      document.getElementById("sidebar").classList.toggle("open");
    });

    document.addEventListener("click", (e) => {
      const sb = document.getElementById("sidebar");
      if (innerWidth <= 900 && sb.classList.contains("open")) {
        if (
          !sb.contains(e.target) &&
          !document.getElementById("menuBtn").contains(e.target)
        ) {
          sb.classList.remove("open");
          overlay.classList.remove("active");
        }
      }
    });
  }

  orderAlertSystem() {
    const domEl = Utility.el("orderAlert");
    if (!domEl) return;
    Order.orderNotification();
  }
}

new AllRoutes();
