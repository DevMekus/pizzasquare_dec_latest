 <div class="sidebar_con">
        <a class="navbar-brand brand" href="<?= BASE_URL ?>secure/admin/overview">
            <img src="<?= BASE_URL ?>assets/images/logo_color.png" alt="Pizzasquare" />
            <div>
                <div class="brand-title text-center">Administration</div>
                <div class="small muted motto text-center">Dashboard • Welcome back</div>

            </div>
        </a>
        <div class="p-2">
            <div class="link-wrap <?= $route == "overview" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/admin/overview" class="">
                    <span class="icon">📊</span>
                    <span class="label">Overview</span>
                </a>
            </div>
            <div class="link-wrap <?= $route == "orders" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/admin/orders" class="">
                    <span class="icon">🛒</span>
                    <span class="label">Orders</span>
                </a>
            </div>
              <div class="link-wrap <?= $route == "reports" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/admin/reports" class="">
                    <span class="icon">📑 </span>
                    <span class="label">Reports</span>
                </a>
            </div>
            <div class="link-wrap <?= $route == "account" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/admin/account" class="">
                    <span class="icon">👤 </span>
                    <span class="label">Accounts</span>
                </a>
            </div>
            <p class="muted">Inventory Management</p>
            <div class="link-wrap <?= $route == "menu" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/admin/menu" class="">
                    <span class="icon">🍕</span>
                    <span class="label">Menu</span>
                </a>
            </div>
            <div class="link-wrap <?= $route == "categories" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/admin/categories" class="">
                    <span class="icon">📑 </span>
                    <span class="label">Categories</span>
                </a>
            </div>
            <div class="link-wrap <?= $route == "sizes" ? 'active' : '' ?>">
                <a href="<?= BASE_URL; ?>secure/admin/sizes" class="">
                    <span class="icon">👤 </span>
                    <span class="label">Sizes</span>
                </a>
            </div>
               <div class="link-wrap <?= $route == "level_stock" ? 'active' : '' ?>">
                    <a href="<?= BASE_URL; ?>secure/admin/level_stock" class="">
                        <span class="icon">🗃️</span>
                        <span class="label">Stock Manager</span>
                    </a>
                </div>
            <div class="">
                <p class="muted">Manage also</p>
                
             
                
                <div class="link-wrap <?= $route == "extras" ? 'active' : '' ?>">
                    <a href="<?= BASE_URL; ?>secure/admin/extras" class="">
                        <span class="icon">🧀</span>
                        <span class="label">Toppings</span>
                    </a>
                </div>
                <div class="link-wrap <?= $route == "deals" ? 'active' : '' ?>">
                    <a href="<?= BASE_URL; ?>secure/admin/deals" class="">
                        <span class="icon">🎉</span>
                        <span class="label">Special Promo</span>
                    </a>
                </div>
                <div class="link-wrap <?= $route == "zones" ? 'active' : '' ?>">
                    <a href="<?= BASE_URL; ?>secure/admin/zones" class="">
                        <span class="icon">🚚</span>
                        <span class="label">Delivery Zones</span>
                    </a>
                </div>
                <div class="link-wrap <?= $route == "coupons" ? 'active' : '' ?>">
                    <a href="<?= BASE_URL; ?>secure/admin/coupons" class="">
                        <span class="icon">🏷️</span>
                        <span class="label">Coupons</span>
                    </a>
                </div>
                <div class="link-wrap <?= $route == "activities" ? 'active' : '' ?>">
                    <a href="<?= BASE_URL; ?>secure/admin/activities" class="">
                        <span class="icon">🕒</span>
                        <span class="label">Activity log</span>
                    </a>
                </div>
            </div>

        </div>
    </div>