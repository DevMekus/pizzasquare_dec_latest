import Utility from "../Classes/Utility.js";
import Product from "../Classes/Product.js";
import Sizes from "../Classes/Sizes.js";
import { postItem } from "../Utils/CrudRequest.js";
import Category from "../Classes/Category.js";

export default class MenuUtils{
    static openEditModal(productId) {
        const product = Product.PRODUCTS.find(p => p.id === productId);
        if (!product) return;

        document.getElementById("editProductId").value = product.id;
        document.getElementById("editProductName").value = product.name;
        document.getElementById("editProductSKU").value = product.sku;
        document.getElementById("editProductDescription").value = product.description;
        document.getElementById("editProductStatus").value = product.is_active;

        // Render sizes for the product category
        const sizesContainer = document.getElementById("sizesContainer");
        sizesContainer.innerHTML = "";
        const categorySizes = Sizes.SIZES.filter(s => s.category_id === product.category_id);

        categorySizes.forEach(size => {
            const row = document.createElement("div");
            row.classList.add("col-md-3");
            row.innerHTML = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="size-${size.id}" data-size-id="${size.id}">
                <label class="form-check-label" for="size-${size.id}">${size.label}</label>
                <input type="number" class="form-control mt-1 size-price" placeholder="Price" data-size-id="${size.id}">
                <div class="form-check mt-1">
                <input class="form-check-input shared-stock" type="checkbox" data-size-id="${size.id}">
                <label class="form-check-label">Shared Stock</label>
                </div>
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

    // async loadEvents(){
    //     Product.renderProducts(document.getElementById("category_id").value);
    //     await Category.loadCategories();
    //     Category.categoryFormSelect(); 
        
    //     document.getElementById("category_id").addEventListener("change", function(){
    //         Product.renderProducts(this.value);
    //     });
    // }

    async loadEventDelegations(){
        document.querySelector("#productTable tbody").addEventListener("click", e => {
            if (e.target.classList.contains("edit-btn")) {
                const productId = e.target.dataset.id;
                MenuUtils.openEditModal(productId);
            }
        });
    }

    async handleEditProductForm(){
        document.getElementById("editProductForm").addEventListener("submit", e => {
            e.preventDefault();
            const productId = document.getElementById("editProductId").value;
            const updatedName = document.getElementById("editProductName").value;
            const updatedSKU = document.getElementById("editProductSKU").value;
            const updatedDesc = document.getElementById("editProductDescription").value;
            const updatedStatus = document.getElementById("editProductStatus").value;

            // Collect sizes data
            const sizesData = [];
            document.querySelectorAll("#sizesContainer .form-check-input[type='checkbox']").forEach(cb => {
                const sizeId = cb.dataset.sizeId;
                const price = document.querySelector(`.size-price[data-size-id="${sizeId}"]`).value || 0;
                const sharedStock = document.querySelector(`.shared-stock[data-size-id="${sizeId}"]`).checked ? 1 : 0;

                if (cb.checked) {
                sizesData.push({ size_id: sizeId, price: price, uses_shared_stock: sharedStock });
                }
            });

            console.log("Update Product", {
                productId, updatedName, updatedSKU, updatedDesc, updatedStatus, sizesData
            });

            // TODO: Call backend API to save product and sizes
            });
    }
}
new MenuPage();