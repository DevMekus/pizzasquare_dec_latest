import Order from "../Classes/Order.js";
import { getItem, patchItem } from "../Utils/CrudRequest.js";
import Product from "../Classes/Product.js";
import Utility from "../Classes/Utility.js";

class OverviewPage {
  constructor() {
    this.initialize();
  }

  async initialize() {  
    
    Order.ORDERS = await getItem("orders");   
    Utility.runClassMethods(this, ["initialize"]);
  }

  async renderKPIs(){   
    const todaysOrders = Order.ORDERS.filter((order) => order.created_at.split(" ")[0] === Utility.today);
    document.getElementById("kpiOrdersNum").textContent = todaysOrders.length;
    document.getElementById("kpiRevenueNum").textContent = Utility.fmtNGN(
      Order.getTodayRevenue(Order.ORDERS)
    );

    // const newCustomerCount = Order.getNewCustomers(Order.ORDERS);
    // document.getElementById("kpiNewCustomersNum").textContent = newCustomerCount;

    //Low stock products
    const categoryStock = await getItem("admin/category-stocks") || [];
    const productsStock = await getItem("admin/product-stocks") || [];
   
    const lowAlerts = Product.countTheLowStocks(productsStock, categoryStock);
    document.getElementById("kpiLowStockNum").textContent = lowAlerts;
   
  }

  async manageVatActions(){
    //show current vat
    try {
      const response = await getItem("vat");     
      if (response) {
        const data = await response[0];
        const vatWhole = data.vat * 100;
        document.getElementById("currentVat").textContent = vatWhole;
        document.getElementById("vatInput").value = vatWhole;


         //update vat
        document.getElementById("vatForm").addEventListener("submit", async (e) => {
          e.preventDefault();
          const currentVatEl = document.getElementById("currentVat");
          const vatInputEl = document.getElementById("vatInput");
          $("#vatModal").modal("hide")
          const patch = await patchItem(`admin/vat/${data.id}`, { vat: parseFloat(vatInputEl.value) }, "Update Vat to " + vatInputEl.value + "% ?");  
          
          if (patch){
                Utility.toast("Order status updated successfully","success");
                setTimeout(() => {
                    Utility.reloadPage();
                }, 1000);
                
            } else {
                Utility.toast("Failed to update order status");
            }
        
        });
      }
    } catch (error) {
      console.error("Error fetching VAT:", error);
    }

   

    
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

    async renderTopDishes() {
        const el = document.getElementById("topDishes");
        // const topDishes = Order.calculateTopDishes(5); // compute top 3

        const topDishes = await getItem('analytics/top-dishes')

        if (!topDishes.length) {
            el.innerHTML = "<p>No orders yet.</p>";
            return;
        }

        const max = Math.max(...topDishes.map(d => d.total_qty));

        el.innerHTML = topDishes
            .map(d => `
            <div class="dish-bar" style="display:flex;align-items:center;margin-bottom:.375rem">
            <img src="${d.image_url}" alt="${d.name}" style="width:2rem;height:2rem;object-fit:cover;border-radius:.25rem;margin-right:.5rem"/>            
                <strong style="width:7.5rem">${d.name}</strong>
                <div class="bar" style="flex:1;height:.75rem;background:#e5e7eb;border-radius:.375rem;margin:0 .5rem;overflow:hidden">
                    <i style="display:block;height:100%;width:${(d.total_qty / max) * 100}%;background:#00b034;border-radius:.375rem"></i>
                </div>
                <div style="width:3rem;text-align:right;color:var(--muted)">${d.total_qty}</div>
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

    const updates = await getItem("news_updates") || [];

    if (updates.length == 0) {
      el.innerHTML = `<p class="muted">
        No news updates at the moment.
      </p>`;
      return;
    }

    el.innerHTML = updates.map(
      (
        p
      ) => `<div style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <strong>${p.title}</strong>
          <div style="color:var(--muted);font-size:.8125rem">
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