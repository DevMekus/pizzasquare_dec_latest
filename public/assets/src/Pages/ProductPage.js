import Utility from "../Classes/Utility.js";
import Product from "../Classes/Product.js";
import Sizes from "../Classes/Sizes.js";
import { postItem } from "../Utils/CrudRequest.js";
import Category from "../Classes/Category.js";

class ProductPage {
    constructor() {      
       this.initialize();
    }

    async initialize() {      
        await Sizes.loadSizes();   
        await Category.loadCategories();            
        Utility.runClassMethods(this, ["initialize"]);      
    }

    loadCategoriesDropDown(){
        Category.categoryFormSelect()
    }

     

    loadSizesDropDown(){       
        Sizes.SIZES.forEach(s => {
            document.getElementById("sizeSelect").innerHTML += `
                <option value="${s.code}" data-sid="${s.id}">
                    ${Utility.toTitleCase(s.label)}
                </option>`;
        });
    }


    sizeSelectRenderRow(){
        document.getElementById("sizeSelect").addEventListener("change", function () {

            const selected = Array.from(this.selectedOptions).map(o => ({
                code: o.value,
                id: o.dataset.sid
            }));

            const container = document.getElementById("selectedSizesContainer");
            container.innerHTML = "";

            selected.forEach(s => {
                container.innerHTML += `
                    <div class="kpi-card-n border-secondary-1x mb-1">
                        <h6><strong>Size ${Utility.toTitleCase(s.code)}</strong></h6>

                        <div class="row">
                            <div class="col-sm-6">
                                <label>Price</label>
                                <input type="number" 
                                    class="form-control size-price" 
                                    data-size="${s.code}" 
                                    placeholder="Enter price">
                            </div>

                            <div class="col-sm-6">
                                <label>Shared Stock?</label>
                                <select class="form-select size-stock" data-size="${s.code}">
                                    <option value="1">Yes (Use parent stock)</option>
                                    <option value="0">No (Own stock)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
    }


    saveProductSizes(){
        document.getElementById("assignSizesForm").addEventListener("submit", function (e) {
            e.preventDefault();           

            const product_id = Utility.el("productSelect").value;
            const category_id = Utility.el("productSelect").selectedOptions[0].dataset.ci;

            if (!product_id){
                Utility.SweetAlertResponse({success: false, message: "Please select a product first."});
                return;
            }

            const selectedSizes = Array.from(
                document.querySelectorAll("#sizeSelect option:checked")
            ).map(o => ({
                code: o.value,        // size code (M, L, XL)
                id: o.dataset.sid     // <--- size id
            }));

            let payload = selectedSizes.map(s => ({
                product_id: product_id,
                size_code: s.code,
                category_id: category_id,
                size_id: s.id, // <----- REQUIRED
                price: document.querySelector(`.size-price[data-size="${s.code}"]`).value,
                shared_stock: document.querySelector(`.size-stock[data-size="${s.code}"]`).value
            }));  
            
           

            //API SUBMIT
            postItem('admin/product-sizes', {data: JSON.stringify(payload)},"Create Sizes?").then(success => {
                if (success) {
                    Utility.SweetAlertResponse({success: true, message: "Sizes assigned successfully!"});
                    document.getElementById("assignSizesForm").reset();
                    document.getElementById("selectedSizesContainer").innerHTML = "";
                    //reload product page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    Utility.SweetAlertResponse({success: false, message: "Failed to assign sizes."});
                }
            });
        });
    }


    

    saveNewProduct(){
        document.getElementById("newProductForm").addEventListener("submit", async (e)=>{
            e.preventDefault();
            const payload = new FormData(e.target);            
            
            const success = await postItem('admin/products', payload,"Create New Product?");
            
                if (success) {               
                    Utility.SweetAlertResponse({success: true, message: "Product created! Select Sizes"});
                    document.getElementById("assignSizesCard").style.display = "block";
                    
                    await Product.loadProducts();  
                    Product.loadProductDropdowns();
                    e.target.reset();
                } else {
                    Utility.SweetAlertResponse({success: false, message: "Failed to create product."});
                }
        })
    }

    

    

}

new ProductPage();