import Utility from "../Classes/Utility.js";
import Product from "../Classes/Product.js";
import Pos from "../Classes/Pos.js";
import Checkout from "../Classes/Checkout.js";
import Category from "../Classes/Category.js";
import { getItem, postItem } from "../Utils/CrudRequest.js";
import AuthHelper from "./AuthPage.js";
import Order from "../Classes/Order.js";

class PosPage{
    constructor() {
        this.initialize();
    }

    async initialize() {
        Pos.setTax();
        await Checkout.getAndSetVAT();
        Category.CATEGORIES = await getItem("categories") || [];
        Product.PRODUCTS = await getItem("products") || [];
        Product.EXTRAS = await getItem("extras") || [];
        Pos.categories = Category.CATEGORIES;   
        Utility.runClassMethods(this, ["initialize"]);
    }

    renderCurrentTime() {
    const clockEl = document.getElementById("clock");
    if (!clockEl) return;
    setInterval(() => {
      const d = new Date();
      clockEl.textContent = d.toLocaleString();
    }, 1000);
    }

    loadPOSMenuCategories() {
        const POS_SYSTEM = document.getElementById("catalogGrid");
        if (!POS_SYSTEM) return;

        Checkout.isPos = true;

        function loadChips() {
            // Render category buttons
            Pos.categoryChips.innerHTML = Pos.categories
                .map(
                    (c) =>
                        `<button class="filter-chip bounce-card ${
                            c.slug === Pos.filterCat ? "active" : ""
                        }" data-cat="${c.slug}">${Utility.toTitleCase(c.name)}</button>`
                )
                .join("");

            // Bind click events AFTER rendering
            Pos.categoryChips.querySelectorAll(".filter-chip").forEach((btn) => {
                btn.addEventListener("click", () => {
                    Pos.filterCat = btn.dataset.cat;
                    loadChips();       // re-render chips to update "active" class
                    Pos.renderCatalog(); // update catalog based on selected category
                });
            });
        }

        loadChips();            // initial render of category chips
        Pos.renderCatalog();     // initial catalog render
        Checkout.renderCart();   // render cart
        Checkout.applyCoupon();  // apply coupon if any
    }
    
    searchProduct() {
        const POS_SYSTEM = document.getElementById("catalogGrid");
        if (!POS_SYSTEM) return;

        Pos.searchInput.addEventListener("input", () => {
        Pos.query = Pos.searchInput.value.trim().toLowerCase();
        Pos.renderCatalog();
        });
    }

    confirmCustomersOrder() {
        const POS_SYSTEM = document.getElementById("catalogGrid");
        if (!POS_SYSTEM) return;

        document
        .getElementById("confirmOrder")
        .addEventListener("click", async () => {
            const transaction = await Checkout.packageOrder();
            console.log(transaction);

            if (transaction.proceed) {
                Pos.sendPOSTransaction(transaction);
            }
        });
    }

    printYourReceipt() {
        const POS_SYSTEM = document.getElementById("catalogGrid");
        if (!POS_SYSTEM) return;

        Utility.printReceipt();    
    }

     logoutFunction() {
    AuthHelper.logout();
  }

  enableNotification() {
    const enableBtn = Utility.el("enableBtn");
    if (!enableBtn) return;
    
    Utility.requestNotificationPermission();

    enableBtn.addEventListener("click", () => {
      Utility.enableNotificationAudio();
    });

   
  }

}

new PosPage();