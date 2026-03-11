
import Utility from "../Classes/Utility.js";
import XtraThursdayPromo from "../Classes/XtraThursdayPromo.js";
import { getItem } from "../Utils/CrudRequest.js";
import Cart from "../Classes/Cart.js";

class PromoDayPage{

    constructor(){
        this.title = Utility.el("promo-title");
        this.description = Utility.el("promo-description");
        this.today = new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
        this.iniializePromoDayDeals();
    }

    iniializePromoDayDeals(){
        //get the promo code from the url
        const urlParams = new URLSearchParams(window.location.search);
        const promoCode = urlParams.get('id');

        //Run the function to load promo day deals
        this.loadPromoDayDeals(promoCode);
    }

    async loadPromoDayDeals(promoCode){
        // Get promotion and check if the promo code matches and if it's active today
        let domEl = Utility.el("promoDayDealsRow")

        const promotion = await getItem(`promotions/${promoCode}`);    

        //turn "monday, tuesday, wed into an array and trim spaces
        if(promotion[0] && promotion[0].active_day){
            promotion[0].active_day = promotion[0].active_day.split(',').map(day => day.trim().toLowerCase());
        }
       

        if(!promotion || promotion.length === 0){          
            domEl.innerHTML = `<div class="col-12 h-100"><p class="text-center">No promotion found.</p></div>`;
            return;
        }
       
        if(promotion[0].status !== 'active'){          
            domEl.innerHTML = `<div class="col-12 h-100"><p class="text-center">This promotion is not active.</p></div>`;
            return;
        }

        if (!promotion[0].active_day.includes(this.today)) {
            domEl.innerHTML = `<div class="col-12 h-100"><p class="text-center">This promotion is not available today.</p></div>`;
            return;
        }

        switch(promoCode){
            case 'xtra_thursday_offer':
                this.title.textContent = promotion[0].title;
                this.description.textContent = promotion[0].description;
                 await
                this.loadXtraThursdayDeals();
            break;
            case 'half_pizza':
                this.title.textContent = promotion[0].title;
                this.description.textContent = promotion[0].description;
                 await
                this.loadHalfPizzaDeals();

            break;
           
            default:
                console.error("No promo day deals found for the given promo code");
            break;
        }
    }

    async loadXtraThursdayDeals(){
        //Fetch the deals from Promotions class
        const pizzaProducts = await getItem('pizzas-with-sizes');
        XtraThursdayPromo.TOPPINGS = await getItem("extras");
        XtraThursdayPromo.XTRATHURSDAYPRODUCTS = await XtraThursdayPromo.packageXLandM(pizzaProducts);     
        XtraThursdayPromo.xtraThursdayProducts(XtraThursdayPromo.XTRATHURSDAYPRODUCTS['xl']);
    }

