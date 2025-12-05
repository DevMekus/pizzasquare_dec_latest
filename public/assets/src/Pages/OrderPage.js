import Order from "../Classes/Order.js";
import Utility from "../Classes/Utility.js";
import { postItem, getItem, deleteItem, patchItem } from "../Utils/CrudRequest.js";

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

    filterByStatus() {
        const statusFilter = Utility.el("statusFilter");
        const checkToday = Utility.el("checkToday");
        if (!statusFilter) return;

        function applyFilters() {
        const val = statusFilter.value;
        const todayChecked = checkToday && checkToday.checked;
        let filtered = [];

        if (todayChecked) {
            filtered = Order.ORDERS.filter((order) => order.created_at.split(" ")[0] == Utility.today); 
            if (val === "all") {
                Order.renderOrders(filtered);
                return;
            } else {
                filtered = filtered.filter((order) => order.status === val);
                Order.renderOrders(filtered);              
            }
        } else {
            if (val === "all") {
                Order.renderOrders(Order.ORDERS);
                return;
            } else {
                filtered = Order.ORDERS.filter((order) => order.status === val);
                Order.renderOrders(filtered);
            }
        }
        }

        applyFilters();
        statusFilter.addEventListener("change", applyFilters);

        if (checkToday) {
        checkToday.addEventListener("change", applyFilters);
        }
    }

    exportCsv() {
        const btn = Utility.el("exportCsv");
        if (!btn) return;

        btn.addEventListener("click", () => {
            Utility.exportToCSV(Order.ORDERS, "orders.csv");
        });
    }

    orderEventDelegations(){
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest("button");
            if (!btn) return;

            const action = btn.getAttribute("data-action");
            const id = btn.getAttribute("data-id");

            const order = Order.ORDERS.find((x) => x.id == id);
            if (action === "view") {
                Order.viewOrder(order);
            } else if (action === "delete") {
                $("#displayDetails").modal("hide")
                const del = await deleteItem(`admin/orders/${id}`,"Delete this order?")
                if (del) {
                    Utility.toast("Order deleted successfully","success");         
                    setTimeout(() => {
                        Utility.reloadPage();
                    }, 1000);
                } else {
                    Utility.toast("Failed to delete order");
                }
            } else if (action === "printOrder"){
                Order.transactionSummaryFromApi(order);
                Utility.printReceipt();
            }            
        })

        document.addEventListener("change", async(e) => {
        if (e.target && e.target.id === "statusTool") {
            const status = document.getElementById("statusTool").value;
            const id = document.getElementById("statusTool").dataset.id;
            $("#displayDetails").modal("hide")
            const patch = await patchItem(`orders/${id}`, { status }, "Update order status to " + status + "?");
            if (patch){
                Utility.toast("Order status updated successfully","success");
                setTimeout(() => {
                    Utility.reloadPage();
                }, 1000);
               
            } else {
                Utility.toast("Failed to update order status");
            }
            
        }
        });
    }

}

new OrderPage();