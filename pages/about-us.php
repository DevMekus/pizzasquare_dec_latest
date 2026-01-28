<?php
require_once ROOT_PATH . '/siteConfig.php';
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/includes/navbar.php';

?>

<body class="theme-light" id="dealsPage">
    <!-- HERO SECTION -->
<section class="about-hero d-flex align-items-center text-white">
  <div class="container text-center" data-aos="fade-up">

    <!-- Credibility Badge -->
    <div class="since-badge mb-4" data-aos="zoom-in">
      <span>Serving Quality Since</span>
      <strong>2018</strong>
    </div>

    <svg class="pizza-steam" width="120" height="120" viewBox="0 0 120 120" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path d="M40 100 C30 70, 50 60, 40 30" />
        <path d="M60 100 C50 75, 70 60, 60 30" />
        <path d="M80 100 C70 70, 90 60, 80 30" />
    </svg>


    <h1 class="fw-bold display-4">
      Crafting Moments, One Slice at a Time
    </h1>

    <p class="lead mt-3">
      At PizzaSquare, we don’t just make pizza — we create experiences worth sharing.
    </p>

  </div>
</section>


<!-- STORY SECTION -->
<section class="py-5">
  <div class="container">
    <div class="row">
      
      <div class="col-md-6" data-aos="fade-right">
        <h2 class="fw-bold mb-3 text-primary">About Pizza Square Nigeria</h2>
        <p class="mb-4 text-justify">
          Pizza Square Nigeria is a proudly indigenous company formed to bring the timeless charm of Italian pizza and craftsmanship to Nigeria, while staying true to the classic recipe, style, standards, and taste. Each pizza is crafted with expertise —honoring traditional techniques and high-quality ingredients—while weaving in a distinctly Nigerian market and offeringsthat resonates with our customers.
        </p>
        <h3 class="fw-bold mt-4 mb-2 text-secondary">What sets us apart</h3>
        <p>
          Every recipe, every ingredient, and every delivery reflects our obsession 
          with doing things the right way — not the easy way.
        </p>
        <ul class="about-list">
          <li><span class="fw-bold">✔ Authentic Italian foundation:</span> we adhere to the iconic dough, sauce, and ingredients that define the classic Italian pizza, ensuring consistent texture, aroma, and flavor.</li>
          <li><span class="fw-bold">✔ Indigenous pride:</span> as a homegrown brand, we celebrate Nigeria’s diverse tastes and culinary culture, inviting innovation that respects the craft.</li>
          <li><span class="fw-bold">✔ Commitment to quality: </span>meticulous sourcing, dough fermentation, precise baking, and attentive service underpin every order, dine-in or takeaway.</li>
          <li><span class="fw-bold">✔  Made with love from Italy:</span> our ingredients and techniques travel a path from Italy to Nigeria, maintaining the essence of the original while embracing local preferences.</li>
        </ul>
         <h3 class="fw-bold mt-4 mb-2 text-secondary">Our Mission</h3>
        <ul class="about-list">
          <li>To be the most loved Nigerian pizza brand by delivering exceptional pizzas, friendly service and pricing, and memorable experiences across our communities.</li>
          <li>
            To scale responsibly, aiming to serve up to 100,000 pizzas annually while preserving the integrity of our craft.
          </li>
        </ul>
        <p>
          Pizza Square Nigeria is more than a restaurant—it’s a bridge between Italian pizza artistry and Nigerian hospitality. We invite you to come have a true taste of Italy.
        </p>
      </div>

      <div class="col-md-6" data-aos="fade-left">
        <div class="image-overlay">
          <div class="overlay_s"></div>
          <img src="<?= BASE_URL ?>/assets/images/about_psquare.jpeg" class="img-fluid rounded shadow" alt="Pizza preparation">
        </div>
       
        <h3 class="fw-bold mt-4 mb-2 text-secondary">What you can expect</h3>
        <ul class="about-list">
          <li>A welcoming environment with consistent, high-quality pizzas, shawarma, burgers, and more that honor the classic Italian recipe.</li>
          <li>
            Menu versatility that respects tradition while offering Nigerian-inspired twists and seasonal specials.
          </li>
          <li>
            Reliable delivery and catering options designed to bring people together for gatherings, celebrations, and everyday meals.

          </li>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- VALUES SECTION -->
<section class="values-section py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="fw-bold">What We Stand For</h2>
      <p class="text-muted">Our values guide every slice we serve</p>
    </div>

    <div class="row g-4">

      <div class="col-md-4" data-aos="zoom-in">
        <div class="value-card">
          <h5 class="fw-bold">Quality First</h5>
          <p>Only fresh ingredients, handcrafted dough, and time-tested recipes.</p>
        </div>
      </div>

      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
        <div class="value-card">
          <h5 class="fw-bold">Customer Obsession</h5>
          <p>Your satisfaction drives every decision we make.</p>
        </div>
      </div>

      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
        <div class="value-card">
          <h5 class="fw-bold">Consistency</h5>
          <p>The same great taste — every order, every time.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- PROCESS SECTION -->
<section class="py-5">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-md-6 text-center" data-aos="fade-right">
        <img src="<?= BASE_URL ?>/assets/images/pack.jpeg" class="img-fluid rounded shadow" alt="Pizza process">
      </div>

      <div class="col-md-6" data-aos="fade-left">
        <h2 class="fw-bold mb-3">How We Do It</h2>
        <ul class="process-list">
          <li>✔ Freshly prepared dough daily</li>
          <li>✔ Premium toppings sourced carefully</li>
          <li>✔ Oven-baked to perfection</li>
          <li>✔ Fast and reliable delivery</li>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- TEAM SECTION -->
<!-- <section class="team-section py-5 bg-dark text-white">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="fw-bold">Meet the Team</h2>
      <p class="text-muted">Passionate people behind every pizza</p>
    </div>

    <div class="row g-4 justify-content-center">

      <div class="col-md-3" data-aos="flip-left">
        <div class="team-card text-center">
          <img src="images/team1.jpg" class="img-fluid rounded-circle mb-3" alt="">
          <h6 class="fw-bold mb-0">Head Chef</h6>
          <small>Flavor Architect</small>
        </div>
      </div>

      <div class="col-md-3" data-aos="flip-left" data-aos-delay="100">
        <div class="team-card text-center">
          <img src="images/team2.jpg" class="img-fluid rounded-circle mb-3" alt="">
          <h6 class="fw-bold mb-0">Operations Lead</h6>
          <small>Quality Control</small>
        </div>
      </div>

    </div>
  </div>
</section> -->

<!-- CTA SECTION -->
<section class="cta-section py-5 text-center">
  <div class="container" data-aos="zoom-in">
    <h2 class="fw-bold mb-3">Ready for Your Next Slice?</h2>
    <p class="mb-4">Order now and taste the PizzaSquare difference.</p>
    <a href="<?= BASE_URL ?>#menu" class="btn btn-primary btn-lg">Order Now</a>
  </div>
</section>
    <?php require_once ROOT_PATH . '/includes/footer-links.php'; ?>
    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
  

</body>

</html>