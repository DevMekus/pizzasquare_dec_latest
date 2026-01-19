import Promotions from "../Classes/Promotions";
import Utility from "../Classes/Utility";
import XtraThursdayPromo from "../Classes/XtraThursdayPromo";
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
        if(!promotion){          
            domEl.innerHTML = `<div class="col-12 h-100"><p class="text-center">No promotion found.</p></div>`;
            return;
        }
        if(!promotion){  
            domEl.innerHTML = `<div class="col-12 h-100"><p class="text-center">Invalid promo code.</p></div>`;
            return;
        }
        if(promotion[0].status !== 'active'){          
            domEl.innerHTML = `<div class="col-12 h-100"><p class="text-center">This promotion is not active.</p></div>`;
            return;
        }

        if (promotion[0].active_day.toLowerCase().indexOf(this.today) === -1){
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
}

new PromoDayPage();