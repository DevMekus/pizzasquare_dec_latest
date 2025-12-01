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
                    <header data-aos="fade-down" class="controls">
                        <div class="filter-buttons btn-group" role="group">
                            <button class="btn btn-sm btn-primary" data-range="today">Today</button>
                            <button class="btn btn-sm btn-outline-primary" data-range="week">This Week</button>
                            <button class="btn btn-sm btn-outline-primary" data-range="month">This Month</button>
                            <button class="btn btn-sm btn-outline-primary" data-range="year">This Year</button>
                            <button class="btn btn-sm btn-outline-secondary" data-range="all">All</button>
                        </div>

                        <div class="date-controls">
                            <input type="date" placeholder="Start Date" class="form-control form-control-sm datepicker" id="startDate">
                            <input type="date" class="form-control form-control-sm datepicker" placeholder="End Date" id="endDate">
                            <button id="applyDate" class="btn btn-sm btn-secondary">Apply</button>
                        </div>
                    </header>

                    <!-- Orders Overview -->
                    <div class="chart-card" data-aos="fade-down">
                        <div class="d-flex overview-header">
                            <h5 class="overview-title">Orders Overview </h5>

                            <div class="chart-controls" id="chartTypeOrders">
                                <!-- <label class="small-radio">Chart:</label>
                                <input type="radio" name="ordersChartType" value="bar" checked> Bar
                                <input type="radio" name="ordersChartType" value="line"> Line
                                <input type="radio" name="ordersChartType" value="pie"> Pie -->
                                <input type="radio" name="ordersChartType" value="doughnut" checked> Doughnut
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <canvas id="ordersChart" style="max-height:360px;"></canvas>
                            </div>
                            <div class="col-sm-6">
                                <div class="table-wrap table-responsive">
                                    <table class="table table-striped table-smS" id="ordersTable">
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
                        </div>



                    </div>

                    <!-- Payment Methods -->
                    <div class="chart-card" data-aos="fade-down">
                        <div class="d-flex overview-header">
                            <h5>Payment Methods </h5>
                            <div class="chart-controls" id="chartTypeMethods">
                                <!-- <label class="small-radio">Chart:</label>
                                <input type="radio" name="methodsChartType" value="bar" checked> Bar
                                <input type="radio" name="methodsChartType" value="line"> Line
                                <input type="radio" name="methodsChartType" value="pie"> Pie -->
                                <input type="radio" name="methodsChartType" value="doughnut" checked> Doughnut
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <canvas id="methodsChart" style="max-height:360px;"></canvas>
                            </div>
                            <div class="col-sm-6">
                                <div class="table-wrap table-responsive">
                                    <table class="table table-striped table-smA" id="methodsTable">
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
                        </div>


                    </div>

                    <!-- Products Breakdown -->
                    <div class="chart-card" data-aos="fade-down">
                        <div class="d-flex overview-header">
                            <h5>Products Overview</h5>
                            <div class="chart-controls" id="chartTypeProducts">
                                <!-- <label class="small-radio">Chart:</label>
                                <input type="radio" name="productsChartType" value="bar" checked> Bar
                                <input type="radio" name="productsChartType" value="pie"> Pie -->
                                <input type="radio" name="productsChartType" value="doughnut" checked> Doughnut
                                <!-- <input type="radio" name="productsChartType" value="line"> Line -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <canvas id="productsChart" style="max-height:360px;"></canvas>
                            </div>
                            <div class="col-sm-6">
                                <div class="table-wrap table-responsive">
                                    <table class="table table-striped table-smS" id="productsTable">
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
                        </div>


                    </div>
                    <!-- Customer Type Breakdown -->
                    <div class="chart-card mt-4" data-aos="fade-down">
                        <div class="d-flex overview-header">
                            <h5>Platform Overview</h5>
                            <div class="chart-controls" id="chartTypeCustomers">
                                <!-- <label class="small-radio">Chart:</label>
                                <input type="radio" name="customersChartType" value="bar" checked> Bar
                                <input type="radio" name="customersChartType" value="pie"> Pie -->
                                <input type="radio" name="customersChartType" value="doughnut" checked> Doughnut
                                <!-- <input type="radio" name="customersChartType" value="line"> Line -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <canvas id="customersChart" style="max-height:360px;"></canvas>
                            </div>
                            <div class="col-sm-6">
                                <div class="table-wrap table-responsive">
                                    <table class="table table-striped table-smS" id="customersTable">
                                        <thead>
                                            <tr>

                                                <th>Customer Type</th>
                                                <th>Visits</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
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