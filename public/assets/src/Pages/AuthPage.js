import { CONFIG } from "../Utils/config.js";
import Utility from "../Classes/Utility.js";
import { HttpRequest } from "../Utils/httpRequest.js";
import { destroyCurrentSession, startNewSession } from "../Classes/Session.js";

/**
 * @class AuthHelper
 * Provides utility methods for authentication forms, including loading state management.
 */
export default class AuthHelper {
  // DOM Elements
  static submitBtn = document.getElementById("submitBtn");
  static btnText = document.getElementById("btnText");
  static btnSpinner = document.getElementById("btnSpinner");

  static emailInput = document.getElementById("email");
  static emailError = document.getElementById("emailError");
  static pwError = document.getElementById("pwError");
  static password = document.getElementById("password");

  static loginForm = document.getElementById("loginForm");
  static register = document.getElementById("registerForm");
  static recoverForm = document.getElementById("recoverForm");
  static resetForm = document.getElementById("resetForm");

  static _originalLabel = null;

  /**
   * Toggle loading state for submit buttons.
   * @param {boolean} on
   */
  static setLoading(on) {
    try {
      if (!AuthHelper._originalLabel) {
        AuthHelper._originalLabel = AuthHelper.btnText?.textContent || "";
      }

      if (on) {
        AuthHelper.btnSpinner.style.display = "inline-block";
        AuthHelper.btnText.textContent = "Please wait...";
        AuthHelper.submitBtn.disabled = true;
      } else {
        AuthHelper.btnSpinner.style.display = "none";
        AuthHelper.btnText.textContent = AuthHelper._originalLabel;
        AuthHelper.submitBtn.disabled = false;
      }
    } catch (error) {
      console.error("Error toggling loading state:", error);
    }
  }

  static redirect(token) {
    const decryptToken = jwt_decode(token);
    const { userid, email, role } = decryptToken;
    let url = null;

    if (role == "1") url = ``;
    if (role == "2") url = `secure/management/overview`;
    if (role == "3") url = `secure/pos/overview`;    
    if (role == "4") url = `secure/admin/overview`;

    setTimeout(() => {
      window.location.href = `${CONFIG.BASE_URL}/${url}`;
    }, CONFIG.TIMEOUT);
  }

  static logout() {
    const logout = document.querySelector(".logout");
    if (!logout) return;

    logout.addEventListener("click", async () => {
      const result = await Utility.confirm("Logging out?");
      const userid = logout.dataset.id;
      if (result.isConfirmed) {
        Utility.toast("Please wait...", "info");

        const response = await HttpRequest(
          `${CONFIG.API}/auth/logout`,
          { userid },
          "POST"
        );

         Utility.SweetAlertResponse(response);
        response.success && destroyCurrentSession();
      }
    });
  }
}

/**
 * @class AuthPage
 * Manages login, registration, password recovery, and reset forms.
 */
class AuthPage {
  constructor() {
    this.initialize();
  }

  /**
   * Initialize the page and bind all methods.
   */
  initialize() {
    Utility.runClassMethods(this, ["initialize"]);
  }

  /**
   * Show/hide password toggle functionality.
   */
  showHidePassword() {
    try {
      const pwToggle = document.querySelector(".pw-toggle");
      if (!pwToggle || !AuthHelper.password) return;

      pwToggle.addEventListener("click", () => {
        const showing = AuthHelper.password.type === "text";
        AuthHelper.password.type = showing ? "password" : "text";
        pwToggle.setAttribute(
          "aria-label",
          showing ? "Show password" : "Hide password"
        );
        pwToggle.innerHTML = showing
          ? '<i class="bi bi-eye"></i>'
          : '<i class="bi bi-eye-slash"></i>';
      });
    } catch (error) {
      console.error("Error toggling password visibility:", error);
    }
  }

