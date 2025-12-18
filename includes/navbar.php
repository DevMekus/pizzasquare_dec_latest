<?php

use App\Utils\Utility;

$current = Utility::currentRoute();
$parts = explode("/", trim($current, "/"));

$userid = $_SESSION['userid'] ?? null;

// Current day of the week (full name, e.g., Monday)
$dayName = date("l"); 



?>

<nav class="navbar navbar-expand-lg navbar-dark bg-nav-primary sticky shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="<?= BASE_URL ?>">
            <img src="<?= BASE_URL ?>assets/images/logo_white.png" alt="Pizzasquare Logo" height="34">
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbars"
            aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Items -->
        <div class="collapse navbar-collapse" id="navbars">

            <ul class="navbar-nav ms-auto mb-3 mb-lg-0 align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link <?= $current == 'home' || $current == '' ? 'active' : '' ?>" href="<?= BASE_URL ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current == '#menu' ? 'active' : '' ?>" href="<?= BASE_URL ?>#menu">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current == 'hot-deals' ? 'active' : '' ?>" href="<?= BASE_URL ?>hot-deals">Deals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current == 'track-order' ? 'active' : '' ?>" href="<?= BASE_URL ?>track-order">Track Order</a>
                </li>

                <?php if ($dayName === 'Thursday'):  ?>
                      <li class="nav-item">
                        <a class="nav-link <?= $current == 'xtra-thursday' ? 'active' : '' ?>" href="<?= BASE_URL ?>xtra-thursday">XtraThursday</a>
                    </li>
                <?php  endif; ?>

                <!-- Cart Icon only once -->
                <li class="nav-item">
                    <a class="nav-link p-0 d-inline-block position-relative" href="<?= BASE_URL ?>checkout">

                        <i class="bi bi-cart"></i>
                        <span id="cartCount"
                            class="position-absolute top-0 start-100 translate-middle  rounded-pill bg-success">
                        </span>
                    </a>
                </li>

                <!-- Auth Buttons -->
                <li class="nav-item d-lg-none">
                   <?php if ($userid) : ?>
                        <button class="btn btn-primary w-100 my-1 logout" data-id="<?= $userid; ?>"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</button>
                    <?php else : ?>
                        <a href="<?= BASE_URL ?>auth/login" class="btn btn-primary w-100 my-1">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Sign In
                        </a>
                    <?php endif; ?>
                </li>

            </ul>

            <!-- Desktop Auth Buttons -->
            <div class="d-none d-lg-flex ms-3 gap-2">
                <?php if ($userid) : ?>
                    <button class="btn btn-primary-brand logout" data-id="<?= $userid; ?>"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</button>
                <?php else : ?>
                <a href="<?= BASE_URL ?>auth/login" class="btn btn-primary-brand">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>