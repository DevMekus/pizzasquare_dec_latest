<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

?>

<body class="theme-light" id="dealsPage">
    <div class="container dealsWrapper">
        <div class="deals-header" data-aos="fade-down">
            <h2>What's New? 🔥</h2>
            <p>Deals, Promotions, Updates and more!</p>
        </div>

        <!-- Category Filter -->
        <!-- <div class="filter-buttons" id="filterBtn" data-aos="fade-up">
            <button class="btn active" data-category="all">All</button>
            <button class="btn" data-category="pizza">Pizza</button>
            <button class="btn" data-category="shawarma">Shawarma</button>
            <button class="btn" data-category="dessert">Dessert</button>
            <button class="btn" data-category="drink">Drinks</button>
        </div> -->

        <div class="row" id="dealsRow">
            <!-- Deals will be injected here -->
        </div>
    </div>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/DealPage.js"></script>

</body>

</html>