  /**
   * Bind login form events.
   */
  async login() {
    try {
      if (!AuthHelper.loginForm) return;

      AuthHelper.emailInput?.addEventListener("input", () =>
        Utility.validateEmail(AuthHelper.emailInput, AuthHelper.emailError)
      );
      AuthHelper.password?.addEventListener("input", () =>
        Utility.validatePassword(AuthHelper.password, AuthHelper.pwError)
      );

      AuthHelper.loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const ok =
          Utility.validateEmail(AuthHelper.emailInput, AuthHelper.emailError) &
          Utility.validatePassword(AuthHelper.password, AuthHelper.pwError);

        if (!ok) {
          Utility.toast("Please fix the errors in the form", "error");
          return;
        }

        AuthHelper.setLoading(true);

        const formData = Utility.toObject(new FormData(e.target));

        const response = await HttpRequest(
          `${CONFIG.API}/auth/login`,
          formData,
          "POST"
        );

      Utility.SweetAlertResponse(response);

        if (!response.success) {
          AuthHelper.setLoading(false);
          Utility.toast(`${response.message}`, "error");
          return;
        }

        AuthHelper.setLoading(false);
        Utility.toast(response.message, response.success ? "success" : "error");

        if (response.success) {
          const session = await startNewSession(response.data.token);
          if (session.success) {
            Utility.toast("Welcome back! Redirecting...", "success");
            AuthHelper.redirect(response.data.token);
          }
        }
      });
    } catch (error) {
      console.error("Error in login process:", error);
      Utility.toast("Login failed. Please try again.", "error");
      AuthHelper.setLoading(false);
    }
  }

  /**
   * Bind registration form events.
   */
  async register() {
    try {
      if (!AuthHelper.register) return;

      AuthHelper.emailInput?.addEventListener("input", () =>
        Utility.validateEmail(AuthHelper.emailInput, AuthHelper.emailError)
      );
      AuthHelper.password?.addEventListener("input", () =>
        Utility.validatePassword(AuthHelper.password, AuthHelper.pwError)
      );

      AuthHelper.register.addEventListener("submit", async (e) => {
        e.preventDefault();

        const ok =
          Utility.validateEmail(AuthHelper.emailInput, AuthHelper.emailError) &
          Utility.validatePassword(AuthHelper.password, AuthHelper.pwError);

        if (!ok) {
          Utility.toast("Please fix the errors in the form", "error");
          return;
        }

      
        const result = await Utility.confirm("Create account?");
        if (result.isConfirmed) {
            AuthHelper.setLoading(true);
            const formData = new FormData(e.target);
       
            const response = await HttpRequest(
            `${CONFIG.API}/auth/register`,
                formData,
                "POST"
            );

           AuthHelper.setLoading(false);

          if (!response.success) {
            Utility.toast("An error has occurred", "error");
            return;
          } 
          Utility.toast("Registration successful. Please sign in.", "success");
          setTimeout(() => {
            window.location.href = `${CONFIG.BASE_URL}/auth/login?f-bk=new`;
          }, CONFIG.TIMEOUT);
        }
      });
    } catch (error) {
      console.error("Error in registration process:", error);
      Utility.toast("Registration failed. Please try again.", "error");
      AuthHelper.setLoading(false);
    }
  }

  /**
   * Bind account recovery form events.
   */
  async recoverAccount() {
    try {
      if (!AuthHelper.recoverForm) return;

      AuthHelper.emailInput?.addEventListener("input", () =>
        Utility.validateEmail(AuthHelper.emailInput, AuthHelper.emailError)
      );

      AuthHelper.recoverForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const ok = Utility.validateEmail(
          AuthHelper.emailInput,
          AuthHelper.emailError
        );
        if (!ok) {
          Utility.toast("Please fix the errors in the form", "error");
          return;
        }

        AuthHelper.setLoading(true);
        const formData = Utility.toObject(new FormData(e.target));

        const response = await HttpRequest(
          `${CONFIG.API}/auth/recover`,
          formData,
          "POST"
        );
        console.log(response)

        AuthHelper.setLoading(false);

        Utility.SweetAlertResponse(response);

        if (response.success) {
          Utility.toast(
            response.message,
            response.success ? "success" : "error"
          );
          const messageContainer = document.getElementById("message");

          if (messageContainer) {
            messageContainer.innerHTML = `<p class="bold ${
              response.success ? "success" : "danger"
            }">${response.message}</p>`;
          }
        }
      });
    } catch (error) {
      console.error("Error in account recovery:", error);
      AuthHelper.setLoading(false);
    }
  }

  /**
   * Bind password reset form events.
   */
  async resetPassword() {
    try {
      if (!AuthHelper.resetForm) return;

      AuthHelper.password?.addEventListener("input", () =>
        Utility.validatePassword(AuthHelper.password, AuthHelper.pwError)
      );

      AuthHelper.resetForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const ok = Utility.validatePassword(
          AuthHelper.password,
          AuthHelper.pwError
        );
        if (!ok) {
          Utility.toast("Please fix the errors in the form", "error");
          return;
        }

        AuthHelper.setLoading(true);
        const formData = Utility.toObject(new FormData(e.target));

        const response = await HttpRequest(
          `${CONFIG.API}/auth/reset`,
          formData,
          "POST"
        );

        AuthHelper.setLoading(false);

        Utility.SweetAlertResponse(response);

        if (response.success) {
          Utility.toast(
            response.message,
            response.success ? "success" : "error"
          );
          if (response.success) {
            setTimeout(() => {
              window.location.href = `${CONFIG.BASE_URL}/auth/login`;
            }, CONFIG.TIMEOUT);
          }
        }
      });
    } catch (error) {
      console.error("Error resetting password:", error);
      AuthHelper.setLoading(false);
    }
  }

  /**
   * Show feedback messages based on URL parameters.
   */
  pageFeedback() {
    try {
      const params = new URLSearchParams(document.location.search);
      const dom = Utility.el("a-info")
      const urlParam = params.get("f-bk");
      if (!urlParam) return;

      if (urlParam === "UNAUTHORIZED"){
        Utility.toast("UNAUTHORIZED! Please sign in", "error");
        dom.innerHTML = `<span class="bold color-red">Sign in to continue</span>`;
      }
       
      if (urlParam === "logout"){
        Utility.toast("Logout successful", "success");
        dom.innerHTML = `<span class="bold color-red">Logout successful</span>`;
      }

      if (urlParam === "new"){
        Utility.toast("Registration Successful", "success");
        dom.innerHTML = `<span class="bold color-success">Registration Successful. Login to continue</span>`;
      }
       if (urlParam === "ctrue"){
        Utility.toast("Login to continue", "success");
        dom.innerHTML = `<span class="bold color-red">Login to continue</span>`;
      }
        
    } catch (error) {
      console.error("Error showing page feedback:", error);
    }
  }

  auth_side_products() {
    const heroImage = document.querySelector(".hero-visual .product");
    if (!heroImage) return;

    const images = [
      `${CONFIG.BASE_URL}/assets/images/auth/1.jpg`,  
      `${CONFIG.BASE_URL}/assets/images/auth/3.jpg`,
      `${CONFIG.BASE_URL}/assets/images/auth/4.jpg`,
      `${CONFIG.BASE_URL}/assets/images/auth/5.jpg`,
    ];

    let currentIndex = 0;

    setInterval(() => {
      heroImage.style.opacity = 0;
      setTimeout(() => {
        currentIndex = (currentIndex + 1) % images.length;
        heroImage.innerHTML = `<img src="${images[currentIndex]}" alt="Car illustration" />`;
        heroImage.style.opacity = 1;
      }, 500);
    }, 5000);
  }
}

// Initialize authentication page
new AuthPage();
