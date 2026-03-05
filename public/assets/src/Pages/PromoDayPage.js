
import Utility from "../Classes/Utility.js";
import XtraThursdayPromo from "../Classes/XtraThursdayPromo.js";
import { getItem } from "../Utils/CrudRequest.js";

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
                    <select class="pizza-list" id="listLeft"></select>   
                    <h4 id="previewNameLeft" class="previewName">First Half</h4>            
                </div>

                <!-- DIVIDER -->
                <div class="divider">
                    <div class="div-line"></div>
                    <div class="div-badge"><span>½ ½</span></div>
                    <div class="div-line"></div>
                </div>

                <!-- RIGHT -->
                <div class="half right">
                    <select class="pizza-list" id="listRight">
                    </select> 
                    <h4 id="previewNameRight" class="previewName flex flex-end text-end">Second Half</h4>                      
                </div>
            </div>
            <section id="halfAndHalfGeneral">
                <div class="preview-pizza">
                    <div class="ph ph-l" id="previewL"><div class="ph-empty">?</div></div>
                    <div class="seam"></div>
                    <div class="ph ph-r" id="previewR"><div class="ph-empty">?</div></div>
                </div>
                <div class="size-strips toggle-group slide-in" id="sizes"></div>
                <div class="size-strip" id="toppings"></div>
                <div class="size-strip" id="ingredients"></div>
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
                <button class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button class="btn-cart"   id="btnCart" disabled>🛒 Add to Cart</button>
            </div>
        </section> 
        
        `;

        let sel = { 
            left:null, 
            right:null, 
            size:'m',           
        }; 

        let totalPrice = 0;
        
        let ingredients = {
                left: [],
                right: []
        }

        let toppings = [];

        /* ── HELPERS ──────────────────────────────────────── */
        const getSize = (pizza, code) => pizza.sizes.find(s => s.size_code === code) || pizza.sizes[0];
        const fmt = n => parseFloat(n).toLocaleString('en-US', { minimumFractionDigits:0, maximumFractionDigits:0 });

       

        function renderList(side) {
            const el = document.getElementById(side =='left' ? 'listLeft' : 'listRight');
            
            const options = PIZZAS.map(p => {               
                const isSel  = sel[side] === p.id;
                const size   = getSize(p, sel.size); 

                return `
                <option value="${p.id}" ${isSel ? 'selected' : ''} data-product="${p.id}" data-size="${size?.size_code}" data-side="${side}">${p.name} - ${Utility.fmtNGN(size?.price || 0)} </option>`;
            }).join('');

            el.innerHTML = ``;
            el.innerHTML = `
            <option value="" disabled ${!sel[side] ? 'selected' : ''}>Select a pizza</option>
            ${options}
            `;

            document.addEventListener('change', e => {
                if (e.target.classList.contains('pizza-list')) {                              
                    sel[side] = Number(e.target.value);
                    refresh();
                }
            });
                     
        }

              /* ── RENDER SIZES ─────────────────────────────────── */
        function renderSizes() {
            const el = document.getElementById('sizes');
            const cur = sel.size;
            el.innerHTML = PIZZAS[0].sizes.map((s, i) => {           

                return `
                <label class="toggle-item mb-3 ${!s.size_code == cur ? "disabled" : ""}">
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

            const el = Utility.el('ingredients');

            //combine left and right ingredients into one array with a side property
            const productIngredients = [...ingredients.left.map(i => ({...i, side:'left'})), ...ingredients.right.map(i => ({...i, side:'right'}))];

            let uniqueIngr = [];

            ingredients.left.forEach(ing => {
                if (!uniqueIngr.some(i => i.id === ing.id)) {
                    uniqueIngr.push({...ing});
                }

            });

            ingredients.right.forEach(ing => {
                if (!uniqueIngr.some(i => i.id === ing.id)) {
                    uniqueIngr.push({...ing});
                }
            });

       

            el.innerHTML = '';

            if (uniqueIngr.length === 0) {
                el.innerHTML = '<p class="muted small center-mobile">No ingredients assigned to this product.</p>';
                return
            }

            el.innerHTML = uniqueIngr.map(ing => `
                <div id="IngredientsOptions-${ing.id}" 
                class="ing collection-pill active" 
                data-ingredient="${ing.id}"
                data-ingredient-name="${ing.ingredient_name}" 
                data-side="${ing.side}">
                ${ing.ingredient_name}
            </div>`).join('');

            ingredientClickHandler(el)
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
            renderList('left');  
            renderList('right'); 
            renderSizes()

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
                    const lPrice = parseFloat(getSize(lp, sel.leftSize).price)  / 2;
                    const rPrice = parseFloat(getSize(rp, sel.rightSize).price) / 2;
                    const total  = lPrice + rPrice;
                    const lLabel = getSize(lp, sel.leftSize).size_label;
                    const rLabel = getSize(rp, sel.rightSize).size_label;
                    document.getElementById('previewName').innerHTML  = `${lp.name}  <span class="versus">VS</span>  ${rp.name}`;
                    document.getElementById('previewSub').textContent   = `${lLabel} left  ·  ${rLabel} right`;
                    document.getElementById('previewPrice').textContent = fmt(total);
                    document.getElementById('btnCart').disabled = false;
                } else {
                    document.getElementById('previewName').innerHTML  = lp ? `${lp.name}  ·  pick a right half` : 'Choose one pizza on each side';
                    document.getElementById('previewSub').textContent   = 'Scroll each column & tap to select';
                    document.getElementById('previewPrice').textContent = '—';
                    document.getElementById('btnCart').disabled = true;
                }
        }


        function runProgram() {      
            refresh(); 
            bindSizeClicks()           
            
        } 



        runProgram();
    }

   
    
    
}

new PromoDayPage();