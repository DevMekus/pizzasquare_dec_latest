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
  const itemsRows = data.items
    .map((i) => {
      const toppings =
        Array.isArray(i.toppings) && i.toppings.length
          ? `<div class="text-success small">
               <strong>+ Toppings:</strong> ${i.toppings
                 .map((t) => t.extras)
                 .join(", ")}
             </div>`
          : "";

      const removed =
        Array.isArray(i.removed_ingredients) && i.removed_ingredients.length
          ? `<div class="text-danger small">
               <strong>− Removed:</strong> ${i.removed_ingredients
                 .map((r) => r.ingredient_name)
                 .join(", ")}
             </div>`
          : "";

      return `
        <tr>
          <td>${i.qty}</td>
          <td>
            <strong>${i.title}</strong>
            ${i.size && i.size !== "null" ? `<div class="small text-muted">Size: ${i.size}</div>` : ""}
            ${toppings}
            ${removed}
          </td>
          <td class="text-end">${Utility.fmtNGN(i.price * i.qty)}</td>
        </tr>
      `;
    })
    .join("");

    const summary = `
  <div class="receipt">

    <h4 class="text-center mb-0">Pizza Square Nigeria</h4>
    <div class="text-center small text-muted mb-3">
      Website Order Receipt
    </div>

    <p class="mb-1">Thank you, <strong>${data.name}</strong></p>
    <p class="small text-muted mb-3">
      Order ID: <strong>${data.id}</strong>
    </p>

    <table class="table table-sm">
      <thead class="table-light">
        <tr>
          <th>Qty</th>
          <th>Item</th>
          <th class="text-end">Amount</th>
        </tr>
      </thead>
      <tbody>
        ${itemsRows}
      </tbody>
      <tfoot>
        <tr>
          <th colspan="2">Total</th>
          <th class="text-end">${Utility.fmtNGN(data.total)}</th>
        </tr>
      </tfoot>
    </table>

    <div class="small">
      <strong>Service Method:</strong> ${Cart.method}
    </div>

    <p class="small text-muted mt-2">
      A confirmation has been sent to ${data.email || "your email"}.
    </p>

  </div>
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
    type = "regular",
    removed_ingredients = [],
    promo_product = false,
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
        type,
        removed_ingredients,
        total: price * qty,
        promo_product,
      };
      Cart.cart.push(newItem);
      Utility.toast(`${title} added to your cart.`);
     
    }

    if (Checkout.isPos) {
      Checkout.renderCart();
    }
    Checkout.updateCart();
    Checkout.countCartItem();
    $("#displayDetails").modal("hide");
  }

  // static BUYNOW({
  //   product_id,
  //   title,
  //   size,
  //   size_id,
  //   barbecueSauce = null,
  //   price,
  //   qty,
  //   image,
  //   toppings,
  //   type = "regular",
  //   removed_ingredients = [],
  // }){
  //   //Send payment modal.
  //   //packageOrder();
  //   //submit oder after payment and show order summary.
  //   Cart.placeOrder();
  //   Cart.cart = []; // Clear existing cart
  // }

  // static async handleHomeDelivery() {
  //   Cart.ORDERBTN.disabled = true;
  //   Cart.method = "Delivery";
  //   Cart.deliveryFields.style.display = "block";
  //   Cart.pickupFields.style.display = "none";

  //   Cart.deliveryFeedback.innerHTML = `Searching for your location...${Utility.inlineLoader()}`;
  //   const locationObj = await Utility.detectLocation();

  //   if (!locationObj|| !locationObj.delivery_fee) {
  //     await Cart.renderManualLocation();
  //     Cart.handleManualLocation();
  //     return;
  //   }
    
  //   const rawData = locationObj.raw;
  //   const addresses = rawData["address"];
  //   const currentLocation = addresses["amenity"];

  //   //Location not found
  //   if (!currentLocation || currentLocation == undefined) {
  //     Cart.deliveryFeedback.innerHTML = `<p>We could not detect your location. Select manually</p>`;
  //     await Cart.renderManualLocation();
  //     setTimeout(() => {
  //       Cart.handleManualLocation();
  //     }, 2000);
  //     return;
  //   }

  //   const deliveryFee = locationObj.delivery_fee;
  //   Cart.deliveryArea = addresses.suburb;
  //   Cart.DELIVERY_BASE = Number(deliveryFee);
  //   Cart.deliveryAddress.value = currentLocation;

  //   Cart.deliveryFeedback.innerHTML = "";
  //   Cart.deliveryFeedback.innerHTML = `<p>🚚 Delivery to "<strong>${currentLocation}</strong>" is <em>${Utility.fmtNGN(
  //     deliveryFee
  //   )}</em></p>`;
  //   Checkout.renderCart();
  //   Cart.ORDERBTN.disabled = false;
  // }

  // static async handleManualLocation() {
  //   const locationSelect = Utility.el("manual-locations-select");

  //   function searchLocation(currentArea) {
  //     const deliveryAreas = Cart.DELIVERY_AREAS;

  //     const found = deliveryAreas.find(
  //       (a) =>
  //         currentArea &&
  //         currentArea.toLowerCase().includes(a.city.toLowerCase())
  //     );

  //     if (found) {
  //       Cart.deliveryFeedback.innerHTML = `<p>🚚 Delivery to <strong>${
  //         found.city
  //       }</strong>: <br/>${Utility.fmtNGN(found.delivery_price)}</p>`;
  //       Cart.DELIVERY_BASE = Number(found.delivery_price);
  //       Cart.deliveryArea = found.city;
  //     } else {

  //       if (!Cart.isPos){
  //           Cart.deliveryFeedback.innerHTML = `<p>We could not detect your location. Select manually</p>`;
  //       }    
  //       Cart.ORDERBTN ? Cart.ORDERBTN.disabled = true : null;
  //       return;
  //     }
  //     Cart.ORDERBTN ?Cart.ORDERBTN.disabled = false : null;
  //     Checkout.renderCart();
  //   }

  //   locationSelect.addEventListener("change", (e) => {
  //     searchLocation(e.target.value);
  //   });

  //   searchLocation(locationSelect.value);
  // }

  // static async renderManualLocation() {
  //   const deliveryAreas = Cart.DELIVERY_AREAS;

  //   const areas = deliveryAreas.map((p, idx) => {
  //     const selected = p.idx === 0 ? "selected" : "";
  //     return `<option value="${p.city}" ${selected} data-price="${p.delivery_price}">${p.city}</option>`;
  //   });

  //   Utility.el("manual-delivery").innerHTML = `
  //     <label class="form-label small">Select Delivery Area</label>
  //     <select class="select-tags" id="manual-locations-select">
  //       <option value="">-- Select Area --</option>     
  //       ${areas}
  //     </select>
  //   `;
  // }

  // static handlePickupDelivery() {
  //   Cart.method = "Pickup";
  //   Cart.deliveryFields.style.display = "none";
  //   Cart.pickupFields.style.display = "block";
  //   Cart.DELIVERY_BASE = 0;
  //   Utility.el("address").value = "";
  //   Checkout.renderCart();
  //   Cart.ORDERBTN.disabled = false;
  // }
}
