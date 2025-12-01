import { getItem } from "../Utils/CrudRequest";


export default class StockManager {
    static CATEGORYSTOCK = []
    static currentAction = ''

    static async loadCategoryStocks(){
        StockManager.CATEGORYSTOCK = await getItem('/category-stock')
        StockManager.renderCategoryStocks()
    }

    static async findCategorySizes(value){
        try {

            const data = await getItem(`/sizes/${value}`);
            let options = "";
                data.forEach(size => {
                    options += `<option value="${size.id}">${size.label}</option>`;
                });
                document.getElementById("stockSizeSelect").innerHTML = options;
           
        } catch (error) {
            
        }
    }


    static renderCategoryStocks() {
        fetch('/category-stock/all')
            .then(res => res.json())
            .then(data => {
                let html = "";
                data.forEach((row, index) => {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${row.category_name}</td>
                            <td>${row.size_label}</td>
                            <td>${row.qty}</td>
                            <td>${row.low_stock_threshold}</td>
                            <td>${row.updated_at}</td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                        data-action="edit-stock"
                                        data-id="${row.id}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger"
                                        data-action="delete-stock"
                                        data-id="${row.id}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById("categoryStockBody").innerHTML = html;
            });
    }
    
   
}