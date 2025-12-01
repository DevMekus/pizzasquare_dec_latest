import Utility from "../Classes/Utility.js";
import Checkout from "../Classes/Checkout.js";
import { deleteItem, getItem, patchItem, postItem, putItem } from "../Utils/CrudRequest.js";

class CouponPage {
  constructor() {
    this.initialize();
  }

  async initialize() {
    Checkout.COUPONS = await getItem("coupon");
    Utility.runClassMethods(this, ["initialize"]);
  }
  
  renderCouponTable() {
    const domEl = Utility.el("couponTable");
    if (!domEl) return;
    Checkout.CouponTable(Checkout.COUPONS);
  }

   eventDelegations() {
    document.addEventListener("click", async (e) => {
      const btn = e.target.closest("[data-delete]");
      if (!btn) return;

      const id = btn.getAttribute("data-delete");
      const del = await deleteItem(`admin/coupon/${id}`, "Delete Coupon?");   
      if (del) {
        Checkout.COUPONS = await getItem("coupon");
        Checkout.CouponTable(Checkout.COUPONS);
      }
    });
  }

  submitCoupon() {
    const domForm = Utility.el("newCoupon");
    if (!domForm) return;

    domForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const data = Utility.toObject(new FormData(e.target));
      $("#couponModal").modal("hide");
      const createNew = await postItem("admin/coupon", data, "Create new coupon?");
      if (createNew) {
        Checkout.COUPONS = await getItem("coupon");
        Checkout.CouponTable(Checkout.COUPONS);
      }
    });
  }

}

new CouponPage();