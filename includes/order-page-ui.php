       <section class="summary-cards" id="orderSummary" data-aos="fade-up">
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-default">
                                <i class="bi bi-cart-plus fs-2"></i>
                            </div>
                            <h2 id="newOrders"></h2>
                            <p>New Orders Today</p>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-default"><i class="bi bi-egg-fried fs-2"></i></div>
                            <h2 id="preparingOrders"></h2>
                            <p>Preparing</p>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-default"><i class="bi bi-truck fs-2"></i></div>
                            <h2 id="outOrders"></h2>
                            <p>Out for Delivery</p>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-success"><i class="bi bi-check-lg fs-2"></i></div>
                            <h2 id="deliveredOrders"></h2>
                            <p>Completed</p>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-primary">
                                <i class="bi bi-x-lg fs-2"></i>
                            </div>
                            <h2 id="cancelledOrders"></h2>
                            <p>Cancelled</p>
                        </div>
                    </section>

                    <section class="mt-4">
                        <div class="row">
                             <div class="col-sm-4">
                                <div class="summary-cards">
                                    <div class="kpi-card">
                                        <h5 class="text-center fw-bold">Order Summary</h5>
                                        <ul class="list-unstyled mb-2 small">
                                            <li class="d-flex justify-content-between border-bottom py-1">
                                                <strong>TRANSFERS:</strong> <span id="transferOrderAmt">N 0.00</span>
                                            </li>
                                            <li class="d-flex justify-content-between border-bottom py-1">
                                                <strong>CASH:</strong> <span id="cashOrderAmt">N 0.00</span>
                                            </li>
                                            <li class="d-flex justify-content-between border-bottom py-1">
                                                <strong>CARD:</strong> <span id="cardOrderAmt">N 0.00</span>
                                            </li>
                                            <li class="d-flex justify-content-between border-bottom py-1">
                                                <strong>ONLINE:</strong> <span id="onlineOrderAmt"></span>
                                            </li>
                                          
                                            <li class="d-flex justify-content-between border-bottom py-1">
                                                <strong class="color-danger">Subtotal:</strong> <span id="Subtotal" class="color-danger fw-bold"></span>
                                            </li>
                                            <li class="d-flex justify-content-between border-bottom py-1">
                                                <strong><em>(+) Delivery fees</em>:</strong> <span id="deliveryFeeAmt"></span>
                                            </li>
                                             <li class="d-flex justify-content-between border-bottom py-1">
                                                <strong class="color-success">Grand Total:</strong> <span id="totalAmtToday" class="color-success fw-bold"></span>
                                            </li>
                                            
                                        </ul>
                                        
                                        <div class="d-flex justify-content-end w-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="checkToday" checked>
                                                <label class="form-check-label fw-bold color-success" for="checkToday">
                                                    Todays order (<?= Date('Y-m-d') ?>)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="filter-bar d-flex mb-2 gap-3 w-100" data-aos="fade-right"> 
                                        <input type="text" id="search" placeholder="Search orders..." class="form-control" />
                                        <select id="statusFilter" class="form-select">
                                            <option value="all">All Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="preparing">Preparing</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                        <?php if ($user['role'] == 'admin'): ?>
                                        <button id="exportCsv" class="btn btn-sm btn-ghost">
                                            <i class="fa-solid fa-file-csv"></i> Export
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="pizzasquare-table table-sm table-responsive" data-aos="fade-up">
                                        <table id="ordersTable">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>          
                                                    <th>Type</th>
                                                    <th>Amount</th>         
                                                    <th>Status</th>
                                                    <th>Delivery</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div id="pagination" class="p-4 pagination"></div>
                                    <div id="no-data"></div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php require_once ROOT_PATH . '/includes/receipt-modal.php'; ?>