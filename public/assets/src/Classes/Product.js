import {getItem, postItem} from '../Utils/CrudRequest.js'
import Pagination from './Pagination.js';
import Utility from './Utility.js';
import Category from './Category.js';
import Cart from './Cart.js';   


export default class Product {
    static PRODUCTS = [];  
    static INGREDIENTS = [];
    static PRODUCT_INGREDIENTS = [];
    static ASSIGNED_INGREDIENTS = [];
    static currentCategory = null;  
    static currentCategoryId = null;
    static isAdmin = false;
    static EXTRAS = [];
    static pagination = Utility.el("pagination");
    static STATUS = ["available", "unavailable"];
 

    static async loadProducts() {
        const products = await getItem('products');       
        Product.PRODUCTS = products;   
        
    }
    
    static renderMenuTab() {
        const tabs = document.getElementById("categoryTabs");

        Category.CATEGORIES.forEach((cat, i) => {
            const btn = document.createElement("button");
            btn.classList.add("bounce-card", ...(i === 0 ? ["active"] : []));
            btn.textContent = Utility.toTitleCase(cat.name);
            btn.dataset.category = cat.slug;
            btn.dataset.categoryId = cat.id;

            tabs.appendChild(btn);

            // Set current category ONLY for the first item
            if (i === 0) {
                Product.currentCategory = cat.slug;
                Product.currentCategoryId = cat.id;
            }
        });


      
        tabs.querySelectorAll("button").forEach((tab) => {
            tab.onclick = () => {
                tabs
                .querySelectorAll("button")
                .forEach((t) => t.classList.remove("active"));
                tab.classList.add("active");
                Product.currentCategory = tab.dataset.category;
                Product.currentCategoryId = parseInt(tab.dataset.categoryId);
                Utility.CURRENTPAGE = 1;
                Product.renderMenu();
            };
        });

        Product.renderMenu();
    }

    static renderMenu() { 
        Product.renderMenuCard(Product.PRODUCTS);
        Product.renderProducts(Product.currentCategoryId);
      
    }

    static loadProductDropdowns(){
        const productSelect = document.getElementById("productSelect");
        productSelect.innerHTML = '';
        productSelect.innerHTML = '<option value="">-- Select Product --</option>';
        Product.PRODUCTS.forEach(p => {
        productSelect.innerHTML += `<option value="${p.id}" data-cid="${p.category_id}">${p.name}</option>`;
        });
    }

     static updateKpis(data) {
        const domEl = Utility.el("ADMIN_SYSTEM");
        if (!domEl) return;
        document.getElementById("totalItems").textContent = data.length;
        document.getElementById("availableItems").textContent = data.filter(
        (i) => i.is_active == "1"
        ).length;
        document.getElementById("outOfStock").textContent = data.filter(
        (i) =>i.is_active == "0"
        ).length;
        // if (data.length > 0)
        // document.getElementById("popularDish").textContent = data.reduce((a, b) =>
        //     a.rating > b.rating ? a : b
        // ).title;
  }

