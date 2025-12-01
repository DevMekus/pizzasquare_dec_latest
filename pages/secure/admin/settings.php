<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='admin')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');
?>

<body id="ADMIN_SYSTEM" class="theme-light SETTINGSPAGE">
    <?php require "navbar.php" ?>
    <main class="admin-wrap">
        <?php require "sidebar.php" ?>
        <section class="inner-container">

            <div class="content-centered p-4">
                <div class="topbar" data-aos="fade-down">
                    <h2>Settings</h2>
                </div>

                <section>
                    <div class="card" data-aos="fade-up">
                        <h4>General Settings</h4>
                        <div class="form-group">
                            <label>Website Name</label>
                            <input type="text" id="siteName" placeholder="Food Ordering Platform">
                        </div>
                        <div class="form-group">
                            <label>Admin Email</label>
                            <input type="email" id="adminEmail" placeholder="admin@example.com">
                        </div>
                        <div class="form-group">
                            <label>Currency</label>
                            <select id="currency">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="NGN">NGN</option>
                            </select>
                        </div>
                        <button class="btn" id="saveGeneral">Save Changes</button>
                    </div>

                    <div class="card" data-aos="fade-up" data-aos-delay="100">
                        <h4>Security Settings</h4>
                        <div class="form-group">
                            <label>Password Strength</label>
                            <select id="passwordStrength">
                                <option value="medium">Medium</option>
                                <option value="strong">Strong</option>
                                <option value="veryStrong">Very Strong</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Enable 2FA</label>
                            <select id="twoFA">
                                <option value="enabled">Enabled</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>
                        <button class="btn" id="saveSecurity">Save Security</button>
                    </div>

                    <div class="card" data-aos="fade-up" data-aos-delay="200">
                        <h4>Notification Settings</h4>
                        <div class="form-group">
                            <label>Email Notifications</label>
                            <select id="emailNotif">
                                <option value="all">All</option>
                                <option value="important">Only Important</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Push Notifications</label>
                            <select id="pushNotif">
                                <option value="all">All</option>
                                <option value="important">Only Important</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        <button class="btn" id="saveNotif">Save Notifications</button>
                    </div>
                </section>

            </div>

            <?php require "footer.php" ?>
        </section>

    </main>


    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>

</body>

</html>