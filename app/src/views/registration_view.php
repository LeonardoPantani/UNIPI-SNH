<?php
$title = "Registration";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Registration</h1>
<form action="./registration.php" method="POST">
    <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
            <input class="input" type="email" id="email" name="email" required placeholder="Your email">
        </div>
    </div>

    <div class="field">
        <label class="label" for="username">Username</label>
        <div class="control">
            <input 
                pattern="<?= $vars["username_pattern"] ?>" 
                class="input" 
                type="text" 
                id="username" 
                name="username" 
                minlength="<?= $vars["username_minlength"] ?>" 
                maxlength="<?= $vars["username_maxlength"] ?>" 
                required 
                placeholder="Your username">
        </div>
        <p class="help">Username must be at least <?= $vars["username_minlength"] ?> characters long and can only contain letters, numbers, hyphens (-), and underscores (_).</p>
    </div>

    <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
            <input 
                class="input" 
                type="password" 
                id="password" 
                name="password" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required 
                placeholder="Your password">
        </div>
        <p class="help">Password must be at least <?= $vars["password_minlength"] ?> characters long.</p>
    </div>

    <div class="field">
        <label class="label" for="password_confirm">Confirm password</label>
        <div class="control">
            <input 
                class="input" 
                type="password" 
                id="password_confirm" 
                name="password_confirm" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required 
                placeholder="Confirm password">
        </div>
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-primary" type="submit">Register</button>
        </div>
    </div>
</form>


<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';
