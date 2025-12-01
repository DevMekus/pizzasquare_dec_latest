import Utility from "../Classes/Utility.js";
import Category from "../Classes/Category.js";
import StockManager from "../Classes/StockManager.js";
import { postItem, putItem } from "../Utils/CrudRequest.js";

class StockManagement {
    constructor() {
        this.initialize();
    }
    
    async initialize() {
        await Category.loadCategories()
        Utility.runClassMethods(this, ["initialize"]);      
    }
    
    categoryFormSelect(){
        Category.categoryFormSelect()
    }

    bindEvents(){
        document.getElementById("category_id").addEventListener("change", function() {
           StockManager.findCategorySizes(this.value);
        });
       
    }

    addStockSubmit(){
        try {
            document.getElementById("addCategoryStockForm").addEventListener("submit", async function(e) {
                e.preventDefault();
                
                bootstrap.Modal.getInstance(document.getElementById("addStockModal")).hide();
                let formData = Utility.toObject(new FormData(this));
                const result = await postItem('/category-stock/create', formData);
                if (!result) {
                    Utility.toast('Error', result.message, 'error');
                    return;
                }

                StockManager.loadCategoryStocks();
                this.reset();
               
                Utility.toast('Success', 'Stock added successfully', 'success');
            });
           
        } catch (error) {
            
        }
    }

    eventDelegation(){
         document.getElementById("categoryStockBody").addEventListener("click", async function(e) {
            const action = e.target.dataset.action;
            const id = e.target.dataset.id;

            if (action === "edit-stock") {
                fetch(`/category-stock/${id}`)
                    .then(res => res.json())
                    .then(row => {
                        document.getElementById("editStockId").value = row.id;
                        document.getElementById("editCategoryName").value = row.category_name;
                        document.getElementById("editSizeLabel").value = row.size_label;
                        document.getElementById("editQty").value = row.qty;
                        document.getElementById("editLowStock").value = row.low_stock_threshold;

                        new bootstrap.Modal(document.getElementById("editStockModal")).show();
                    });
            }

            if (action === "delete-stock") {
                if (!confirm("Delete this stock item?")) return;

                fetch(`/category-stock/delete/${id}`, { method: 'DELETE' })
                    .then(() => loadCategoryStock());
            }
        });
    }

    updateStockSubmit(){
        document.getElementById("editCategoryStockForm").addEventListener("submit", function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            fetch('/category-stock/update', { method: 'POST', body: formData })
                .then(() => {
                    loadCategoryStock();
                    bootstrap.Modal.getInstance(document.getElementById("editStockModal")).hide();
                });
        });
    }

   
}

new StockManagement();