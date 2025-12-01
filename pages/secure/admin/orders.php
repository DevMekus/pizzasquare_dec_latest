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
                        <div class="welcome">Order Manager! <span id="orderCount"></span></div>
                        <div class="center-mobile">Here's a quick overview of your shop today.</div>
                    </div>
                    
                  <?php include ROOT_PATH . '/includes/order-page-ui.php' ?>
                    
                </div>

                <?php require "footer.php" ?>
            </section>
        </div>



    </section>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/OrderPage.js"></script>

</body>

</html>