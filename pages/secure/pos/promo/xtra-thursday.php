<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='cashier')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
?>

<body id="POS_SYSTEM" class="theme-light" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
      <?php require_once ROOT_PATH . '/pages/secure/pos/navbar.php'; ?>
        <div class="container dealsWrapper mt-4">
            <div class="deals-header" data-aos="fade-down">
                <h2>Xtra Thursday 🔥</h2>
                <p>Order any Xtra Large (XL) Pizza and get a Medium (M) Pizza Free!! Added automatically to your order.</p>
            </div>

          

            <div class="row" id="xtraThursdayDealsRow">
                <!-- Dynamic Content -->          
            </div>
        </div>
      <?php require_once ROOT_PATH . '/pages/secure/pos/footer.php'; ?>


    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/XtraThursdayPage.js"></script>

</body>

</html>