    static renderProducts(category_id) {
      
        const tbody = document.querySelector("#productTable tbody");
        if(!tbody) return;
        const table = Utility.el("productTable")
        tbody.innerHTML = "";
        Utility.NODATA.innerHTML = "";
        const filtered = Product.PRODUCTS.filter(p => p.category_id == category_id);  

        Product.updateKpis(filtered);  
        
        if (filtered.length === 0) {
            Utility.renderEmptyState(Utility.NODATA)
            table.style.display = "none";
            return
        }

        table.style.display = "table";

        filtered.forEach((p, i) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
            <td>${i + 1}</td>
            <td><img src="${p.image}" alt="${p.name}" style="width:50px;height:50px;"></td>
            <td>${p.name}</td>
            <td>${p.sku}</td>
            <td>${p.description}</td>
            <td>${p.category}</td>
            <td><span class="status ${p.is_active == "1" ? "active" : "danger"}">${p.is_active == "1" ? "Active" : "Inactive"}</span></td>
            <td>
                <button class="btn btn-sm btn-primary edit-btn" data-id="${p.id}">Edit</button>
                ${Utility.role==='admin' ? `<button class="btn btn-sm btn-outline-error delete-btn" data-id="${p.id}">Delete</button>` : ''}
            </td>
            `;
            tbody.appendChild(tr);
        });
    }

    static renderMenuCard(data, page = 1) {
        const menuGrid = Utility.el("menuGrid");
        if (!menuGrid) return;
        menuGrid.innerHTML = "";
        Utility.NODATA.innerHTML = "";
        
        const products = Array.isArray(data) ? data : Object.values(data || {});

        const start = (page - 1) * Utility.PAGESIZE;
        const end = start + Utility.PAGESIZE;

        let filtered = products.filter(
            (i) => i.category.toLowerCase() === Product.currentCategory.toLowerCase()
          );

        if (filtered.length === 0) {
            Utility.renderEmptyState(Utility.NODATA)
            return;
        }
        
        const paginatedData = filtered.slice(start, end);
        
        paginatedData.forEach((o, idx) => {
            menuGrid.insertAdjacentHTML("beforeend", Product.MenuCard(o, idx));
        });

        Product.menuCardEvents();

        if (filtered.length > Utility.PAGESIZE)
            Pagination.render(filtered.length, page, filtered, Product.renderMenuCard);        
    }

    static menuCardEvents(){
        const cards = document.querySelectorAll(".menu-card");
        cards.forEach((card) => {
            card.addEventListener("click", (e) => {
                const productId = card.dataset.product;
                Product.singleProductModal(productId);
            });
        });
    }

    static MenuCard(product, index) {
        return `
            <div class="col-6 col-md-3" data-aos="fade-up" 
                data-aos-delay="${index * 50}">
                <div class="menu-card bounce-card position-relative h-100" 
                    data-product="${product.id}">
                    <span class="badge text-bg-success">
                    ${Utility.toTitleCase(product.category)}</span>
                    <div style="display:flex; justify-content:center; align-items:center;">
                    <img loading="lazy"
                        src="${product.image}"
                        alt="${product.name}"
                        style="max-width:100%; height:auto; object-fit:cover; max-height:170px;" />
                    </div>

                    <div class="p-3">
                        <div class="w-100 d-flex justify-content-between align-items-center flex-wrap flex-lg-nowrap">
                        <h6 class="mb-0 product-title center-mobile text-center">${product.name}</h6>
                        <div class="small text-muted mb-2 center-mobile"></div>
                    </div>
                </div>
          </div>
        </div>
        `        
    }


  

    static async singleProductModal(productId, promotion = []) {
        const res = await getItem(`products/full/${productId}`);
        if (!res || !res.product || res.product.length === 0) return;

            

        const productIngredients = await getItem(`products/ingredients/${productId}`);

        const product = res.product[0];
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
                        const available = Number(sz.real_stock) > 0;
                        return `
                        <label class="toggle-item mb-3 ${!available ? "disabled" : ""}">
                            <input type="radio" 
                                name="size" 
                                value="${i}" 
                                ${available && !defaultAvailable && i === 0 ? "checked" : ""}
                                ${available && defaultAvailable && sz.size_label === defaultAvailable.size_label ? "checked" : ""}
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
                    <hr/>
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


    static flyToCartAnimation(productImgSelector, cartIconSelector) {
        const productImg = document.querySelector(productImgSelector);
        const cartIcon = document.querySelector(cartIconSelector);

        if (!productImg || !cartIcon) {
        console.warn("❌ Fly-to-cart elements not found.");
        return;
        }

        const imgRect = productImg.getBoundingClientRect();
        const cartRect = cartIcon.getBoundingClientRect();

        const clone = productImg.cloneNode(true);
        clone.style.position = "fixed";
        clone.style.top = imgRect.top + "px";
        clone.style.left = imgRect.left + "px";
        clone.style.width = imgRect.width + "px";
        clone.style.height = imgRect.height + "px";
        clone.style.zIndex = "9999";
        clone.style.transition = "all 0.8s ease";
        document.body.appendChild(clone);

        requestAnimationFrame(() => {
            clone.style.top = cartRect.top + "px";
            clone.style.left = cartRect.left + "px";
            clone.style.width = "30px";
            clone.style.height = "30px";
            clone.style.opacity = "0.4";
        });

        clone.addEventListener("transitionend", () => {
            clone.remove();
        });
    }

    static loadProductDropdowns() {
              
        const productSelect = document.getElementById("productSelect");
        Product.PRODUCTS.forEach(p => {
            productSelect.innerHTML += `<option value="${p.id}" data-ci="${p.category_id}">${p.name}</option>`;
        });
            
           
    }

    static renderExtrasTable(data, page = 1) {
        const tbody = document.querySelector("#extraTable tbody");
        const notDATA = Utility.el("no-data");

        tbody.innerHTML = "";
        notDATA.innerHTML = "";

        const extras = Array.isArray(data) ? data : Object.values(data || {});

        const start = (page - 1) * Utility.PAGESIZE;
        const end = start + Utility.PAGESIZE;

        if (!extras || extras.length == 0) {
        Utility.renderEmptyState(notDATA);

        Product.pagination.style.display = "none";
        return;
        }

        Product.pagination.style.display = "flex";

        const paginatedData = extras.slice(start, end);

        paginatedData.forEach((item, idx) => {
        const tr = document.createElement("tr");
        tr.classList.add("bounce-card");
        tr.innerHTML = `
        <td>${idx + 1}</td>
        <td>${Utility.toTitleCase(item.extras)}</td>      
        <td>${Utility.fmtNGN(item.extras_price)}</td>
        <td>${Utility.toTitleCase(item.name)}</td>
        <td>
            <button class="btn btn-sm btn-primary" data-open="${item.id}">
            View
            </button>
            <button class="btn btn-sm btn-ghost" 
            data-delete="${item.id}">
            Delete
            </button>
        </td>
        `;
        tbody.appendChild(tr);
        });
        if (extras.length > Utility.PAGESIZE)
        Pagination.render(extras.length, page, extras, Product.renderExtrasTable);
    }

    static addExtrasModal() {
        let domBody = Utility.el("detailModalBody");
        const domFooter = Utility.el("detailModalButtons");
        let domTitle = Utility.el("detailModalLabel");

        domTitle.innerHTML = "";
        domBody.innerHTML = "";
        domFooter.innerHTML = "";
        domTitle.textContent = `New Extras`;

        const categoryHtml = Category.CATEGORIES.map((i, idx) => {
        return `<option value="${i.id}" ${idx === 0 ? "selected" : ""}>
        ${Utility.toTitleCase(i.name)}</option>`;
        }).join("");

        domBody.innerHTML = `
        <form class="row"  id="addExtras">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="muted">Extras name</label>
                            <input type="text" id="dishName" name="extras" placeholder="eg: Extra cheese, Mushrooms">
                        </div>
                        <div class="form-group">
                        <label class="muted">Price</label>
                            <input type="number" id="dishPrice" name="extras_price" placeholder="Price">
                        </div>
                        <div class="form-group">
                        <label class="muted">Category</label>
                        <select id="dishCategory" name="category_id">
                            ${categoryHtml}
                        </select>
                        <div id="categorySizes" class="mt-2"></div> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="image-box"></div>
                        <p class="muted mt-2">By clicking on the submit button, you will make changes to the product information.</p>
                        <button class="btn btn-primary mt-2" type="submit">Save Extras</button>
                    </div>
                </div>
            </div>
        </form>
        `;
        $("#displayDetails").modal("show");
    }

     static openExtraModal(id) {
        const extras = Product.EXTRAS.find((extra) => extra.id == id);
        if (!extras) {
            Utility.toast("extras not found", "error");
            return;
        }

        let domBody = Utility.el("detailModalBody");
        const domFooter = Utility.el("detailModalButtons");
        let domTitle = Utility.el("detailModalLabel");

        domTitle.innerHTML = "";
        domBody.innerHTML = "";
        domFooter.innerHTML = "";

    domTitle.textContent = `Manage ${extras.extras}`;

    const categoryHtml = Category.CATEGORIES.map((i, idx) => {
      return `<option value="${i.id}" ${
        extras.category_id == i.id ? "selected" : ""
      }>
        ${Utility.toTitleCase(i.name)}</option>`;
    }).join("");

    domBody.innerHTML = `
      <form class="row" id="updateExtras" data-id="${id}">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="muted">Extras name</label>
                        <input type="text" id="dishName" name="extras" value="${extras.extras}" placeholder="eg: Extra cheese, Mushrooms">
                    </div>
                    <div class="form-group">
                      <label class="muted">Price</label>
                        <input type="number" id="dishPrice" value="${extras.extras_price}" name="extras_price" placeholder="Price">
                    </div>
                    <div class="form-group">
                      <label class="muted">Category</label>
                      <select id="dishCategory" name="category_id">
                        ${categoryHtml}
                      </select>
                      <div id="categorySizes" class="mt-2"></div> 
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="image-box"></div>
                    <p class="muted mt-2">By clicking on the submit button, you will make changes to the product information.</p>
                    <button class="btn btn-primary mt-2" type="submit">Save Changes</button>
                </div>
            </div>
        </div>
      </form>
    `;
    $("#displayDetails").modal("show");
  }

  static inventorySnapShot(data, page = 1) {
    const el = document.getElementById("inventoryList");
    const notDATA = Utility.el("no-data");

        el.innerHTML = "";
        notDATA.innerHTML = "";

        if (!data || data.length === 0) {
            Utility.renderEmptyState(notDATA);
            return;
        }

        const start = (page - 1) * Utility.PAGESIZE;
        const end = start + Utility.PAGESIZE;
        const paginatedData = data.slice(start, end);

        const html = paginatedData.map(product => {
            const statusLabel = product.is_active === "1" 
                ? '<span class="status delivered">Active</span>' 
                : '<span class="status inactive">Inactive</span>';

            return `
            <div class="inventory-item" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;padding:6px 0;border-bottom:1px solid #e5e7eb">
                <div style="display:flex;align-items:center;gap:10px">
                    <img src="${product.image}" alt="${product.name}" style="width:48px;height:48px;object-fit:cover;border-radius:6px">
                    <div>
                        <strong>${Utility.toTitleCase(product.name)}</strong>
                        <div style="color:var(--muted);font-size:13px">
                            Category: ${Utility.toTitleCase(product.category)}<br>
                            SKU: ${product.sku}
                        </div>
                    </div>
                </div>
                <div>${statusLabel}</div>
            </div>
            `;
        }).join("");

        el.innerHTML = html;

        // Pagination
        if (data.length > Utility.PAGESIZE) {
            Pagination.render(data.length, page, data, Product.inventorySnapShot);
        }
}


    static countTheLowStocks(productStock, categoryStock){
        let lowProductStocks = productStock.filter(item => {
            return parseInt(item.qty) <= parseInt(item.low_stock_threshold);
        }).length;

        let lowCategoryStocks = categoryStock.filter(item => {
            return parseInt(item.qty) <= parseInt(item.low_stock_threshold);
        }).length;

        const totalLowStocks = lowProductStocks + lowCategoryStocks;
        return totalLowStocks;
    }

   


    static loadIngredientsTable(ingredients, page = 1) {
        const tbody = document.querySelector("#ingredientsTable tbody");
        const notDATA = Utility.el("no-data");
        tbody.innerHTML = "";
       
        const start = (page - 1) * Utility.PAGESIZE;
        const end = start + Utility.PAGESIZE;
        if (!ingredients || ingredients.length == 0) {
        Utility.renderEmptyState(notDATA);   
        return;
        }
     
        const paginatedData = ingredients.slice(start, end);
        paginatedData.forEach((item, idx) => {
            const tr = document.createElement("tr");
            tr.classList.add("bounce-card");
            tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>${item.category || ''}</td>   
            <td>${Utility.toTitleCase(item.ingredient_name)}</td>              
            <td>
                <button class="btn btn-xs btn-primary" data-action='edit-ingredient' data-id="${item.id}">
                Edit
                </button>
                <button class="btn btn-xs btn-default"
                data-id="${item.id}" data-action='delete-ingredient'>
                Delete
                </button>
            </td>
            `;
            tbody.appendChild(tr);
            }
        );
        if (ingredients.length > Utility.PAGESIZE)
        Pagination.render(ingredients.length, page, ingredients, Product.loadIngredientsTable,"listPagination");
    
    }

    static assignIngredientsModal(){
     const domBody = Utility.ModalBody;
        const title = Utility.ModalTitle;
        title.textContent = `Assign Ingredients to Product`;
        const categoryHtml = Category.CATEGORIES.map((i, idx) => {
            return `<option value="${i.id}" data-name="${i.name}" ${idx === 0 ? "selected" : ""}>
            ${Utility.toTitleCase(i.name)}</option>`;
            }).join("");

        domBody.innerHTML = `
                <form id="assignIngredientsForm">
                 <p class="small muted" data-aos="fade-right" data-aos-delay="100">Browse all product ingredients on. Use the filters below to find exactly what you’re looking for.</p>
                    
                    <div class="form-group">
                        <label>Select Ingredient Category</label>
                        <select id="categorySelect">
                            <option value="">-- Select Category --</option>
                            ${categoryHtml}
                        </select>
                    </div>
                    <div id="productSelectDom"></div>
                    
                    <p class="small muted mt-2" data-aos="fade-right" data-aos-delay="100">
                        Select the ingredients you want to assign to the selected product.
                    </p>
                     <div class="collectionPit d-flex flex-wrap gap-2 align-items-center" id="collectionPit">
                    </div>
                    <div class="form-group mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Assign Ingredients</button>
                    </div>
                </form>
        
        `

        $("#displayDetails").modal("show");
      
        displayIngredients(document.querySelector("#categorySelect").value);
        
        
        document.querySelector("#categorySelect").addEventListener("change", (e) => {
            const selectedCategoryId = e.target.value;
          
            displayIngredients(selectedCategoryId);
        });
        

        function displayIngredients(selectedCategoryId){
            const container = Utility.el("collectionPit");
            container.innerHTML = "";
            
            const ingredients = Product.INGREDIENTS.filter(ing => ing.category_id == selectedCategoryId);

            const categoryName = document.querySelector("#categorySelect").options[document.querySelector("#categorySelect").selectedIndex].dataset.name;

            if (!selectedCategoryId){
                container.innerHTML = `<p class="muted">Please select a category to load ingredients.</p>`;
                title.textContent = `Assign Ingredients to Product`;
                Utility.el("productSelectDom").innerHTML = "";
                return;
            }
            // set the title
            title.textContent = `Assign ${Utility.toTitleCase(categoryName)} Ingredients to Product`;

            //inject the products select that category_id matches
            const productSelectDom = Utility.el("productSelectDom");
            productSelectDom.innerHTML = `
                <div class="form-group">
                    <label>Select Product</label>
                    <select id="productSelectForIngredients">
                        <option value="">-- Select Product --</option>
                        ${Product.PRODUCTS.filter(p => p.category_id == selectedCategoryId).map(p => `<option value="${p.id}">${Utility.toTitleCase(p.name)}</option>`).join("")}
                    </select>
                </div>
            `;

            ingredients.forEach(ingredient => {
                const div = document.createElement("div");
                div.className = "ingredient-item";
                div.innerHTML = `
                    <input type="checkbox" id="ingredient-${ingredient.id}" value="${ingredient.id}">
                    <label for="ingredient-${ingredient.id}">${Utility.toTitleCase(ingredient.ingredient_name)}</label>
                `;
                container.appendChild(div);
            });
        }

        Product.assignIngredientsToProduct();
    }

    static assignIngredientsToProduct() {
        document.querySelector("#assignIngredientsForm").addEventListener("submit", async (e) => {
            e.preventDefault();
            const selectedProductId = document.querySelector("#productSelectForIngredients").value;
            if (!selectedProductId) {
                Utility.toast("Please select a product to assign ingredients to.", "error");
                return;
            }
            const selectedIngredients = [];
            document.querySelectorAll("#collectionPit input[type='checkbox']:checked").forEach(checkbox => {
                selectedIngredients.push(checkbox.value);
            }
            );
            if (selectedIngredients.length === 0) {
                Utility.toast("Please select at least one ingredient to assign.", "error");
                return;
            }
           
            const data = {
                product_id: selectedProductId,
                ingredient_ids: selectedIngredients
            };

            $("#displayDetails").modal("hide");
            const sendAssignRequest = await postItem("/admin/products/ingredients/assign", data,"Assigning ingredients to product...?");
            if (sendAssignRequest) {
                Utility.toast("Ingredients assigned successfully.", "success");
                
            } else {
                Utility.toast(sendAssignRequest.message || "Failed to assign ingredients.", "error");
            }
        });
    }

     static async loadAssignedIngredientsTable(productId){
        const tbody = document.querySelector("#assignedIngredientsTable tbody");
        tbody.innerHTML = "";
        Product.ASSIGNED_INGREDIENTS = [];

        Product.ASSIGNED_INGREDIENTS = await getItem(`products/ingredients/${productId}`);

        if (!productId){
            tbody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <p class="muted small">Please select a product to view its assigned ingredients.</p>
                    </td>
                </tr>
            `;
            return;
        }

        if (Product.ASSIGNED_INGREDIENTS.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <p class="muted small">No ingredients assigned to this product.</p>
                    </td>
                </tr>
            `;
            return;
        }
        Product.ASSIGNED_INGREDIENTS.forEach((ingredient, i) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${i+1}</td>
                <td>${Utility.toTitleCase(ingredient.ingredient_name)}</td>
                <td>
                    <button class="btn btn-xs btn-default" data-action="delete" data-id="${ingredient.id}">Delete</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

     }





   
    





    

    

    
}