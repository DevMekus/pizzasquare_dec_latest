import Utility from "../Classes/Utility.js";
import Product from "../Classes/Product.js";
import Sizes from "../Classes/Sizes.js";
import { postItem, getItem, deleteItem } from "../Utils/CrudRequest.js";
import Category from "../Classes/Category.js";


export default class MenuUtils{
    static async openEditModal(productId) {       
        const res = await getItem(`products/full/${productId}`);
        if (!res || !res.product || res.product.length === 0) return;
       
        const product =  res.product[0];
        const sizes = res.sizes || [];

        document.getElementById("editProductId").value = product.id;
        document.getElementById("editProductName").value = product.name;
        // document.getElementById("editProductSKU").value = product.sku;
        document.getElementById("editProductDescription").value = product.description;
        document.getElementById("editProductStatus").value = product.is_active;

        document.getElementById("currentProductImage").src = product.image || '/assets/images/no-image.png';

        // Render sizes for the product category
        const sizesContainer = document.getElementById("sizesContainer");
        sizesContainer.innerHTML = "";
       

        sizes.forEach(size => {
            const row = document.createElement("div");
            row.classList.add("col-md-3");
            row.innerHTML = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="size-${size.size_id}" data-size-id="${size.size_id}">
                <label class="form-check-label" for="size-${size.size_id}">${size.size_label}</label>
                <input type="number" class="form-control mt-1 size-price" value="${size.price}" placeholder="Price" data-size-id="${size.size_id}" data-location="${size.id}">
              
               
            </div>
            `;
            sizesContainer.appendChild(row);
        });

        // Show modal
        const editModal = new bootstrap.Modal(document.getElementById("editProductModal"));
        editModal.show();
        }
}

class MenuPage {
     constructor() {      
        this.initialize();
    }

    async initialize() {      
        await Product.loadProducts(); 
        await Sizes.loadSizes();  
        await Category.loadCategories();
        Utility.runClassMethods(this, ["initialize"]);      
    }

    renderCategoriesTab() {
        const tabs = document.getElementById("categoryTabs");
        if (!tabs) return;
        Product.isAdmin = true;
        Product.renderMenuTab();
            
    }

    

    async loadEventDelegations(){
        document.querySelector("#productTable tbody").addEventListener("click", e => {
            if (e.target.classList.contains("edit-btn")) {
                const productId = e.target.dataset.id;
                MenuUtils.openEditModal(productId);
            }
        });
    }

    async handleEditProductForm(){
        document.getElementById("editProductForm").addEventListener("submit", async e => {
            e.preventDefault();
           
            const data = new FormData(e.target);           
            const sizesData = [];
            document.querySelectorAll("#sizesContainer .form-check-input[type='checkbox']").forEach(cb => {
                const sizeId = cb.dataset.sizeId;
                const price = document.querySelector(`.size-price[data-size-id="${sizeId}"]`).value || 0;
                const location = document.querySelector(`.size-price[data-size-id="${sizeId}"]`).dataset.location || null;               
                sizesData.push({ size_id: sizeId, price: price, id: location });
                
            });

            data.append("sizes", JSON.stringify(sizesData));           
            const id = data.get("product_id");
            $("#editProductModal").modal("hide");

            
            //API SUBMIT
            const success = await postItem(`admin/products/${id}`, data, "Update Product?");
            if (success) {
                Utility.SweetAlertResponse({success: true, message: "Product updated successfully!"});
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                Utility.SweetAlertResponse({success: false, message: "Failed to update product."});
            }
            

        
        });
    }

    deleteProductDelegation(){
        document.addEventListener("click", async (e) => {
            if (e.target.classList.contains("delete-btn")) {
                const id = e.target.dataset.id;
                if (!id) return;

                // Confirm delete action
                console.log("Attempting to delete product with ID:", id);
                try {
                    const success = await deleteItem(`admin/products/${id}`, "Delete Product?");                 

                    if (success) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);                        
                    }
                } catch (err) {
                    console.error("Delete error:", err);
                    Utility.toast("Error deleting product!");
                }
            }
        });


    }
}
new MenuPage();