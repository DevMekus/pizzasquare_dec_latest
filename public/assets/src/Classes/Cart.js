import PaymentChannel from "./PaymentChannel.js";
import Utility from "./Utility.js";
import { HttpRequest } from "../Utils/httpRequest.js";
import { CONFIG } from "../Utils/config.js";
import Checkout from "./Checkout.js";

export default class Cart {
  static cartBody = document.getElementById("cartBody");
  static subtotalEl = document.getElementById("subtotal");
  static taxEl = document.getElementById("tax");
  static deliveryFeeEl = document.getElementById("deliveryFee");
  static discountEl = document.getElementById("discount");
  static grandTotalEl = document.getElementById("grandTotal");
  static sumItemsEl = document.getElementById("sumItems");
  static couponEl = document.getElementById("coupon");
  static etaEl = document.getElementById("eta");
  static mobileTotalEl = document.getElementById("mobileTotal");
  static ORDERBTN = Utility.el("placeOrder");

  static deliveryFields = Utility.el("deliveryFields");
  static pickupFields = Utility.el("pickupFields");
  static deliveryFeedback = Utility.el("areaDeliveryFee");
  static deliveryAddress = Utility.el("address");
  static deliveryArea = "";

  static GRANDTOTAL = 0;

  static cart = localStorage.getItem("cart")
    ? JSON.parse(localStorage.getItem("cart"))
    : [];

  static TAX_RATE = 0;
  static DELIVERY_BASE = 0;
  static discountRate = 0;
  static method = "Delivery";

  static DELIVERY_AREAS = [];

  static SIZES = {
    Small: 0,
    Medium: 800,
    Large: 1500,
  };

  static COUPONS = {
    PIZZA10: 0.1,
    WELCOME5: 0.05,
  };

  static async getAndSetDeliveryAreas() {
    const response = await HttpRequest(`${CONFIG.API}/city`);
    Cart.DELIVERY_AREAS = response.success ? response.data : [];
  }

  static async placeOrder() {
    try {
      const transaction = await Checkout.packageOrder();

      console.log(transaction);    
     
      if (transaction.proceed) {        
        const makePayment = await PaymentChannel.payWithPaystack(transaction);

        if (!makePayment.success) {
          Utility.toast("payment verification failed");
          Utility.SweetAlertResponse({success:false, message:"Payment verification failed"});
          return;
        }

        Utility.alertLoader()

        const sendOrder = await HttpRequest(
          `${CONFIG.API}/orders/create`,
          transaction,
          "POST"
        );

         console.log(sendOrder);   

        Utility.clearAlertLoader()
        if (!sendOrder.success) {         
          Utility.SweetAlertResponse({success:false, message:sendOrder.message});
          return;
        }

       Cart.transactionSummary({
          total: Cart.GRANDTOTAL,
          items: Cart.cart,
          name: transaction.customer_name,
          email: transaction.email_address,
          id: transaction.order_id,
        });
      }
    } catch (error) {
      Utility.toast("An error has occurred");
    }
  }

  static transactionSummary(data) {
    const itemsHtml = data.items
      .map(
        (i) => {
            let toppingsHtml = "";
            if (Array.isArray(i.toppings) && i.toppings.length > 0) {
              const extrasList = i.toppings.map((t) => t.extras).join(", ");
              toppingsHtml = ` - Toppings: ${extrasList}`;
            }
          return `<li>${i.qty} x ${i.title} ${i.size && i.size !== "null" ? `(${i.size})` : ""} ${toppingsHtml} - ${Utility.fmtNGN(
            i.price * i.qty
          )}</li>`;
        }
      )
      .join("");

    const summary = `
      <p>Thank you, <strong>${data.name}</strong>! </p>
      <p>Your order has been placed and your order id is <strong>${
        data.id
      }</strong>.</p>
      <ul class="list-unstyled">
        <li><strong>Item(s) ordered:</strong>
          <ul class="ms-3">
            ${itemsHtml}
          </ul>
        </li>
        <li><strong>Total:</strong> ${Utility.fmtNGN(data.total)}</li>
        <li><strong>Method:</strong> ${Cart.method}</li>
      </ul>
      <p class="small text-muted">A confirmation has been sent to ${
        data.email || "your email"
      }.</p>
     
    `;

    document.getElementById("successBody").innerHTML = summary;
    new bootstrap.Modal(document.getElementById("successModal")).show();

    // Reset
    Cart.cart = [];
    Checkout.updateCart();
    Utility.toast("Cart cleared");
    Checkout.renderCart();
  }

