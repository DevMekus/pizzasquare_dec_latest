import Cart from "./Cart.js";
import Utility from "./Utility.js";
import { HttpRequest } from "../Utils/httpRequest.js";
import { CONFIG } from "../Utils/config.js";
import Pagination from "./Pagination.js";

/**
 * Reusable functions for the checkouty and POS
 */
export default class Checkout {
  static isPos = false;
  static cartBody = document.getElementById("cartBody");
  static COUPONS = [];

  static async fetchVAT() {
    const response = await HttpRequest(`${CONFIG.API}/vat`);
    return response.success ? response.data[0] : [];
  }

  static async getAndSetVAT() {
    const vat = await Checkout.fetchVAT();
    const vatPercentEl = document.getElementById("vatPercent");
    if (vatPercentEl) vatPercentEl.textContent = parseFloat(vat.vat) * 100;

    Cart.TAX_RATE = parseFloat(vat.vat);
  }

  static async deleteCoupon(id) {
    const result = await Utility.confirm("Delete Coupon?");
    if (result.isConfirmed) {
      //---Send to Server

      Utility.alertLoader()
      const response = await HttpRequest(
        `${CONFIG.API}/admin/coupon/${id}`,
        {},
        "DELETE"
      );

      Utility.clearAlertLoader()
      Utility.toast(
        `${response.message}`,
        `${response.success ? "success" : "error"}`
      );
      Utility.SweetAlertResponse(response);

      return response.success;
    } else {
      Utility.toast("Action cancelled");
    }
  }

  static async createCoupon(data) {
    $("#couponModal").modal("hide");
    const result = await Utility.confirm("Create new Coupon?");
    if (result.isConfirmed) {
      //---Send to Server
      Utility.alertLoader()
      const response = await HttpRequest(
        `${CONFIG.API}/admin/coupon`,
        data,
        "POST"
      );

      Utility.clearAlertLoader()

      Utility.toast(
        `${response.message}`,
        `${response.success ? "success" : "error"}`
      );

      Utility.SweetAlertResponse(response);

      return response.success;
    } else {
      Utility.toast("Action cancelled");
    }
  }

  static updateCart() {
    localStorage.setItem("cart", JSON.stringify(Cart.cart));
  }

  static renderCart() {
    Checkout.isPos ? Checkout.renderCartPos() : Checkout.renderCartWeb();
    Checkout.countCartItem();
    Checkout.bindCartControls();
    Checkout.renderSummary();
    Checkout.clearCartItems();
  }
  static renderSummary() {
    const t = Checkout.calcTotals();

    Cart.sumItemsEl.textContent = t.items;
    Cart.subtotalEl.textContent = Utility.fmtNGN(t.subtotal);
    Cart.taxEl.textContent = Utility.fmtNGN(t.tax);
    Cart.deliveryFeeEl.textContent = Utility.fmtNGN(Cart.DELIVERY_BASE || 0);
    Cart.discountEl.textContent = Utility.fmtNGN(t.discount);
    Cart.grandTotalEl.textContent = Utility.fmtNGN(t.total);
    Cart.GRANDTOTAL = t.total;
  }

