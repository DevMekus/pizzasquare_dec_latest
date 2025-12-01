 <!-- Header -->
 <?php

    use App\Utils\Utility;

    $current = Utility::currentRoute();
    $parts = explode("/", trim($current, "/"));
    ?>

 <nav class="navbar navbar-expand-lg pos-header sticky-top shadow-sm">
     <div class="container">
         <a class="navbar-brand brand" href="<?= BASE_URL ?>secure/pos/overview">
             <img src="<?= BASE_URL ?>assets/images/logo_color.png" alt="Pizzasquare" />
             <div class="d-none d-lg-block titleTop">
                 <div class="brand-title">Point of Sale</div>
                 <div class="small muted motto">Fast • Reliable • Delightful</div>
             </div>

         </a>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse" id="navbars">
             <ul class="navbar-nav me-auto mb-2 mb-lg-0">
             </ul>
             <div class="d-flex flex-wrap flex-md-nowrap gap-3 justify-content-between align-items-end mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="enableBtn" >
                    <label class="form-check-label" for="checkDefault">
                         Enable Sound
                    </label>
                </div>
                
                 <a href="<?= BASE_URL ?>secure/pos/orders" class="icon-btn" aria-label="Notifications">
                     <div class="me-2 position-relative">
                         <i class="bi bi-cart"></i>
                         <span
                             class="badge bg-danger position-absolute"
                             style="top: -6px; right: -6px; font-size: 0.65rem"
                             id="orderAlert">0</span>
                     </div>
                 </a>
                 <span class="badge cashier-badge rounded-pill px-3 py-2">
                     <i class="fa-solid fa-user me-1"></i><span id="cashierName"> <?= Utility::truncateText(!empty($user['fullname']) ? ucfirst($user['fullname']) : ucfirst($role), 15); ?>: <span class="color-success"><?= strtoupper($user['role']) ?></span></span>
                 </span>
                 <span class="muted motto small" id="clock"></span>
                 <button id="themeToggle" class="btn btn-outline-primary btn-sm" aria-pressed="false" title="Toggle theme"><i class="bi bi-moon-stars"></i></button>

                 <button class="btn btn-outline-error btn-sm logout" data-id="<?= $userid; ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
             </div>


         </div>
     </div>
 </nav>