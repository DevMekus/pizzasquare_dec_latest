import { deleteItem, getItem, postItem } from "../Utils/CrudRequest.js";
import Utility from "../Classes/Utility.js";
import NewsUpdate from "../Classes/NewsUpdate.js";

class UpdatesPage{
    
    constructor() {
        this.initialize();
    }

  async initialize() {
    NewsUpdate.NEWSUPDATES = await getItem('news_updates');   
    Utility.runClassMethods(this, ["initialize"]);
  }

    renderNewsUpdateCard() {
        const el = document.getElementById("updateRow");        
        if (!el) return;
        NewsUpdate.renderNewsUpdate();
    }
    
    renderNewsUpdateTable() {
        const tbody = document.querySelector("#update-table tbody");
        if (!tbody) return;
        NewsUpdate.renderUpdatesTable(NewsUpdate.NEWSUPDATES);
    }

    createDeal() {
        const domEl = Utility.el("addUpdateBtn");
        if (!domEl) return;

        domEl.addEventListener("click", NewsUpdate.createUpdatesModal);
    }
    
    
    eventDelegations() {
        //--Add Extra
        document.addEventListener("submit", async(e) => {
            if (e.target && e.target.matches && e.target.matches("#newUpdateForm")) {
                e.preventDefault();
                const data = new FormData(e.target);
                
                $("#displayDetails").modal("hide");
                const postData = await postItem(`admin/news_updates`,data, "Upload New Public Update?")
                if (postData) {                
                    NewsUpdate.NEWSUPDATES  = await getItem('news_updates');   
                    NewsUpdate.renderUpdatesTable(NewsUpdate.NEWSUPDATES);
                } else{
                    Utility.toast("Operation cancelled or failed");
                }
                    
            
            }

            if (e.target && e.target.matches && e.target.matches("#updateUpdateForm")) {
                e.preventDefault();
                const data = new FormData(e.target);
                const id = e.target.dataset.id;
                
                $("#displayDetails").modal("hide");
                const sendData = await postItem(`admin/news_updates/${id}`, data, "Change Public Update?");
                if (sendData) {                
                    NewsUpdate.NEWSUPDATES  = await getItem('news_updates');   
                    NewsUpdate.renderUpdatesTable(NewsUpdate.NEWSUPDATES);
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
            const delItem  = await deleteItem(`admin/news_updates/${id}`, "Delete this Update?");
            if (delItem) {                
                NewsUpdate.NEWSUPDATES  = await getItem('news_updates');   
                NewsUpdate.renderUpdatesTable(NewsUpdate.NEWSUPDATES);
            } else{
                Utility.toast("Operation cancelled or failed");
            }
        }

        const openBtn = e.target.closest("[data-open]");
        if (openBtn) {
            NewsUpdate.openUpdatesModal(openBtn.dataset.open);
        }
        });
    }
    
    
}

new UpdatesPage();