  static renderCartPos() {
    if (Cart.cart.length === 0) {
      Checkout.cartBody.innerHTML =
        '<div class="text-center text-muted py-4">Your cart is empty</div>';
    } else {
      Checkout.cartBody.innerHTML = Cart.cart
        .map((it) => {
          return `         
              <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                <div>
                  <div>
                    <span class="fw-semibold text">${it.title}</span>
                    <span class="muted">
                      ${it.size && it.size !== "null" ? `(${it.size})` : ""}
                    </span>

                  </div>
                  <div class="small muted text">
                  ${Utility.fmtNGN(it.price)} × 
                  ${it.qty}</div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="qty-btn" 
                      data-id="${it.id}" data-op="dec">-</button>
                      <span class="px-2">${it.qty}</span>
                    <button class="qty-btn" 
                      data-id="${
                        it.id
                      }" data-op="inc">+</button>                  
                  <button class="btn btn-sm btn-outline-error" 
                  data-id="${it.id}" data-op="remove"><i class="bi bi-x"></i>
                  </button>
                </div>
              </div>
            `;
        })
        .join("");
    }
  }
  static renderCartWeb() {
    if (!Cart.cart || Cart.cart.length === 0) {
      Cart.cartBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Your cart is empty</td></tr>`;
      return;
    }

    Checkout.cartBody.innerHTML = Cart.cart
      .map((item) => {
        const lineTotal = item.price * item.qty;
        let toppingsHtml = "";
         if (Array.isArray(item.toppings) && item.toppings.length > 0) {
          const extrasList = item.toppings.map((t) => t.extras).join(", ");
          toppingsHtml = `
          <br/>
          <small>
            <strong>Toppings:</strong> ${extrasList}
          </small>`;
          }       
        return `
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img class="cart-thumb" 
                      src="${item.image}" alt="${item.title}">
                      <div>
                        <div class="fw-semibold">${item.title}</div>
                        <p class="muted"> ${
                          item.size && item.size !== "null" ? `(${item.size})` : ""
                        }</p>
                        ${toppingsHtml}
                      </div>
                    </div>
                  </td>             
                  <td class="text-center">
                    <div class="d-inline-flex align-items-center gap-1">
                      <button class="qty-btn" 
                      data-id="${item.id}" data-op="dec">-</button>
                      <span class="px-2">${item.qty}</span>
                      <button class="qty-btn" 
                      data-id="${item.id}" data-op="inc">+</button>
                    </div>
                  </td>
                  <td class="text-end">${Utility.fmtNGN(item.price)}</td>
                  <td class="text-end">${Utility.fmtNGN(lineTotal)}</td>
                  <td class="text-end">
                  <button class="btn btn-sm btn-outline-error" 
                  data-id="${
                    item.id
                  }" data-op="remove"><i class="bi bi-x"></i></button></td>
                </tr>
              `;
      })
      .join("");
  }
  static countCartItem() {
    const cartCount = Utility.el("cartCount");
    if (!cartCount) return;

    let count = Cart.cart.reduce((sum, p) => sum + p.qty, 0);

    count > 0 && cartCount.classList.add("badge");
    cartCount.textContent = count != 0 ? count : "";
  }

  static bindCartControls() {
    Checkout.cartBody.querySelectorAll("[data-op]")?.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const id = btn.dataset.id;
        const op = btn.dataset.op;

        const idx = Cart.cart.findIndex((x) => String(x.id) == String(id));

        if (idx === -1) return;
        if (op === "inc") Cart.cart[idx].qty++;
        if (op === "dec") {
          Cart.cart[idx].qty--;
          if (Cart.cart[idx].qty <= 0) {
            Cart.cart.splice(idx, 1);
          }
        }
        if (op === "remove") Cart.cart.splice(idx, 1);
        Checkout.updateCart();
        Checkout.renderCart();
      });
    });
  }

  static calcTotals() {
    const items = Cart.cart.reduce((s, x) => s + x.qty, 0);
    const subtotal = Cart.cart.reduce((s, x) => s + x.price * x.qty, 0);
    const tax = Math.round(subtotal * Cart.TAX_RATE);

    const delivery =
      Cart.method === "Delivery" && subtotal > 0 ? Cart.DELIVERY_BASE : 0;

    const discount = Math.round(subtotal * Cart.discountRate);
    const total = Math.max(0, subtotal + tax + delivery - discount);  
    return {
      items,
      subtotal,
      tax,
      delivery,
      discount,
      total,
    };
  }

  static async fetchCOUPONS() {
    const response = await HttpRequest(`${CONFIG.API}/coupon`);
    return response.success ? response.data : [];
  }

  static applyCoupon() {
    document
      .getElementById("applyCoupon")
      .addEventListener("click", async () => {
        const code = Cart.couponEl.value.trim().toUpperCase();
        if (!code) return;

        //fetch coupons
        Utility.toast("Searching...");
        Utility.alertLoader();    
        const coupons = await Checkout.fetchCOUPONS();
        Utility.clearAlertLoader();

        //Coupons not available
        if (coupons.length === 0) {
          Cart.discountRate = 0;
          Utility.toast("Invalid coupon");
          Checkout.renderCart();
          return;
        }

        const couponExist = coupons.find((coupon) => coupon["coupon"] == code);

        //Coupon not found
        if (!couponExist || couponExist.length === 0) {
          Cart.discountRate = 0;
          Utility.toast("Invalid coupon");
          Checkout.renderCart();
          return;
        }

        Cart.discountRate = parseFloat(couponExist.discount);

        Utility.toast(
          `Coupon applied: ${Math.round(Cart.discountRate * 100)}% off`
        );
        //!TODO: Save to DB
        Checkout.renderCart();
      });
  }

  static clearCartItems() {
    document.getElementById("clearCart").addEventListener("click", () => {
      Cart.cart = [];
      Checkout.updateCart();
      Checkout.renderCart();
      Utility.toast("Cart cleared");
    });
  }

  static async packageOrder() {
    let proceed = true;

    if (Cart.cart.length === 0 || parseFloat(Cart.GRANDTOTAL) <= 0) {
      Utility.toast("Your cart is empty");

      Utility.SweetAlertResponse({
        success: false,
        message: "Your cart is empty",
      });
      proceed = false;
    }

    const t = Checkout.calcTotals();
    const customer = {
      name: document.getElementById("name").value.trim(),
      phone: document.getElementById("phone")?.value.trim(),
      email: document.getElementById("email")?.value.trim() || null,
      location: "",
      locationArea: "",
    };

    if (!customer.name || !customer.phone || customer.phone.length < 10) {
      Utility.SweetAlertResponse({
        success: false,
        message: "Customer name and valid phone number are required",
      });
      proceed = false;
    }

    if (Checkout.isPos) {
      const cash = Utility.el("cashAmount")
        ? parseFloat(Utility.el("cashAmount").value || 0)
        : 0;
      const card = Utility.el("cardAmount")
        ? parseFloat(Utility.el("cardAmount").value || 0)
        : 0;
      const transfer = Utility.el("transferAmount")
        ? parseFloat(Utility.el("transferAmount").value || 0)
        : 0;

          if (cash == 0 && card == 0 && transfer == 0) {
            Utility.SweetAlertResponse({
              success: false,
              message: "At least one payment method is required",
            });
            proceed = false;
          }
    }
    

    return {
      order_id: Utility.generateId(),
      total_amount: Cart.GRANDTOTAL,
      cart: Cart.cart,
      customer_name: customer.name,
      email_address: customer.email,
      customer_type: Checkout.isPos ? "walk_in" : "website",
      delivery_address: Cart.deliveryAddress ? Cart.deliveryAddress.value : "",
      city: Cart.deliveryArea ? Cart.deliveryArea : "",
      delivery_type: !Checkout.isPos ? Cart.method : "pickup",
      customer_phone: Utility.el("phone")?.value  || null,
      order_note: Utility.el("instructions")?.value || null,
      userid: !Checkout.isPos ? Utility.el("userid")?.value  : null,
      attendant: Utility.el("attendant")?.value || null,

      payment: {
        payment_type: Utility.el("splitPaymentCheck")?.checked ? "split" : "single",
        item_amount: parseFloat(Cart.GRANDTOTAL) - parseFloat(Cart.method === "Delivery" ? Cart.DELIVERY_BASE : 0),
        total_paid: parseFloat(Cart.GRANDTOTAL),
        cash: Utility.el("cashAmount") ? parseFloat(Utility.el("cashAmount").value || 0) : 0,
        card: Utility.el("cardAmount") ? parseFloat(Utility.el("cardAmount").value || 0) : 0,
        online: !Checkout.isPos ? parseFloat(Cart.GRANDTOTAL) - parseFloat(Cart.method === "Delivery" ? Cart.DELIVERY_BASE : 0) : 0,
        transfer: Utility.el("transferAmount") ? parseFloat(Utility.el("transferAmount").value || 0) : 0,
        delivery_fee:
          Cart.method === "Delivery" ? Cart.DELIVERY_BASE : 0,
      },
      proceed,
    };
  }

  static CouponTable(data, page = 1) {
    const tbody = document.querySelector("#couponTable tbody");
    if (!tbody) return;

    const notDATA = Utility.el("no-data");
    tbody.innerHTML = "";
    notDATA.innerHTML = "";

    if (!data || data.length == 0) {
      Utility.renderEmptyState(notDATA);
      return;
    }

    const start = (page - 1) * Utility.PAGESIZE;
    const end = start + Utility.PAGESIZE;
    const paginatedData = data.slice(start, end);

    paginatedData.forEach((o, idx) => {
      const tr = document.createElement("tr");
      tr.classList.add("bounce-card");
      tr.innerHTML = `
          <td>${idx + 1}</td>         
           <td>${o.coupon ? o.coupon : "-"}</td>       
          <td>${o.discount ? o.discount : 0}</td>
          <td class="actions">
           <button class="btn-error btn btn-sm" 
              data-delete="${o.id}"
              data-action='delete'
            >Delete</button> 
          </td>
        `;
      tbody.appendChild(tr);
    });
    if (paginatedData.length > Utility.PAGESIZE)
      Pagination.render(data.length, page, data, Checkout.CouponTable);
  }

  static checkOrderingStatus(orderType) {
      const statusEl = Utility.el("orderingStatus");
      const statusTextEl = Utility.el("orderingStatusText");
   
      const now  = new Date();
      const day = now.toLocaleDateString('en-US', { weekday: 'long' });
      const hour = now.getHours();
      const minute = now.getMinutes();

      //Convert to HHMM format (e.g, 19:30 -> 1930)
      const time = parseInt(hour.toString().padStart(2, "0") + minute.toString().padStart(2, "0"));
          
      // ---- RULE 1: Delivery closes 7pm every day ----
      const deliveryClose = 1900;

      // ---- RULE 2: Ordering hours ----
      const weekdayOpen  = 1000;  // 10 AM
      const weekdayClose = 2200;  // 10 PM

      const sundayOpen  = 1200;   // 12 PM
      const sundayClose = 2200;   // 10 PM

      function orderingMessage(message) {
        statusEl.style.display = "flex"; 
        statusEl.classList.remove("d-none");
        statusTextEl.textContent = `${message}`;
     
      }

        // -------- A. DELIVERY RULES ----------
      if (orderType === "delivery") {
          // No delivery on Sundays
          if (day === "Sunday") {
            orderingMessage("Delivery not available today");
            return false;
          }

          // Delivery after 7PM every day
          if (time >= deliveryClose) {
            orderingMessage("Delivery Orders Closed, Please Select 'Pickup Option'");
            return false;
          }
      }

       // -------- B. ORDERING HOURS RULES ----------
      if (day === "Sunday") {

        // Before Sunday opening hours (12PM)
        if (time < sundayOpen) {
          orderingMessage("We're closed at the moment, please check back during our opening hours");
            return false;
        }

        // After 10PM
        if (time > sundayClose) {
          orderingMessage("We're closed at the moment, please check back during our opening hours");
            return false;
        }

      } else {
          // Weekdays (Mon–Sat)
          if (time < weekdayOpen) {
              orderingMessage("We're closed at the moment, please check back during our opening hours");
              return false;
          }

          if (time > weekdayClose) {
              orderingMessage("We're closed at the moment, please check back during our opening hours");
              return false;
          }
      }

      // If all rules pass → Allow ordering
      return true;
  }
  

     

      
}
