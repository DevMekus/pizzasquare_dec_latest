<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

?>

<body class="theme-light" id="dealsPage">
    <div class="container dealsWrapper">
        <div class="deals-header" data-aos="fade-down">
            <h2>What's New? 🔥</h2>
            <p>News, Gists, Updates and more!</p>
        </div>

        <div class="row" id="updateRow">
            <!-- Updates will be injected here -->
        </div>
    </div>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/UpdatesPage.js"></script>

</body>

</html>