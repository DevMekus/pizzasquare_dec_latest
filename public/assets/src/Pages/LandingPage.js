import Utility from "../Classes/Utility.js";
import { CONFIG } from "../Utils/config.js";
import Product from "../Classes/Product.js";
import Category from "../Classes/Category.js";
import { getItem } from "../Utils/CrudRequest.js";
import Cart from "../Classes/Cart.js";

class LandingPage {
    constructor() {
        this.initialize();
    }

    async initialize() { 
        
        Utility.runClassMethods(this, ["initialize"]);
    }

    hero_animation() {
    const domEl = document.querySelector(".hero-section");
    if (!domEl) return;

    const heroContent = [
      {
        title: "Made With Love From Italy",
        text: "Hot, cheesy and mouthwatering. Dive in!",
        thumb: `${CONFIG.BASE_URL}/assets/images/hero/pizza2.png`,
        btn: "Order Now",
        aos: "fade-up",
        action: "spin",
      },
      {
        title: "Wrapped in Flavour, Rolled With Love",
        text: "Grilled to perfection with rich sauces.",
        thumb: `${CONFIG.BASE_URL}/assets/images/hero/shawar.png`,
        btn: "Order Now",
        aos: "fade-left",
        action: "zoom",
      },
      {
        title: "Tasty IceCream & Desserts",
        text: "Cool down with our delightful flavors.",
        thumb: `${CONFIG.BASE_URL}/assets/images/hero/waffles.png`,
        btn: "Order Now",
        aos: "fade-right",
        action: "zoom-in",
      },
      {
        title: "Slice of Heaven in Every Bite",
        text: "Chill with our icy, fruity beverages.",
        thumb: `${CONFIG.BASE_URL}/assets/images/hero/palmon.png`,
        btn: "Order Now",
        aos: "zoom-in",
        action: "zoom-out",
      },
    ];

    let current = 0;
    const heroRight = document.getElementById("hero-right");
    const heroBtn = document.getElementById("hero-btn");

    function updateHero() {
      const item = heroContent[current];

      Utility.el(
        "heroTitleCon"
      ).innerHTML = `<h1 id="hero-title" class="hero-title" data-aos="${item.aos}">
      ${item.title}
      </h1>`;

      heroBtn.innerText = item.btn;
      heroRight.innerHTML = `
      <div class="hero-imaged" data-aos="${item.aos}">
          <img loading="lazy" id="hero-image"
            class="img-fluid  product-img ${item.action}" 
            src="${item.thumb}" />
      </div>
      `;

      if (typeof AOS !== "undefined") {
        AOS.refresh();
      }

      current = (current + 1) % heroContent.length;
    }

    updateHero();
    setInterval(updateHero, 6000);
    }

    async loadMenu(){
      Utility.cardSkelecton("menuGrid", 8);
      await Category.loadCategories();
      await Product.loadProducts();
      const domEl = Utility.el("categoryTabs");
      if (!domEl) return;
      Product.isAdmin = false;
      Product.renderMenuTab();
        //call the menu route and get all data once
        //show skelectons while loading
    }    
  

    // async buildYourPizza() {
    // const domEl = document.getElementById("buildYourPizza");
    // if (!domEl) return;

    // // Load Extras & Pizzas
    // Product.EXTRAS = await getItem("extras");
    // const pizzasFull = await getItem("pizzas-with-sizes"); 
    // // (OPTIONAL ENDPOINT NOTE: see note below)

    // const pizzaSelect = Utility.el("pizzaSelect");
    // const crustSelect = Utility.el("crustSelect");
    // const toppingsWrap = Utility.el("toppingsWrap");
    // const builderTotal = Utility.el("builderTotal");
    // const addBtn = Utility.el("builderAddToCart");

    // let selectedPizza = null;
    // let selectedSize = null;
    // let toppingsSelected = [];

    // // --------------------------------------------------
    // // LOAD PIZZA SELECTION
    // // --------------------------------------------------
    // pizzaSelect.innerHTML = pizzasFull.map((p, i) => `
    //     <option value="${p.id}" ${i === 0 ? "selected" : ""}>
    //         ${Utility.toTitleCase(p.name)}
    //     </option>
    // `).join("");

