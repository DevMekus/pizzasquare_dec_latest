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
                        <div class="welcome">Promotion Manager!</div>
                        <div class="center-mobile">Manage all the <? BRAND_NAME ?> promotionals</div>
                        <div class="actions">
                            <button class="btn btn-primary" id="addDealBtn"><i class="fa fa-plus"></i> New Deal</button>
                        </div>
                    </div>

                    <!-- <section id="summary-cards" class="summary-cards" data-aos="fade-down">
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-default"><i class="bi bi-gift fs-2"></i></div>
                            <p>Total Deals</p>
                            <h2 id="dealTotal">0</h2>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-success"><i class="bi bi-check-lg fs-2"></i></div>
                            <p>Active</p>
                            <h2 id="dealActive">0</h2>
                        </div>
                    </section> -->

                    <section class="pizzasquare-table table-responsive" data-aos="fade-up">
                        <table id="dealsTable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Id</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/DealPage.js"></script>

</body>

</html>