import {getItem} from '../Utils/CrudRequest.js'
import Pagination from './Pagination.js';
import Utility from './Utility.js';
import Category from './Category.js';


export default class Product {
    static PRODUCTS = []  
    static currentCategory = null;  
    static currentCategoryId = null;
    static isAdmin = false;
    static EXTRAS = [];
    static pagination = Utility.el("pagination");
    static STATUS = ["available", "unavailable"]; 

    static async loadProducts() {
        const products = await getItem('products');       
        Product.PRODUCTS = products;    
        // Product.renderProducts(products);
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
        productSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
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


    static async singleProductModal(productId) {
        const res = await getItem(`products/full/${productId}`);
        if (!res || !res.product || res.product.length === 0) return;

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

        // -------------------------------------------
        // STEP 3: Build size selector UI
        // -------------------------------------------
        const sizeSectionHtml = hasSizes
            ? `
            <div class="size-toggle">
                <label class="section-title">Choose Size</label>
                <div class="toggle-group slide-in">
                ${sizesObj
                    .map((sz, i) => {
                        const available = Number(sz.real_stock) > 0;
                        return `
                        <label class="toggle-item ${!available ? "disabled" : ""}">
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
            product.category.toLowerCase() === "pizza"
                ? `
            <div class="toppings-section mt-3">
                <h6 class="muted center-mobile">Toppings</h6>
                <div id="toppingsOptions" class="toppings-toggle center-mobile"></div>
            </div>
            `
                : "";

        // Modal
        const domBody = Utility.ModalBody;
        const title = Utility.ModalTitle;
        title.textContent = `Customize Your Order`;

        domBody.innerHTML = `
            <div class="product-layout">
                <div class="product-left">
                    <img src="${imageUrl}" alt="${product.name}" class="product-image zoom">
                    <p class="text-center muted mt-4">${product.description || ""}</p>
                </div>

                <div class="product-right">
                    <h3 class="mb-1">${Utility.toTitleCase(product.name)}</h3>
                    <div class="badges center-mobile">
                        <span class="tag">${Utility.toTitleCase(product.category)}</span>
                    </div>

                    ${sizeSectionHtml}
                    ${toppingsSectionHtml}

                    <div class="qty-box center-mobile">
                        <button id="qtyMinus" class="qty-btn">-</button>
                        <span id="qtyValue">1</span>
                        <button id="qtyPlus" class="qty-btn">+</button>
                    </div>

                    <div class="add-cart-footer">
                        <button id="addToCartBtn" 
                            class="btn-add-cart ${defaultAvailable ? "" : "btn-disabled"}"
                            data-size="${defaultSize || ""}"
                            data-base-price="${defaultPrice}"
                            data-final-unit-price="${defaultPrice}"
                            ${!defaultAvailable ? "disabled" : ""}>
                            ${
                                defaultAvailable
                                    ? `Add to Cart • ₦<span id="cartPriceValue">${defaultPrice.toLocaleString()}</span>`
                                    : "Out of Stock"
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
                    addToCartBtn.dataset.basePrice = selected.price;

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

            Cart.addToCart({
                product_id: product.id,
                title: product.name,
                size: addToCartBtn.dataset.size || null,
                price: finalUnitPrice,
                qty,
                image: imageUrl,
                toppings: selectedToppings,
                type: "regular",
            });

            Product.flyToCartAnimation(".product-image", "#cartCount");
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

    

    

    
}