<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

?>

<body class="theme-light" id="dealsPage">
    <section>
      <!-- HERO -->
  <section class="faq-hero d-flex align-items-center text-white">
    <div class="container text-center" data-aos="fade-up">
      <h1 class="fw-bold display-5">Got Questions? We’ve Got Answers.</h1>
      <p class="lead mt-3">
        Everything you need to know before placing your next order.
      </p>
    </div>
  </section>

    <!-- FAQ CONTENT -->
    <section class="py-5">
      <div class="container">

        <!-- ORDERING -->
        <div class="faq-category" data-aos="fade-up">
          <h3 class="fw-bold mb-4">Ordering</h3>

          <div class="accordion" id="orderingFaq">

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#order1">
                  How do I place an order?
                </button>
              </h2>
              <div id="order1" class="accordion-collapse collapse" data-bs-parent="#orderingFaq">
                <div class="accordion-body">
                  Simply browse our menu, add items to your cart, and checkout online. 
                  Once confirmed, our kitchen gets to work immediately.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#order2">
                  Can I customize my pizza?
                </button>
              </h2>
              <div id="order2" class="accordion-collapse collapse" data-bs-parent="#orderingFaq">
                <div class="accordion-body">
                  Yes. You can choose toppings, crust type, and size directly on the menu page.
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- DELIVERY -->
        <div class="faq-category mt-5" data-aos="fade-up">
          <h3 class="fw-bold mb-4">Delivery</h3>

          <div class="accordion" id="deliveryFaq">

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#delivery1">
                  How long does delivery take?
                </button>
              </h2>
              <div id="delivery1" class="accordion-collapse collapse" data-bs-parent="#deliveryFaq">
                <div class="accordion-body">
                  Delivery typically takes 30–45 minutes depending on location and traffic.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#delivery2">
                  Do you deliver late at night?
                </button>
              </h2>
              <div id="delivery2" class="accordion-collapse collapse" data-bs-parent="#deliveryFaq">
                <div class="accordion-body">
                  Our delivery hours are displayed on the website and may vary by day.
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- PAYMENT -->
        <div class="faq-category mt-5" data-aos="fade-up">
          <h3 class="fw-bold mb-4">Payments</h3>

          <div class="accordion" id="paymentFaq">

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#payment1">
                  What payment methods do you accept?
                </button>
              </h2>
              <div id="payment1" class="accordion-collapse collapse" data-bs-parent="#paymentFaq">
                <div class="accordion-body">
                  We accept cards, bank transfers, and other secure online payment options.
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- FOOD & QUALITY -->
        <div class="faq-category mt-5" data-aos="fade-up">
          <h3 class="fw-bold mb-4">Food & Quality</h3>

          <div class="accordion" id="foodFaq">

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#food1">
                  Are your ingredients fresh?
                </button>
              </h2>
              <div id="food1" class="accordion-collapse collapse" data-bs-parent="#foodFaq">
                <div class="accordion-body">
                  Absolutely. We prepare our dough daily and use fresh, high-quality ingredients.
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- CTA -->
        <div class="faq-cta text-center mt-5" data-aos="zoom-in">
          <h4 class="fw-bold mb-3">Still Craving?</h4>
          <p>Order now and let us handle the rest.</p>
          <a href="<?= BASE_URL ?>#menu" class="btn btn-primary btn-lg">Order Now</a>
        </div>

      </div>
    </section>
    </section>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
   

</body>

</html>