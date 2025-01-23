<?php
$title = "Login";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1>Accedi</h1>
<p>Riempi i campi per accedere.</p>

<?php
if(isset($_SESSION["user"])) $_GET['e'] = 3;

if (!empty($_GET['e'])) {
    $errors = [
        1 => 'Errore: Username o password errati.',
        2 => 'Errore: Il tuo account è bloccato. Contatta l\'amministratore.',
        3 => 'Errore: Sei già autenticato.'
    ];
    echo '<p class="has-text-danger">' . ($errors[$_GET['e']] ?? 'Errore sconosciuto. Riprova.') . '</p>';
}
?>

<form action="./login.php" method="POST">
    <div class="field">
        <label class="label" for="username">Username</label>
        <div class="control">
            <input class="input" type="text" id="username" name="username" maxlength="50" required placeholder="Inserisci il tuo username">
        </div>
    </div>

    <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
            <input class="input" type="password" id="password" name="password" required placeholder="Inserisci la tua password">
        </div>
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-primary" type="submit">Accedi</button>
        </div>
    </div>
</form>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base.php';
