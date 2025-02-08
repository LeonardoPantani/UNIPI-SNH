<?php
require_once __DIR__ . '/../libs/utils/config/constants.php';

$title = "Registration";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Registration</h1>
<form action="<?= REGISTRATION_PATH ?>" method="POST">
    <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control has-icons-left has-icons-right">
            <input autofocus class="input" type="email" id="email" name="email" required placeholder="Your email">
            <span class="icon is-small is-left">
                <i class="fas fa-envelope"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="email-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="email-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help is-danger is-hidden" id="email-message-error">Invalid email.</p>
    </div>

    <div class="field">
        <label class="label" for="username">Username</label>
        <div class="control has-icons-left has-icons-right">
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
        <p class="help" id="username-message-error">Username must be at least <?= $vars["username_minlength"] ?> characters long and can only contain letters, numbers, hyphens (-), and underscores (_).</p>
    </div>

    <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control has-icons-left has-icons-right">
            <input 
                class="input" 
                type="password" 
                id="password" 
                name="password" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required 
                placeholder="Your password">
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
        <p class="help" id="password-message-error">Password must be at least <?= $vars["password_minlength"] ?> characters long.</p>
    </div>

    <div class="field">
        <label class="label" for="password_confirm">Confirm password</label>
        <div class="control has-icons-left has-icons-right">
            <input 
                class="input" 
                type="password" 
                id="password_confirm" 
                name="password_confirm" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required 
                placeholder="Confirm password">
            <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_confirm-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_confirm-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help is-invisible is-danger" id="password_confirm-message-error">Passwords do not match.</p>
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-primary" type="submit">Register</button>
        </div>
    </div>
</form>

<script type="text/javascript">
    const EMAIL_REGEX = <?= $vars["email_pattern"] ?>;
    const USERNAME_REGEX = /^<?= $vars["username_pattern"] ?>$/;
    const PASSWORD_MIN_LENGTH = <?= $vars["password_minlength"] ?>;
</script>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';