import Utility from "./Utility.js";
import Pagination from "./Pagination.js";
import { getItem } from "../Utils/CrudRequest.js";
import {CONFIG} from "../Utils/config.js";

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
            const allOrders = Utility.role === 'admin' ? orders: []
            Order.todaysTransactionReport(allOrders)
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
        const todaysOrders = data.filter((order) => order.created_at.split(" ")[0] === today); 
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

    static viewOrder(order) {
        let domBody = Utility.el("detailModalBody");
        const domFooter = Utility.el("detailModalButtons");
        let domTitle = Utility.el("detailModalLabel");

       

        domTitle.innerHTML = "";
        domBody.innerHTML = "";
        domFooter.innerHTML = "";

        domTitle.textContent = `ORDER ID: ${order.order_id}`;

        const items = order.items || [];

        // Build items list
        const itemsHtml = items
            .map((i) => {
                let html = `
                    <li class="mb-2">
                        <strong>${i.product_name}</strong> (x${i.qty}) - 
                        ${Utility.fmtNGN(i.unit_price)}<br/>
                        <small class="muted">${i.size_id ? `Size ID: ${i.size_id}` : ""}</small><br/>
                        <small class="muted">${i.barbecue_sauce ? `Barbecue Sauce: ${i.barbecue_sauce}` : ""}</small><br/>
                        <img src="${i.image}" 
                            alt="${i.product_name}" 
                            width="80" 
                            style="border-radius:8px; margin:4px 0;" />
                `;

                // Toppings (now an array)
                if (Array.isArray(i.toppings) && i.toppings.length > 0) {
                    const extras = i.toppings
                        .map((t) => `${t.topping} (${Utility.fmtNGN(t.unit_price)})`)
                        .join(", ");

                    html += `<br/><small><strong>Toppings:</strong> ${extras}</small>`;
                }

                html += `</li>`;
                return html;
            })
            .join("");

        // Populate Select Options
        const statusHtml = Order.status
            .map((s) => {
                const sel = s === order.status ? "selected" : "";
                return `<option value="${s}" ${sel}>${Utility.toTitleCase(s)}</option>`;
            })
            .join("");

        const contactName = order.customer_name || "N/A";
        const contactPhone = order.customer_phone || "N/A";
        const contactEmail = order.email_address || "N/A";

        domBody.innerHTML = `
            <div class="container">
                <div class="row">

                    <!-- LEFT SIDE -->
                    <div class="col-sm-7">

                        <!-- Basic Order Info -->
                        <ul class="list-unstyled mb-2 small">
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Order ID:</strong> <span>${order.order_id}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Customer:</strong> <span>${contactName}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Phone:</strong> <span>${contactPhone}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Email:</strong> <span>${contactEmail}</span>
                            </li>
                        </ul>

                        <!-- Items -->
                        <div class="bg-light p-2 rounded">
                            <strong>Items:</strong>
                            <ul class="list-unstyled mt-2 ms-2">
                                ${itemsHtml}
                            </ul>
                        </div>

                        <!-- Order Summary -->
                        <ul class="list-unstyled mt-3 small order-summary">
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Total:</strong> <span>${Utility.fmtNGN(order.total_paid)}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Delivery Fee:</strong> <span>${Utility.fmtNGN(order.delivery_fee)}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Payment Type:</strong> <span>${Utility.toTitleCase(order.payment_type)}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Status:</strong> 
                                <span class="fw-bold text-${
                                    order.status === "delivered"
                                        ? "success"
                                        : order.status === "pending"
                                        ? "warning"
                                        : "danger"
                                }">${Utility.toTitleCase(order.status)}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <strong>Note:</strong> 
                                <span>${order.order_note || "N/A"}</span>
                            </li>
                            <li class="d-flex justify-content-between py-1">
                                <strong>Ordered At:</strong> 
                                <span>${order.created_at}</span>
                            </li>
                        </ul>

                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="col-sm-5">
                        <div class="p-3 rounded shadow-sm bg-white">

                            <h5>Update Order Status</h5>
                            <p class="muted mb-1">Modify the order status below:</p>

                            <select id="statusTool" 
                                    data-id="${order.order_id}" 
                                    class="form-select form-select-sm mb-3">
                                ${statusHtml}
                            </select>

                            <button data-action="printOrder"
                                    data-id="${order.id}"
                                    class="btn btn-ghost btn-sms mb-2 w-100">
                                <i class="bi bi-printer"></i> Print Order
                            </button>

                            ${
                                Utility.role === "admin"
                                    ? `
                            <div class="border-top pt-3">
                                <p class="fw-bold mb-1 text-danger">Delete Order</p>
                                <p class="muted mb-2">
                                    This will remove the order and all related data. <strong>This action cannot be undone.</strong>
                                </p>

                                <button id="deleteBtn"
                                        data-id="${order.order_id}"
                                        data-action="delete"
                                        class="btn btn-ghost w-100">
                                    <i class="bi bi-trash"></i> Delete Order
                                </button>
                            </div>`
                                    : ""
                            }

                        </div>
                    </div>

                </div>
            </div>
        `;

        $("#displayDetails").modal("show");
    }

    static transactionSummaryFromApi(order) {
        // Extract values
        const items = order.items;
        const subtotal = Number(order.item_amount);       // items total
        const deliveryFee = Number(order.delivery_fee);   // delivery
        const tax = 0;
        const discount = 0;
        const grandTotal = Number(order.total);           // already includes delivery
        const paymentType = order.payment_type;

        // Build items HTML
        const itemsHtml = items
            .map((it) => {
            let toppingsHtml = "";

            // Normalize toppings from API
            if (Array.isArray(it.toppings) && it.toppings.length > 0) {
                const extrasList = it.toppings
                .map((t) => t.topping?.trim() || "")
                .filter(Boolean)
                .join(", ");

                if (extrasList) {
                toppingsHtml = `
                    <br/>
                    <small><strong>Toppings:</strong> ${extrasList}</small>
                `;
                }
            }

            return `
                <tr>
                <td>
                    ${Utility.toTitleCase(it.product_name)}
                    ${toppingsHtml}
                </td>
                <td class="text-end">${it.qty}</td>
                <td class="text-end">${Utility.fmtNGN(Number(it.unit_price))}</td>
                <td class="text-end">${Utility.fmtNGN(Number(it.subtotal))}</td>
                </tr>
            `;
            })
            .join("");

        const receipt = `
            <div class="text-center mb-3">
            <h5 class="mb-0">Pizza Square Nigeria</h5>
            <div class="small text-muted">Online Order Re-print Receipt</div>
            </div>

            <div class="d-flex justify-content-between small">
            <div>Order: <strong>${order.order_id}</strong></div>
            <div>${order.created_at}</div>
            </div>

            <div class="small">Customer: ${order.customer_name}</div>
            <div class="small">Phone: ${order.customer_phone}</div>
            <div class="small">Attendant: ${order.attendant ?? ""}</div>
            <hr/>

            <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
                </thead>
                <tbody>${itemsHtml}</tbody>
            </table>
            </div>

            <div class="receipt-totals">

            <div>
                <span>Subtotal:</span>
                <span><strong>${Utility.fmtNGN(subtotal)}</strong></span>
            </div>

            <div>
                <span>Delivery Fee:</span>
                <span><strong>${Utility.fmtNGN(deliveryFee)}</strong></span>
            </div>

            <div>
                <span>Tax:</span>
                <span><strong>${Utility.fmtNGN(tax)}</strong></span>
            </div>

            <div>
                <span>Discount:</span>
                <span><strong>${Utility.fmtNGN(discount)}</strong></span>
            </div>

            <div class="grand">
                <span>Grand Total:</span>
                <span><strong>${Utility.fmtNGN(grandTotal)}</strong></span>
            </div>

            <div class="small">
                <span>Payment:</span>
                <span>${Utility.toTitleCase(paymentType)}</span>
            </div>

            <div class="small">
                <span>Status:</span>
                <span>${Utility.toTitleCase(order.status)}</span>
            </div>

            </div>
        `;

        document.getElementById("receiptBody").innerHTML = receipt;

        const modal = new bootstrap.Modal(document.getElementById("receiptModal"));
        $("#displayDetails").modal("hide");
        modal.show();
    }

    static orderNotification() {
        
        async function pingNotification() {
            const orders = await getItem("orders") || [];
            if (orders.length == 0) return;
            //pending orders
            const pending = orders.filter(
                (o) => o.status === "pending" || o.status === "preparing"
            );

            Utility.el("orderAlert").textContent = pending.length;
            let route = ''
            if(Utility.role === 'admin'){
                route = `${CONFIG.BASE_URL}/secure/admin/orders`
            } else if(Utility.role === 'cashier'){
                 route = `${CONFIG.BASE_URL}/secure/pos/orders`
            } else {
                 route = `${CONFIG.BASE_URL}/secure/management/orders`
            }

            if (pending.length > 0) {
                Swal.fire({
                    title: "Pending Order",
                    text: "You have a pending order.",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: "Go to Orders",
                    cancelButtonText: "Close",
                    confirmButtonColor: "#d51d28",
                    }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = route;
                    }
                });


                Utility.showNotification(
                "Pending Order",
                `You have ${pending.length} pending order.`
                );
            }
        }

        setInterval(() => {
        pingNotification();
        }, 120000); //runs every 2 minutes

        pingNotification();
    }

    static orderNotFound() {
        Order.renderDom.innerHTML = ``;
        Order.renderDom.innerHTML = `
        <div class="order-not-found" data-aos="fade-up">
            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" 
            alt="Not Found" class="not-found-icon" />
            <h2>Oops! We Could not Find This Order</h2>
            <p>
                Double-check your order number and try again. <br/>
                Maybe it ran off to grab some extra cheese 🍕.
            </p>          
        </div>

        `;
    }

    static getTodayRevenue(data) {
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0]; // "YYYY-MM-DD"

        const todaysOrders = data.filter(order => {
        // Extract only the date part from created_at
        const orderDate = order.created_at.split(' ')[0];
        return orderDate === todayStr;
        });

        const totalRevenue = todaysOrders.reduce((sum, order) => {
            return sum + Number(order.total || 0);
        }, 0);

        return totalRevenue;
    }


    static renderRecentOrders(filter = "all", q = "") {
        const tbody = document.getElementById("ordersTbody");
        const moreBtn = document.getElementById("moreOrdersBtn");

        // Step 1: filter by status
        let rows = Order.ORDERS.filter(o =>
            filter === "all" ? true : o.status.toLowerCase() === filter.toLowerCase()
        );

        // Step 2: search filter
        if (q) {
            const query = q.toLowerCase();
            rows = rows.filter(o =>
                o.order_id.toLowerCase().includes(query) ||
                o.customer_name.toLowerCase().includes(query) ||
                o.customer_email.toLowerCase().includes(query)
            );
        }

        // Step 3: sort by date (newest first)
        rows = rows.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        // Step 4: take latest 5
        const recentRows = rows.slice(0, 5);

        // Step 5: render rows
        tbody.innerHTML = recentRows
        .map((o, i) => {
            
            // Extract items properly
            const itemsList = o.items
                .map(item => {
                    const toppingStr = item.toppings?.length
                    ? ` (+${item.toppings.map(t => t.topping).join(", ")})`
                    : "";
                    return `${item.qty}x ${item.product_name}${toppingStr}`;
                })
                .join(", ");

            return `
            <tr>
                <td>${i + 1}</td>
                <td>${o.customer_name}</td>
                <td>${Utility.fmtNGN(o.total_paid)}</td>
                
                <td>
                    <span class="status ${o.status.toLowerCase()}">${Utility.toTitleCase(o.status)}</span>
                </td>
            </tr>`;
        })
        .join("");

        // Step 6: toggle more button
        moreBtn.style.display = rows.length > 5 ? "block" : "none";
    }

    static calculateTopDishes(limit = 3) {
        const dishCount = {};

        Order.ORDERS.forEach(o => {
            (o.items || []).forEach(item => {
                const name = item.product_name || `#${item.product_id}`;
                if (!dishCount[name]) dishCount[name] = 0;

                // Count the quantity of the main product
                dishCount[name] += Number(item.qty || 0);

                // Optionally, include toppings as separate counts
                if (Array.isArray(item.toppings) && item.toppings.length) {
                    item.toppings.forEach(t => {
                        const toppingName = `${t.topping} (topping)`;
                        if (!dishCount[toppingName]) dishCount[toppingName] = 0;
                        dishCount[toppingName] += Number(t.qty || 0);
                    });
                }
            });
        });

        return Object.entries(dishCount)
            .map(([name, count]) => ({ name, count }))
            .sort((a, b) => b.count - a.count) // highest first
            .slice(0, limit);
    }

    static getNewCustomers(data) {
        const customers = {};

        data.forEach(order => {
        // Use a unique key for the customer
        const key = order.customer_phone || order.userid || order.customer_email || 'guest';

        if (!customers[key]) {
            customers[key] = 0;
        }
        customers[key]++;
        });

        // Count customers who only appear once (new customers)
            const newCustomers = Object.values(customers).filter(visits => visits === 1).length;

        return newCustomers;
    }


    static userOrderSummary(order) {
        // Items are already an array in your model
        let items = order.items || [];

    // Compute total from items (or fallback to order.total)
        let total = 0;
        const itemsHtml = items
            .map((item) => {
            const price = Number(item.unit_price);
            const qty = Number(item.qty);
            const itemTotal = price * qty;
            total += itemTotal;

            return `
                <div class="summary-item bounce-card">
                <div class="item-details">
                    <h4>${item.product_name}</h4>
                    <small>Quantity: ${qty}</small>
                </div>
                <span class="price">${Utility.fmtNGN(itemTotal)}</span>
                </div>
            `;
            })
            .join("");

    // If backend provides total, use it as final override
            const grandTotal = order.total ? Number(order.total) : total;

        Order.renderDom.innerHTML = `
            <section class="container">
            <section class="order-summary" data-aos="fade-up">
                <h2>🧾 Your Order Summary</h2>

                <!-- Order tracker -->
                <div class="tracker">
                <div class="tracker-progress" id="progress"></div>

                <div class="step bounce-card" id="step1">
                    <div class="step-icon"><i class="fas fa-pizza-slice"></i></div>
                    <p>Baking</p>
                </div>

                <div class="step bounce-card" id="step2">
                    <div class="step-icon"><i class="fas fa-motorcycle"></i></div>
                    <p>On the Way</p>
                </div>

                <div class="step bounce-card" id="step3">
                    <div class="step-icon"><i class="fas fa-check"></i></div>
                    <p>Delivered</p>
                </div>
                </div>

                <!-- Status -->
                <p><strong>Status:</strong> 
                <span class="badge-status" id="statusText">${order.status}</span>
                </p>

                <!-- Customer Info -->
                <p><strong>Customer:</strong> ${order.customer_name}</p>
                <p><strong>Phone:</strong> ${order.customer_phone}</p>

                <!-- Items -->
                <div class="summary-items">
                ${itemsHtml}
                </div>

                <!-- Totals -->
                <div class="summary-total">
                <strong>Total:</strong>
                <span>${Utility.fmtNGN(grandTotal)}</span>
                </div>             

            </section>
            </section>
        `;

        Order.updateOrderTracker(order);
    }


    static updateOrderTracker(order) {
    const steps = [
      { status: "Pending", progress: 0, activeSteps: 1 },
      { status: "Preparing", progress: 50, activeSteps: 2 },
      { status: "Delivered", progress: 100, activeSteps: 3 },
    ];

    function updateTracker(stepIndex) {
      document.getElementById("progress").style.width =
        steps[stepIndex].progress + "%";
      document.getElementById("statusText").innerText = steps[stepIndex].status;

      for (let i = 1; i <= 3; i++) {
        document.getElementById("step" + i).classList.remove("active");
      }
      for (let i = 1; i <= steps[stepIndex].activeSteps; i++) {
        document.getElementById("step" + i).classList.add("active");
      }
    }

    // map backend status to step index
    let currentStep = 0;
    switch (order.status.toLowerCase()) {
      case "pending":
        currentStep = 0;
        break;
      case "preparing":
        currentStep = 1;
        break;
      case "delivered":
        currentStep = 2;
        break;
      default:
        currentStep = 0; // fallback
    }

    updateTracker(currentStep);
  }





    






}