import Order from "../Classes/Order.js";
import Utility from "../Classes/Utility.js";
import { getItem } from "../Utils/CrudRequest.js";

class TrackPage {
  constructor() {
    this.initialize();
  }

  async initialize() {
    Utility.runClassMethods(this, ["initialize"]);
  }

  processOrderID() {
    const formEl = Utility.el("trackForm");
    if (!formEl) return;

    formEl.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = Utility.toObject(new FormData(e.target));
      const orderId = formData["orderId"];
      const orders = await getItem(`orders/${orderId}`);

      if (!orders || orders.length == 0) {
        Utility.toast("Order not found");
        Order.orderNotFound();
        return;
      }

      Order.userOrderSummary(orders);
    });
  }
}

new TrackPage();
