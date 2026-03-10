<?php 
// Current day of the week (full name, e.g., Monday)
use App\Utils\Utility;

$current = Utility::currentRoute();
$parts = explode("/", trim($current, "/"));

$route = $parts[1] ?? null;

$dayName = date("l"); 
if ($dayName === 'Thursday'):  ?>
    <div class="xtra-thursday-card mb-4" role="alert">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="center-div">
                         <h5 class="alert-heading">Xtra Thursday Offer 🍕</h5>
                        <p>Order any Xtra Large (XL) Pizza and get a Medium (M) Pizza Free!! </p>            
                                         
                        <p class="mb-0 bottom">
                            <a href="<?=  BASE_URL; ?><?= $route =='pos'?'secure/pos/':'' ?>deals" class="btn btn-secondary"><?= $route =='pos'?'Place Order':'View Offer' ?></a>
                        </p>
                    </div>
                </div>
             
                <div class="col-sm-6">
                    <div class="xtrathursday-img">
                        <img loading="lazy" src="<?= BASE_URL; ?>assets/images/hero/pizza2.png" alt="Pizza" class="zoom-out">
                        <h1>+</h1>
                        <img loading="lazy" src="<?= BASE_URL; ?>assets/images/xtra-thursday-free.png" alt="Pizza" class="zoom-out smaller">
                    </div>
                </div>             
                
            </div>
        </div>
       
    </div>

<?php  endif; ?>