    // // --------------------------------------------------
    // // LOAD SIZES USING shared_stock
    // // --------------------------------------------------
    // function loadSizes(pizza) {
    //     const sizesBox = document.getElementById("sizesBox");
    //     sizesBox.innerHTML = "";

    //     const sizesData = pizza.sizes || [];

    //     if (!sizesData.length) {
    //         sizesBox.innerHTML = `
    //             <p class="text-danger fw-bold mt-2">No sizes found</p>
    //         `;
    //         return;
    //     }

    //     const firstAvailable = sizesData.find(sz => Number(sz.shared_stock) > 0);
    //     const allUnavailable = sizesData.every(sz => Number(sz.shared_stock) <= 0);

    //     sizesBox.innerHTML = `
    //         <label class="form-label fw-bold mt-2">Sizes</label>
    //         <div class="d-flex gap-2 flex-wrap">
    //             ${sizesData.map(sz => `
    //                 <button 
    //                     type="button"
    //                     class="btn btn-sm btn-outline-dark size-btn ${Number(sz.shared_stock) > 0 ? "" : "disabled"}"
    //                     data-size="${sz.size_label}"
    //                     data-price="${sz.price}"
    //                     ${Number(sz.shared_stock) <= 0 ? "disabled" : ""}
    //                 >
    //                     ${sz.size_label} - ${Utility.fmtNGN(sz.price)}
    //                     ${Number(sz.shared_stock) <= 0 ? " (Out)" : ""}
    //                 </button>
    //             `).join("")}
    //         </div>
    //     `;

    //     if (allUnavailable) {
    //         selectedSize = null;
    //         addBtn.disabled = true;
    //         addBtn.textContent = "OUT OF STOCK";
    //         builderTotal.textContent = "₦0";
    //         return;
    //     }

    //     // Default size
    //     selectedSize = firstAvailable.size_label;

    //     // Highlight active
    //     document.querySelectorAll(".size-btn").forEach(btn => {
    //         btn.classList.toggle("active", btn.dataset.size === selectedSize);
    //     });

    //     calcTotal();

    //     // Add event listeners
    //     document.querySelectorAll(".size-btn").forEach(btn => {
    //         if (btn.disabled) return;

    //         btn.addEventListener("click", () => {
    //             document.querySelectorAll(".size-btn").forEach(b => b.classList.remove("active"));
    //             btn.classList.add("active");
    //             selectedSize = btn.dataset.size;
    //             calcTotal();
    //         });
    //     });
    // }

    // // --------------------------------------------------
    // // CALCULATE TOTAL
    // // --------------------------------------------------
    // function calcTotal() {
    //     if (!selectedPizza || !selectedSize) return;

    //     const sizeInfo = selectedPizza.sizes.find(s => s.size_label === selectedSize);
    //     const base = Number(sizeInfo?.price || 0);

    //     const crustCost = Number(crustSelect.selectedOptions[0].dataset.price || 0);

    //     const extrasTotal = toppingsSelected.reduce(
    //         (sum, e) => sum + Number(e.price),
    //         0
    //     );

    //     const total = base + crustCost + extrasTotal;

    //     builderTotal.textContent = Utility.fmtNGN(total);

    //     addBtn.disabled = false;
    //     addBtn.textContent = "Add to Cart";

    //     return total;
    // }

    // // --------------------------------------------------
    // // LOAD TOPPINGS
    // // --------------------------------------------------
    // function loadExtras() {
    //     toppingsWrap.innerHTML = "";
    //     toppingsSelected = [];

    //     Product.EXTRAS.forEach(t => {
    //         const chip = document.createElement("span");
    //         chip.className = "topping-btn";
    //         chip.textContent = `${t.extras} (+${Utility.fmtNGN(t.extras_price)})`;

    //         chip.addEventListener("click", () => {
    //             chip.classList.toggle("active");

    //             if (chip.classList.contains("active")) {
    //                 toppingsSelected.push({
    //                     label: t.extras,
    //                     price: t.extras_price
    //                 });
    //             } else {
    //                 toppingsSelected = toppingsSelected.filter(x => x.label !== t.extras);
    //             }

    //             calcTotal();
    //         });

    //         toppingsWrap.appendChild(chip);
    //     });
    // }