  static addToCart({
    product_id,
    title,
    size,
    size_id,
    barbecueSauce = null,
    price,
    qty,
    image,
    toppings,
  }) {
    const existingIndex = Cart.cart.findIndex(
      (item) => item.id === product_id && item.size === size
    );

    if (existingIndex !== -1) {
      Cart.cart[existingIndex].qty += qty;
      Utility.toast(`${title} qty updated.`);
    } else {
      const newItem = {
        id: product_id,
        title,
        size: size ?? null,
        size_id: size_id ?? null,
        barbecueSauce,
        price,
        qty,
        image,
        toppings,
        total: price * qty,
      };
      Cart.cart.push(newItem);
      Utility.toast(`${title} added to Order.`);
     
    }

    if (Checkout.isPos) {
      Checkout.renderCart();
    }
    Checkout.updateCart();
    Checkout.countCartItem();
    $("#displayDetails").modal("hide");
  }

  static async handleHomeDelivery() {
    Cart.ORDERBTN.disabled = true;
    Cart.method = "Delivery";
    Cart.deliveryFields.style.display = "block";
    Cart.pickupFields.style.display = "none";

    Cart.deliveryFeedback.innerHTML = `Searching for your location...${Utility.inlineLoader()}`;
    const locationObj = await Utility.detectLocation();

    if (!locationObj) {
      await Cart.renderManualLocation();
      Cart.handleManualLocation();
      return;
    }

    const rawData = locationObj.raw;
    const addresses = rawData["address"];
    const currentLocation = addresses["amenity"];

    //Location not found
    if (!currentLocation || currentLocation == undefined) {
      Cart.deliveryFeedback.innerHTML = `We could not detect your location. Select manually`;
      await Cart.renderManualLocation();
      setTimeout(() => {
        Cart.handleManualLocation();
      }, 2000);
      return;
    }
    const deliveryFee = locationObj.delivery_fee;
    Cart.deliveryArea = addresses.suburb;
    Cart.DELIVERY_BASE = Number(deliveryFee);
    Cart.deliveryAddress.value = currentLocation;

    Cart.deliveryFeedback.innerHTML = "";
    Cart.deliveryFeedback.innerHTML = `<p>🚚 Delivery to "<strong>${currentLocation}</strong>" is <em>${Utility.fmtNGN(
      deliveryFee
    )}</em></p>`;
    Checkout.renderCart();
    Cart.ORDERBTN.disabled = false;
  }

  static async handleManualLocation() {
    const locationSelect = Utility.el("manual-locations-select");

    function searchLocation(currentArea) {
      const deliveryAreas = Cart.DELIVERY_AREAS;

      const found = deliveryAreas.find(
        (a) =>
          currentArea &&
          currentArea.toLowerCase().includes(a.city.toLowerCase())
      );

      if (found) {
        Cart.deliveryFeedback.innerHTML = `<p>🚚 Delivery to <strong>${
          found.city
        }</strong>: ${Utility.fmtNGN(found.delivery_price)}</p>`;
        Cart.DELIVERY_BASE = Number(found.delivery_price);
        Cart.deliveryArea = found.city;
      } else {
        Cart.deliveryFeedback.innerHTML = `<p>⚠️ We don’t have a set price for this area</p>`;
        Cart.ORDERBTN.disabled = true;
        return;
      }
      Cart.ORDERBTN.disabled = false;
      Checkout.renderCart();
    }

    locationSelect.addEventListener("change", (e) => {
      searchLocation(e.target.value);
    });

    searchLocation(locationSelect.value);
  }

  static async renderManualLocation() {
    const deliveryAreas = Cart.DELIVERY_AREAS;

    const areas = deliveryAreas.map((p, idx) => {
      const selected = p.idx === 0 ? "selected" : "";
      return `<option value="${p.city}" ${selected} data-price="${p.delivery_price}">${p.city}</option>`;
    });

    Utility.el("manual-delivery").innerHTML = `
      <label class="form-label small">Select Delivery Area</label>
      <select class="select-tags" id="manual-locations-select">     
        ${areas}
      </select>
    `;
  }

  static handlePickupDelivery() {
    Cart.method = "Pickup";
    Cart.deliveryFields.style.display = "none";
    Cart.pickupFields.style.display = "block";
    Cart.DELIVERY_BASE = 0;
    Utility.el("address").value = "";
    Checkout.renderCart();
    Cart.ORDERBTN.disabled = false;
  }
}
