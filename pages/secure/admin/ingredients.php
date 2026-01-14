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
                <div class="content-centered">
                    <div data-aos="fade-down" class="page-header">
                        <div class="welcome">Ingredients Manager!</div>
                        <div class="center-mobile">
                            Manage all the product ingredients and toppings
                        </div>
                        <div class="actions">
                           
                        </div>
                    </div>

                    <section class="row">
                        <div class="col-sm-5">
                             <section class="pizzasquare-table table-responsive" data-aos="fade-up">
                                <div class="w-100 d-flex justify-content-between align-items-center">
                                    <h5>Ingredients</h5>
                                     <button class="btn btn-default btn-xs" id="addIngredient"><i class="fa fa-plus"></i> Add Ingredient</button>
                                </div>
                                <table id="ingredientsTable" class="table-sm">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Category</th>
                                            <th>Ingredient</th> 
                                            <th><i class="fa fa-cogs"></i></th>                                   
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div id="listPagination" class="p-4 pagination"></div>
                             </section>
                        </div>
                        <div class="col-sm-7">
                            <section class="pizzasquare-table table-responsive" data-aos="fade-up">
                                <div class="w-100 d-flex justify-content-between align-items-center">
                                    <h5>product Ingredients</h5>
                                    <div id="productSelectionDom" class="w-50"></div>
                                     <button class="btn btn-default btn-xs" id="assignIngredient"><i class="fa fa-plus"></i> Assign Ingredient</button>
                                </div>
                                <table id="assignedIngredientsTable" class="table-sm mt-2">
                                    <thead>
                                        <tr>
                                            <th>#</th>                                           
                                            <th>Ingredient</th> 
                                           <th><i class="fa fa-cogs"></i></th>                                               
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div id="listProductPagination" class="p-4 pagination"></div>
                            </section>
                        </div>
                    </section>
                   
                </div>

                <?php require "footer.php" ?>
            </section>
        </div>
    </section>

    <div class="modal fade" id="ingredientModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="ingredientModalTitle">Ingredient Manager</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                     <form id="ingredientForm">
                        <div class="modal-body">                    
                            <div class="form-group">
                                <input type="text" class="form-control" id="ingredientNameInput" name="ingredient_name" placeholder="Enter Ingredient Name" required />                             
                            </div>
                            <div class="form-group">
                                <label>Select Product Category</label>
                                <select id="category_id" name="category_id"></select>                         
                            </div>
                            <div class="formInfo"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit_btn" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/IngredientsPage.js"></script>
</body>

</html>