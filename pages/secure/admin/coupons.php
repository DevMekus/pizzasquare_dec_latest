<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='admin')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
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
                        <div class="welcome">Discount & Coupon Manager!</div>
                        <div class="center-mobile">Manage all the product discounts and business VAT</div>
                        <div class="actions">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#couponModal">
                                New Coupon
                            </button>
                        </div>
                    </div>

                    <div>
                        <h5>Coupons & Discounts</h5>
                        <section class="pizzasquare-table table-responsive" data-aos="fade-up">
                            <table id="couponTable">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Coupon</th>
                                        <th>Discount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </section>
                        <div id="pagination" class="p-4"></div>
                        <div id="no-data"></div>
                    </div>
                </div>

                <?php require "footer.php" ?>
            </section>
        </div>



    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/CouponPage.js"></script>

    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Coupon and Discounts</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="newCoupon">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="coupon" class="form-label">Coupon Code</label>
                            <input type="text" class="form-control" name="coupon" id="coupon" placeholder="Eg: PIZZA10" required>
                        </div>
                        <div class="mb-3">
                            <label for="discount" class="form-label">Coupon Discount (%)</label>
                            <input type="text" class="form-control" name="discount" id="discount" placeholder="Eg: 10" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>