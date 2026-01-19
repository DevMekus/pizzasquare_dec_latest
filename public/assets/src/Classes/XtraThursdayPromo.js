import {getItem, postItem} from '../Utils/CrudRequest.js'
import Utility from './Utility.js';
import Cart from './Cart.js';
import PaymentChannel from "./PaymentChannel.js";

export default class XtraThursdayPromo{
    static XTRATHURSDAYPRODUCTS = []
    static INGREDIENTS = []
    static TOPPINGS = [] 


    static async packageXLandM(products){
        const result = {
            xl: [],
            m: []
        }

        products.forEach(product =>{
            product.sizes.forEach(size =>{
                if (size.size_label === "XL"){
                    result.xl.push({
                        product_id: product.id,
                        product_name: product.name,
                        product_image: product.image,
                        product_description: product.description,
                        size: size.size_label,
                        stock_qty: size.category_stock_quantity,
                        price: parseFloat(size.price)
                    })
                }

                if (size.size_label === "M"){
                    result.m.push({
                        product_id: product.id,
                        product_name: product.name,
                        product_image: product.image,
                        product_description: product.description,
                        size: size.size_label,
                        stock_qty: size.category_stock_quantity,
                        price: parseFloat(size.price)
                    })
                }
            })
        })

        return result
    }

