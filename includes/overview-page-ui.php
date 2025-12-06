   <section class="inner-container">
        <div class="content-centered p-4">
            <div data-aos="fade-down" class="page-header">
                <div class="welcome">Welcome back, <?= $user['fullname'] ?>!</div>
                <div>Here's a quick overview of your shop today.  </div>
            </div>
            <div class="controls mt-3 mb-3" data-aos="fade-down">
                <div class="search">
                    <input id="globalSearch" placeholder="Search recent orders..." />
                </div>

            </div>
            <div class="row">
                <div class="col-sm-4">
                    <a href="<?= $user['role']=='admin'? 'reports': '#' ?>" class="" data-aos="fade-up">
                        <div class="kpi-card-n">                           
                            <div class="kpi-card-info">                                
                                <h3 class="num loading" style="color: #d51d28;" id="kpiOrdersNum"></h3>
                                <div class="muted">Today's Order</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="orders" class="" data-aos="fade-up">
                        <div class="kpi-card-n">                            
                            <div class="kpi-card-info">                                
                                <h3 class="num loading" style="color: #d51d28;" id="kpiRevenueNum"></h3>
                                <div class="muted">Revenue Today</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-4">
                    <a href="orders" class="" data-aos="fade-up">
                        <div class="kpi-card-n">                            
                            <div class="kpi-card-info">                                
                                <h3 class="num loading" style="color: #d51d28;" id="kpiNewCustomersNum"></h3>
                                <div class="muted">New Customers</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-sm-6">
                     <!-- Orders list -->
                    <div class="kpi-card-n orders bounce-card" data-aos="fade-up">
                        <div class="orders-header">
                            <h5>Recent Orders</h5>
                            <div class="orders-actions">
                                <select id="orderFilter">
                                    <option value="all">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="preparing">Preparing</option>
                                    <option value="onroute">On Route</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button class="btn btn-ghost" id="refreshOrders">Refresh</button>
                            </div>
                        </div>


                        <div style="overflow:auto;max-height:360px">
                            <table class="table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>                                       
                                        <th>Total</th>
                                         <!-- <th>Items</th> -->
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
                </div>
                <div class="col-sm-6">
                      <!-- Analytics -->
                    <div class="kpi-card-n analytics bounce-card" data-aos="fade-left">
                        <h5 style="margin-top:0">Top Dishes</h5>
                        <div id="topDishes" class="list"></div>
                    </div>
                </div>
            </div>

             <div class="row mt-4">
                <div class="col-sm-6">
                      <!-- Inventory -->
                    <div class="kpi-card-n half bounce-card" data-aos="fade-up">
                        <h5 style="margin-top:0">Inventory Snapshot</h5>
                        <div id="inventoryList" class="list"></div>
                        <div id="pagination" class="pagination p3"></div>
                        <div id="no-data"></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="kpi-card-n half bounce-card" data-aos="fade-up">
                        <h5 style="margin-top:0">Active Promotions</h5>
                        <div id="promos" class="list"></div>
                    </div>
                </div>
             </div>
          
        </div>

        <?php require "footer.php" ?>
    </section>