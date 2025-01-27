<?php
$title = "Registrazione";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Registrazione</h1>

<form action="./registration.php" method="POST">
    <div class="field">
        <label class="label" for="username">Email</label>
        <div class="control">
            <input class="input" type="email" id="email" name="email" required placeholder="Inserisci la tua email">
        </div>
    </div>

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
        <label class="label" for="password_confirm">Conferma password</label>
        <div class="control">
            <input class="input" type="password" id="password_confirm" name="password_confirm" required placeholder="Conferma la tua password">
        </div>
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-primary" type="submit">Registrati</button>
        </div>
    </div>
</form>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base.php';
