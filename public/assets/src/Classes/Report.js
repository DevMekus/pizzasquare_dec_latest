/* Report.js
   Exposes a global Report class. Usage: new Report()
   Requires Chart.js to be loaded before this script.
*/

class Report {
  constructor(options = {}) {
    // selectors (IDs from your HTML)
    this.ids = {
      paymentsCanvas: 'paymentsDoughnut',
      productsCanvas: 'productsBar',
      salesCanvas: 'salesLine',
      orderOverviewMetrics: 'orderOverviewMetrics',
      orderOverviewTable: 'orderOverviewTable',
      paymentsTable: 'paymentsTable',
      topProductsTable: 'topProductsTable',
      platformTable: 'platformTable',
      customerTable: 'customerTable',
      filterButtons: '.filter-buttons .btn',
      applyDateBtn: 'applyDate',
      startDate: 'startDate',
      endDate: 'endDate'
    };

    // charts
    this.charts = { payments: null, products: null, sales: null };

    // default range
    this.currentRange = 'today';

    // Sample ORDERS array (you asked it to be embedded here)
    this.ORDERS = await (async () => {
      try {
        const resp = await fetch('admin/orders');
        if (!resp.ok) throw new Error('Failed to fetch orders');
        return await resp.json();
      } catch (e) {
        console.error('Error fetching orders:', e);
        return [];
      }
    })();

    // small helpers
    this.Utility = {
      fmtNGN: (v) => {
        const n = Number(v) || 0;
        return n.toLocaleString('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 2 });
      },
      toTitleCase: (s) => (s || '').toString().replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1)),
      el: (id) => document.getElementById(id)
    };

