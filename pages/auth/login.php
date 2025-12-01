<?php

require_once ROOT_PATH . '/includes/header.php';

?>

<body>
    <section id="AUTHContainer" class="loginPage">
        <main class="card-auth" role="main" aria-labelledby="loginTitle">

            <!-- Left / Branding -->
            <aside class="side-brand" aria-hidden="false" data-aos="fade-right">
                <div class="logo">
                    <a href="<?= BASE_URL  ?>" class="navbar-brand">
                        <img class="brand" src="<?= BASE_URL ?>assets/images/logo_color.png" />
                    </a>
                    <div>
                        <div style="font-weight:800"><?= BRAND_NAME; ?></div>
                        <div style="font-size:13px;color:var(--muted)"> <?= TAG; ?></div>
                    </div>
                </div>

                <div class="side-ill hero-visual" role="img" aria-label="Car illustration placeholder">
                    <div class="product"></div>
                </div>

                <p class="side-copy">
                    <?= AUTH_INTRO; ?>
                </p>

                <div class="muted">
                    Need help? <a href="#" aria-label="Contact support">Contact support</a>
                </div>

            </aside>

            <!-- Right / Login form -->
            <section class="side-form" data-aos="fade-left">
                <div class="logo d-block d-lg-none">
                    <a href="<?= BASE_URL  ?>" class="navbar-brand">
                        <img class="brand" src="<?= BASE_URL ?>assets/images/logo_color.png" />
                    </a>
                    <div>
                        <div style="font-size:13px;color:var(--muted)"> <?= TAG; ?></div>
                    </div>
                </div>
                <div class="h">
                    <div>
                        <h2 id="loginTitle">Welcome back</h2>
                        <div class="desc">Sign in to continue to your dashboard</div>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button id="themeToggle" class="btn btn-sm" aria-pressed="false" title="Toggle theme"><i class="bi bi-moon-stars"></i></button>
                    </div>
                </div>

                <form id="loginForm" novalidate>
                    <!-- Email / Username -->
                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email_address" type="email" inputmode="email" placeholder="you@example.com" required aria-required="true" />
                        <div id="emailError" class="muted" style="display:none;color:var(--danger);font-size:13px"></div>
                    </div>

                    <!-- Password -->
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="pw-wrap">
                            <input id="password" name="user_password" type="password" placeholder="Enter your password" minlength="6" required aria-required="true" />
                            <button type="button" class="pw-toggle" aria-label="Show password" title="Show/hide password"><i class="bi bi-eye"></i></button>
                        </div>
                        <div id="pwError" class="muted" style="display:none;color:var(--danger);font-size:13px"></div>
                    </div>

                    <div class="rows" style="width:100%; display:flex; justify-content:space-between; align-items:center;margin-top:6px">
                        <div></div>
                        <!-- <label class="checkbox"><input id="remember" type="checkbox" aria-label="Remember me"> <span class="muted">Remember me</span></label> -->
                        <a href="recover-account" class="muted">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <div style="display:flex;gap:12px;align-items:center;margin-top:6px">
                        <button id="submitBtn" class="btn btn-primary" type="submit" aria-live="polite">
                            <span id="btnText">Login</span>
                            <span id="btnSpinner" style="display:none;margin-left:8px;vertical-align:middle"><span class="spinner" aria-hidden="true"></span></span>
                        </button>
                        <!-- <button type="button" id="guestBtn" class="btn btn-ghost" title="Continue as guest">Continue as guest</button> -->
                    </div>
                    <!-- Social login -->
                    <!-- <div style="margin-top:6px">
                    <div class="muted" style="margin-bottom:8px">Or login with</div>
                    <div class="socials" aria-hidden="false">
                        <button class="social-btn" id="googleBtn" aria-label="Login with Google"><i class="bi bi-google"></i> Google</button>
                        <button class="social-btn" id="fbBtn" aria-label="Login with Facebook"><i class="bi bi-facebook"></i> Facebook</button>
                    </div>
                </div> -->

                    <div class="foot text-center text-md-start">
                        <div class="muted">Don't have an account? <a href="register">Sign Up</a></div>
                        <div class="muted">© <span id="yr"></span> <?= BRAND_NAME;  ?></div>
                    </div>
                </form>
            </section>
        </main>

    </section>

    <script src="https://cdn.jsdelivr.net/npm/jwt-decode/build/jwt-decode.min.js"></script>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/AuthPage.js"></script>
</body>

</html