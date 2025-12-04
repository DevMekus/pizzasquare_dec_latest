import Order from "../Classes/Order.js";
import Utility from "../Classes/Utility.js";
import { postItem, getItem } from "../Utils/CrudRequest.js";

class OrderPage {
     constructor() {
        this.initialize();
    }

    async initialize() {
        Order.ORDERS  = await getItem("orders") || [];   
        Utility.runClassMethods(this, ["initialize"]);
    } 

    renderPizzaSquareOrders() {
        const tbody = document.querySelector("#ordersTable tbody");
        if (!tbody) return;
        
        Order.switchOrderFunction(Order.ORDERS)

       
    }

    searchOrder() {
        const domEl = Utility.el("search");
        const checkToday = Utility.el("checkToday");
        if (!domEl) return;

    //placeholder change on checkbox toggle
        function updatePlaceholder() {
            if (checkToday && checkToday.checked) {
                domEl.placeholder = "Search Today's Orders...";
            } else {
                domEl.placeholder = "Search All Orders...";
            }
        }

        if (checkToday) {
            checkToday.addEventListener("change", updatePlaceholder);
            //initial call to set placeholder based on checkbox state
            updatePlaceholder();
        }

    domEl.addEventListener("input", (e) => {
        const q = e.target.value.trim().toLowerCase();

        let filtered = [];

        if (checkToday && checkToday.checked) {
        //render only today's orders
            filtered = Order.searchTodaysOrders(Order.ORDERS, q);
       
        } else {
            //render all orders
            filtered = Order.ORDERS.filter(
            (o) =>
                (o.fullname && o.fullname.toLowerCase().includes(q)) ||
                (o.customer && o.customer.toLowerCase().includes(q)) ||
                (o.city && o.city.toLowerCase().includes(q)) ||
                (o.location && o.location.toLowerCase().includes(q)) ||
                (o.method && o.method.toLowerCase().includes(q)) ||
                (o.ip_address && o.ip_address.includes(q)) ||
                (o.order_id && o.order_id.toLowerCase().includes(q))
            );
        }
        Order.renderOrders(filtered);
    });    
}
}

new OrderPage();