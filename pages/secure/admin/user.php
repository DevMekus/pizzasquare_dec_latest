<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/reuse.php';
require_once ROOT_PATH . '/includes/header.php';

if($user['role']!=='admin')header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');

use App\Utils\Utility;

$userid = $_GET['userid'] ?? null;

if (!$userid) header('location: ' . BASE_URL . 'auth/login?f-bk=UNAUTHORIZED');

$url        = BASE_URL . "api/v1/users/$userid";
$getProfile = Utility::requestClient($url);
$userArray = $getProfile['data'] ?? null;

$user = $userArray[0];
$image = isset($user['avatar']) ? json_decode($user['avatar']) : null;
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
                        <div class="welcome">User Information! </div>
                    </div>
                    <div class="card shadow-sm border-0 rounded-3 mt-4 mb-4" data-aos="fade-up">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <?php if (!isset($image)): ?>
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:24px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:100px; height:100px; font-size:24px;">
                                        <img src="<?= htmlspecialchars($image) ?>" style="width:70px; height:70px; " />
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <h5 class="card-title mb-0" id="userName"><?= htmlspecialchars($user['fullname']) ?>.</h5>
                                    <small class="text-muted" id="userRole"><?= htmlspecialchars($user['role']) ?></small>
                                </div>
                            </div>

                            <ul class="list-group list-group-flush" id="profileInfo">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-envelope me-2 text-primary"></i>Email</span>
                                    <span><?= htmlspecialchars($user['email_address']) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-phone me-2 text-success"></i>Phone</span>
                                    <span><?= htmlspecialchars($user['phone']) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-map-marker-alt me-2 text-danger"></i>Address</span>
                                    <span><?= htmlspecialchars($user['address']) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-user-tag me-2 text-warning"></i>Status</span>
                                    <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= htmlspecialchars(strtoupper($user['status'])) ?>
                                    </span>

                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php require "footer.php" ?>
            </section>
        </div>
    </section>

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>

</body>

</html>