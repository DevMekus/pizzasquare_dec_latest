<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

?>

<body class="theme-light" id="dealsPage">
    <div class="container">
        <div class="contact-header" data-aos="fade-down">
            <h2>Contact us</h2>
            <p>
               We'd love to hear your questions, feedback, concerns and appraisals. Feel free to reach out to us:
            </p>
        </div>
           <div class="row mt-4">
                <!-- Form -->
                <div class="contact-form col-sm-6 bg-light p-4" data-aos="fade-right">
                    <h4>Send Us A Message</h4>
                    <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                    <form id="sendMessage">
                       <div class="form-group">
                        <label for="contactForm" class="form-label">Your Name</label>
                         <input type="text" name="name" placeholder="" required />
                       </div>
                        <div class="form-group">
                            <label for="contactForm" class="form-label">Your Email</label>
                            <input type="email" name="email" placeholder="" required />
                        </div>
                       <div class="form-group">
                         <label for="contactForm" class="form-label">Subject</label>
                         <input type="text" name="subject" placeholder="" required />
                       </div>
                      <div class="form-group">
                          <label for="contactForm" class="form-label">Your Message</label>
                          <textarea name="message" placeholder="" rows="4" required></textarea>
                      </div>
                       <div class="w-100 d-flex justify-content-end flex-end">
                         <button type="submit" class="btn btn-primary">Send Message</button>
                       </div>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-info col-sm-6 bg-light p-4" data-aos="fade-left">
                    <h5>Get In Touch</h5>
                    <div class="contact-icon-container">
                        <div class="icon-wrap"><i class="fas fa-envelope"></i></div>
                        <div><p><?= BRAND_EMAIL ?></p></div>
                    </div>
                    <div class="contact-icon-container"><div class="icon-wrap"><i class="fas fa-phone"></i></div><div><p><?= BRAND_PHONE ?></p></div></div>
                    <div class="contact-icon-container"><div class="icon-wrap"><i class="fas fa-map-marker-alt"></i></div><div><p><?= COMPANY_ADDRESS ?></p></div></div>
                    <div class="contact-icon-container"><div class="icon-wrap"><i class="fas fa-clock"></i></div><div><p>
                        Monday to Saturday: 10:00 AM - 10:00 PM (WAT)<br/>
                        Sunday: 12:00 PM - 10:00 PM (WAT)</p></div></div>
                        <hr/>
                    <h4>Lets Be Social</h4>
                    <div class="right social-links contact-social-links">
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
      

      
    </div>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <script type="module" src="<?php echo BASE_URL; ?>assets/src/Pages/ContactPage.js"></script>

</body>

</html>