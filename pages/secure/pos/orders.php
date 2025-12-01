<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='cashier')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
?>

<body id="POS_SYSTEM" class="theme-light ORDERSPAGE" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
    <?php require "navbar.php" ?>
    <main class="container">
        <div data-aos="fade-down" class="page-header mt-4">
            <div class="welcome">Order Manager!</div>
            <div class="center-mobile">Here's a quick overview of your shop today.</div>
        </div>
            
        <?php include ROOT_PATH . '/includes/order-page-ui.php' ?>

       
    </main>
    <?php require "footer.php" ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/PosPage.js"></script>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/OrderPage.js"></script>
</body>

</html>