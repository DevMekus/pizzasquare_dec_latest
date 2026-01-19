import {getItem, postItem} from '../Utils/CrudRequest.js'
import Pagination from './Pagination.js';
import Utility from './Utility.js';
import Category from './Category.js';
import Cart from './Cart.js';
import Product from './Product.js';   

export default class XtraThursdayPromo{
    static XTRATHURSDAYPRODUCTS = [] 

    static xtraThursdayProducts(products){
        const domElement = Utility.el("xtraThursdayDealsRow")
        if (!domElement) return

        if (products.length === 0) {
            domElement.innerHTML = `
                <div class="empty-state">
                    <p class="muted">No Xtra Thursday products available at the moment.</p>
                </div>
            `;
            return;
        }
        let html = '';
        products.forEach((product, index) => {
            html += XtraThursdayPromo.xtraThursdayProductCard(product, index);
        });
        domElement.innerHTML = html;

        document.querySelectorAll(".xtrathursday-card").forEach((card) => {
            card.addEventListener("click", (e) => {
                const productId = card.dataset.product;
                XtraThursdayPromo.singleXtraThursdayProductModal(productId);
            }); 
        });
    }

    static async PromoProductModal(productId, promotion = []) {
        const res = await getItem(`products/full/${productId}`);
        if (!res || !res.product || res.product.length === 0) return; 
        const product = res.product[0];
    
        const productIngredients = await getItem(`products/ingredients/${productId}`);
    
        
        const sizesObj = Array.isArray(res.sizes) ? res.sizes : [];
    
        const imageUrl = product.image?.replace(/"/g, "") || "";
        const hasSizes = sizesObj.length > 0;
    
          
    
            // -------------------------------------------
            // STEP 1: Compute real_stock for each size
            // -------------------------------------------
            sizesObj.forEach((sz) => {
                const shared = Number(sz.shared_stock) === 1;
    
                const productQty = sz.product_stock_quantity !== null
                    ? Number(sz.product_stock_quantity)
                    : null;
    
                const categoryQty = sz.category_stock_quantity !== null
                    ? Number(sz.category_stock_quantity)
                    : null;
    
                // APPLY RULE:
                // If shared_stock=1 OR product_stock_quantity=null -> use category quantity
                if (shared || productQty === null) {
                    sz.real_stock = categoryQty ?? 0;
                } else {
                    sz.real_stock = productQty ?? 0;
                }
            });
    
            // -------------------------------------------
            // STEP 2: Find first available size
            // -------------------------------------------
            let defaultAvailable = hasSizes
                ? sizesObj.find((sz) => Number(sz.real_stock) > 0)
                : null;
    
            // If all sizes are 0 stock
            const defaultPrice = defaultAvailable ? Number(defaultAvailable.price) : 0;
            const defaultSize = defaultAvailable ? defaultAvailable.size_label : null;
            const defaultSizeId = defaultAvailable ? defaultAvailable.size_id : null;
    
            // -------------------------------------------
            // STEP 3: Build size selector UI
            // -------------------------------------------
            const sizeSectionHtml = hasSizes
                ? `
                <div class="size-toggle">
                    <label class="section-title">Select Size</label>
                    <div class="toggle-group slide-in mt-2">
                    ${sizesObj
                        .map((sz, i) => {
                            const available = Number(sz.real_stock) > 0 && sz.size_label.toLowerCase() == 'xl';
                            return `
                            <label class="toggle-item mb-3 ${!available ? "disabled" : ""}">
                                <input type="radio" 
                                    name="size" 
                                    value="${i}" 
                                    ${available && !defaultAvailable && i === 0 ? "checked" : ""}
                                    ${available ? "checked" : ""}
                                    ${!available ? "disabled" : ""}>
                                <span>${sz.size_label}</span>                       
                            </label>
                            `;
                        })
                        .join("")}
                    </div>
                </div>`
                : "";
    
            // Toppings
            const toppingsSectionHtml =
                product.category.toLowerCase() === "pizza" || promotion.length > 0 && promotion[0].title == 'xtraThursday'
                    ? `
                <div class="toppings-section">
                    <h6 class="muted center-mobile">Toppings</h6>
                    <div id="toppingsOptions" class="toppings-toggle center-mobile"></div>
                </div>
                `
                    : "";
    
    
                const ingredientsSectionHtml = productIngredients.length > 0 ? `
                <div class="toppings-section">
                     <h6 class="muted center-mobile">Product Ingredients</h6>
                     ${productIngredients.length > 0 
                     
                     ? `
                        <p class="muted small center-mobile">
                            <i class="fa fa-info-circle"></i> Click to remove unwanted ingredients
                        </p>
                         <div id="IngredientsOptions" class="collectionPit center-mobile"></div>
                        ` :
                         '<p class="muted small center-mobile">No ingredients assigned to this product.</p>'}
                </div>`:"";
    
                // Promotionals
           
    
    
            // Modal
            const domBody = Utility.ModalBody;
            const title = Utility.ModalTitle;
            title.textContent = `${Utility.toTitleCase(product.name)}`;
    
            domBody.innerHTML = `
                <div class="product-layout ${productIngredients.length <= 0 ? 'single-layout' : ''}">
                    <div class="product-left">
                        <img src="${imageUrl}" alt="${product.name}" class="product-image ${product.category.toLowerCase() === 'pizza' ? 'spin' : 'zoom'}">
                         <p class="text-center muted">${product.description || ""}</p>
                          ${sizeSectionHtml}
                        <div class="qty-box center-mobile">
                            <button id="qtyMinus" class="qty-btn btn btn-sm"><i class="fa fa-minus"></i></button>
                            <span id="qtyValue">1</span>
                            <button id="qtyPlus" class="qty-btn btn btn-sm primary"><i class="fa fa-plus"></i></button>
                        </div>
                        
                    </div>
    
                    <div class="product-right">                                
                        ${toppingsSectionHtml}
                        ${ingredientsSectionHtml}
    
                        <div class="add-cart-footer">
                            <button id="addToCartBtn" 
                                class="btn-add-cart ${defaultAvailable ? "" : "btn-disabled"}"
                                data-size="${defaultSize || ""}"
                                data-size-id="${defaultSizeId || ""}"
                                data-base-price="${defaultPrice}"                          
                                data-final-unit-price="${defaultPrice}"
                                ${!defaultAvailable ? "disabled" : ""}>
                                ${
                                    defaultAvailable
                                        ? `Add to Cart • ₦<span id="cartPriceValue">${defaultPrice.toLocaleString()}</span>`
                                        : "Unavailable"
                                }
                            </button>
                        </div>
                    </div>
                </div>
            `;
    
            $("#displayDetails").modal("show");
    
            // If no sizes available, exit early
            if (!defaultAvailable) return;
    
            const addToCartBtn = Utility.el("addToCartBtn");
            const qtyValue = Utility.el("qtyValue");
            let qty = 1;
    
            // -------------------------------------------
            // PRICE CALCULATOR
            // -------------------------------------------
            function updateTotalPrice() {
                const base = Number(addToCartBtn.dataset.basePrice);
    
                const toppingsTotal = [
                    ...document.querySelectorAll(".topping-btn.active"),
                ].reduce((sum, t) => sum + Number(t.dataset.price || 0), 0);
    
                const finalUnitPrice = base + toppingsTotal;
                addToCartBtn.dataset.finalUnitPrice = finalUnitPrice;
    
                Utility.el("cartPriceValue").textContent = (finalUnitPrice * qty).toLocaleString();
            }
    
            // -------------------------------------------
            // SIZE CHANGE HANDLER
            // -------------------------------------------
            if (hasSizes) {
                domBody.querySelectorAll("input[name='size']").forEach((radio) => {
                    radio.addEventListener("change", () => {
                        const index = Number(radio.value);
                        const selected = sizesObj[index];
    
                        addToCartBtn.dataset.size = selected.size_label;
                        addToCartBtn.dataset.sizeId = selected.size_id;
                        addToCartBtn.dataset.basePrice = selected.price;
    
                        updateTotalPrice();
                    });
                });
            }
    
            // -------------------------------------------
            // If There are Ingredients
            // -------------------------------------------
            if (productIngredients.length > 0){
                const IngredientsContainer = Utility.el("IngredientsOptions");
                const ingredientsElm = productIngredients.map(ing => `
                    <span 
                        class="collection-pill active mb-2" 
                        data-ingredient-name="${ing.ingredient_name}" 
                      >
                    ${ing.ingredient_name}
                    </span>
                    `).join('');
                IngredientsContainer.innerHTML = ingredientsElm;
    
                IngredientsContainer.querySelectorAll('.collection-pill').forEach(item => {
                    item.addEventListener('click', () => {
                        item.classList.toggle('active');
                        item.classList.contains('active') ? item.classList.remove('deselected') : item.classList.add('deselected');
                        updateTotalPrice();
                    });
                });
            }
              
    
    
            // -------------------------------------------
            // TOPPINGS
            // -------------------------------------------
            if (product.category.toLowerCase() === "pizza") {
                const toppingsContainer = Utility.el("toppingsOptions");
                const toppingsList = Product.EXTRAS.filter(
                    (t) => t.category_id == product.category_id
                );          
    
    
                toppingsList.forEach((topping) => {
                    const item = document.createElement("button");
                    item.className = "topping-btn";
                    item.dataset.id = topping.id;
                    item.dataset.price = topping.extras_price;
                    item.type = "button";
                    item.textContent = `${topping.extras} +₦${Number(topping.extras_price).toLocaleString()}`;
    
                    item.addEventListener("click", () => {
                        item.classList.toggle("active");
                        updateTotalPrice();
                    });
    
                    toppingsContainer.appendChild(item);
                });
            }
    
            // -------------------------------------------
            // QUANTITY HANDLERS
            // -------------------------------------------
            Utility.el("qtyPlus").addEventListener("click", () => {
                qty++;
                qtyValue.textContent = qty;
                updateTotalPrice();
            });
    
            Utility.el("qtyMinus").addEventListener("click", () => {
                if (qty > 1) {
                    qty--;
                    qtyValue.textContent = qty;
                    updateTotalPrice();
                }
            });
    
            updateTotalPrice();
    
            // -------------------------------------------
            // ADD TO CART
            // -------------------------------------------
            addToCartBtn.addEventListener("click", () => {
                const finalUnitPrice = Number(addToCartBtn.dataset.finalUnitPrice);
    
                const selectedToppings = [
                    ...document.querySelectorAll(".topping-btn.active"),
                ].map((t) => ({
                    id: t.dataset.id,
                    extras: t.textContent.split(" +₦")[0],
                    price: Number(t.dataset.price),
                }));
    
                const deselectedIngredients = productIngredients.length > 0 ?
                [
                    ...document.querySelectorAll(".collection-pill:not(.active)"),
                ].map(t => {
                    return {
                        ingredient_name: t.dataset.ingredientName,                   
                    };                  
                     
                }) : [];
    
                Cart.addToCart({
                    product_id: product.id,
                    title: product.name,
                    size: addToCartBtn.dataset.size || null,
                    size_id: addToCartBtn.dataset.sizeId || null,
                    barbecueSauce: product.category.toLowerCase() === "pizza" ? "beneath" : null,
                    price: finalUnitPrice,
                    qty,
                    image: imageUrl,
                    toppings: selectedToppings,
                    type: "regular",
                    removed_ingredients: deselectedIngredients,
                });
    
                Utility.flyToCartAnimation(".product-image", "#cartCount");
                addToCartBtn.classList.add("added");
                setTimeout(() => addToCartBtn.classList.remove("added"), 1000);
            });
    }
    static xtraThursdayGiftPackCards(products) {
       if (!products) return '';
      
        return products.map((product) => {
            if (product.product_name.toLowerCase().includes('margherita classic')){
                
                return `
                    <div>
                        <img loading="lazy"
                        src="${product.product_image}"
                        alt="${product.product_name}"
                        style="max-width:100%; height:auto; object-fit:cover; max-height:150px;" />
                        <p class="muted small text-center mt-2">${product.product_name} - ₦${product.price.toLocaleString()}</p>
                    </div>
                    
                `  
            }
        }).join('');
            
    }

    static xtraThursdayProductCard(product, index) {
        return `
            <div class="col-6 col-md-3 mb-2" data-aos="fade-up" 
                data-aos-delay="${index * 50}">
                <div class="menu-card xtrathursday-card  bounce-card position-relative h-100" 
                    data-product="${product.product_id}">                    
                    <div style="display:flex; justify-content:center; align-items:center;">
                    <img loading="lazy"
                        src="${product.product_image}"
                        alt="${product.product_name}"
                        style="max-width:100%; height:auto; object-fit:cover; max-height:170px;" />
                    </div>

                    <div class="p-3">
                        <div class="w-100 d-flex justify-content-between align-items-center flex-wrap flex-lg-nowrap">
                        <h6 class="mb-0 product-title center-mobile text-center">${product.product_name}</h6>
                        <div class="small text-muted  center-mobile">
                            ₦${product.price.toLocaleString()}
                        </div>
                    </div>
                </div>
          </div>
        </div>
        `      
    }

    static async singleXtraThursdayProductModal(productId) {

        const allProducts = XtraThursdayPromo.XTRATHURSDAYPRODUCTS['xl'];
        const smallPizzas = XtraThursdayPromo.XTRATHURSDAYPRODUCTS['m'];

        XtraThursdayPromo.PromoProductModal(productId, smallPizzas);

        
    }

    static mainPromotionalProducts(product, ingredients = []) {

        return ``
    }

    static sidePromotionalProducts(product) {
        return ``
    }


    static promotionalIngredients(product) {
        return ``
    }

    static promotionalToppings(product) {
        return ``
    }

    
    static promotionalAction(product) {
        return ``
    }
}