<?php
$title = "Novel";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Novel</h1>

<div class="content">
    <p>
        <i class="subtitle is-4"><?= $vars["novel"]["title"] ?></i>, 
        by <?= $vars["novel_user"]["username"] ?>
    </p>
    <p><?= $vars["novel"]["formContent"] ?></p>
</div>
<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';