    // Initialize (attach listeners + initial render)
    document.addEventListener('DOMContentLoaded', () => this.init());
  }

  /* -------------------------
     Initialization & wiring
  -------------------------*/
  init() {
    this.wireFilterButtons();
    this.wireApplyDate();
    // initial render using default 'today' range
    this.applyRange('today');
  }

  wireFilterButtons() {
    const buttons = document.querySelectorAll(this.ids.filterButtons);
    buttons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        const range = btn.getAttribute('data-range');
        // clear custom date inputs when using a preset range
        const sd = this.Utility.el(this.ids.startDate); const ed = this.Utility.el(this.ids.endDate);
        if (sd) sd.value = '';
        if (ed) ed.value = '';
        this.applyRange(range);
      });
    });
  }

  wireApplyDate() {
    const btn = this.Utility.el(this.ids.applyDateBtn);
    if (!btn) return;
    btn.addEventListener('click', () => {
      const s = this.Utility.el(this.ids.startDate).value;
      const e = this.Utility.el(this.ids.endDate).value;
      if (!s || !e) {
        alert('Please select both start and end dates');
        return;
      }
      this.applyRange('custom', s, e);
    });
  }

  /* -------------------------
     Date utilities & filters
  -------------------------*/
  startOfDay(date) { return new Date(date.getFullYear(), date.getMonth(), date.getDate()); }
  endOfDay(date) { return new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59, 999); }

  parseOrderDate(o) {
    // expect created_at like "YYYY-MM-DD HH:MM:SS"
    // replace space with 'T' to parse as local
    return new Date(o.created_at.replace(' ', 'T'));
  }

  filterOrdersByRange(orders, range, startDate = null, endDate = null) {
    const now = new Date();
    const todayStart = this.startOfDay(now);

    return orders.filter(order => {
      const od = this.parseOrderDate(order);
      switch (range) {
        case 'today': return od >= todayStart && od <= this.endOfDay(now);
        case 'yesterday': {
          const y = this.startOfDay(new Date()); y.setDate(y.getDate() - 1);
          return od >= y && od <= this.endOfDay(y);
        }
        case 'week': {
          const cur = this.startOfDay(new Date());
          const day = cur.getDay(); // 0 Sun ... 6 Sat
          const diffToMon = (day + 6) % 7; // days since Monday
          const mon = this.startOfDay(new Date()); mon.setDate(cur.getDate() - diffToMon);
          return od >= mon && od <= this.endOfDay(now);
        }
        case 'month':
          return od.getMonth() === now.getMonth() && od.getFullYear() === now.getFullYear();
        case 'year':
          return od.getFullYear() === now.getFullYear();
        case 'custom':
          if (!startDate || !endDate) return true;
          const s = this.startOfDay(new Date(startDate));
          const e = this.endOfDay(new Date(endDate));
          return od >= s && od <= e;
        case 'all':
        default:
          return true;
      }
    });
  }

  /* -------------------------
     Reports generator
  -------------------------*/
  generateReports(filteredOrders) {
    // Order overview
    const orderCount = filteredOrders.length;
    const totalRevenue = filteredOrders.reduce((s, o) => s + Number(o.total || 0), 0);
    const totalDeliveryFees = filteredOrders.reduce((s, o) => s + Number(o.delivery_fee || 0), 0);

    // Payment overview
    const paymentSummary = {
      transfer: filteredOrders.reduce((s, o) => s + Number(o.transfer || 0), 0),
      cash:     filteredOrders.reduce((s, o) => s + Number(o.cash || 0), 0),
      card:     filteredOrders.reduce((s, o) => s + Number(o.card || 0), 0),
      online:   filteredOrders.reduce((s, o) => s + Number(o.online || 0), 0)
    };

    // Platform overview
    const platformOverview = {};
    filteredOrders.forEach(o => {
      const k = o.customer_type || 'unknown';
      if (!platformOverview[k]) platformOverview[k] = { count: 0, amount: 0 };
      platformOverview[k].count++;
      platformOverview[k].amount += Number(o.total || 0);
    });

    // Product performance
    const productStats = {};
    filteredOrders.forEach(order => {
      (order.items || []).forEach(item => {
        const key = item.product_name || item.title || `#${item.product_id}`;
        if (!productStats[key]) productStats[key] = { qty: 0, amount: 0, name: key };
        productStats[key].qty += Number(item.qty || 0);
        productStats[key].amount += Number(item.subtotal || 0);
        if (Array.isArray(item.toppings) && item.toppings.length) {
          item.toppings.forEach(t => {
            productStats[key].amount += Number(t.subtotal || 0);
          });
        }
      });
    });

    // Customer insights
    const customers = {};
    filteredOrders.forEach(o => {
      const key = o.customer_phone || o.userid || o.customer_email || 'guest';
      if (!customers[key]) customers[key] = { name: o.customer_name || '', visits: 0, total: 0, firstSeen: this.parseOrderDate(o), lastSeen: this.parseOrderDate(o) };
      customers[key].visits++;
      customers[key].total += Number(o.total || 0);
      const dt = this.parseOrderDate(o);
      if (dt < customers[key].firstSeen) customers[key].firstSeen = dt;
      if (dt > customers[key].lastSeen) customers[key].lastSeen = dt;
    });

    const custVals = Object.values(customers);
    const newCustomers = custVals.filter(c => c.visits === 1).length;
    const returningCustomers = custVals.filter(c => c.visits > 1).length;

    // Sales over time (group by day)
    const salesByDay = {};
    filteredOrders.forEach(o => {
      const d = this.parseOrderDate(o);
      const key = d.toISOString().slice(0, 10); // YYYY-MM-DD
      if (!salesByDay[key]) salesByDay[key] = 0;
      salesByDay[key] += Number(o.total || 0);
    });

    return {
      orderOverview: { orderCount, totalRevenue, totalDeliveryFees },
      paymentOverview: paymentSummary,
      platformOverview,
      productPerformance: Object.values(productStats).sort((a, b) => b.qty - a.qty),
      customerInsights: { newCustomers, returningCustomers, totalCustomers: custVals.length, customers },
      salesByDay
    };
  }

  /* -------------------------
     Rendering helpers
  -------------------------*/
  clearChart(chartRef) {
    if (chartRef && typeof chartRef.destroy === 'function') {
      try { chartRef.destroy(); } catch (e) { /* ignore */ }
    }
  }

  renderReports(reports) {
    // Order Overview metrics
    const metricsEl = this.Utility.el(this.ids.orderOverviewMetrics);
    if (metricsEl) {
      metricsEl.innerHTML = `
        <div class="metric"><div class="mini">Orders</div><strong>${reports.orderOverview.orderCount}</strong></div>
        <div class="metric"><div class="mini">Revenue</div><strong>${this.Utility.fmtNGN(reports.orderOverview.totalRevenue)}</strong></div>
        <div class="metric"><div class="mini">Delivery Fees</div><strong>${this.Utility.fmtNGN(reports.orderOverview.totalDeliveryFees)}</strong></div>
      `;
    }

    // Order Overview table
    const ovT = document.querySelector(`#${this.ids.orderOverviewTable} tbody`);
    if (ovT) {
      ovT.innerHTML = `
        <tr><td>Total Orders</td><td>${reports.orderOverview.orderCount}</td></tr>
        <tr><td>Total Revenue</td><td>${this.Utility.fmtNGN(reports.orderOverview.totalRevenue)}</td></tr>
        <tr><td>Delivery Fees</td><td>${this.Utility.fmtNGN(reports.orderOverview.totalDeliveryFees)}</td></tr>
      `;
    }

    // Payment data + table
    const paymentData = reports.paymentOverview;
    const paymentsTableBody = document.querySelector(`#${this.ids.paymentsTable} tbody`);
    if (paymentsTableBody) {
      paymentsTableBody.innerHTML = `
        <tr><td>Transfer</td><td>${this.Utility.fmtNGN(paymentData.transfer)}</td></tr>
        <tr><td>Cash</td><td>${this.Utility.fmtNGN(paymentData.cash)}</td></tr>
        <tr><td>Card</td><td>${this.Utility.fmtNGN(paymentData.card)}</td></tr>
        <tr><td>Online</td><td>${this.Utility.fmtNGN(paymentData.online)}</td></tr>
      `;
    }

    // Payments chart
    const paymentLabels = ['Transfer','Cash','Card','Online'];
    const paymentValues = [paymentData.transfer, paymentData.cash, paymentData.card, paymentData.online];

    const paymentsCanvas = this.Utility.el(this.ids.paymentsCanvas);
    if (paymentsCanvas && window.Chart) {
      const ctx = paymentsCanvas.getContext('2d');
      this.clearChart(this.charts.payments);
      this.charts.payments = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: paymentLabels,
          datasets: [{ data: paymentValues, backgroundColor: ['#60a5fa','#f59e0b','#34d399','#d51d28'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
      });
    }

    // Top products table & bar
    const topProducts = reports.productPerformance.slice(0, 8);
    const prodLabels = topProducts.map(p => p.name);
    const prodQtys = topProducts.map(p => p.qty);
    const prodAmounts = topProducts.map(p => p.amount);

    const prodTableBody = document.querySelector(`#${this.ids.topProductsTable} tbody`);
    if (prodTableBody) {
      prodTableBody.innerHTML = topProducts.map(p => `<tr><td>${p.name}</td><td>${p.qty}</td><td>${this.Utility.fmtNGN(p.amount)}</td></tr>`).join('');
    }

    const productsCanvas = this.Utility.el(this.ids.productsCanvas);
    if (productsCanvas && window.Chart) {
      const ctx = productsCanvas.getContext('2d');
      this.clearChart(this.charts.products);
      this.charts.products = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: prodLabels,
          datasets: [
            { label: 'Qty sold', data: prodQtys, backgroundColor: '#2563eb' },
            { label: 'Revenue', data: prodAmounts, backgroundColor: '#f97316' }
          ]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { position: 'bottom' } } }
      });
    }

    // Sales line chart
    const salesKeys = Object.keys(reports.salesByDay).sort();
    const lineLabels = salesKeys.length ? salesKeys : (() => { const a = []; for (let i = 6; i >= 0; i--) { const d = new Date(); d.setDate(d.getDate() - i); a.push(d.toISOString().slice(0,10)); } return a; })();
    const lineValues = lineLabels.map(k => Number(reports.salesByDay[k] || 0));
    const salesCanvas = this.Utility.el(this.ids.salesCanvas);
    if (salesCanvas && window.Chart) {
      const ctx = salesCanvas.getContext('2d');
      this.clearChart(this.charts.sales);
      this.charts.sales = new Chart(ctx, {
        type: 'line',
        data: {
          labels: lineLabels,
          datasets: [{ label: 'Sales (NGN)', data: lineValues, fill: true, borderColor: '#06b6d4', backgroundColor: 'rgba(6,182,212,0.12)' }]
        },
        options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
      });
    }

    // Platform table
    const platformBody = document.querySelector(`#${this.ids.platformTable} tbody`);
    if (platformBody) {
      platformBody.innerHTML = Object.entries(reports.platformOverview).map(([k, v]) => `<tr><td>${this.Utility.toTitleCase(k)}</td><td>${v.count}</td><td>${this.Utility.fmtNGN(v.amount)}</td></tr>`).join('');
    }

    // Customer insights
    const customerBody = document.querySelector(`#${this.ids.customerTable} tbody`);
    if (customerBody) {
      customerBody.innerHTML = `
        <tr><td>New customers</td><td>${reports.customerInsights.newCustomers}</td></tr>
        <tr><td>Returning customers</td><td>${reports.customerInsights.returningCustomers}</td></tr>
        <tr><td>Total unique</td><td>${reports.customerInsights.totalCustomers}</td></tr>
      `;
    }
  }

  /* -------------------------
     Public API: apply a date range, triggers re-render
  -------------------------*/
  applyRange(range, start = null, end = null) {
    this.currentRange = range;
    // toggle active class on buttons
    document.querySelectorAll(this.ids.filterButtons).forEach(btn => {
      btn.classList.toggle('primary', btn.getAttribute('data-range') === range);
    });

    const filtered = this.filterOrdersByRange(this.ORDERS, range, start, end);
    const reports = this.generateReports(filtered);
    this.renderReports(reports);
  }
}

new Report();

// expose globally so HTML can call new Report()
// window.Report = Report;
