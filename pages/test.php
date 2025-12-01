<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Orders Analytics</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <style>
    .chart-card {
      padding: 1rem;
      border: 1px solid #e6e6e6;
      border-radius: 8px;
      margin-bottom: 1rem;
    }

    .chart-controls {
      display: flex;
      gap: 0.5rem;
      align-items: center;
      flex-wrap: wrap;
    }

    .small-radio {
      margin-right: .5rem;
    }

    .table-wrap {
      max-height: 320px;
      overflow: auto;
      margin-top: .75rem;
    }
  </style>
</head>

<body class="p-3">

  <h1 class="mb-3">Orders Analytics (Demo)</h1>

  <div class="controls d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div class="filter-buttons btn-group" role="group">
      <button class="btn btn-sm btn-primary" data-range="today">Today</button>
      <button class="btn btn-sm btn-outline-primary" data-range="week">This Week</button>
      <button class="btn btn-sm btn-outline-primary" data-range="month">This Month</button>
      <button class="btn btn-sm btn-outline-primary" data-range="year">This Year</button>
      <button class="btn btn-sm btn-outline-secondary" data-range="all">All</button>
    </div>

    <div class="d-flex gap-2 align-items-center">
      <input type="date" class="form-control form-control-sm" id="startDate">
      <input type="date" class="form-control form-control-sm" id="endDate">
      <button id="applyDate" class="btn btn-sm btn-success">Apply</button>
    </div>
  </div>

  <!-- Orders Overview -->
  <div class="chart-card">
    <div class="d-flex justify-content-between align-items-center">
      <h5>Orders Overview — Date vs Amount & Quantity</h5>
      <div class="chart-controls" id="chartTypeOrders">
        <label class="small-radio">Chart:</label>
        <input type="radio" name="ordersChartType" value="bar" checked> Bar
        <input type="radio" name="ordersChartType" value="line"> Line
        <input type="radio" name="ordersChartType" value="pie"> Pie
        <input type="radio" name="ordersChartType" value="doughnut"> Doughnut
      </div>
    </div>

    <canvas id="ordersChart" style="max-height:360px;"></canvas>
    <div class="table-wrap">
      <table class="table table-striped table-sm" id="ordersTable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Payment Methods -->
  <div class="chart-card">
    <div class="d-flex justify-content-between align-items-center">
      <h5>Payment Methods — Date vs Amount by Method</h5>
      <div class="chart-controls" id="chartTypeMethods">
        <label class="small-radio">Chart:</label>
        <input type="radio" name="methodsChartType" value="bar" checked> Bar
        <input type="radio" name="methodsChartType" value="line"> Line
        <input type="radio" name="methodsChartType" value="pie"> Pie
        <input type="radio" name="methodsChartType" value="doughnut"> Doughnut
      </div>
    </div>

    <canvas id="methodsChart" style="max-height:360px;"></canvas>
    <div class="table-wrap">
      <table class="table table-striped table-sm" id="methodsTable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Method</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Products Breakdown -->
  <div class="chart-card">
    <div class="d-flex justify-content-between align-items-center">
      <h5>Products — Quantity & Amount per Product</h5>
      <div class="chart-controls" id="chartTypeProducts">
        <label class="small-radio">Chart:</label>
        <input type="radio" name="productsChartType" value="bar" checked> Bar
        <input type="radio" name="productsChartType" value="pie"> Pie
        <input type="radio" name="productsChartType" value="doughnut"> Doughnut
        <input type="radio" name="productsChartType" value="line"> Line
      </div>
    </div>

    <canvas id="productsChart" style="max-height:360px;"></canvas>
    <div class="table-wrap">
      <table class="table table-striped table-sm" id="productsTable">
        <thead>
          <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Amount</th>
            <th>Last Order Date</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Include Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <script>
    /***********************
     * Sample orders array (paste your orders variable here)
     ***********************/
    const orders = [{
        "id": "3",
        "order_id": "PS-HGWO-6451",
        "customer": "ps-6463615",
        "customer_type": "website",
        "order_time": "2025-10-08 10:37:52",
        "order_items": "[{\"id\": \"5\", \"qty\": 1, \"tag\": \"pizza\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c93d35da72c8.77664109.jpg\\\"\", \"price\": \"3900\", \"stock\": \"12\", \"title\": \"Rice Pizza\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9712980\", \"category_id\": \"1\"}, {\"id\": \"4\", \"qty\": 1, \"tag\": \"drink\", \"size\": \"330ml\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c8f1f757d2a0.04287677.jpg\\\"\", \"price\": \"500\", \"stock\": \"13\", \"title\": \"Coca Cola\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9886891\", \"category_id\": \"3\"}, {\"id\": \"3\", \"qty\": 1, \"tag\": \"shawarma\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c7f098534e21.04649486.jpg\\\"\", \"price\": \"120000\", \"stock\": \"10\", \"title\": \"Omega Swayili Dish\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9430248\", \"category_id\": \"2\"}]",
        "amount": "134930",
        "status": "delivered",
        "order_note": "Serve Hot",
        "delivery_type": "delivery",
        "created_at": "2025-10-08",
        "fullname": "Peace Okoye",
        "email_address": "peaceok@yahoo.com",
        "location": "23 Presslane Enugu",
        "city": "New Haven",
        "country_state": "enugu",
        "avatar": "",
        "transaction_amount": "134930",
        "transaction_status": "successful",
        "method": "card",
        "ip_address": "102.90.96.250",
        "meta_data": "{\"referrer\": \"http://localhost/pizzasquare/checkout\", \"custom_fields\": [{\"value\": \"Peace Okoye\", \"display_name\": \"Name\"}]}"
      },
      {
        "id": "4",
        "order_id": "PS-R3Z1-1474",
        "customer": "ps-1729072",
        "customer_type": "website",
        "order_time": "2025-10-08 11:02:32",
        "order_items": "[{\"id\": \"5\", \"qty\": 1, \"tag\": \"pizza\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c93d35da72c8.77664109.jpg\\\"\", \"price\": \"3900\", \"stock\": \"12\", \"title\": \"Rice Pizza\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9712980\", \"category_id\": \"1\"}, {\"id\": \"4\", \"qty\": 1, \"tag\": \"drink\", \"size\": \"330ml\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c8f1f757d2a0.04287677.jpg\\\"\", \"price\": \"500\", \"stock\": \"13\", \"title\": \"Coca Cola\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9886891\", \"category_id\": \"3\"}, {\"id\": \"3\", \"qty\": 1, \"tag\": \"shawarma\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c7f098534e21.04649486.jpg\\\"\", \"price\": \"120000\", \"stock\": \"10\", \"title\": \"Omega Swayili Dish\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9430248\", \"category_id\": \"2\"}]",
        "amount": "135230",
        "status": "delivered",
        "order_note": "Fast Delivery",
        "delivery_type": "delivery",
        "created_at": "2025-10-08",
        "fullname": "Kate Ugwu",
        "email_address": "kateu@gmail.com",
        "location": "34 Doglass Street",
        "city": "Independence Layout",
        "country_state": "enugu",
        "avatar": "",
        "transaction_amount": "135230",
        "transaction_status": "successful",
        "method": "card",
        "ip_address": "102.90.96.250",
        "meta_data": "{\"referrer\": \"http://localhost/pizzasquare/checkout\", \"custom_fields\": [{\"value\": \"Kate Ugwu\", \"display_name\": \"Name\"}]}"
      },
      {
        "id": "5",
        "order_id": "PS-0YFH-5570",
        "customer": "John Doe",
        "customer_type": "walk-in",
        "order_time": "2025-10-08 11:20:27",
        "order_items": "[{\"id\": \"3\", \"qty\": 1, \"tag\": \"shawarma\", \"size\": \"Medium\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c7f098534e21.04649486.jpg\\\"\", \"price\": \"120000\", \"stock\": \"10\", \"title\": \"Omega Swayili Dish\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9430248\", \"category_id\": \"2\"}, {\"id\": \"5\", \"qty\": 1, \"tag\": \"pizza\", \"size\": \"Medium\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c93d35da72c8.77664109.jpg\\\"\", \"price\": \"3900\", \"stock\": \"12\", \"title\": \"Rice Pizza\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9712980\", \"category_id\": \"1\"}]",
        "amount": "133193",
        "status": "delivered",
        "order_note": "",
        "delivery_type": "pickup",
        "created_at": "2025-10-08",
        "fullname": null,
        "email_address": null,
        "location": null,
        "city": null,
        "country_state": null,
        "avatar": null,
        "transaction_amount": "133193",
        "transaction_status": "success",
        "method": "cash",
        "ip_address": "::1",
        "meta_data": "{\"fullname\": \"John Doe\", \"order_type\": \"pos\", \"order_amount\": 133193}"
      },
      {
        "id": "6",
        "order_id": "PS-2JG0-3691",
        "customer": "John Doe",
        "customer_type": "walk-in",
        "order_time": "2025-10-08 11:33:34",
        "order_items": "[{\"id\": \"5\", \"qty\": 1, \"tag\": \"pizza\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c93d35da72c8.77664109.jpg\\\"\", \"price\": \"3900\", \"stock\": \"12\", \"title\": \"Rice Pizza\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9712980\", \"category_id\": \"1\"}]",
        "amount": "4193",
        "status": "delivered",
        "order_note": "",
        "delivery_type": "pickup",
        "created_at": "2025-10-08",
        "fullname": null,
        "email_address": null,
        "location": null,
        "city": null,
        "country_state": null,
        "avatar": null,
        "transaction_amount": "4193",
        "transaction_status": "successful",
        "method": "cash",
        "ip_address": "::1",
        "meta_data": "{\"fullname\": \"John Doe\", \"order_type\": \"walk-in\", \"order_amount\": 4193}"
      },
      {
        "id": "7",
        "order_id": "PS-DI2U-3011",
        "customer": "John Doe",
        "customer_type": "walk-in",
        "order_time": "2025-10-08 11:34:44",
        "order_items": "[{\"id\": \"5\", \"qty\": 2, \"tag\": \"pizza\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c93d35da72c8.77664109.jpg\\\"\", \"price\": \"3900\", \"stock\": \"12\", \"title\": \"Rice Pizza\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9712980\", \"category_id\": \"1\"}, {\"id\": \"4\", \"qty\": 2, \"tag\": \"drink\", \"size\": \"330ml\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c8f1f757d2a0.04287677.jpg\\\"\", \"price\": \"500\", \"stock\": \"13\", \"title\": \"Coca Cola\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9886891\", \"category_id\": \"3\"}, {\"id\": \"3\", \"qty\": 1, \"tag\": \"shawarma\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c7f098534e21.04649486.jpg\\\"\", \"price\": \"120000\", \"stock\": \"10\", \"title\": \"Omega Swayili Dish\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9430248\", \"category_id\": \"2\"}]",
        "amount": "138460",
        "status": "delivered",
        "order_note": "",
        "delivery_type": "pickup",
        "created_at": "2025-10-08",
        "fullname": null,
        "email_address": null,
        "location": null,
        "city": null,
        "country_state": null,
        "avatar": null,
        "transaction_amount": "138460",
        "transaction_status": "successful",
        "method": "cash",
        "ip_address": "::1",
        "meta_data": "{\"fullname\": \"John Doe\", \"order_type\": \"walk-in\", \"order_amount\": 138460}"
      },
      {
        "id": "8",
        "order_id": "PS-6M7U-4971",
        "customer": "Okey",
        "customer_type": "walk-in",
        "order_time": "2025-10-08 11:40:26",
        "order_items": "[{\"id\": \"5\", \"qty\": 1, \"tag\": \"pizza\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c93d35da72c8.77664109.jpg\\\"\", \"price\": \"3900\", \"stock\": \"9\", \"title\": \"Rice Pizza\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9712980\", \"category_id\": \"1\"}, {\"id\": \"4\", \"qty\": 1, \"tag\": \"drink\", \"size\": \"330ml\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c8f1f757d2a0.04287677.jpg\\\"\", \"price\": \"500\", \"stock\": \"11\", \"title\": \"Coca Cola\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9886891\", \"category_id\": \"3\"}]",
        "amount": "4730",
        "status": "delivered",
        "order_note": "",
        "delivery_type": "pickup",
        "created_at": "2025-10-08",
        "fullname": null,
        "email_address": null,
        "location": null,
        "city": null,
        "country_state": null,
        "avatar": null,
        "transaction_amount": "4730",
        "transaction_status": "successful",
        "method": "card",
        "ip_address": "::1",
        "meta_data": "{\"fullname\": \"Okey\", \"order_type\": \"walk-in\", \"order_amount\": 4730}"
      },
      {
        "id": "9",
        "order_id": "PS-NM7I-6924",
        "customer": "Okey",
        "customer_type": "walk-in",
        "order_time": "2025-10-08 11:40:48",
        "order_items": "[{\"id\": \"4\", \"qty\": 1, \"tag\": \"drink\", \"size\": \"330ml\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c8f1f757d2a0.04287677.jpg\\\"\", \"price\": \"500\", \"stock\": \"11\", \"title\": \"Coca Cola\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9886891\", \"category_id\": \"3\"}]",
        "amount": "538",
        "status": "delivered",
        "order_note": "",
        "delivery_type": "pickup",
        "created_at": "2025-10-08",
        "fullname": null,
        "email_address": null,
        "location": null,
        "city": null,
        "country_state": null,
        "avatar": null,
        "transaction_amount": "538",
        "transaction_status": "successful",
        "method": "transfer",
        "ip_address": "::1",
        "meta_data": "{\"fullname\": \"Okey\", \"order_type\": \"walk-in\", \"order_amount\": 538}"
      },
      {
        "id": "10",
        "order_id": "PS-O8PS-1491",
        "customer": "John Doe",
        "customer_type": "walk-in",
        "order_time": "2025-10-08 11:43:24",
        "order_items": "[{\"id\": \"5\", \"qty\": 1, \"tag\": \"pizza\", \"size\": \"Medium\", \"type\": \"regular\", \"image\": \"\\\"http:\\\\/\\\\/localhost\\\\/pizzasquare\\\\/public\\\\/UPLOADS\\\\/products\\\\/68c93d35da72c8.77664109.jpg\\\"\", \"price\": \"3900\", \"stock\": \"8\", \"title\": \"Rice Pizza\", \"rating\": \"5\", \"status\": \"available\", \"product_id\": \"ps-9712980\", \"category_id\": \"1\"}]",
        "amount": "4193",
        "status": "delivered",
        "order_note": "",
        "delivery_type": "pickup",
        "created_at": "2025-10-08",
        "fullname": null,
        "email_address": null,
        "location": null,
        "city": null,
        "country_state": null,
        "avatar": null,
        "transaction_amount": "4193",
        "transaction_status": "successful",
        "method": "transfer",
        "ip_address": "::1",
        "meta_data": "{\"fullname\": \"John Doe\", \"order_type\": \"walk-in\", \"order_amount\": 4193}"
      }
    ];

    /***********************
     * Helpers: parse and aggregate
     ***********************/
    function parseItemsField(itemsRaw) {
      // order.order_items is a JSON string (with escaped quotes and slashes)
      // attempt JSON.parse directly, if fails try to replace backslashes
      try {
        const parsed = JSON.parse(itemsRaw);
        return parsed.map(normalizeItem);
      } catch (e) {
        try {
          const cleaned = itemsRaw.replace(/\\+/g, '\\'); // try reduce escapes
          return JSON.parse(cleaned).map(normalizeItem);
        } catch (err) {
          // as fallback try to replace \" wrapper around image fields
          try {
            const replaced = itemsRaw.replace(/\\"/g, '"');
            return JSON.parse(replaced).map(normalizeItem);
          } catch (e2) {
            console.error('Failed to parse order_items', itemsRaw, e2);
            return [];
          }
        }
      }
    }

    function normalizeItem(it) {
      // ensure qty and price are numbers and clean image string
      const price = Number(it.price ?? it.amount ?? 0);
      const qty = Number(it.qty ?? 0);
      let image = it.image ?? '';
      // remove wrapping quotes if present
      image = ('' + image).replace(/^"|"$/g, '').replace(/\\\//g, '/');
      return {
        ...it,
        price,
        qty,
        image
      };
    }

    function toDateOnly(ts) {
      // input: "2025-10-08 10:37:52" -> "2025-10-08"
      if (!ts) return null;
      return ts.split(' ')[0];
    }

    function parseOrders(rawOrders) {
      return rawOrders.map(o => {
        const dateOnly = toDateOnly(o.order_time ?? o.created_at);
        const amount = Number(o.amount ?? o.transaction_amount ?? 0);
        const items = parseItemsField(o.order_items ?? '[]');
        // compute total qty across items for this order
        const totalQty = items.reduce((s, i) => s + (Number(i.qty) || 0), 0);
        return {
          ...o,
          dateOnly,
          amount,
          items,
          totalQty,
          method: (o.method || '').toLowerCase()
        };
      });
    }

    /***********************
     * Date range helpers
     ***********************/
    function startOfWeek(date) {
      // Monday as start of week
      const d = new Date(date);
      const day = d.getDay(); // 0 Sun .. 6 Sat
      const diff = (day === 0 ? -6 : 1 - day); // if Sunday go back 6 days
      d.setDate(d.getDate() + diff);
      d.setHours(0, 0, 0, 0);
      return d;
    }

    function startOfMonth(date) {
      const d = new Date(date);
      d.setDate(1);
      d.setHours(0, 0, 0, 0);
      return d;
    }

    function startOfYear(date) {
      const d = new Date(date);
      d.setMonth(0);
      d.setDate(1);
      d.setHours(0, 0, 0, 0);
      return d;
    }

    function endOfDay(date) {
      const d = new Date(date);
      d.setHours(23, 59, 59, 999);
      return d;
    }

    function getRangeDates(rangeName) {
      const now = new Date();
      let start, end = endOfDay(now);
      switch (rangeName) {
        case 'today':
          start = new Date(now);
          start.setHours(0, 0, 0, 0);
          break;
        case 'week':
          start = startOfWeek(now);
          break;
        case 'month':
          start = startOfMonth(now);
          break;
        case 'year':
          start = startOfYear(now);
          break;
        case 'all':
        default:
          // very early start
          start = new Date(2000, 0, 1);
      }
      return {
        start,
        end
      };
    }

    function inRange(dateStr, startDate, endDate) {
      if (!dateStr) return false;
      const d = new Date(dateStr + 'T00:00:00'); // date-only
      return d >= startDate && d <= endDate;
    }

    /***********************
     * Aggregation functions
     ***********************/
    function aggregateByDate(parsedOrders, startDate, endDate) {
      // returns array of {date, amount, qty}
      const map = new Map();
      parsedOrders.forEach(o => {
        if (!inRange(o.dateOnly, startDate, endDate)) return;
        const key = o.dateOnly;
        const existing = map.get(key) || {
          amount: 0,
          qty: 0
        };
        existing.amount += Number(o.amount || 0);
        existing.qty += Number(o.totalQty || 0);
        map.set(key, existing);
      });
      // Sort dates ascending
      const result = Array.from(map.entries()).sort((a, b) => a[0].localeCompare(b[0])).map(([date, vals]) => ({
        date,
        amount: vals.amount,
        qty: vals.qty
      }));
      return result;
    }

    function aggregateMethodsByDate(parsedOrders, startDate, endDate) {
      // returns {dates: [..], methodList: [...], data: {method: [amount per date]}} suitable for stacked chart
      const dateSet = new Set();
      const methodSet = new Set();
      const map = {}; // map[date][method] = amount
      parsedOrders.forEach(o => {
        if (!inRange(o.dateOnly, startDate, endDate)) return;
        const d = o.dateOnly;
        dateSet.add(d);
        const m = (o.method || 'unknown');
        methodSet.add(m);
        map[d] = map[d] || {};
        map[d][m] = (map[d][m] || 0) + Number(o.amount || 0);
      });
      const dates = Array.from(dateSet).sort();
      const methods = Array.from(methodSet);
      const data = {};
      methods.forEach(m => {
        data[m] = dates.map(dt => (map[dt] && map[dt][m]) ? map[dt][m] : 0);
      });
      return {
        dates,
        methods,
        data
      };
    }

    function aggregateProducts(parsedOrders, startDate, endDate) {
      // returns array of {product_id, title, qty, amount, lastDate}
      const products = {};
      parsedOrders.forEach(o => {
        if (!inRange(o.dateOnly, startDate, endDate)) return;
        o.items.forEach(it => {
          const pid = it.product_id || it.id || it.title;
          if (!products[pid]) products[pid] = {
            product_id: pid,
            title: it.title || pid,
            qty: 0,
            amount: 0,
            lastDate: o.dateOnly
          };
          products[pid].qty += Number(it.qty || 0);
          // price could be per item (it.price). Use qty * price
          products[pid].amount += (Number(it.price || 0) * Number(it.qty || 0));
          // update last date
          if (o.dateOnly && products[pid].lastDate < o.dateOnly) products[pid].lastDate = o.dateOnly;
        });
      });
      return Object.values(products).sort((a, b) => b.qty - a.qty);
    }

    /***********************
     * Chart utilities
     ***********************/
    let ordersChart = null,
      methodsChart = null,
      productsChart = null;

    function destroyIfExists(chart) {
      if (chart && typeof chart.destroy === 'function') {
        chart.destroy();
      }
    }

    function renderOrdersChart(rows, chartType) {
      const ctx = document.getElementById('ordersChart');
      destroyIfExists(ordersChart);
      // If chart type is pie/doughnut we display amounts only (pie uses labels)
      if (chartType === 'pie' || chartType === 'doughnut') {
        ordersChart = new Chart(ctx, {
          type: chartType,
          data: {
            labels: rows.map(r => r.date),
            datasets: [{
              label: 'Amount',
              data: rows.map(r => r.amount),
            }]
          },
          options: {
            responsive: true
          }
        });
        return;
      }

      // for line/bar: show amount & qty (qty on second axis)
      ordersChart = new Chart(ctx, {
        type: chartType,
        data: {
          labels: rows.map(r => r.date),
          datasets: [{
              label: 'Amount',
              data: rows.map(r => r.amount),
              yAxisID: 'y',
              tension: 0.2
            },
            {
              label: 'Quantity',
              data: rows.map(r => r.qty),
              yAxisID: 'yQty',
              type: chartType === 'line' ? 'line' : chartType
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              position: 'left',
              title: {
                display: true,
                text: 'Amount'
              }
            },
            yQty: {
              position: 'right',
              title: {
                display: true,
                text: 'Quantity'
              },
              grid: {
                drawOnChartArea: false
              }
            }
          }
        }
      });
    }

    function renderMethodsChart(agg, chartType) {
      const ctx = document.getElementById('methodsChart');
      destroyIfExists(methodsChart);

      // If pie/doughnut picked: show method totals (not per date)
      if (chartType === 'pie' || chartType === 'doughnut') {
        const methodTotals = {};
        agg.methods.forEach(m => {
          methodTotals[m] = agg.data[m].reduce((s, x) => s + x, 0);
        });
        methodsChart = new Chart(ctx, {
          type: chartType,
          data: {
            labels: agg.methods,
            datasets: [{
              label: 'Amount',
              data: agg.methods.map(m => methodTotals[m])
            }]
          },
          options: {
            responsive: true
          }
        });
        return;
      }

      // stacked bar or line showing methods across dates
      const datasets = agg.methods.map((m, i) => ({
        label: m,
        data: agg.data[m],
        stack: 'methods'
      }));

      methodsChart = new Chart(ctx, {
        type: chartType,
        data: {
          labels: agg.dates,
          datasets
        },
        options: {
          responsive: true,
          scales: {
            x: {
              stacked: chartType === 'bar'
            },
            y: {
              stacked: chartType === 'bar'
            }
          }
        }
      });
    }

    function renderProductsChart(rows, chartType) {
      const ctx = document.getElementById('productsChart');
      destroyIfExists(productsChart);

      // For pie/doughnut show by qty or amount: use qty
      if (chartType === 'pie' || chartType === 'doughnut') {
        productsChart = new Chart(ctx, {
          type: chartType,
          data: {
            labels: rows.map(r => r.title),
            datasets: [{
              label: 'Qty',
              data: rows.map(r => r.qty)
            }]
          },
          options: {
            responsive: true
          }
        });
        return;
      }

      // bar/line show qty and amount (amount scaled)
      productsChart = new Chart(ctx, {
        type: chartType,
        data: {
          labels: rows.map(r => r.title),
          datasets: [{
              label: 'Quantity',
              data: rows.map(r => r.qty),
              yAxisID: 'yQty'
            },
            {
              label: 'Amount',
              data: rows.map(r => r.amount),
              yAxisID: 'yAmount'
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            yQty: {
              position: 'left',
              title: {
                display: true,
                text: 'Qty'
              },
              grid: {
                drawOnChartArea: false
              }
            },
            yAmount: {
              position: 'right',
              title: {
                display: true,
                text: 'Amount'
              }
            }
          }
        }
      });
    }

    /***********************
     * Table rendering
     ***********************/
    function renderOrdersTable(rows) {
      const tbody = document.querySelector('#ordersTable tbody');
      tbody.innerHTML = '';
      rows.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.date}</td><td>${r.amount.toLocaleString()}</td><td>${r.qty}</td>`;
        tbody.appendChild(tr);
      });
    }

    function renderMethodsTable(parsedOrders, startDate, endDate) {
      const tbody = document.querySelector('#methodsTable tbody');
      tbody.innerHTML = '';
      // show each order date + method + amount row
      const filtered = parsedOrders.filter(o => inRange(o.dateOnly, startDate, endDate));
      // sort by date
      filtered.sort((a, b) => (a.dateOnly || '').localeCompare(b.dateOnly || ''));
      filtered.forEach(o => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${o.dateOnly}</td><td>${o.method||'unknown'}</td><td>${(o.amount||0).toLocaleString()}</td>`;
        tbody.appendChild(tr);
      });
    }

    function renderProductsTable(rows) {
      const tbody = document.querySelector('#productsTable tbody');
      tbody.innerHTML = '';
      rows.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.title}</td><td>${r.qty}</td><td>${r.amount.toLocaleString()}</td><td>${r.lastDate || ''}</td>`;
        tbody.appendChild(tr);
      });
    }

    /***********************
     * Main update function
     ***********************/
    const parsedOrders = parseOrders(orders);

    function updateAll(rangeOrCustom) {
      // rangeOrCustom: {type: 'preset', value: 'today' } or {type:'custom', start: Date, end: Date}
      let startDate, endDate;
      if (rangeOrCustom.type === 'preset') {
        const r = getRangeDates(rangeOrCustom.value);
        startDate = r.start;
        endDate = r.end;
      } else {
        startDate = new Date(rangeOrCustom.start);
        startDate.setHours(0, 0, 0, 0);
        endDate = new Date(rangeOrCustom.end);
        endDate.setHours(23, 59, 59, 999);
      }

      // Orders overview
      const ordersRows = aggregateByDate(parsedOrders, startDate, endDate);
      const ordersChartType = document.querySelector('input[name="ordersChartType"]:checked').value;
      renderOrdersChart(ordersRows, ordersChartType);
      renderOrdersTable(ordersRows);

      // Methods
      const methodsAgg = aggregateMethodsByDate(parsedOrders, startDate, endDate);
      const methodsChartType = document.querySelector('input[name="methodsChartType"]:checked').value;
      renderMethodsChart(methodsAgg, methodsChartType);
      renderMethodsTable(parsedOrders, startDate, endDate);

      // Products
      const prodRows = aggregateProducts(parsedOrders, startDate, endDate);
      const productsChartType = document.querySelector('input[name="productsChartType"]:checked').value;
      renderProductsChart(prodRows, productsChartType);
      renderProductsTable(prodRows);
    }

    /***********************
     * Init event listeners
     ***********************/
    // Filter buttons
    document.querySelectorAll('.filter-buttons button').forEach(btn => {
      btn.addEventListener('click', (e) => {
        document.querySelectorAll('.filter-buttons button').forEach(b => b.classList.remove('btn-primary'));
        e.currentTarget.classList.add('btn-primary');
        const value = e.currentTarget.dataset.range || 'all';
        updateAll({
          type: 'preset',
          value
        });
      });
    });

    // Date apply
    document.getElementById('applyDate').addEventListener('click', () => {
      const s = document.getElementById('startDate').value;
      const e = document.getElementById('endDate').value;
      if (!s || !e) {
        alert('Please pick start and end dates');
        return;
      }
      updateAll({
        type: 'custom',
        start: s,
        end: e
      });
    });

    // Chart type switchers
    document.querySelectorAll('input[name="ordersChartType"]').forEach(r => r.addEventListener('change', () => {
      // re-render with same date range as currently selected (use currently active preset or date inputs)
      triggerCurrentUpdate();
    }));
    document.querySelectorAll('input[name="methodsChartType"]').forEach(r => r.addEventListener('change', () => triggerCurrentUpdate()));
    document.querySelectorAll('input[name="productsChartType"]').forEach(r => r.addEventListener('change', () => triggerCurrentUpdate()));

    function triggerCurrentUpdate() {
      // Try to detect the active preset button
      const active = document.querySelector('.filter-buttons button.btn-primary');
      if (active) {
        updateAll({
          type: 'preset',
          value: active.dataset.range || 'all'
        });
      } else {
        // fall back to custom date inputs if present
        const s = document.getElementById('startDate').value;
        const e = document.getElementById('endDate').value;
        if (s && e) updateAll({
          type: 'custom',
          start: s,
          end: e
        });
        else updateAll({
          type: 'preset',
          value: 'all'
        });
      }
    }

    // initial render: use 'today' by default if matching dates exist, otherwise 'all'
    (function initial() {
      // find a preset that actually selects something; default to today if it matches any orderDate
      const anyToday = parsedOrders.some(o => o.dateOnly === toDateOnly(new Date().toISOString().slice(0, 10)));
      if (anyToday) {
        document.querySelector('.filter-buttons button[data-range="today"]').classList.add('btn-primary');
        updateAll({
          type: 'preset',
          value: 'today'
        });
      } else {
        document.querySelector('.filter-buttons button[data-range="all"]').classList.add('btn-primary');
        updateAll({
          type: 'preset',
          value: 'all'
        });
      }
    })();
  </script>
</body>

</html>