<?php
$title = "Novel";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title mb-3"><?= $vars["novel"]["title"] ?></h1>
<h2 class="subtitle">Created by <strong><?= $vars["novel_user"]["username"] ?></strong> <small>on <?= $vars["novel"]["created_at"] ?></small></h2>

<div class="content">
    <p class="allow-new-line-text"><?= $vars["novel"]["formContent"] ?></p>
</div>
<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';