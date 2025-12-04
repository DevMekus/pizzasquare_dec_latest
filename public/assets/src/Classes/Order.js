import Utility from "./Utility.js";
import Pagination from "./Pagination.js";

export default class Order {
    static ORDERS = [];
    static status = ["pending", "preparing", "delivered", "cancelled", "enroute"];
    static renderDom = Utility.el("order-details");

    static switchOrderFunction(orders){
        const today = Utility.today;
        const checkToday = Utility.el("checkToday");
    
        function isToday(){
            if (!checkToday.checked){
            //display all table
            Order.renderOrders(orders);
            Order.todaysTransactionReport([])
            return;
            } else{
            const todaysOrders = orders.filter((order) => order.created_at.split(" ")[0] === today);   
            Order.renderOrders(todaysOrders);
            Order.todaysTransactionReport(todaysOrders)
            }
        }
        checkToday.addEventListener("change", () => isToday());
        isToday();
    }

    static renderOrders(data, page = 1) {
        const tbody = document.querySelector("#ordersTable tbody");
        const notDATA = Utility.el("no-data");
        tbody.innerHTML = "";
        notDATA.innerHTML = "";

        Order.updateSummary(data);

        if (!data || data.length == 0) {
            Utility.renderEmptyState(Utility.NODATA);
            return;
        }

        const start = (page - 1) * Utility.PAGESIZE;
        const end = start + Utility.PAGESIZE;
        const paginatedData = data.slice(start, end);

        paginatedData.forEach((o, idx) => {
        const tr = document.createElement("tr");
        tr.classList.add("bounce-card");
        const contactName = o.customer_name  ?? "N/A";

        tr.innerHTML = `
            <td class="color-danger fw-bold">${o.order_id}</td>        
            <td>${
                o.customer_type ? Utility.toTitleCase(o.customer_type) : "-"
            }</td>
            <td class="color-success fw-bold">
            ${o.total ? Utility.fmtNGN(Number(o.total)) : 0}</td>        
            <td>
            <span class="status ${o.status ? o.status : ""}">
            ${o.status ? Utility.toTitleCase(o.status) : ""}</span></td>
            <td>
            ${o.delivery ? Utility.toTitleCase(o.delivery) : "-"}</td>
            <td>${o.created_at ? o.created_at : "-"}</td>
            <td class="actions">
                <button class="btn btn-sm btn-primary" data-action="view" data-id="${o.id}" >Manage</button>          
                ${
                    Utility.role == "admin"
                    ? `
                    <button class="btn-ghost btn btn-sm" 
                    data-id="${o.id}" data-action="delete"
                    >Delete</button> 
                    `
                    : ``
                }    
            </td>
            `;
        tbody.appendChild(tr);
        });
        if (data.length > Utility.PAGESIZE)
        Pagination.render(data.length, page, data, Order.renderOrders);
    }

    static todaysTransactionReport(orders) {
        const transferOrderAmt = Utility.el("transferOrderAmt");
        const cashOrderAmt     = Utility.el("cashOrderAmt");
        const cardOrderAmt     = Utility.el("cardOrderAmt");
        const onlineOrderAmt   = Utility.el("onlineOrderAmt");
        const deliveryFeeAmt   = Utility.el("deliveryFeeAmt");
        const subtotal         = Utility.el("Subtotal");
        const totalAmtToday    = Utility.el("totalAmtToday");

        // Initialize totals
        const totals = {
            transfer: 0,
            cash: 0,
            card: 0,
            online: 0,
            delivery: 0,
            subtotal: 0
        };

        // Loop through all orders
        for (const order of orders) {
            totals.transfer += Number(order.transfer) || 0;
            totals.cash     += Number(order.cash)     || 0;
            totals.card     += Number(order.card)     || 0;
            totals.online   += Number(order.online)   || 0;
            totals.delivery += Number(order.delivery_fee) || 0;
            totals.subtotal += Number(order.item_amount) || 0;
        }

        // Update the DOM
        transferOrderAmt.textContent = Utility.fmtNGN(totals.transfer);
        cashOrderAmt.textContent     = Utility.fmtNGN(totals.cash);
        cardOrderAmt.textContent     = Utility.fmtNGN(totals.card);
        onlineOrderAmt.textContent   = Utility.fmtNGN(totals.online);
        deliveryFeeAmt.textContent   = Utility.fmtNGN(totals.delivery);

        // Total for today (including delivery fees) 
   
        subtotal.textContent = Utility.fmtNGN(totals.subtotal);
        totalAmtToday.textContent = Utility.fmtNGN(
            totals.transfer + totals.cash + totals.card + totals.online + totals.delivery
        );
    }

    static updateSummary(data) {
        const domEl = Utility.el("orderSummary");
        if (!domEl) return;

        // document.getElementById("orderCount").textContent = `(${data.length})`;
        document.getElementById("newOrders").textContent = data.sort(
        (a, b) => new Date(b.created_at.split(" ")[0]) - new Date(a.created_at.split(" ")[0])
        ).length;
        document.getElementById("preparingOrders").textContent = data.filter(
        (o) => o.status === "pending" || o.status === "preparing"
        ).length;
        document.getElementById("outOrders").textContent = data.filter(
        (o) => o.status === "enroute"
        ).length;
        document.getElementById("deliveredOrders").textContent = data.filter(
        (o) => o.status === "delivered"
        ).length;
        document.getElementById("cancelledOrders").textContent = data.filter(
        (o) => o.status === "cancelled"
        ).length;
    }

     static searchTodaysOrders(data, query) {
    const today = Utility.today;
    const todaysOrders = data.filter((order) => order.created_at === today); 
    const q = query.trim().toLowerCase();
    
    return todaysOrders.filter(
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



}