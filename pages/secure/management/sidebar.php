<?php

use App\Utils\Utility;

$current = Utility::currentRoute();
$parts = explode("/", trim($current, "/"));

$route = $parts[2] ?? null;


?>

<div class="sidebar" id="sidebar">
    <div class="sidebar_con">
        <a class="navbar-brand brand" href="<?= BASE_URL ?>secure/admin/overview">
            <img src="<?= BASE_URL ?>assets/images/logo_color.png" alt="Pizzasquare" />
            <div>
                <div class="brand-title text-center">Management</div>
                <div class="small muted motto text-center">Dashboard • Welcome back</div>

            </div>
        </a>
        <div class="p-2">
            <div class="link-wrap <?= $route == "overview" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/management/overview" class="">
                    <span class="icon">📊</span>
                    <span class="label">Overview</span>
                </a>
            </div>
            <div class="link-wrap <?= $route == "orders" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/management/orders" class="">
                    <span class="icon">🛒</span>
                    <span class="label">Orders</span>
                </a>
            </div>
            <div class="link-wrap <?= $route == "menu" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/management/menu" class="">
                    <span class="icon">🍕</span>
                    <span class="label">Menu</span>
                </a>
            </div>
           
          
        </div>
    </div>
</div>