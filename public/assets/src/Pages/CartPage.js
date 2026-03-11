import Utility from "../Classes/Utility.js";
import Cart from "../Classes/Cart.js";
import Checkout from "../Classes/Checkout.js";
import LocationService from "../Classes/LocationService.js";
class CartPage {
  constructor() {
    this.initialize();
  }

  async initialize() {
    // await Cart.getAndSetDeliveryAreas();
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
  const mins = 15;
  const end = Date.now() + mins * 60 * 1000;

  const timer = setInterval(() => {
    const remain = Math.max(0, end - Date.now());
    const m = Math.floor(remain / 60000);
    const s = Math.floor((remain % 60000) / 1000);

    Cart.etaEl.textContent = `${m}m ${s}s`;

    if (remain <= 0) {
      clearInterval(timer);
    }
  }, 1000);
}




  deliveryPickupFunction() {
    const isDeliveryToggled = document.getElementById("deliveryToggle");
    const radioDelivery = document.getElementById("methodDelivery");
    const radioPickup = document.getElementById("methodPickup");
    const orderBtn = Utility.el("placeOrder");

  
    function updateOrderButton() {
        const status = Checkout.checkOrderingStatus(radioDelivery.checked ? "delivery" : "pickup");
        orderBtn.style.display = status ? "block" : "none";
    }

  

    function serviceCheck() {
      
      isDeliveryToggled.classList.toggle("active");
      
        if (isDeliveryToggled.classList.contains("active")) {
          radioPickup.checked = true;
          radioDelivery.checked = false;
       
          Checkout.handlePickupDelivery();
        } else {
          radioDelivery.checked = true;
          radioPickup.checked = false;
          Checkout.handleHomeDelivery();
          
        }

      updateOrderButton();
    }

   
    isDeliveryToggled.addEventListener("click", () => {
       serviceCheck()
    });

    // on page load, set the correct order button visibility
    updateOrderButton();
    serviceCheck()
}


  submitYourOrder() {
    document
      .getElementById("placeOrder")
      .addEventListener("click", Cart.placeOrder);
  }
}

new CartPage();
