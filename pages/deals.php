<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

?>

<body class="theme-light" id="dealsPage">
    <div class="container dealsWrapper">
        <div class="deals-header" data-aos="fade-down">
            <h2>Hot Deals 🔥</h2>
            <p>Deals, Promotions, Combos and more!</p>
        </div>

      

        <div class="row" id="pizzaSquareDealsRow">
            <!-- Deals will be injected here -->
             <p class="text-center">Coming Soon...</p>
        </div>
    </div>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/PromotionsPage.js"></script>

</body>

</html>