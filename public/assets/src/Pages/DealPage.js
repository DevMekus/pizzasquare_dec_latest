import { deleteItem, getItem, postItem, putItem } from "../Utils/CrudRequest.js";
import Utility from "../Classes/Utility.js";
import Deals from "../Classes/Deals.js";

class DealPage{
    
    constructor() {
        this.initialize();
    }

  async initialize() {
    Deals.DEALS = await getItem('deals');   
    Utility.runClassMethods(this, ["initialize"]);
  }

    renderDealsCard() {
        const el = document.getElementById("dealsPage");
        const container = document.getElementById("filterBtn");
        if (!el) return;
        Deals.renderDeals();
    }
    
    renderDealsTable() {
        const tbody = document.querySelector("#dealsTable tbody");
        if (!tbody) return;

        Deals.renderDealsTable(Deals.DEALS);
    }

    createDeal() {
        const domEl = Utility.el("addDealBtn");
        if (!domEl) return;

        domEl.addEventListener("click", Deals.newDealsModal);
    }
    
    static newDealsModal() {
        let domBody = Utility.el("detailModalBody");
        const domFooter = Utility.el("detailModalButtons");
        let domTitle = Utility.el("detailModalLabel");

        domTitle.innerHTML = "";
        domBody.innerHTML = "";
        domFooter.innerHTML = "";
        domTitle.textContent = `New Promotions`;

        domBody.innerHTML = `
            <form class="row"  id="createDeal" enctype="multipart/form-data">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Promotion name</label>
                            <input type="text" id="dishName" name="title" placeholder="eg: October Splash">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" maxlength="100" placeholder="Write briefly about promo"></textarea>
                            <small id="charCount">0 / 100</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="image-box"></div>
                        <div>
                            <label class="muted mt-2">Product image</label>
                            <input type="file" id="dishStock" name="dealsBanner" accepts="image/*" placeholder="Upload Images">
                        </div>
                        <p class="muted mt-2">By clicking on the submit button, you will make upload this information.</p>
                        <button class="btn btn-primary mt-2" type="submit">Create Promo</button>
                    </div>
                </div>
            </div>
            </form>
        `;
        $("#displayDetails").modal("show");
        Deals.runCounter();
    }
    
    eventDelegations() {
        //--Add Extra
        document.addEventListener("submit", async(e) => {
            if (e.target && e.target.matches && e.target.matches("#createDeal")) {
                e.preventDefault();
                const data = new FormData(e.target);
                
                $("#displayDetails").modal("hide");
                const postData = await postItem(`admin/deals`,data, "Create Deal?")
                if (postData) {                
                    Deals.DEALS = await getItem('deals');   
                    Deals.renderDealsTable(Deals.DEALS);
                } else{
                    Utility.toast("Operation cancelled or failed");
                }
                    
            
            }

            if (e.target && e.target.matches && e.target.matches("#updateDeal")) {
                e.preventDefault();
                const data = new FormData(e.target);
                const id = e.target.dataset.id;
                
                $("#displayDetails").modal("hide");
                const sendData = await postItem(`admin/deals/${id}`, data, "Update Deal?");
                if (sendData) {                
                    Deals.DEALS = await getItem('deals');   
                    Deals.renderDealsTable(Deals.DEALS);
                } else{
                    Utility.toast("Operation cancelled or failed");
                }
            
            }
        });

        document.addEventListener("click", async (e) => {
        const deleteBtn = e.target.closest("[data-delete]");
        if (deleteBtn) {
            const id = deleteBtn.dataset.delete;
            $("#displayDetails").modal("hide");
            const delItem  = await deleteItem(`admin/deals/${id}`, "Delete this promotion?");
            if (delItem) {                
                Deals.DEALS = await getItem('deals');
                Deals.renderDealsTable(Deals.DEALS);
            } else{
                Utility.toast("Operation cancelled or failed");
            }
        }

        const openBtn = e.target.closest("[data-open]");
        if (openBtn) {
            Deals.openDealModal(openBtn.dataset.open);
        }
        });
    }
    
    
}

new DealPage();