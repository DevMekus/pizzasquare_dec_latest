<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';

if (!isset($_SESSION['userid'])) {
    $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
    header('location: ' . BASE_URL . 'auth/login?f-bk=delete-account');
    exit;
} else {
    require_once ROOT_PATH . '/includes/reuse.php';
}

require_once ROOT_PATH . '/includes/navbar.php';

?>

<body class="theme-light" id="dealsPage" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
    <div class="container dealsWrapper">
        <div class="deals-header" data-aos="fade-down">
            <h2>Delete Account ?</h2>
            <p>Are you sure you want to delete your account? This action cannot be undone.</p>
        </div>

      

        <div class="row p-4" id="deleteAccountRow">
            <p>
               You are about to delete your account. Please confirm your decision by clicking the button below. <br/>This action is irreversible and all your data will be permanently removed from our system. If you have any concerns or questions, please contact our support team before proceeding.
            </p>  
          <div class="bg-light p-4 rounded" id="deleteAccountFormWrapper">
           
           <form id="deleteAccountForm" class="row g-3">
              <div class="col-sm-6">
                  <input type="password" id="confirmPassword" placeholder="Enter your password to confirm" class="p-3" required>
              </div>
              <div class="col-sm-6">
                <input type="checkbox" id="confirmDeleteCheckbox">
                <label for="confirmDeleteCheckbox">I understand that this action cannot be undone.</label><br>
                <button type="submit" id="deleteAccountButton" class="btn btn-primary" disabled>Delete Account</button>
              </div>
           </form>
          </div>        
        </div>
    </div>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/UserPage.js"></script>

</body>

</html>