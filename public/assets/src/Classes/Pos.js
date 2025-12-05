import { CONFIG } from "../Utils/config.js";
import { getItem } from "../Utils/CrudRequest.js";
import { HttpRequest } from "../Utils/httpRequest.js";
import Cart from "./Cart.js";
import Checkout from "./Checkout.js";
import Product from "./Product.js";
import Utility from "./Utility.js";

export default class Pos {
  static categories = [];
  static catalogGrid = document.getElementById("catalogGrid");
  static categoryChips = document.getElementById("categoryChips");
  static searchInput = document.getElementById("searchInput");
  static cartItems = document.getElementById("cartItems");
  static subtotalEl = document.getElementById("subtotal");
  static taxEl = document.getElementById("tax");
  static discountEl = document.getElementById("discount");
  static grandTotalEl = document.getElementById("grandTotal");
  static customerNameEl = document.getElementById("customerName");
  static couponEl = document.getElementById("coupon");
  static historyList = document.getElementById("historyList");

  static setTax() {
    const domEl = Utility.el("tax_val");
    if (!domEl) return;
    Pos.tax_val = Utility.el("tax_val").textContent = `(${Cart.TAX_RATE})`;
  }

  static filterCat = "pizza";
  static query = "";
  static discountRate = 0;

  static renderCatalog() {
    Pos.catalogGrid.innerHTML = "";

    const items = Product.PRODUCTS.filter(
      (p) =>
        p.category.toLowerCase() === Pos.filterCat && p.name.toLowerCase().includes(Pos.query)
    );      

    if (!items.length) {
      Pos.catalogGrid.innerHTML =
        '<div class="col-12 text-center text text-muted py-4">No items found</div>';
      return;
    }

    items.forEach((o, idx) => {
      Pos.catalogGrid.insertAdjacentHTML("beforeend", Product.MenuCard(o, idx));
    });

    const cards = document.querySelectorAll(".menu-card");
    cards.forEach((card) => {
      card.addEventListener("click", (e) => {
        const productId = card.dataset.product;
        Product.singleProductModal(productId);
      });
    });
  
    AOS.refresh();
  }

  static async sendPOSTransaction(order) {
    try {
      const result = await Utility.confirm("Upload Transaction?");

      if (result.isConfirmed) {

        Utility.alertLoader()
        const response = await HttpRequest(
          `${CONFIG.API}/orders/create`,
          order,
          "POST"
        );

        Utility.clearAlertLoader()

        Utility.SweetAlertResponse(response);

        if (response.success) await Pos.transactionSummaryFromApi(order.order_id);
      }
    } catch (error) {
      console.error(error);
      Utility.toast("An error has occurred", "error");
    }
  }

  static async transactionSummaryFromApi(order_id) {
      // Fetch order details from API
        const order = await getItem(`orders/${order_id}`);
          if(!order ) {
              Utility.toast("Unable to fetch order details for receipt", "error");
              return;
          }
          // Extract values
          const items = order.items;
          const subtotal = Number(order.item_amount);       // items total
          const deliveryFee = Number(order.delivery_fee);   // delivery
          const tax = 0;
          const discount = 0;
          const grandTotal = Number(order.total);           // already includes delivery
          const paymentType = order.payment_type;
  
          // Build items HTML
          const itemsHtml = items
              .map((it) => {
              let toppingsHtml = "";
  
              // Normalize toppings from API
              if (Array.isArray(it.toppings) && it.toppings.length > 0) {
                  const extrasList = it.toppings
                  .map((t) => t.topping?.trim() || "")
                  .filter(Boolean)
                  .join(", ");
  
                  if (extrasList) {
                  toppingsHtml = `
                      <br/>
                      <small><strong>Toppings:</strong> ${extrasList}</small>
                  `;
                  }
              }
  
              return `
                  <tr>
                  <td>
                      ${Utility.toTitleCase(it.product_name)}
                      ${toppingsHtml}
                  </td>
                  <td class="text-end">${it.qty}</td>
                  <td class="text-end">${Utility.fmtNGN(Number(it.unit_price))}</td>
                  <td class="text-end">${Utility.fmtNGN(Number(it.subtotal))}</td>
                  </tr>
              `;
              })
              .join("");
  
          const receipt = `
              <div class="text-center mb-3">
              <h5 class="mb-0">Pizza Square Nigeria</h5>
              <div class="small text-muted">Point of Sale Order Receipt</div>
              </div>
  
              <div class="d-flex justify-content-between small">
              <div>Order: <strong>${order.order_id}</strong></div>
              <div>${order.created_at}</div>
              </div>
  
              <div class="small">Customer: ${order.customer_name}</div>
              <div class="small">Phone: ${order.customer_phone}</div>
              <div class="small">Attendant: ${order.attendant ?? ""}</div>
              <hr/>
  
              <div class="table-responsive">
              <table class="table table-sm">
                  <thead>
                  <tr>
                      <th>Item</th>
                      <th class="text-end">Qty</th>
                      <th class="text-end">Price</th>
                      <th class="text-end">Total</th>
                  </tr>
                  </thead>
                  <tbody>${itemsHtml}</tbody>
              </table>
              </div>
  
              <div class="receipt-totals">
  
              <div>
                  <span>Subtotal:</span>
                  <span><strong>${Utility.fmtNGN(subtotal)}</strong></span>
              </div>
  
              <div>
                  <span>Delivery Fee:</span>
                  <span><strong>${Utility.fmtNGN(deliveryFee)}</strong></span>
              </div>
  
              <div>
                  <span>Tax:</span>
                  <span><strong>${Utility.fmtNGN(tax)}</strong></span>
              </div>
  
              <div>
                  <span>Discount:</span>
                  <span><strong>${Utility.fmtNGN(discount)}</strong></span>
              </div>
  
              <div class="grand">
                  <span>Grand Total:</span>
                  <span><strong>${Utility.fmtNGN(grandTotal)}</strong></span>
              </div>
  
              <div class="small">
                  <span>Payment:</span>
                  <span>${Utility.toTitleCase(paymentType)}</span>
              </div>
  
              <div class="small">
                  <span>Status:</span>
                  <span>${Utility.toTitleCase(order.status)}</span>
              </div>
  
              </div>
          `;
  
          document.getElementById("receiptBody").innerHTML = receipt;
  
          const modal = new bootstrap.Modal(document.getElementById("receiptModal"));
          $("#displayDetails").modal("hide");
          modal.show();

          Cart.cart = [];
          Cart.discountRate = 0;
          Pos.couponEl.value = "";
          Checkout.updateCart();
          Checkout.renderCart();
      }

}
