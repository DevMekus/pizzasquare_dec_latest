<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='admin')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
?>

<body id="ADMIN_SYSTEM" class="theme-light" data-role="<?= $user['role']; ?>" data-userid="<?= $userid; ?>">
    <div id="overlay"></div>
    <section id="adminLayout">
        <?php require "sidebar.php" ?>
        <div id="rightContent">
            <?php require "navbar.php" ?>
            <section class="inner-container">
                <div class="content-centered p-4">
                    <div data-aos="fade-down" class="page-header mt-4">
                        <div class="welcome">Accounts Management! </div>
                        <div class="center-mobile">Manage all the accounts in the system</div>
                    </div>

                    <section id="summary-cards" class="summary-cards" data-aos="fade-down">
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-default"><i class="bi bi-people fs-2"></i></div>
                            <p>Total users</p>
                            <h2 id="totalUsers"></h2>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-success"><i class="bi bi-person fs-2"></i></div>
                            <p>Customers</p>
                            <h2 id="customers"></h2>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-primary">
                                <i class="bi bi-cash fs-2"></i>
                            </div>
                            <p>Cashiers</p>
                            <h2 id="cashier"></h2>
                        </div>
                        <div class="kpi-card bounce-card">
                            <div class="icon-box bg-default">
                                <i class="bi bi-shield-lock fs-2"></i>
                            </div>
                            <p>Admins</p>
                            <h2 id="admin">-</h2>
                        </div>
                    </section>
                    <div class="filter-bar d-flex gap-3 p-2 w-100" data-aos="fade-right">
                        <input type="text" id="searchUsers" class="chip" placeholder="Search users...">
                        <button class="btn btn-primary" id="addUser">Add User</button>
                    </div>
                    <section data-aos="fade-down">
                        <div class="pizzasquare-table table-responsive">
                            <table id="usersTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Id</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody"></tbody>
                            </table>
                        </div>
                        <div id="pagination" class="p-4 pagination"></div>
                        <div class="no-data" id="no-data"></div>
                    </section>

                </div>

                <?php require "footer.php" ?>
            </section>
        </div>
    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?= BASE_URL; ?>assets/src/Pages/UserPage.js"></script>

</body>

</html>