    async loadHalfPizzaDeals(){
        const PIZZAS = await getItem("pizzas-with-sizes");
        const el = Utility.el("promoDayDealsRow");
        el.innerHTML = "";

        el.innerHTML = `
        <section id="halfAndHalfContainer">
            <div class="split-arena">
                <!-- LEFT -->
                <div class="half left">
                    <label class="form-label flex flex-start text-end">Select 1st Half</label>
                    <select class="pizza-list" id="listLeft"></select>   
                    <p id="previewNameLeft" class="previewName mt-1">First Half</p>            
                </div>

                <!-- DIVIDER -->
                <div class="divider">
                    <div class="div-line"></div>
                    <div class="div-badge"><span>VS</span></div>
                    <div class="div-line"></div>
                </div>

                <!-- RIGHT -->
                <div class="half right">
                    <label class="form-label flex flex-end text-end">Select 2nd Half</label>
                    <select class="pizza-list" id="listRight">
                    </select> 
                    <p id="previewNameRight" class="mt-1">Second Half</p>                      
                </div>
            </div>
            <section id="halfAndHalfGeneral">
                <div class="preview-pizza">
                    <div class="ph ph-l" id="previewL"><div class="ph-empty">?</div></div>
                    <div class="seam"></div>
                    <div class="ph ph-r" id="previewR"><div class="ph-empty">?</div></div>
                </div>
                <label class="section-title">Select Size</label>
                <div class="size-strips toggle-group slide-in" id="sizes"></div>
                <div class="toppings-section">
                    <h6 class="muted center-mobile">Toppings</h6>
                    <div id="toppings" class="toppings-toggle center-mobile"></div>
                </div>
                <div>
                    <p class="muted small">
                        <i class="fa fa-info-circle hideOnMobile"></i> Click to remove unwanted ingredients
                    </p>
                    <div id="IngredientsOptions"></div>
                </div>
            </section>

            <!-- PREVIEW -->
            <div class="preview-bar">             
                <div class="preview-text">
                    <h4 id="previewName">Choose one pizza on each side</h4>
                    <p  id="previewSub">Scroll each column &amp; tap to select</p>
                </div>
                <div class="preview-total">
                    <div class="big"  id="previewPrice">—</div>
                    <div class="note">Total</div>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-cancel">Cancel</button>
                <button class="btn-cart"   id="btnCart" disabled>🛒 Add to Cart</button>
            </div>
        </section> 
        
        `;

        let sel = { 
            left:null, 
            right:null, 
            size:'m',           
        }; 

      
        
        let ingredients = {
                left: [],
                right: []
        }

        let toppings = [];
        let totalPrice = 0;

        /* ── HELPERS ──────────────────────────────────────── */
        const getSize = (pizza, code) => pizza.sizes.find(s => s.size_code === code) || pizza.sizes[0];
        const fmt = n => parseFloat(n).toLocaleString('en-US', { minimumFractionDigits:0, maximumFractionDigits:0 });

       

       
        
        function renderList(side) {

            const el = document.getElementById(side === 'left' ? 'listLeft' : 'listRight');

            const selected = sel[side];

            const options = PIZZAS.map(p => {

                const size = getSize(p, sel.size)

                return `
                <option value="${p.id}" ${selected == p.id ? "selected" : ""}>
                    ${p.name} - ${Utility.fmtNGN(size?.price || 0)}
                </option>
                `
            }).join('');

            el.innerHTML = `
                <option value="" disabled ${!selected ? "selected" : ""}>-- Select a pizza --</option>
                ${options}
            `;
        }

        function bindPizzaSelects() {
            const previewNameRight = document.getElementById("previewNameRight");
            const previewNameLeft = document.getElementById("previewNameLeft");

            document.getElementById("listLeft")
            .addEventListener("change", e => {
                sel.left = Number(e.target.value)
                e.target.value = sel.left  
                const selectedOption = writeProductInfo('left', sel.left);
                previewNameRight.textContent = selectedOption ? "Pick a right half" : "Second Half";
                previewNameLeft.textContent = selectedOption ? selectedOption : "First Half";              

                refresh()
                updateTotalPrice()
            })

            document.getElementById("listRight")
            .addEventListener("change", e => {
                sel.right = Number(e.target.value)
                e.target.value = sel.right
                const selectedOption = writeProductInfo('right', sel.right);
                previewNameRight.textContent = selectedOption ? selectedOption : "Second Half";
                previewNameLeft.textContent = selectedOption ? "Pick a left half" : "First Half";
                refresh()
                updateTotalPrice()
            })
        }

        function updatePreviewNames() {

            const previewNameLeft = document.getElementById("previewNameLeft");
            const previewNameRight = document.getElementById("previewNameRight");

            if (sel.left) {
                previewNameLeft.textContent = writeProductInfo('left', sel.left);
            } else {
                previewNameLeft.textContent = "First Half";
            }

            if (sel.right) {
                previewNameRight.textContent = writeProductInfo('right', sel.right);
            } else {
                previewNameRight.textContent = "Second Half";
            }

            if (sel.left && !sel.right) {
                previewNameRight.textContent = "Pick a right half";
            }

            if (sel.right && !sel.left) {
                previewNameLeft.textContent = "Pick a left half";
            }
        }

        function writeProductInfo(side, productId) {
            const pizza = PIZZAS.find(p => p.id == productId);           
            return `${pizza.description}`;
        }

              /* ── RENDER SIZES ─────────────────────────────────── */
        function renderSizes() {
            const el = document.getElementById('sizes');
            const cur = sel.size;
            el.innerHTML = PIZZAS[0].sizes.map((s, i) => {           

                return `
                <label class="toggle-item mb-3" ${s.size_code !== cur ? "disabled" : ""}>
                    <input type="radio" 
                        name="size" 
                        value="${i}"
                        class="size-radio btn btn-sm btn-toggle" 
                        data-size="${s.size_code}"
                        ${cur === s.size_code ? 'checked' : ''}
                        >
                    <span>${s.size_label}</span>                       
                </label>

                `}).join('');
        }

        function bindSizeClicks() {
            document.getElementById('sizes').addEventListener('click', e => {
                const sz = e.target.closest('.size-radio');
                if (!sz) return;                 
                sel.size = sz.dataset.size; 
                refresh();
                updateTotalPrice();
               
            });

           
        }

         /* ── TOPPINGS ──────────────────────────────────── */
            async function renderToppings() {
                const el = Utility.el('toppings');
                const toppings = await getItem("extras");

                toppings.forEach(t => {
                const btn = document.createElement('button');
                btn.className = "tp topping-btn btn btn-sm m-1";
                btn.dataset.price = t.extras_price;
                btn.dataset.id = t.id;
                btn.dataset.topping = t.id;
                // btn.dataset.side = side;
                btn.textContent = `${t.extras} +${Utility.fmtNGN(t.extras_price)}`;

                btn.addEventListener("click", () => {
                    btn.classList.toggle("active");
                      updateTotalPrice();
                });
                
                el.appendChild(btn);
            });

                
            }

       

        /* ── INGREDIENTS ──────────────────────────────────── */

        async function fetchIngredients(side, productId) {
            
            const productIngredients = await getItem(`products/ingredients/${productId}`);

            if (productIngredients) {
                ingredients[side] = productIngredients;
                renderIngredients();
            }
         }

        async function renderIngredients() {

            const el = Utility.el('IngredientsOptions');

            const combined = [
                ...(ingredients.left || []),
                ...(ingredients.right || [])
            ];

            const map = new Map();

            combined.forEach(ing => {
                const key = ing.ingredient_name.toLowerCase().trim();
                if (!map.has(key)) {
                    map.set(key, ing);
                }
            });

            const uniqueIngr = [...map.values()];

            if (uniqueIngr.length === 0) {
                el.innerHTML = '<p class="muted small center-mobile">No ingredients assigned to this product.</p>';
                return;
            }

            el.innerHTML = uniqueIngr.map(ing => `
                <span id="IngredientsOptions-${ing.id}" 
                    class="ing collection-pill active"
                    data-ingredient="${ing.id}"
                    data-ingredient-name="${ing.ingredient_name}">
                    ${ing.ingredient_name}
                </span>
            `).join('');

            ingredientClickHandler(el);
        }

        function ingredientClickHandler(el) {
            const IngredientsContainer = el;
          

           IngredientsContainer.querySelectorAll('.collection-pill').forEach(item => {
                item.addEventListener('click', () => {
                    item.classList.toggle('active');
                    item.classList.contains('active') ? item.classList.remove('deselected') : item.classList.add('deselected');
                    updateTotalPrice();
                });
            });
        }
        /* ── REFRESH ALL ──────────────────────────────────── */
        function refresh() {
            renderList('left')
            renderList('right')
            renderSizes()
            updatePreviewNames();

            if(sel.left) fetchIngredients('left', sel.left);  
            if(sel.right)fetchIngredients('right', sel.right);

            renderPreview();

         
        }

        /* ── RENDER PREVIEW ───────────────────────────────── */
        function renderPreview() {
           
            const lp = PIZZAS.find(p => p.id == sel.left);
            const rp = PIZZAS.find(p => p.id == sel.right);

            const pL = document.getElementById('previewL');
            const pR = document.getElementById('previewR');             

            pL.innerHTML = lp
                ? `<img src="${lp.image}" alt="${lp.name}" onerror="this.style.display='none'"/>`
                : `<div class="ph-empty">?</div>`;
            pR.innerHTML = rp
                ? `<img src="${rp.image}" alt="${rp.name}" onerror="this.style.display='none'"/>`
                : `<div class="ph-empty">?</div>`;

                if (lp && rp) {
                    const lPrice = parseFloat(getSize(lp, sel.size).price)  / 2;
                    const rPrice = parseFloat(getSize(rp, sel.size).price) / 2;
                    const total  = lPrice + rPrice;
                    const lLabel = getSize(lp, sel.size).size_label;
                    const rLabel = getSize(rp, sel.size).size_label;
                    document.getElementById('previewName').innerHTML  = `${lp.name}  <span class="versus">VS</span>  ${rp.name}`;
                    document.getElementById('previewSub').textContent   = `${lLabel} Selected`;
                    document.getElementById('previewPrice').textContent = fmt(total);
                    document.getElementById('btnCart').disabled = false;
                } else {
                    document.getElementById('previewName').innerHTML  = lp ? `${lp.name}  ·  pick a right half` : 'Choose one pizza on each side';
                    document.getElementById('previewSub').textContent   = 'Scroll each column & tap to select';
                    document.getElementById('previewPrice').textContent = '—';
                    document.getElementById('btnCart').disabled = true;
                }
        }

        
        
        function updateTotalPrice() {
         
            const lp = PIZZAS.find(p => p.id == sel.left);
            const rp = PIZZAS.find(p => p.id == sel.right);
            
            if (!lp || !rp) return;
           

            let total = 0;

            const lPrice = parseFloat(getSize(lp, sel.size).price)  / 2;
            const rPrice = parseFloat(getSize(rp, sel.size).price) / 2;
            
            total  = lPrice + rPrice;

            const lLabel = getSize(lp, sel.size).size_label;
            const rLabel = getSize(rp, sel.size).size_label;
            const toppings = document.querySelectorAll('.topping-btn.active');
            
            const toppingsTotal = Array.from(toppings).reduce((sum, t) => sum + parseFloat(t.dataset.price || 0), 0);
            
            total  = lPrice + rPrice + toppingsTotal;
            totalPrice = total;
            
            document.getElementById('previewPrice').textContent = fmt(total);

            
            return {
                total,
                lPrice,
                rPrice,
                toppingsTotal,
                lp,
                rp,
                lLabel,
                rLabel

            }

        }

        function AddToCart() {
            const addBtn = document.getElementById('btnCart');

            addBtn.addEventListener('click', () => {             

                const calculatedTotal = updateTotalPrice();
                const selectedToppings = [
                ...document.querySelectorAll(".topping-btn.active"),
                ].map((t) => ({
                    id: t.dataset.id,
                    extras: t.textContent.split(" +₦")[0],
                    price: Number(t.dataset.price),
                }));

                const deselectedIngredients = 
                [
                    ...document.querySelectorAll(".collection-pill:not(.active)"),
                ].map(t => {
                    return {
                        ingredient_name: t.dataset.ingredientName,                   
                    };                  
                    
                })

                const productId = [calculatedTotal.lp.id, calculatedTotal.rp.id]               
                    
                    Cart.addToCart({
                        product_id: `${productId}`,
                        title: `${calculatedTotal.lp.name} . ${calculatedTotal.rp.name}`,
                        size: `${sel.size.toUpperCase()}`,
                        size_id: `${getSize(calculatedTotal.lp,sel.size).size_id}`,
                        barbecueSauce: "beneath",
                        price: calculatedTotal.total,
                        qty: 1,
                        image: calculatedTotal.lp.image, // you might want to create a custom image for half & half
                        toppings: selectedToppings,
                        type: "half_half",
                        removed_ingredients: deselectedIngredients,
                    });
            
                Utility.toast(`Added: ${calculatedTotal.lp.name} + ${calculatedTotal.rp.name} — ${fmt(calculatedTotal.total)}`);
            });
        }

        function runProgram() {      
            refresh(); 
            bindSizeClicks()  
            bindPizzaSelects()         
            renderList('left');  
            renderList('right'); 
            renderToppings()
            updateTotalPrice()
            AddToCart()
        } 



        runProgram();
    }

   
    
    
}

new PromoDayPage();