import { deleteItem, getItem, postItem } from "../Utils/CrudRequest.js";
import Utility from "../Classes/Utility.js";
import Promotions from "../Classes/Promotions.js";

class PromotionsPage {
    constructor() {
        this.initialize();
    }
        
    async initialize() {
        Promotions.PROMOTIONS = await getItem('promotions');
        Utility.runClassMethods(this, ["initialize"]);
    }

    renderPromotions() {
        const promotionsTableBody = document.querySelector('#promotions-table tbody');
        if (!promotionsTableBody) return;

        Promotions.renderPromotions(Promotions.PROMOTIONS);
    }

    openDealModal() {
        const addPromotionBtn = Utility.el("addPromotionBtn");
        if (!addPromotionBtn) return;
        addPromotionBtn.addEventListener("click", () => {
            Promotions.addpromotionModal();
        });
    }

    displayDealsCard(){
        const dealsCardContainer = document.querySelector('#pizzaSquareDealsRow');
        if (!dealsCardContainer) return;
        Promotions.promotionCards(Promotions.PROMOTIONS);
    }









    promotionEventsDelegation(){
        //Submits
          document.addEventListener("submit", async(e) => {
            if (e.target && e.target.matches && e.target.matches("#newPromotionForm")) {
                e.preventDefault();
                const data = new FormData(e.target);
                
                $("#displayDetails").modal("hide");
                const postData = await postItem(`admin/promotions`,data, "Upload New promotion?")
                if (postData) {                
                    Promotions.PROMOTIONS  = await getItem('promotions');   
                    Promotions.renderPromotions(Promotions.PROMOTIONS);
                } else{
                    Utility.toast("Operation cancelled or failed");
                }                   
            
            }

            if (e.target && e.target.matches && e.target.matches("#editPromotionForm")) {
                e.preventDefault();
                const data = new FormData(e.target);
                const id = e.target.dataset.id;
                
                $("#displayDetails").modal("hide");
                const sendData = await postItem(`admin/promotions/${id}`, data, "Update Promotion?");
                if (sendData) {                
                    Promotions.PROMOTIONS  = await getItem('promotions');   
                    Promotions.renderPromotions(Promotions.PROMOTIONS);
                } else{
                    Utility.toast("Operation cancelled or failed");
                }
            
            }
        });
        //Clicks
        document.addEventListener("click", async (e) => {
        const actionBtn = e.target.closest("[data-action]");
        const id = actionBtn ? actionBtn.dataset.id : null;

        if (actionBtn) {
            const action = actionBtn.dataset.action;

            if (action === "edit") {
                const promotion = Promotions.PROMOTIONS.find(promo => promo.id == id);
                if (promotion) {
                    Promotions.EditPromotionModal(promotion);
                }
            }
            if (action === "delete") {
                $("#displayDetails").modal("hide");
                const delItem  = await deleteItem(`admin/promotions/${id}`, "Delete this Promotion?");
                if (delItem) {                
                    Promotions.PROMOTIONS  = await getItem('promotions');   
                    Promotions.renderPromotions(Promotions.PROMOTIONS);
                } else{
                    Utility.toast("Operation cancelled or failed");
                }
            }
        }
        })
    }
}

new PromotionsPage();