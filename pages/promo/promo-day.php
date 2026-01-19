<?php


require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

$dayName = date("l"); 

?>

<body class="theme-light" id="dealsPage">
    <div class="container dealsWrapper">
        <div class="deals-header" data-aos="fade-down">
            <h2><span id="promo-title"></span> 🔥</h2>
            <p id="promo-description"></p>
        </div>

        <div class="row" id="promoDayDealsRow">
                    <!-- Dynamic Content -->          
        </div>
       

      
    </div>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/PromoDayPage.js"></script>

</body>

</html>