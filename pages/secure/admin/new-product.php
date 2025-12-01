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
                        <div class="welcome">Create New Product!</div>
                        <div class="center-mobile">Create a new product and assign size to it</div>
                       
                    </div>

                    <section class="">
                        <div class="row">
                            <div class="col-sm-6">                             
                                <form id="newProductForm" class="kpi-card-n border-secondary-2x" enctype="multipart/form-data">
                                    <p class="muted">Fill out the product form below to get started!</p>
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-selects" name="category_id" id="category_id" required>                           
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" id="productName" name="name" class="form-control" placeholder="Enter product name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Product Description (Optional)</label>
                                        <textarea id="productDescription" name="description" class="form-controll" rows="2" placeholder="Enter product description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Product Image</label>
                                        <input type="file" id="productImage" name="productImage" accept="image/*" class="form-control" required>
                                    </div>                                       

                                    <button type="submit" id="createProduct" class="btn btn-primary mt-3">Create Product</button>
                                </form>


                            
                            </div>
                            <div class="col-sm-6">
                                <div style="display: none;" id="assignSizesCard" class="kpi-card-n">
                                    <h5 class="card-header">Assign Sizes to Product</h5>
                                     <form id="assignSizesForm">
                                        <div class="mb-3">
                                            <label class="form-label">Select Product</label>
                                            <select id="productSelect" class="form-select">                                                
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Select Sizes</label>
                                            <select id="sizeSelect" class="form-select" multiple>                               
                                            </select>
                                            <small class="text-muted">Hold CTRL to select multiple sizes.</small>
                                        </div>

                                        <div id="selectedSizesContainer"></div>

                                        <button id="saveSizes" class="btn btn-secondary btn-sm mt-3">Save Sizes</button>
                                    </form>
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
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/ProductPage.js"></script>
</body>

</html>