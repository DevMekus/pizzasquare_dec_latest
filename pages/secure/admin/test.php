<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
// require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='admin')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Orders Report Dashboard</title>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root{
      --bg:#f4f6f8; --card:#fff; --muted:#6b7280; --accent:#d51d28;
      --success:#16a34a; --warning:#f59e0b; --danger:#ef4444;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
    }
    body{margin:0;background:var(--bg);color:#111;}

    /* NAVBAR */
    nav {
      background: var(--accent);
      padding: 10px 14px;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 12px;
      position: sticky;
      top: 0;
      z-index: 50;
    }
    nav button {
      background: rgba(255,255,255,0.2);
      border: 0;
      padding: 6px 10px;
      border-radius: 6px;
      color: #fff;
      cursor: pointer;
      font-size: 14px;
    }
    nav .title {
      font-size: 16px;
      font-weight: 600;
      margin-left: auto;
      margin-right: auto;
      text-align: center;
    }

    .container{max-width:1200px;margin:28px auto;padding:16px;}
    header.controls{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:18px;}
    .filter-buttons{display:flex;gap:6px;flex-wrap:wrap;}
    .filter-buttons .btn{padding:6px 10px;border-radius:6px;border:0;background:#e6eef6;cursor:pointer}
    .filter-buttons .btn.primary{background:var(--accent);color:#fff}
    .filter-buttons .btn.secondary{background:#e2e8f0}

    .date-controls{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
    input[type="date"]{padding:6px;border-radius:6px;border:1px solid #d1d5db}
    button#applyDate{padding:7px 10px;border-radius:6px;border:0;background:#6b7280;color:#fff;cursor:pointer}

    /* GRID SYSTEM */
    .grid{
      display:grid;
      grid-template-columns:repeat(12,1fr);
      gap:12px;
    }
    .card{background:var(--card);border-radius:8px;padding:14px;box-shadow:0 1px 4px rgba(0,0,0,0.06)}
    .col-4{grid-column:span 4}
    .col-6{grid-column:span 6}
    .col-8{grid-column:span 8}
    .col-12{grid-column:span 12}

    h3{margin:0 0 10px 0;font-size:16px}

    table{width:100%;border-collapse:collapse;font-size:14px}
    table th, table td{padding:8px;border-bottom:1px solid #eef2f6;text-align:left}

    .mini{font-size:13px;color:var(--muted)}
    .muted{color:var(--muted)}

    /* CHART SIZING */
    #paymentsDoughnut {
      max-width: 260px !important;
      max-height: 260px !important;
      margin: auto;
    }

    /* MOBILE VIEW */
    @media(max-width:900px){
      .grid{grid-template-columns:1fr;}
      .col-4,.col-6,.col-8,.col-12{
        grid-column:span 12;
      }
      nav .title {
        font-size: 14px;
      }
      #paymentsDoughnut {
        max-width: 120px !important;
        max-height: 120px !important;
      }
    }
  </style>
</head>

<body>

<!-- Navbar -->
<nav>
  <button onclick="history.back()">← Back</button>
  <button onclick="window.location.href='/'">🏠 Home</button>
  <div class="title">Reports Dashboard</div>
</nav>

<div class="container">

  <!-- Filters -->
  <header class="controls">
    <div class="filter-buttons">
      <button class="btn primary" data-range="today">Today</button>
      <button class="btn" data-range="yesterday">Yesterday</button>
      <button class="btn" data-range="week">This Week</button>
      <button class="btn" data-range="month">This Month</button>
      <button class="btn" data-range="year">This Year</button>
      <button class="btn secondary" data-range="all">All</button>
    </div>

    <div class="date-controls">
      <input type="date" id="startDate">
      <input type="date" id="endDate">
      <button id="applyDate">Apply</button>
    </div>
  </header>


  <!-- Report Area -->
  <div id="reportArea" class="grid">

    <!-- Order Overview -->
    <div class="card col-4">
      <h3>Order Overview</h3>
      <div class="metrics" id="orderOverviewMetrics"></div>
      <table id="orderOverviewTable">
        <thead><tr><th>Metric</th><th class="mini">Value</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>

    <!-- Payments Overview -->
    <div class="card col-8">
      <h3>Payments Overview</h3>
      <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <div style="flex:1;display:flex;justify-content:center;">
          <canvas id="paymentsDoughnut"></canvas>
        </div>
        <div style="width:240px">
          <table id="paymentsTable">
            <thead><tr><th>Method</th><th class="mini">Amount</th></tr></thead>
            <tbody></tbody>
          </table>
          <div class="mini muted">Delivery fees counted in Orders only.</div>
        </div>
      </div>
    </div>

    <!-- Product Performance -->
    <div class="card col-8">
      <h3>Top Products</h3>
      <canvas id="productsBar" height="140"></canvas>
      <table id="topProductsTable">
        <thead><tr><th>Product</th><th class="mini">Qty</th><th class="mini">Amount</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>

    <!-- Sales Over Time -->
    <div class="card col-4">
      <h3>Sales Over Time</h3>
      <canvas id="salesLine" height="180"></canvas>
    </div>

    <!-- Platform -->
    <div class="card col-6">
      <h3>Platform Overview</h3>
      <table id="platformTable">
        <thead><tr><th>Platform</th><th>Count</th><th>Amount</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>

    <!-- Customers -->
    <div class="card col-6">
      <h3>Customer Insights</h3>
      <table id="customerTable">
        <thead><tr><th>Metric</th><th>Value</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>

  </div>
</div>

 <script type="module" src="<?= BASE_URL; ?>assets/src/Classes/Report.js"></script>

</body>
</html>

