<?php
require_once __DIR__ . '/../libs/utils/config/constants.php';

$title = "Login";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Login</h1>

<form action="<?= LOGIN_PATH ?>" method="POST">
    <input type="hidden" value="<?= $vars["token"] ?>" id="token" name="token">
    <div class="field">
        <label class="label" for="username">Username</label>
        <div class="control has-icons-left has-icons-right">
            <input autofocus pattern="<?= $vars["username_pattern"] ?>" class="input" type="text" id="username" name="username" minlength="<?= $vars["username_minlength"] ?>" maxlength="<?= $vars["username_maxlength"] ?>" required placeholder="Your username">
            <span class="icon is-small is-left">
                <i class="fas fa-user"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="username-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="username-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help is-danger is-hidden" id="username-message-error">Username must be at least <?= $vars["username_minlength"] ?> characters long and can only contain letters, numbers, hyphens (-), and underscores (_).</p>
    </div>

    <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control has-icons-left has-icons-right">
            <input class="input" pattern="<?= $vars["password_pattern"] ?>" type="password" id="password" name="password" minlength="<?= $vars["password_minlength"] ?>" required placeholder="Your password">
            <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help is-danger is-invisible" id="password-message-error">Password must be at least <?= $vars["password_minlength"] ?> characters long and must contains one uppercase, lowercase, digit and special char.</p>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button class="button is-primary" type="submit">Log in</button>
        </div>
        <div class="py-2">
			<p>or</p>
		</div>
		<div class="control">
            <a class="button is-secondary" href="<?= FORGOT_PASSWORD_PATH ?>">Forgot Password</a>
        </div>
    </div>
</form>

<script type="text/javascript" nonce="<?= $nonce ?>" src="/assets/javascript/utils.js"></script>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';