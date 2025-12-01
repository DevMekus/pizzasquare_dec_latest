 <!-- Header -->
 <?php

    use App\Utils\Utility;
    ?>


 <nav class="navbar navbar-expand-lg admin-nav sticky-top shadow-sm">
     <div class="container-fluid">

         <!-- Sidebar toggle: left, visible on mobile -->
         <button id="menuBtn" class="btn sidebar-toggle d-lg-none" aria-label="Toggle sidebar">
             <i class="bi bi-list"></i> <!-- Better & cleaner icon -->
         </button>

         <!-- Brand / Page title on mobile -->
         <span class="admin-brand d-lg-none">
             Admin Panel
         </span>

         <!-- Navbar toggle: right on mobile -->
         <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbars"
             aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
             <i class="bi bi-three-dots-vertical"></i>
         </button>

         <div class="collapse navbar-collapse" id="navbars">

             <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>

             <div class="d-flex align-items-center gap-3 flex-wrap flex-sm-nowrap justify-content-end admin-nav-actions">

                 <span class="badge cashier-badge rounded-pill px-3 py-2">
                     <i class="fa-solid fa-user me-1"></i>
                     Manager:
                     <span id="cashierName">
                         <?= Utility::truncateText(!empty($user['fullname']) ? ucfirst($user['fullname']) : ucfirst($role), 7); ?>
                     </span>
                 </span>

                 <span class="muted motto small" id="clock"></span>

                 <button id="themeToggle" class="btn btn-outline-primary btn-sm" aria-pressed="false"
                     title="Toggle theme"><i class="bi bi-moon-stars"></i></button>

                 <a href="<?= BASE_URL ?>secure/admin/orders" class="icon-btn position-relative" aria-label="Orders">
                     <i class="bi bi-cart fs-5"></i>
                     <span class="badge bg-danger position-absolute order-badge" id="orderAlert">0</span>
                 </a>

                 <button class="btn btn-error btn-sm logout" data-id="<?= $userid; ?>">
                     <i class="fa-solid fa-right-from-bracket"></i>
                     Logout
                 </button>
             </div>

         </div>
     </div>
 </nav>