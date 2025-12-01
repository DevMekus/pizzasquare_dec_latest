<footer class="footer section-pad">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><?= BRAND_NAME; ?></h5>
                <p><?= TAG ?></p>
            </div>
            <div class="col-md-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= BASE_URL ?>#featured">Menu</a></li>
                    <li><a href="<?= BASE_URL ?>hot-deals">Deals</a></li>
                    <li><a href="<?= BASE_URL ?>track-order">Track Order</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Contact</h6>
                <ul class="list-unstyled mb-4">
                    <li><?= BRAND_PHONE ?></li>
                    <li><?= BRAND_EMAIL ?></li>
                    <li>Enugu, Nigeria</li>
                </ul>
                <h6>Lets Be Social</h6>
                <div class="right">
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