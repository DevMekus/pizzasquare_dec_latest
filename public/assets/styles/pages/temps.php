<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';
?>

<body id="ADMIN_SYSTEM" class="theme-light">
    <?php require "navbar.php" ?>
    <main class="admin-wrap">
        <?php require "sidebar.php" ?>
        <section class="inner-container">
            <div class="content-centered p-4">
                <div data-aos="fade-down" class="page-header">
                    <div class="welcome">Welcome back, <?= $user['fullname'] ?>!</div>
                    <div>Here's a quick overview of your shop today.</div>
                </div>
                <div class="controls mt-3 mb-3">
                    <div class="search">
                        <input id="globalSearch" placeholder="Search orders, customers, dishes..." />
                    </div>

                </div>
                <section class="grid">
                    <!-- KPIs -->
                    <div class="card kpi bounce-card" data-aos="zoom-in" id="kpiOrders">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <div class="muted">Total Orders</div>
                                <div class="num" id="kpiOrdersNum">0</div>
                            </div>
                            <div style="text-align:right;color:var(--muted)">+<span id="kpiOrdersTrend">0</span>%</div>
                        </div>
                    </div>

                    <div class="card kpi bounce-card" data-aos="zoom-in" id="kpiRevenue">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <div class="muted">Revenue Today</div>
                                <div class="num" id="kpiRevenueNum">$0</div>
                            </div>
                            <div style="text-align:right;color:var(--muted)">vs yesterday</div>
                        </div>
                    </div>

                    <div class="card kpi bounce-card" data-aos="zoom-in" id="kpiActive">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <div class="muted">Active Orders</div>
                                <div class="num" id="kpiActiveNum">0</div>
                            </div>
                            <div style="text-align:right;color:var(--muted)">Live</div>
                        </div>
                    </div>

                    <div class="card kpi bounce-card" data-aos="zoom-in" id="kpiCSat">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <div class="muted">Avg Rating</div>
                                <div class="num" id="kpiCSatNum">0 <div class="icon-box bg-primary">⭐</div>
                                </div>
                            </div>
                            <div style="text-align:right;color:var(--muted)">Customers</div>
                        </div>
                    </div>

                    <!-- Orders list -->
                    <div class="card orders bounce-card" data-aos="fade-up">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                            <h4 style="margin:0">Recent Orders</h4>
                            <div style="display:flex;gap:8px">
                                <select id="orderFilter">
                                    <option value="all">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="preparing">Preparing</option>
                                    <option value="onroute">On Route</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button class="btn" id="refreshOrders">Refresh</button>
                            </div>
                        </div>

                        <div style="overflow:auto;max-height:360px">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="ordersTbody"></tbody>
                            </table>
                            <button id="moreOrdersBtn" class="btn btn-ghost" style="display:none;" onclick="window.location.href='orders'">
                                See All Orders
                            </button>
                        </div>
                    </div>

                    <!-- Analytics -->
                    <div class="card analytics bounce-card" data-aos="fade-left">
                        <h4 style="margin-top:0">Top Dishes</h4>
                        <div id="topDishes" class="list"></div>
                    </div>

                    <!-- Inventory -->
                    <div class="card half bounce-card" data-aos="fade-up">
                        <h4 style="margin-top:0">Inventory Snapshot</h4>
                        <div id="inventoryList" class="list"></div>
                    </div>

                    <!-- Promotions -->
                    <div class="card half bounce-card" data-aos="fade-up">
                        <h4 style="margin-top:0">Active Promotions</h4>
                        <div id="promos" class="list"></div>
                    </div>


                </section>
            </div>

            <?php require "footer.php" ?>
        </section>

    </main>


    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/OverviewPage.js"></script>

</body>

</html>