<?php 
// Current day of the week (full name, e.g., Monday)
use App\Utils\Utility;

$current = Utility::currentRoute();
$parts = explode("/", trim($current, "/"));

$route = $parts[2] ?? null;

$dayName = date("l"); 
if ($dayName === 'Friday'):  ?>
    <div class="xtra-thursday-card" role="alert">
        <div class="containerw">
            <div class="row">
                <div class="col-sm-<?= $route !=='pos' ? '6':'12' ?>">
                    <div class="center-div">
                         <h4 class="alert-heading">Xtra Thursday Offer 🔥</h4>
                        <?php if ( $route !=='pos'): ?>
                            <p>Order any Xtra Large (XL) Pizza and get a Medium (M) Pizza Free!! Added automatically to your order.</p>
                        <?php endif; ?>                
                                         
                        <p class="mb-0 bottom">
                            <a href="<?=  BASE_URL; ?><?= $route =='pos'?'secure/pos/':'' ?>promo/xtra-thursday" class="alert-link btn btn-secondary"><?= $route =='pos'?'Place Order':'View Offer and Order Now' ?></a>
                        </p>
                    </div>
                </div>
                <?php  if ($route !=='pos'): ?>
                    <div class="col-sm-6">
                        <div class="xtrathursday-img">
                            <img loading="lazy" src="<?= BASE_URL; ?>assets/images/hero/pizza2.png" alt="Pizza" class="zoom-out">
                            <h1>+</h1>
                            <img loading="lazy" src="<?= BASE_URL; ?>assets/images/xtra-thursday-free.png" alt="Pizza" class="zoom-out smaller">
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
       
    </div>

<?php  endif; ?>