    // // --------------------------------------------------
    // // CHANGE PIZZA
    // // --------------------------------------------------
    // pizzaSelect.addEventListener("change", () => {
    //     const pizzaId = pizzaSelect.value;
    //     selectedPizza = pizzasFull.find(p => p.id == pizzaId);

    //     loadSizes(selectedPizza);
    //     calcTotal();
    // });

    // // --------------------------------------------------
    // // Load defaults on first load
    // // --------------------------------------------------
    // selectedPizza = pizzasFull.find(p => p.id == pizzaSelect.value);

    // loadSizes(selectedPizza);
    // loadExtras();
    // calcTotal();

    // // --------------------------------------------------
    // // ADD TO CART
    // // --------------------------------------------------
    // addBtn.addEventListener("click", () => {
    //     if (!selectedSize) return;

    //     const generatedId = "custom_" + Date.now();

    //     Cart.addToCart({
    //         product_id: generatedId,
    //         title: `${selectedPizza.name} (${selectedSize})`,
    //         size: selectedSize,
    //         bbqSauce: crustSelect.value,
    //         qty: 1,
    //         price: calcTotal(),
    //         type: "custom",
    //         tag: "pizza",
    //         image: selectedPizza.image,
    //         toppings: toppingsSelected.map(x => x.label)
    //     });

    //     Product.flyToCartAnimation(".product-image", "#cartCount");

    //     Utility.toast(
    //         `Custom ${selectedPizza.name} (${selectedSize}) added to Order`,
    //         "success"
    //     );
    // });
    // }

