<?php
$title = "Homepage";
require_once __DIR__ . '/../libs/utils/db/db_connect.php';
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>

<h1>Benvenuto nella Homepage</h1>
<p>Questa è la pagina principale dell'applicazione.</p>

<h4>Lista di utenti</h4>
<?php
if (count($users)) {
    foreach ($users as &$value) {
        echo $value["username"] . "<br>";
    }
} else {
    echo "Non ci sono utenti";
}
?>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();

include __DIR__ . '/base.php';
