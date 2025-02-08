<?php
require_once __DIR__ . '/../libs/utils/config/constants.php';

$title = "Error";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Error <?= $vars["error_code"] ? : "404" ?></h1>
<p class="subtitle"><?= $vars["error_message"] ? : "But all is not lost." ?></p>
<div class="columns">
    <div class="column is-one-third-tablet is-half-mobile">
        <figure class="image is-square">
            <img src="https://cataas.com/cat?type=square" alt="Random cat" />
        </figure>
        <p>Here is a cat to make up for it.</p>
    </div>
</div>


<div class="field">
    <div class="control">
        <a class="button is-secondary" href="<?= ROOT_PATH ?>">Back to Homepage</a>
    </div>
</div>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';