    static xtraThursdayProducts(products){
        const domElement = Utility.el("promoDayDealsRow")
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
                XtraThursdayPromo.xtraThursdayGiftPackModal(productId);
            }); 
        });
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

    static async xtraThursdayGiftPackModal(productId){
        const res = await getItem(`products/full/${productId}`);
        if (!res || !res.product || res.product.length === 0) return; 
        const product = res.product[0];

        XtraThursdayPromo.INGREDIENTS = await getItem(`products/ingredients/${productId}`);

        const domBody = Utility.ModalBody;
        const title = Utility.ModalTitle;
        title.textContent = `${Utility.toTitleCase(product.name)}`;

        const mainProductHtml = await XtraThursdayPromo.mainProductHtml(res);
        const giftProductHtml = await XtraThursdayPromo.giftProductHtml(product);
        domBody.innerHTML = `
            <div class="row">
                <div class="col-sm-6">${mainProductHtml}</div>
                <div class="col-sm-6">${giftProductHtml}</div>
            </div>
        `;

        $("#displayDetails").modal("show");
       XtraThursdayPromo.toppingsCheckboxes();
       XtraThursdayPromo.ingredientsCheckboxes();
       
       XtraThursdayPromo.updateProductQty();

       XtraThursdayPromo.packageXtraThursdayCart();
        
    }

    static updateProductQty(){
        const qtyValue = Utility.el("qtyValue");
        let qty = parseInt(qtyValue.textContent);

        let mainQty = Utility.el("qtyValue").textContent;
        let giftQty = Utility.el("giftqtyValue").textContent;

        //Display Quantity

        Utility.el("qtyPlus").addEventListener("click", () => {
            qty++;
            qtyValue.textContent = qty;
            mainQty = qty;
            giftQty = qty;
             Utility.el("giftqtyValue").textContent = giftQty;
             Utility.el("qtyValue").textContent = mainQty;
            XtraThursdayPromo.updateTotalPrice();
           
        });
    
            Utility.el("qtyMinus").addEventListener("click", () => {
                if (qty > 1) {
                    qty--;
                    qtyValue.textContent = qty;
                    mainQty = qty;
                    giftQty = qty;
                    Utility.el("giftqtyValue").textContent = giftQty;
                    Utility.el("qtyValue").textContent = mainQty;
                    XtraThursdayPromo.updateTotalPrice();
                }
            });
        
    }

    static packageXtraThursdayCart(){
        const addToCartBtn = Utility.el("addToCartBtn");
       
       
        addToCartBtn.addEventListener("click", async () => {
            if (addToCartBtn.classList.contains("btn-disabled")) return;

            const mainQty = Number( Utility.el("qtyValue").textContent);     
            const mainProductTitle = document.querySelector(".main-product-option .muted").textContent;
            const mainProductId = addToCartBtn.parentElement.parentElement.querySelector(".main-product-option").dataset.product;
            const imageUrl = document.querySelector(".main-product-image").src;           
            const mainSize = addToCartBtn.dataset.size;
            const mainSizeId = addToCartBtn.dataset.sizeId;
            const finalUnitPrice = Number(addToCartBtn.dataset.finalUnitPrice);
            const mainSelectedToppings = [
                ...document.querySelectorAll(".main-product-toppings .topping-btn.active"),
            ].map((t) => ({ 
                id: t.dataset.id,
                extras: t.textContent.split(" +₦")[0], 
                price: Number(t.dataset.price) 
            }));

            const mainDeselectedIngredients = [
                ...document.querySelectorAll(".main-product-item .collection-pill.deselected"),
            ].map((ing) => {
                return {
                    ingredient_name: ing.dataset.ingredientName
                }
            });

            const giftQty = Number(Utility.el("giftqtyValue").textContent);
            const giftProductId = document.querySelector(".gift-pack-option").dataset.product;  
            const giftSize = document.querySelector(".gift-pack-option").dataset.size;  
            const giftSizeId = document.querySelector(".gift-pack-option").dataset.sizeId;
            
            const giftSelectedToppings = [
                ...document.querySelectorAll(".gift-product-toppings .topping-btn.active"),
            ].map((t) => ({ 
                id: t.dataset.id,
                extras: t.textContent.split(" +₦")[0], 
                price: Number(t.dataset.price) 
            }));
          
            const giftDeselectedIngredients = [
                ...document.querySelectorAll(".gift-pack-item .collection-pill.deselected"),
            ].map((ing) => {
                return {
                    ingredient_name: ing.dataset.ingredientName
                }
            });
                //Adding Main product to cart
            Cart.addToCart({
               product_id: mainProductId,
               title: mainProductTitle,
               size: mainSize,
               size_id: mainSizeId,
               barbecueSauce: "beneath",
               price: finalUnitPrice,
               qty: mainQty,
               image: imageUrl,
               toppings: mainSelectedToppings,
               type: 'xtra_thursday_offer',
               removed_ingredients: mainDeselectedIngredients,
                promo_product: true,
            });

            //Adding Gift product to cart
            const giftProduct = XtraThursdayPromo.XTRATHURSDAYPRODUCTS['m'].find(p => p.product_id == giftProductId);
            if (giftProduct){
                Cart.addToCart({
                    product_id: giftProductId,
                    title: giftProduct.product_name,
                    size: giftSize,
                    size_id: giftSizeId,
                    barbecueSauce: "beneath",
                    price: 0,
                    qty: giftQty,
                    image: giftProduct.product_image,
                    toppings: giftSelectedToppings,
                    type: 'xtra_thursday_offer_gift',
                    removed_ingredients: giftDeselectedIngredients,
                    promo_product: true,
                });
            }


            Utility.flyToCartAnimation(".main-product-image", "#cartCount");
            addToCartBtn.classList.add("added");
            setTimeout(() => addToCartBtn.classList.remove("added"), 1000);

           
            
        });
        
    }

    static updateTotalPrice() {
        const base = Number(addToCartBtn.dataset.basePrice);
        const mainQty = Number(Utility.el("qtyValue").textContent);
       

        const MainProducttoppingsTotal = [
            ...document.querySelectorAll(".main-product-toppings .topping-btn.active"),
        ].reduce((sum, t) => sum + Number(t.dataset.price || 0), 0);

        const GiftProducttoppingsTotal = [
            ...document.querySelectorAll(".gift-product-toppings .topping-btn.active"),
        ].reduce((sum, t) => sum + Number(t.dataset.price || 0), 0);

        const finalUnitPrice = base + MainProducttoppingsTotal + GiftProducttoppingsTotal;
        addToCartBtn.dataset.finalUnitPrice = finalUnitPrice;

        Utility.el("cartPriceValue").textContent = (finalUnitPrice * mainQty).toLocaleString();
    }

    static async mainProductHtml(res){
        const sizesObj = Array.isArray(res.sizes) ? res.sizes : [];
        const product = res.product[0];
        const imageUrl = product.image?.replace(/"/g, "") || "";
        const hasSizes = sizesObj.length > 0;

        let defaultPrice =  0;
        let defaultSize =  null;
        let defaultSizeId =  null;
        let defaultAvailable =  false;

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
                            const available = Number(sz.category_stock_quantity) > 0 && sz.size_label.toLowerCase() == 'xl';
                           
                            if (available) {
                                defaultAvailable = true;
                                defaultPrice = parseFloat(sz.price);
                                defaultSize = sz.size_label;
                                defaultSizeId = sz.id;
                            }
                            return `
                            <label class="toggle-item mb-3 ${!available ? "disabled" : ""}">
                                <input type="radio" 
                                    name="size" 
                                    value="${i}" 
                                    ${available  ? "checked" : ""}                                    
                                    ${!available ? "disabled" : ""}>
                                <span>${sz.size_label}</span>                       
                            </label>
                            `;
                        })
                        .join("")}
                    </div>
                </div>`
                : "";

        const toppingsSectionHtml =`<div class="toppings-section mt-3 w-100 center-mobile main-product-toppings">
                                        <h6 class="muted center-mobile">Toppings</h6>
                                        <div id="toppingsOptions" class="toppings-toggle center-mobile d-flex justify-content-center align-items-center w-100"></div>
                                    </div>`;
    
        const ingredientsSectionHtml = `
                    <div class="ingredients-section mt-3 center-mobil">
                        <h6 class="muted center-mobile">Product Ingredients</h6>
                        <p class="muted small center-mobile">
                            <i class="fa fa-info-circle"></i> Click to remove unwanted ingredients
                        </p>
                        <div id="IngredientsOptions" class="collectionPit center-mobile"></div>
                    </div>
                `;

                return `
                    <div class="main-product-item mb-3 p-2 rounded d-flex flex-column product-layout ">                        
                        <div class="main-product-option d-flex flex-column align-items-center"
                            style="cursor:pointer;" 
                            data-product="${product.id}">
                            <img loading="lazy"
                            src="${imageUrl}"
                            alt="${product.name}"
                            class="main-product-image"
                            style="max-width:100%; height:auto; object-fit:cover; max-height:250px;" />
                            <div class="muted small text-center mt-2">${product.name}</div>
                        </div>
                        <div class="qty-box w-100 d-flex justify-content-center align-items-center mt-3">
                            <button id="qtyMinus" class="qty-btn btn btn-sm"><i class="fa fa-minus"></i></button>
                            <span id="qtyValue">1</span>
                            <button id="qtyPlus" class="qty-btn btn btn-sm primary"><i class="fa fa-plus"></i></button>
                        </div>
                        ${sizeSectionHtml}
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
                                        ? `BUY NOW • ₦<span id="cartPriceValue">${defaultPrice.toLocaleString()}</span>`
                                        : "Unavailable"
                                }
                            </button>
                        </div>
                    </div>
                `
    }

    static async giftProductHtml(product){  
        const giftProduct  = XtraThursdayPromo.XTRATHURSDAYPRODUCTS['m'];
        if (!giftProduct) return ``;


        const giftToppingHtml = `<div class="mt-3 ingredients-list-container center-mobile gift-product-toppings">
                                    <div class="ingredients-list">
                                        <h6 class="text-center">Choose Toppings</h6>
                                        <div class="d-flex flex-wrap w-100 center-mobile" id="gifttoppingsContainer">                                
                                    </div>                      
                                </div>
                    `
        const giftIngredientsHtml = `<div class="mt-3 ingredients-list-container center-mobile">
                                    <div class="ingredients-list">
                                        <h6 class="text-center">Ingredients</h6>
                                         <p class="muted small center-mobile">
                                            <i class="fa fa-info-circle"></i> Click to remove unwanted ingredients
                                        </p>
                                        <div class="d-flex flex-wrap w-100 center-mobile" id="ingredientsContainer">                                
                                    </div>                      
                                </div>
                    `

  
        const giftHtml = giftProduct.map((product) => {
            if (product.product_name.toLowerCase().includes('margherita classic')){
                console.log(product);
                return `
                   <div class="gift-pack-item mb-3 p-2 border rounded d-flex flex-column bg-light">
                        <div>
                            <p class="muted small text-center">Customize your Gift packs</p>
                        </div>
                        <div class="gift-pack-option d-flex flex-column align-items-center" 
                            style="cursor:pointer;" 
                            data-product="${product.product_id}"
                            data-size="${product.size}"
                            data-size-id="${product.size_id}">
                            

                            <img loading="lazy"
                            src="${product.product_image}"
                            alt="${product.product_name}"
                            style="max-width:100%; height:auto; object-fit:cover; max-height:150px;" />
                            <div class="muted small text-center mt-2">${product.product_name} - ₦${product.price.toLocaleString()} x <span id="giftqtyValue">1</span></div>
                        </div>
                   

                     ${giftToppingHtml}
                     ${giftIngredientsHtml}

                        

                    </div>
                    
                `  
            }
        }).join('');

        

        return giftHtml;
    }

    static async toppingsCheckboxes(){
       function giftProductToppings(){
         XtraThursdayPromo.TOPPINGS.forEach((topping) => {
            const item = document.createElement("button");
            item.className = "topping-btn";
            item.dataset.id = topping.id;
            item.dataset.price = topping.extras_price;
            item.type = "button";
            item.textContent = `${topping.extras} +₦${Number(topping.extras_price).toLocaleString()}`;

            item.addEventListener("click", () => {
                item.classList.toggle("active");
                XtraThursdayPromo.updateTotalPrice();
            });

            document.getElementById("gifttoppingsContainer").appendChild(item);
           
        });
       }

        function mainProductToppings(){
            XtraThursdayPromo.TOPPINGS.forEach((topping) => {
                const item = document.createElement("button");
                item.className = "topping-btn";
                item.dataset.id = topping.id;
                item.dataset.price = topping.extras_price;
                item.type = "button";
                item.textContent = `${topping.extras} +₦${Number(topping.extras_price).toLocaleString()}`;
                item.addEventListener("click", () => {
                    item.classList.toggle("active");
                    XtraThursdayPromo.updateTotalPrice();
                });

                document.getElementById("toppingsOptions").appendChild(item);
            });
        }

        giftProductToppings()
        mainProductToppings()
    }

    static async ingredientsCheckboxes(){ 
        function giftProductIngredients(){
            const IngredientsContainer = Utility.el("ingredientsContainer");
            const ingredientsElm = XtraThursdayPromo.INGREDIENTS.map(ing => `
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
                    // updateTotalPrice();
                });
            });
        }

        function mainProductIngredients(){
            const IngredientsContainer = Utility.el("IngredientsOptions");
            const ingredientsElm = XtraThursdayPromo.INGREDIENTS.map(ing => `
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
                    // updateTotalPrice();
                });
            });
        }

        mainProductIngredients()
        giftProductIngredients()
    }






































    

}