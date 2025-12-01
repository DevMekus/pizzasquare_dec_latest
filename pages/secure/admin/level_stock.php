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
                            <div class="welcome">Stock Management</div>
                            <div class="center-mobile">Manage product stocks.</div>
                        <div class="actions mt-2 mb-3" data-aos="fade-down">                               
                        </div>
                    </div>

                        <!-- Category Selector -->
                        <div class="kpi-card-n mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Category Shared Stock Levels</h6>
                            </div>

                            <div class="mt-4">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Select Category</label>
                                        <select id="category_id" class="form-selects">
                                            <option value="">-- Select Category --</option>
                                            <!-- Loaded by JS -->
                                        </select>
                                    </div>
                                </div>

                                <!-- Shared Stock Section -->
                                <!-- <h6 class="fw-bold mt-3">Category Shared Sizes Stock</h6> -->
                                <table class="table table-bordered align-middle" id="categoryStockTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Size</th>
                                            <th>Current Stock</th>
                                            <th>Low Stock Alert</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loaded dynamically -->
                                    </tbody>
                                </table>

                            </div>

                          
                        </div>
                        <div  class="kpi-card-n mb-4">
                            <div>   
                                <!-- Product Individual Stock -->
                                <h6 class="fw-bold mt-3">Products Using Individual Stock</h6>
                                <table class="table table-bordered align-middle" id="productStockTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Size</th>
                                            <th>Current Stock</th>
                                            <th>Low Stock Alert</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loaded dynamically -->
                                    </tbody>
                                </table>

                            </div>
                        </div>
            
                </div>
                
            <?php require "footer.php" ?>
            </section>
        </div>



    </section>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/CategoryLevelStockPage.js"></script>


</body>

</html>