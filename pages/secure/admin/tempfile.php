<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';
?>

<body id="ADMIN_SYSTEM" class="theme-light" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
    <div id="overlay"></div>
    <section id="adminLayout">
        <?php require "sidebar.php" ?>
        <div id="rightContent">
            <?php require "navbar.php" ?>
            <section class="inner-container">
                <div class="content-centered p-4"></div>

                <?php require "footer.php" ?>
            </section>
        </div>



    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/OverviewPage.js"></script>

</body>

</html>

<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';
?>

<body id="ADMIN_SYSTEM" class="theme-light" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
    <div id="overlay"></div>
    <section id="adminLayout">
        <?php require "sidebar.php" ?>
        <div id="rightContent">
            <?php require "navbar.php" ?>
            <section class="inner-container">
                <div class="content-centered p-4">
                    <div data-aos="fade-down" class="page-header mt-4">
                        <div class="welcome">Reports Manager! <span id="orderCount"></span></div>
                        <div>Here's a quick overview of your shop today.</div>
                    </div>
                    <div class="controls d-flex flex-wrap flex-md-nowrap justify-content-between align-items mb-3">
                        <div class="filter-buttons">
                            <button class="btn btn-sm btn-primary" data-range="today">Today</button>
                            <button class="btn btn-sm btn-outline-primary" data-range="week">This Week</button>
                            <button class="btn btn-sm btn-outline-primary" data-range="month">This Month</button>
                            <button class="btn btn-sm btn-outline-primary" data-range="year">This Year</button>
                        </div>
                        <div class="chip-con">


                            <input type="date" class="form-control datepicker mb-2" placeholder="Start Date" id="startDate">

                            <input type="date" class="form-control datepicker" placeholder="End Date" id="endDate">

                        </div>

                    </div>

                    <section class="container mt-4">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card mb-4">
                                    <h2>Revenue Over Time Chart</h2>
                                    <div class="canvas-container">
                                        <canvas class="reportChart" id="revenueChart"></canvas>
                                    </div>
                                </div>
                                <div class="card mb-4">
                                    <h2>Revenue</h2>
                                    <table id="revenueTable"></table>
                                    <div id="revenueTablePagination"></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card mb-4">
                                    <h2>Orders Trend</h2>
                                    <div class="canvas-container">
                                        <canvas class="reportChart" id="ordersChart"></canvas>
                                    </div>
                                </div>
                                <div class="card mb-4">
                                    <h2>Order Count</h2>
                                    <table id="ordersTable"></table>
                                    <div id="ordersTablePagination"></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card mb-4">
                                    <h2>Top Selling Dishes</h2>
                                    <div class="canvas-container">
                                        <canvas class="reportChart" id="topDishesChart"></canvas>
                                    </div>
                                </div>
                                <div class="card mb-4">
                                    <h2>Top Dish</h2>
                                    <table id="topDishesTable"></table>
                                    <div id="topDishesPagination"></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card mb-4">
                                    <h2>Payment Methods</h2>
                                    <div class="canvas-container">
                                        <canvas class="reportChart" id="paymentsChart"></canvas>
                                    </div>
                                </div>
                                <div class="card mb-4">
                                    <h2>Payment method</h2>
                                    <table id="paymentsTable"></table>
                                    <div id="paymentsTablePagination"></div>
                                </div>
                            </div>
                        </div>





                    </section>

                </div>

                <?php require "footer.php" ?>
            </section>
        </div>



    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/ReportPage.js"></script>
</body>

</html>