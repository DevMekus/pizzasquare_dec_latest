<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

$orderId = $_GET['search'] ?? null;

?>

<body class="theme-light" id="track-page">
    <section class="tracking-page">
        <div class="tracking-section">
            <section class="track-header" data-aos="fade-down">
                <h2>
                    <span class="bounce-card"> 🔎</span>
                    <span class="bounce-card">Track</span>
                    <span class="bounce-card">Your Order!</span>
                </h2>
                <p>We're tracking your order progress. Enter your order ID to get more information.</p>
            </section>

            <form id="trackForm" class="track-form mt-5" data-aos="zoom-in">
                <input
                    type="text"
                    id="orderId"
                    name="orderId"
                    value="<?= isset($orderId) ? $orderId : ""; ?>"
                    placeholder="Enter your Order ID"
                    required />
                <button type="submit" id="trackBtn">Track</button>
            </form>

            <section id="order-details"></section>
        </div>
    </section>

    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/TrackPage.js"></script>

</body>

</html>