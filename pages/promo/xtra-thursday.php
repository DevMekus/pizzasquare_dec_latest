<?php


require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';



?>

<body class="theme-light" id="dealsPage">
    <div class="container dealsWrapper">
        <div class="deals-header" data-aos="fade-down">
            <h2>Xtra Thursday 🔥</h2>
            <p>Order any Xtra Large (XL) Pizza and get a Medium (M) Pizza Free!! Added automatically to your order.</p>
        </div>

       

        <div class="row" id="xtraThursdayDealsRow">
            <!-- Dynamic Content -->          
        </div>
    </div>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/XtraThursdayPage.js"></script>

</body>

</html>