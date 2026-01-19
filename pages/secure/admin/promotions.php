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
                        <div class="welcome">Promotions Manager!</div>
                        <div class="center-mobile">Manage all the <? BRAND_NAME ?> promotions and special offers</div>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" id="addPromotionBtn"><i class="fa fa-plus"></i> New Promotion</button>
                        </div>
                    </div>                    

                    <section class="pizzasquare-table table-responsive" data-aos="fade-up">
                        <table id="promotions-table" class="table-sm">
                            <thead>
                                <tr>
                                    <th>S/N</th>                                  
                                    <th>Title</th>
                                    <th>Active Day</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody ></tbody>
                        </table>

                    </section>
                    <div id="pagination" class="p-4"></div>
                    <div id="no-data"></div>

                </div>


                <?php require "footer.php" ?>
            </section>
        </div>



    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/PromotionsPage.js"></script>

</body>

</html>