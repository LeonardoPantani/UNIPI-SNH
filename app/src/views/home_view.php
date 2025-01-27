<?php
$title = "Homepage";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1>Benvenuto nella Homepage</h1>
<p>Questa è la pagina principale dell'applicazione.</p>

<h4>Lista di utenti</h4>
<?php
if (count($users)) {
    foreach ($users as &$value) {
        echo $value->getUsername() . "<br>";
    }
} else {
    echo "Non ci sono utenti";
}

?>
<h3>QUI SOTTO VEDRAI I DATI DI $_SESSION</h3>
<?php var_dump($_SESSION); ?>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';