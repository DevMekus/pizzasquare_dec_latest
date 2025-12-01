import Utility from "../Classes/Utility.js";
import Category from "../Classes/Category.js";
import CategoryLevelStock from "../Classes/CategoryLevelStock.js";
import { postItem, putItem } from "../Utils/CrudRequest.js";

class CategoryLevelStockPage{

     constructor() {
        this.initialize();
    }
    
    async initialize() {
        await Category.loadCategories()
        Utility.runClassMethods(this, ["initialize"]);      
    }
    
    categoryFormSelect(){
        Category.categoryFormSelect()
        CategoryLevelStock.categorySelect.addEventListener("change", function () {
            const categoryId = this.value;
            if (categoryId) {
                CategoryLevelStock.loadCategorySizesStock(categoryId);
                
            }
        });
    }

    loadAllProductStocks(){
        CategoryLevelStock.loadIndividualProductStock();
    }

    eventDelegation(){
        document.body.addEventListener("click", async function (e) {
            const btn = e.target.closest("button");
            if (!btn) return;

            if (btn.dataset.action === "save-shared-stock") {
                await CategoryLevelStock.updateCategoryStock(btn.dataset.id);
            }

            if (btn.dataset.action === "save-product-stock") {
                await CategoryLevelStock.updateProductStock(btn.dataset.id);
            }
        });
    }

}

new CategoryLevelStockPage();