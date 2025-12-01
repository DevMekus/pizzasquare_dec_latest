import { getItem, putItem } from "../Utils/CrudRequest.js";
import Utility from "../Classes/Utility.js";

export default class CategoryLevelStock{
    static categorySelect = document.getElementById("category_id");
    static categoryStockTable = document.querySelector("#categoryStockTable tbody");
    static productStockTable = document.querySelector("#productStockTable tbody");

    static async loadCategorySizesStock(categoryId){
         try {
            this.categoryStockTable.innerHTML = `<tr><td colspan="4" class="text-center">Loading...${Utility.inlineLoader()}</td></tr>`;
            const data = await getItem(`admin/category-stock/${categoryId}`);
         
            this.categoryStockTable.innerHTML = "";

            if (data.length === 0) {
                this.categoryStockTable.innerHTML = `<tr><td colspan="4" class="text-center">No sizes found for this category.</td></tr>`;
                return;
            }

            data.forEach(item => {
                this.categoryStockTable.innerHTML += `
                    <tr>
                        <td>${item.size}</td>
                        <td>
                            <input type="number" class="form-control" id="shared_qty_${item.id}" value="${item.qty ?? 0}">
                        </td>
                        <td>
                            <input type="number" class="form-control" id="shared_low_${item.id}" value="${item.low_stock_threshold ?? 0}">
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-action="save-shared-stock" data-id="${item.id}">
                                Save
                            </button>
                        </td>
                    </tr>
                `;
            });
        } catch (error) {
            
        }
    }

    static async loadIndividualProductStock(){
        try {
            this.productStockTable.innerHTML = `<tr><td colspan="5" class="text-center">Loading...${Utility.inlineLoader()}</td></tr>`;
            const data = await getItem(`admin/product-stocks`);
            this.productStockTable.innerHTML = "";

            if (data.length === 0) {
                this.productStockTable.innerHTML = `<tr><td colspan="5" class="text-center">No individual product stocks found.</td></tr>`;
                return;
            }

            data.forEach(item => {
                this.productStockTable.innerHTML += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.size_label ?? '-'}</td>
                        <td>
                            <input type="number" class="form-control" id="product_qty_${item.id}" value="${item.qty}">
                        </td>
                        <td>
                            <input type="number" class="form-control" id="product_low_${item.id}" value="${item.low_stock_threshold}">
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-action="save-product-stock" data-id="${item.id}">
                                Save
                            </button>
                        </td>
                    </tr>
                `;
            });
        } catch (error) {
            
        }
       
    }

    /** Update Shared Category Stock */
    static async  updateCategoryStock(stockId) {
        const qty = document.getElementById(`shared_qty_${stockId}`).value;
        const low = document.getElementById(`shared_low_${stockId}`).value;


        const result = await putItem(`admin/category-stock/${stockId}/update`, {stockId, qty, low_stock_threshold: low },"Update Category Stock?");
        if(!result) return;
        const selectedCategoryId = this.categorySelect.value;
        this.loadCategorySizesStock(selectedCategoryId);
       
    }

     /** Update Product Stock */
    static async  updateProductStock(stockId) {
        const qty = document.getElementById(`product_qty_${stockId}`).value;
        const low = document.getElementById(`product_low_${stockId}`).value;

      
        const result = await putItem(`admin/product-stock/${stockId}/update`, {stockId, qty, low_stock_threshold: low },"Update Product Stock?"); 
        if(!result) return;
        this.loadIndividualProductStock();
    }




}