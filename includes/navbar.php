<?php

use App\Utils\Utility;

$current = Utility::currentRoute();
$parts = explode("/", trim($current, "/"));


?>

<!-- Topbar -->
<section class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-nav-primary sticky-top shadow-sm">
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
                        <a class="nav-link <?= $current == '#featured' ? 'active' : '' ?>" href="<?= BASE_URL ?>#featured">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current == 'hot-deals' ? 'active' : '' ?>" href="<?= BASE_URL ?>hot-deals">Deals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current == 'track-order' ? 'active' : '' ?>" href="<?= BASE_URL ?>track-order">Track Order</a>
                    </li>

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
                        <a href="<?= BASE_URL ?>auth/login" class="btn btn-primary w-100 my-1">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Sign In
                        </a>
                    </li>

                </ul>

                <!-- Desktop Auth Buttons -->
                <div class="d-none d-lg-flex ms-3 gap-2">
                    <a href="<?= BASE_URL ?>auth/login" class="btn btn-primary-brand">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </a>

                </div>

            </div>
        </div>
    </nav>
</section>