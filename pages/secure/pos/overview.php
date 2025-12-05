<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='cashier')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
?>

<body id="POS_SYSTEM" class="theme-light" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
    <?php require "navbar.php" ?>
    <main class="container-fluid pos-wrap">
        <!-- Catalog -->
        <section class="catalog" aria-label="Product catalog">
            <div id="topFilter" class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div class="input-group" style="max-width:420px">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i>
                    </span>
                    <input id="searchInput" type="search" class="form-control" placeholder="Search items (BBQ Chicken Xtra, Chicken Shawarma, Hollandia)…" />
                </div>
                <div class="d-flex flex-wrap gap-2" id="categoryChips">
                </div>
            </div>

            <div class="row g-3" id="catalogGrid">
            </div>

        </section>

        <!-- Cart + Checkout -->
        <aside>
            <section class="pos-card mb-3 p-4" id="cartSummarySection" aria-label="Current order">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Customer Order <sup>
                            <span id="cartCount"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                0
                            </span></sup></h5>

                    <button class="btn btn-sm btn-outline-error" id="clearCart"><i class="bi bi-trash-fill"></i> Clear</button>
                </div>
                <div id="cartItems">
                    <div class="text-center text-muted py-4" id="cartBody">Your cart is empty</div>
                </div>
                <hr />
                <div class="totals">
                    <div class="row mb-1">
                        <div class="col">Items</div>
                        <div class="col text-end"><span id="sumItems">0</span></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col">Subtotal</div>
                        <div class="col text-end"><span id="subtotal">0</span></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col">Tax <span id="tax_val"></span></div>
                        <div class="col text-end"><span id="tax">0</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">Delivery</div>
                        <div class="col text-end"><span id="deliveryFee">0</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">Discount</div>
                        <div class="col text-end"><span id="discount">0</span></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col"><strong>Total</strong></div>
                        <div class="col text-end"><strong><span id="grandTotal">0</span></strong></div>
                    </div>
                </div>

                 <input id="attendant" type="hidden" class="form-control" value="<?= $user['fullname']; ?>" />

                <div class="input-group mt-3">
                    <span class="input-group-text bg-white"><i class="bi bi-tags-fill"></i></span>
                    <input id="coupon" type="text" class="form-control" placeholder="Promo code (e.g. PIZZA10)" />
                    <button id="applyCoupon" class="btn btn-outline-primary">Apply</button>
                </div>
             
            </section>
            <section class="pos-card mb-3 p-4 bg-light" id="customerSection" aria-label="customerSection">
                <h5 class="mb-0">Customer Order Details</h5>
                <div class="mt-3 d-grid gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person-fill"></i></span>
                        <input id="name" type="text" class="form-control" placeholder="Enter Customer Fullname" required/>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-phone-fill"></i></span>
                        <input id="phone" type="text" class="form-control" placeholder="Enter Customer Phone" required/>
                    </div>                   
                </div>
            </section>
            <section class="pos-card mb-3 p-4" id="paymentSection" aria-label="paymentSection">
                <h5 class="mb-0">Payment Information</h5>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" value="" id="splitPaymentCheck"/>
                    <label class="form-check-label" for="splitPaymentCheck">
                        Split payment used
                    </label>
                </div>
                <div class="mt-3 d-grid gap-2">
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="bi bi-cash-stack"></i></span>
                        <input id="cashAmount" type="number" class="form-control" placeholder="CASH PAYMENT" required/>
                    </div>

                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="bi bi-credit-card-2-front"></i></span>
                        <input id="cardAmount" type="number" class="form-control" placeholder="CARD PAYMENT" required/>
                    </div>

                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="bi bi-bank"></i></span>
                        <input id="transferAmount" type="number" class="form-control" placeholder="TRANSFER PAYMENT" required/>
                    </div>

                    
                    <div class="d-grid">
                        <button id="confirmOrder" class="btn btn-primary btn-lg"><i class="fa-solid fa-receipt me-1"></i> Proceed with Order</button>                        
                    </div>
                </div>
            </section>

        </aside>
    </main>
    <?php require "footer.php" ?>
    <?php require_once ROOT_PATH . '/includes/receipt-modal.php'; ?>


    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/PosPage.js"></script>

</body>

</html>