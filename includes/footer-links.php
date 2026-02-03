<footer class="footer section-pad">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><?= BRAND_NAME; ?></h5>
                <p><?= TAG ?></p>
            </div>
            <div class="col-md-4">
                <h4>Quick Links</h4>
                <ul class="list-unstyled">
                    <li><a href="<?= BASE_URL ?>contact-us">Contact us</a></li>
                    <li><a href="<?= BASE_URL ?>faq">FAQ</a></li>
                    <li><a href="<?= BASE_URL ?>about-us">About us</a></li>
                     <li><a href="<?= BASE_URL ?>privacy_policy">Privacy Policy</a></li>
                    <li><a href="<?= BASE_URL ?>terms_conditions">Terms & Conditions</a></li>
                   
                   
                </ul>
            </div>
            <div class="col-md-4">
                <h4>Contact</h4>
                <ul class="list-unstyled mb-4">
                    <li>
                        <a href="tel:<?= BRAND_PHONE ?>" class="text-decoration-none">
                            <?= BRAND_PHONE ?>
                        </a>
                    </li>

                    <li>
                        <a href="mailto:<?= BRAND_EMAIL ?>" class="text-decoration-none">
                            <?= BRAND_EMAIL ?>
                        </a>
                    </li>

                    <li>Enugu, Nigeria</li>
                </ul>

                <h6>Lets Be Social</h6>
                <div class="right social-links">
                    <a href="https://web.facebook.com/pizzasquareng/" target="_blank">
                        <i class="fa-brands fa-facebook"></i></a>
                    <a href="https://www.instagram.com/pizzasquareng" target="_blank">
                        <i class="fa-brands fa-instagram"></i></a>
                    <a href="https://x.com/pizzasquareng" target="_blank">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="https://www.tiktok.com/@pizzasquareng" target="_blank">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    <a href="https://share.google/1b5b3h21wxvLrH50d" target="_blank">
                        <i class="fa-brands fa-google"></i>
                    </a>
                    <a href="https://m.youtube.com/@pizzasquareng" target="_blank">
                        <i class="fa-brands fa-youtube"></i>
                    </a>


                </div>
            </div>
        </div>
        <div class="text-center  mt-4">© <span id="year"></span> <?= BRAND_NAME ?>. All rights reserved.</div>

        <a href="https://wa.me/message/SSNSNBBECRONE1"
            target="_blank"
            class="floating-whatsapp">
            <img loading="lazy" src="<?php echo BASE_URL; ?>assets/images/whatsapp.png"
                class="zoom-out"
                alt="whatsapp icon" />
        </a>

    </div>
</footer>