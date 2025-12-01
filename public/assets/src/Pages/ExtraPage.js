import Utility from "../Classes/Utility.js";
import Category from "../Classes/Category.js";
import { deleteItem, getItem, postItem, putItem } from "../Utils/CrudRequest.js";
import Product from "../Classes/Product.js";

class ExtraPage {
    constructor() {
        this.initialize();
    }

    async initialize() {
        Category.CATEGORIES = await getItem("categories");
        Product.EXTRAS = await getItem("extras");

        Utility.runClassMethods(this, ["initialize"]);
    }

    renderExtrasTable() {
        const tbody = document.querySelector("#extraTable tbody");
        if (!tbody) return;

        Product.renderExtrasTable(Product.EXTRAS);
    }

    addExtras() {
        const domEl = Utility.el("addExtrasBtn");
        if (!domEl) return;

        domEl.addEventListener("click", Product.addExtrasModal);
    }

    eventDelegations() {
    //--Add Extra
        document.addEventListener("submit", async (e) => {
            if (e.target && e.target.matches && e.target.matches("#addExtras")) {
                e.preventDefault();
                 const formData = Utility.toObject(new FormData(e.target));
                 $("#displayDetails").modal("hide");
                 const sendData  = await postItem('admin/extras', formData, "Create Toppings");
                    if(sendData) {
                        Product.EXTRAS = await getItem("extras");
                        Product.renderExtrasTable(Product.EXTRAS);
                    } else {                       
                        Utility.toast("Failed to add extra.");
                    }
            }

            if (e.target && e.target.matches && e.target.matches("#updateExtras")) {
                e.preventDefault();
                const formData = Utility.toObject(new FormData(e.target));
                const id = e.target.dataset.id;
                $("#displayDetails").modal("hide");
                const updated = await putItem(`admin/extras/${id}`, formData, "Update Toppings");
                if(updated) {
                    Product.EXTRAS = await getItem("extras");
                    Product.renderExtrasTable(Product.EXTRAS);
                } else {                       
                    Utility.toast("Failed to update extra.");
                }
            }
        });

        document.addEventListener("click", async (e) => {
            const deleteBtn = e.target.closest("[data-delete]");
            if (deleteBtn) {
                const id = deleteBtn.dataset.delete;
                const del = await deleteItem(`admin/extras/${id}`, "Delete Toppings");
                if(del) {
                    Product.EXTRAS = await getItem("extras");
                    Product.renderExtrasTable(Product.EXTRAS);
                } else {
                    Utility.toast("Failed to delete extra.");
                }               
               
            }

            const openBtn = e.target.closest("[data-open]");
            if (openBtn) {
                Product.openExtraModal(openBtn.dataset.open);
            }
        });
  }
}

new ExtraPage();