<?php
$compteur = isset($_COOKIE['compteur']) ? $_COOKIE['compteur'] + 1 : 1;
setcookie("compteur", $compteur, time() + 60 * 60 * 24 * 365);
?>
<pre>
    <?php print_r($_COOKIE); ?>
</pre>
