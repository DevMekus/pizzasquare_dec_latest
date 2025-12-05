import Order from "../Classes/Order.js";
import { getItem } from "../Utils/CrudRequest.js";
import Product from "../Classes/Product.js";
import Utility from "../Classes/Utility.js";

class OverviewPage {
  constructor() {
    this.initialize();
  }

  async initialize() {
    document.querySelectorAll(".loading").forEach((el) => {
    //   el.classList.remove("loading");
        el.innerHTML = Utility.inlineLoader();
    }); 
    
    Order.ORDERS = await getItem("admin/orders");   
    Utility.runClassMethods(this, ["initialize"]);
  }

  async renderKPIs(){   
    const todaysOrders = Order.ORDERS.filter((order) => order.created_at.split(" ")[0] === Utility.today);
    document.getElementById("kpiOrdersNum").textContent = todaysOrders.length;
    document.getElementById("kpiRevenueNum").textContent = Utility.fmtNGN(
      Order.getTodayRevenue(Order.ORDERS)
    );

    const newCustomerCount = Order.getNewCustomers(Order.ORDERS);
    document.getElementById("kpiNewCustomersNum").textContent = newCustomerCount;
   

  }

  recentOrderFiler() {
    document.getElementById("orderFilter").addEventListener("change", (e) => {
      Order.renderRecentOrders(
        e.target.value,
        document.getElementById("globalSearch").value
      );
    });

    document.getElementById("globalSearch").addEventListener("input", (e) => {
      Order.renderRecentOrders(
        document.getElementById("orderFilter").value,
        e.target.value
      );
    });
    Order.renderRecentOrders();
  }

    renderTopDishes() {
        const el = document.getElementById("topDishes");
        const topDishes = Order.calculateTopDishes(3); // compute top 3

        if (!topDishes.length) {
            el.innerHTML = "<p>No orders yet.</p>";
            return;
        }

        const max = Math.max(...topDishes.map(d => d.count));

        el.innerHTML = topDishes
            .map(d => `
            <div class="dish-bar" style="display:flex;align-items:center;margin-bottom:6px">
                <strong style="width:120px">${d.name}</strong>
                <div class="bar" style="flex:1;height:12px;background:#e5e7eb;border-radius:6px;margin:0 8px;overflow:hidden">
                    <i style="display:block;height:100%;width:${(d.count / max) * 100}%;background:#00b034;border-radius:6px"></i>
                </div>
                <div style="width:48px;text-align:right;color:var(--muted)">${d.count}</div>
            </div>
        `)
            .join("");
    }

    async renderInventory() {
        const el = document.getElementById("inventoryList");
        if (!el) return;
        Product.PRODUCTS = await getItem("products") || [];
        Product.inventorySnapShot(Product.PRODUCTS);
    }

    async renderPromos() {
    const el = document.getElementById("promos");

    const deals = await getItem("deals") || [];

    if (deals.length == 0) {
      el.innerHTML = `<p class="muted">You have no active promotion at the moment</p>`;
      return;
    }

    el.innerHTML = deals.map(
      (
        p
      ) => `<div style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <strong>${p.title}</strong>
          <div style="color:var(--muted);font-size:13px">
          ${p.created_at} </div>        
        </div>
        <div>${
          p.status == "active"
            ? '<span class="status active">Active</span>'
            : '<span class="status inactive">Inactive</span>'
        }</div>
      </div>`
    ).join("");
  }



}

new OverviewPage();