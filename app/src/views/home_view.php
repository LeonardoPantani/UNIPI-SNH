<?php
$title = "Homepage";
$is_fullscreen = true;
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<video autoplay muted loop>
    <source src="/assets/images/background.webm" type="video/webm">
</video>
<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';