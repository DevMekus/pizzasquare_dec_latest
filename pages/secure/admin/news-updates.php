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
                        <div class="welcome">News & Updates Manager!</div>
                        <div class="center-mobile">Manage all the <? BRAND_NAME ?> news, updates and public announcements</div>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" id="addUpdateBtn"><i class="fa fa-plus"></i> New Update</button>
                        </div>
                    </div>                    

                    <section class="pizzasquare-table table-responsive" data-aos="fade-up">
                        <table id="update-table" class="table-sm">
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
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/UpdatesPage.js"></script>

</body>

</html>