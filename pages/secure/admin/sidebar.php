<?php

use App\Utils\Utility;

$current = Utility::currentRoute();
$parts = explode("/", trim($current, "/"));

$route = $parts[2] ?? null;


?>

<div class="sidebar" id="sidebar">
     <?php include ROOT_PATH . '/includes/sidebar-ui.php' ?>
</div>