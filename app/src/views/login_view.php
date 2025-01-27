<?php
$title = "Login";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Login</h1>

<form action="./login.php" method="POST">
    <div class="field">
        <label class="label" for="username">Username</label>
        <div class="control">
            <input pattern="<?= $vars["username_pattern"] ?>" class="input" type="text" id="username" name="username" minlength="<?= $vars["username_minlength"] ?>" maxlength="<?= $vars["username_maxlength"] ?>" required placeholder="Your username">
        </div>
    </div>

    <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
            <input class="input" type="password" id="password" name="password" minlength="<?= $vars["password_minlength"] ?>" required placeholder="Your password">
        </div>
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-primary" type="submit">Log in</button>
        </div>
    </div>
</form>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';
