<?php
$title = "Homepage";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Welcome on the Homepage</h1>
<p class="subtitle">This is the main page of the app.</p>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';