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
    const btn = Utility.el("trackBtn")
    if (!formEl) return;

    formEl.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = Utility.toObject(new FormData(e.target));
      const orderId = formData["orderId"];
      btn.innerHTML = `Searching...${Utility.inlineLoader()}`
      const orders = await getItem(`orders/${orderId}`);
      btn.innerHTML = `Track`

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
