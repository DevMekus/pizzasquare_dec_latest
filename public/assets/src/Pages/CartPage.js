import Utility from "../Classes/Utility.js";
import Cart from "../Classes/Cart.js";
import Checkout from "../Classes/Checkout.js";
class CartPage {
  constructor() {
    this.initialize();
  }

  async initialize() {
    await Cart.getAndSetDeliveryAreas();
    await Checkout.getAndSetVAT();    
    Utility.runClassMethods(this, ["initialize"]);
  }

  renderCart() {
    const cartBody = document.getElementById("cartBody");
    if (!cartBody) return;
    Checkout.isPos = false;
    Checkout.renderCart();
    Checkout.applyCoupon();
  }

  startETA() {
    const start = Date.now();
    const mins = 30; // average
    const end = start + mins * 60 * 1000;
    const tick = () => {
      const remain = Math.max(0, end - Date.now());
      const m = Math.floor(remain / 60000);
      const s = Math.floor((remain % 60000) / 1000);
      Cart.etaEl.textContent = `${m}m ${s}s`;
      if (remain > 0) requestAnimationFrame(tick);
    };
    tick();
  }

  

  deliveryPickupFunction() {
    const toggle = document.getElementById("deliveryToggle");
    const radioDelivery = document.getElementById("methodDelivery");
    const radioPickup = document.getElementById("methodPickup");
    const orderBtn = Utility.el("placeOrder");

    function updateOrderButton() {
        const status = Checkout.checkOrderingStatus(radioDelivery.checked ? "delivery" : "pickup");
        orderBtn.style.display = status ? "block" : "none";
    }

    // Initial check
    updateOrderButton();

    if (radioDelivery.checked) Cart.handleHomeDelivery();

    toggle.addEventListener("click", () => {
        toggle.classList.toggle("active");
        Cart.deliveryFeedback.innerHTML = ``;

        if (toggle.classList.contains("active")) {
            radioPickup.checked = true;
            radioDelivery.checked = false;
            Cart.handlePickupDelivery();
        } else {
            radioDelivery.checked = true;
            radioPickup.checked = false;
            Cart.handleHomeDelivery();
        }

        updateOrderButton();
    });
}


  submitYourOrder() {
    document
      .getElementById("placeOrder")
      .addEventListener("click", Cart.placeOrder);
  }
}

new CartPage();
