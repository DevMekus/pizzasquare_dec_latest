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
                        <div class="welcome">Manage  Sizes!</div>
                        <div class="center-mobile">Manage all the product sizes</div>
                        <div class="actions">
                               <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#sizeModal" id="addSizeBtn">
                                        Add New Size
                                    </button>
                        </div>
                    </div>
                    <div class="mt-4">
                          <section class="sizes" ata-aos="fade-up">
                            <h5>Sizes</h5>                                  
                            <div class="pizzasquare-tables table-responsive">
                                    <table class="" id="sizesTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Code</th>         
                                            <th>Ordering</th>         
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                            <div id="pagination" class="p-4 pagination"></div>
                            <div id="no-size-data"></div>
                        </section>
                    </div>
                </div>
                <?php require "footer.php" ?>
            </section>
        </div>
    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/SizePage.js"></script>

    <!-- Size Modal -->
<div class="modal fade" id="sizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="sizeForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="sizeModalTitle">Add New Size</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sizeId">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="category_id" required>                           
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Size Name</label>
                        <input type="text" name="label" placeholder="Eg: M, L, XL" class="form-control" id="sizeName" required>
                    </div>
                   
                    <div class="mb-3">
                        <label for="ordering" class="form-label">Ordering</label>
                        <input type="number" name="ordering" class="form-control" id="ordering" placeholder="Eg: 1 or 2 or 3" required>
                        <small class="text-muted">Unique arrangement sequence (e.g., "m", "l", "xl")</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveSizeBtn">Save Size</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>

</html>