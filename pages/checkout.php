<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';



?>

<body class="theme-light" id="checkout" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
    <main class="py-4">
        <div class="container checkoutContainer">
            <div id="orderingStatus" class="p-3 bg-warning mb-4 rounded d-flex justify-content-center align-items-center d-none">
                <div class="text-dark fw-semibold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="orderingStatusText"></span> 
                </div>

            </div>
            <div class="row g-4">
                <!-- CART -->
                <div class="col-lg-7" id="cartSection">
                    <div class="cart-card p-3" data-aos="fade-up">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Your Cart</h5>
                            <button class="btn btn-sm btn-outline-error" id="clearCart"><i class="bi bi-trash-fill"></i> Clear</button>
                        </div>
                        <div class="table-responsive">
                          
                            <table class="table align-middle" id="cartTable">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Item</th>
                                        <th class="text-center" style="width:140px">Qty</th>
                                        <th class="text-end" style="width:120px">Price</th>
                                        <th class="text-end" style="width:120px">Total</th>
                                        <th style="width:60px"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    <!-- rows inserted by JS -->
                                </tbody>
                            </table>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group coupon-input">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" id="coupon" class="form-control" placeholder="Promo code (e.g. PIZZA10)">
                                    <button class="btn btn-sm btn-outline-secondary" id="applyCoupon">Apply</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end small text-muted">
                                Est. delivery time: <span id="eta">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- DELIVERY / PICKUP -->
                    <div class="cart-card p-3 mt-4" data-aos="fade-up" data-aos-delay="100">
                        <div>
                            <h5>Delivery Method</h5>
                            <p class="muted">Select your preferred order delivery method and our system will help you.</p>
                            <div class="toggle-container mt-2">
                                <!-- Radios for form submission -->
                                <div class="toggle-radios">
                                    <input type="radio" name="method" id="methodDelivery" value="Delivery" checked>
                                    <input type="radio" name="method" id="methodPickup" value="Pickup">
                                </div>

                                <!-- Fancy toggle -->
                                <div id="deliveryToggle" class="toggle-switch">
                                    <span>Delivery</span>
                                    <span>Pickup</span>
                                    <div class="toggle-slider"></div>
                                </div>
                            </div>
                        </div>

                        <div class="location-result mt-4">
                            <div id="areaDeliveryFee" class="bg-light p-1"></div>
                        </div>
                        <div id="deliveryFields" class="mt-3">
                            <div class="row g-3">                               
                                <div class="col-md-8">
                                    <label class="form-label small">Address</label>
                                    <input type="text" id="address" class="form-control" placeholder="Enter delivery address">
                                </div>
                                <div class="col-md-4" id="manual-delivery">                                 
                                </div>                              
                             

                                <div class="col-12">
                                    <label class="form-label small">Delivery Instructions</label>
                                    <input type="text" id="instructions" class="form-control" placeholder="Gate code, drop-off preference… (optional)">
                                </div>

                            </div>
                        </div>
                        <div id="pickupFields" class="mt-3" style="display:none">
                            <div class="row g-3">

                                <div class="list-group shadow-sm">
                                    <a href="https://share.google/U3qIHrqygnIODiNdn">
                                        <div class="list-group-item d-flex align-items-center">
                                            <i class="fa-solid fa-store me-3 text-primary"></i>
                                            <div><strong><?= COMPANY_ADDRESS ?>.</strong>
                                                <div class="text-muted small">Open 10:00–22:00</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CUSTOMER DETAILS -->
                    <div class="cart-card p-3 mt-4" data-aos="fade-up" data-aos-delay="150">
                        <h5>Customer Details</h5>
                        <span class="mb-3">Help us serve you better with a complete and correct information.</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Full Name</label>
                                <input type="text" id="name" value="<?= $user['fullname'] ??''  ?>" placeholder="John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Phone</label>
                                <input type="tel" id="phone" value="<?= $user['phone'] ??''  ?>" placeholder="<?= BRAND_PHONE ?>" >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Email</label>
                                <input type="email" id="email" value="<?= $user['email_address'] ?? '' ?>" placeholder="you@example.com" readonly>
                            </div>
                            <input type="hidden" name="userid" id="userid" value="<?= $userid; ?>">
                        </div>
                    </div>



                </div>

                <!-- SUMMARY -->
                <div class="col-lg-5">
                    <div class="summary cart-card p-3" data-aos="fade-left">
                        <h5>Order Summary</h5>
                        <div class="row mt-2">
                            <div class="col">Items</div>
                            <div class="col" id="sumItems">0</div>
                        </div>
                        <div class="row">
                            <div class="col">Subtotal</div>
                            <div class="col"><span id="subtotal">0</span></div>
                        </div>
                        <div class="row">
                            <div class="col">Vat (<span id="vatPercent"></span>%)</div>
                            <div class="col"><span id="tax">0</span></div>
                        </div>
                        <div class="row">
                            <div class="col">Delivery Fee</div>
                            <div class="col"><span id="deliveryFee">0</span></div>
                        </div>
                        <div class="row">
                            <div class="col">Discount</div>
                            <div class="col"><span id="discount">0</span></div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col fw-bold">Grand Total</div>
                            <div class="col fw-bold"><span id="grandTotal">0</span></div>
                        </div>
                        <button class="btn btn-primary btn-s w-100 mt-3 bounce-card" id="placeOrder"><i class="fa-solid fa-bag-shopping me-1"></i> Place Order</button>
                        <div class="text-center small text-muted mt-2">By placing order, you agree to our Terms.</div>
                    </div>
                </div>
            </div>
        </div>


    </main>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Placed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="successBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary bounce-card" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://js.paystack.co/v1/inline.js"></script>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/CartPage.js"></script>
</body>

</html>