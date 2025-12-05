<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='admin')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
?>

<body id="ADMIN_SYSTEM" class="theme-light" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    <div id="overlay"></div>
    <section id="adminLayout">
        <?php require "sidebar.php" ?>
        <div id="rightContent">
            <?php require "navbar.php" ?>
            <section class="inner-container">
                <div class="content-centered p-4 reports-page">
                    <div data-aos="fade-down" class="page-header mt-4">
                        <div class="welcome">Analytics & Reports Manager! <span id="orderCount"></span></div>
                        <div class="center-mobile">Here's a quick overview of your shop today.</div>
                    </div>
                        <header class="controls">
                            <div class="filter-buttons btn-group" role="group">
                                <button class="btn btn-sm btn-primary" data-range="today">Today</button>
                                <button class="btn btn-sm" data-range="yesterday">Yesterday</button>
                                <button class="btn btn-sm" data-range="week">This Week</button>
                                <button class="btn btn-sm" data-range="month">This Month</button>
                                <button class="btn btn-sm" data-range="year">This Year</button>
                                <button class="btn btn-sm btn-secondary" data-range="all">All</button>
                            </div>

                            <div class="date-controls">
                                <input type="date" class="form-control form-control-sm datepicker" placeholder="Start Date" id="startDate">
                                <input type="date" class="form-control form-control-sm datepicker" placeholder="End Date" id="endDate">
                                <button id="applyDate" class="btn btn-sm btn-secondary">Apply</button>
                            </div>
                        </header>
                        <div id="reportArea" class="row">
                            <!-- Left: Order Overview & Payments -->
                            <div class="kpi-card-n col-4">
                                <h3>Order Overview</h3>
                                <div class="metrics" id="orderOverviewMetrics">
                                <!-- metrics injected here -->
                                </div>
                                <div style="margin-top:12px">
                                <table id="orderOverviewTable">
                                    <thead>
                                    <tr><th>Metric</th><th class="mini">Value</th></tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                </div>
                            </div>

                            <div class="kpi-card-n col-8">
                                <h3>Payments Overview</h3>
                                <div style="display:flex;gap:12px;align-items:center">
                                <div style="flex:1">
                                    <canvas id="paymentsDoughnut" height="160"></canvas>
                                </div>
                                <div style="width:240px">
                                    <table id="paymentsTable">
                                    <thead><tr><th>Method</th><th class="mini">Amount</th></tr></thead>
                                    <tbody></tbody>
                                    </table>
                                    <div style="margin-top:8px" class="mini muted">Delivery fees counted separately in Orders.</div>
                                </div>
                                </div>
                            </div>

                            <!-- Product performance -->
                            <div class="kpi-card-n col-8">
                                <h3>Top Products</h3>
                                <div class="chart-card">
                                <canvas id="productsBar" height="140"></canvas>
                                </div>
                                <div style="margin-top:12px">
                                <table id="topProductsTable">
                                    <thead><tr><th>Product</th><th class="mini">Qty</th><th class="mini">Amount</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                                </div>
                            </div>

                            <!-- Sales over time -->
                            <div class="kpi-card-n col-4">
                                <h3>Sales Over Time</h3>
                                <div class="chart-card">
                                <canvas id="salesLine" height="180"></canvas>
                                </div>
                            </div>

                            <!-- Platform & Customers -->
                            <div class="kpi-card-n col-6">
                                <h3>Platform Overview</h3>
                                <table id="platformTable">
                                <thead><tr><th>Platform</th><th class="mini">Count</th><th class="mini">Amount</th></tr></thead>
                                <tbody></tbody>
                                </table>
                            </div>

                            <div class="kpi-card-n col-6">
                                <h3>Customer Insights</h3>
                                <table id="customerTable">
                                <thead><tr><th>Metric</th><th class="mini">Value</th></tr></thead>
                                <tbody></tbody>
                                </table>
                            </div>

                            </div>
                    
                </div>

                <?php require "footer.php" ?>
            </section>
        </div>



    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/ReportPage.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d"
            });
        });
    </script>

</body>

</html>