    async buildYourPizza() {
    const domEl = document.getElementById("buildYourPizza");
    if (!domEl) return;

    // Load Extras & Pizzas with sizes
    Product.EXTRAS = await getItem("extras");
    const pizzasFull = await getItem("pizzas-with-sizes");

    const pizzaSelect = Utility.el("pizzaSelect");
    const crustSelect = Utility.el("crustSelect");
    const toppingsWrap = Utility.el("toppingsWrap");
    const builderTotal = Utility.el("builderTotal");
    const addBtn = Utility.el("builderAddToCart");

    let selectedPizza = null;
    let selectedSize = null;
    let toppingsSelected = [];

    // --------------------------------------------------
    // LOAD PIZZA SELECTION
    // --------------------------------------------------
    pizzaSelect.innerHTML = pizzasFull
        .map(
            (p, i) => `
        <option value="${p.id}" ${i === 0 ? "selected" : ""}>
            ${Utility.toTitleCase(p.name)}
        </option>`
        )
        .join("");

    // --------------------------------------------------
    // GET REAL QUANTITY
    // --------------------------------------------------
    function getRealQty(size) {
        const shared = Number(size.shared_stock);
        const productQty = Number(size.product_stock_quantity);
        const categoryQty = Number(size.category_stock_quantity);

        if (shared === 1) {
            // Use category stock
            return categoryQty > 0 ? categoryQty : 0;
        } else {
            // Use product stock
            return productQty > 0 ? productQty : 0;
        }
    }

    // --------------------------------------------------
    // LOAD SIZES WITH STOCK LOGIC
    // --------------------------------------------------
    function loadSizes(pizza) {
        const sizesBox = document.getElementById("sizesBox");
        sizesBox.innerHTML = "";

        const sizesData = pizza.sizes || [];

        if (!sizesData.length) {
            sizesBox.innerHTML = `
                <p class="text-danger fw-bold mt-2">No sizes found</p>
            `;
            return;
        }

        // Determine availability per size
        sizesData.forEach(sz => {
            sz.realQty = getRealQty(sz);
            sz.available = sz.realQty > 0;
        });

        const firstAvailable = sizesData.find(sz => sz.available);
        const allUnavailable = sizesData.every(sz => !sz.available);

        sizesBox.innerHTML = `
            <label class="form-label fw-bold mt-2">Sizes</label>
            <div class="d-flex gap-2 flex-wrap">
                ${sizesData
                    .map(
                        sz => `
                    <button 
                        type="button"
                        class="btn btn-sm btn-outline-dark size-btn 
                            ${sz.available ? "" : "disabled"}"
                        data-size="${sz.size_label}"
                        data-price="${sz.price}"
                        data-qty="${sz.realQty}"
                        ${sz.available ? "" : "disabled"}
                    >
                        ${sz.size_label} - ${Utility.fmtNGN(sz.price)}
                        ${sz.available ? "" : " (Out)"}
                    </button>`
                    )
                    .join("")}
            </div>
        `;

        if (allUnavailable) {
            selectedSize = null;
            addBtn.disabled = true;
            builderTotal.textContent = "₦0";
            addBtn.textContent = "OUT OF STOCK";
            return;
        }

        // Default size
        selectedSize = firstAvailable.size_label;

        // Highlight active size
        document.querySelectorAll(".size-btn").forEach(btn => {
            btn.classList.toggle("active", btn.dataset.size === selectedSize);
        });

        calcTotal();

        // Add listeners only to available sizes
        document.querySelectorAll(".size-btn").forEach(btn => {
            if (btn.disabled) return;

            btn.addEventListener("click", () => {
                document.querySelectorAll(".size-btn").forEach(b =>
                    b.classList.remove("active")
                );
                btn.classList.add("active");
                selectedSize = btn.dataset.size;
                calcTotal();
            });
        });
    }

    // --------------------------------------------------
    // CALCULATE TOTAL
    // --------------------------------------------------
    function calcTotal() {
        if (!selectedPizza || !selectedSize) return;

        const sizeInfo = selectedPizza.sizes.find(
            s => s.size_label === selectedSize
        );

        const base = Number(sizeInfo?.price || 0);
        const crustCost = Number(crustSelect.selectedOptions[0].dataset.price);
        const extrasTotal = toppingsSelected.reduce(
            (sum, e) => sum + Number(e.price),
            0
        );

        const total = base + crustCost + extrasTotal;

        builderTotal.textContent = Utility.fmtNGN(total);

        addBtn.disabled = false;
        addBtn.textContent = "Add to Cart";

        return total;
    }

    // --------------------------------------------------
    // LOAD TOPPINGS
    // --------------------------------------------------
    function loadExtras() {
        toppingsWrap.innerHTML = "";
        toppingsSelected = [];

        Product.EXTRAS.forEach(t => {
            const chip = document.createElement("span");
            chip.className = "topping-btn";
            chip.textContent = `${t.extras} (+${Utility.fmtNGN(
                t.extras_price
            )})`;

            chip.addEventListener("click", () => {
                chip.classList.toggle("active");

                if (chip.classList.contains("active")) {
                    toppingsSelected.push({
                        label: t.extras,
                        price: t.extras_price
                    });
                } else {
                    toppingsSelected = toppingsSelected.filter(
                        x => x.label !== t.extras
                    );
                }

                calcTotal();
            });

            toppingsWrap.appendChild(chip);
        });
    }

    // --------------------------------------------------
    // CHANGE PIZZA
    // --------------------------------------------------
    pizzaSelect.addEventListener("change", () => {
        const pizzaId = pizzaSelect.value;
        selectedPizza = pizzasFull.find(p => p.id == pizzaId);

        loadSizes(selectedPizza);
        calcTotal();
    });

    // --------------------------------------------------
    // INITIAL LOAD
    // --------------------------------------------------
    selectedPizza = pizzasFull.find(p => p.id == pizzaSelect.value);

    loadSizes(selectedPizza);
    loadExtras();
    calcTotal();

    // --------------------------------------------------
    // ADD TO CART (Custom Pizza Builder)
    // --------------------------------------------------
    addBtn.addEventListener("click", () => {
        if (!selectedSize) return;

        const generatedId = "custom_" + Date.now();

        Cart.addToCart({
            product_id: generatedId,
            title: `${selectedPizza.name} (${selectedSize})`,
            size: selectedSize,
            qty: 1,
            price: calcTotal(),
            type: "custom",
            tag: "pizza",
            image: selectedPizza.image,
            bbqSauce: crustSelect.value,
            toppings: toppingsSelected.map(x => x.label),
        });

        Product.flyToCartAnimation(".product-image", "#cartCount");

        Utility.toast(
            `Custom ${selectedPizza.name} (${selectedSize}) added to Order`,
            "success"
        );
    });
}





}

new LandingPage();