<?php
require_once __DIR__ . '/../libs/utils/config/constants.php';

$title = "Settings";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Settings</h1>
<p class="subtitle">You can change your password here.</p>

<form action="<?= SETTINGS_PATH ?>" method="POST">
    <input type="hidden" value="<?= $vars["token"] ?>" id="token" name="token">
    <div class="field">
        <label class="label" for="password_old">Your current password</label>
        <div class="control has-icons-left has-icons-right">
            <input 
                class="input" 
                type="password" 
                id="password_old" 
                name="password_old" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required
                placeholder="Your current password">
            <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_old-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_old-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help is-danger is-hidden" id="password_old-message-error">Password must be at least <?= $vars["password_minlength"] ?> characters long.</p>
    </div>

    <div class="field">
        <label class="label" for="password_new">New password</label>
        <div class="control has-icons-left has-icons-right">
            <input 
                class="input" 
                type="password" 
                id="password_new" 
                name="password_new" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required
                placeholder="New password">
            <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_new-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_new-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help" id="password_new-message-error">New password must be at least <?= $vars["password_minlength"] ?> characters long.</p>
    </div>

    <div class="field">
        <label class="label" for="password_new_confirm">Confirm new password</label>
        <div class="control has-icons-left has-icons-right">
            <input 
                class="input" 
                type="password" 
                id="password_new_confirm" 
                name="password_new_confirm" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required
                placeholder="Confirm new password">
            <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_new_confirm-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="password_new_confirm-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help is-invisible is-danger" id="password_new_confirm-message-error">Passwords do not match.</p>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button class="button is-primary" type="submit">Update Settings</button>
        </div>
        <div class="control">
            <a class="button is-secondary" href="<?= ROOT_PATH ?>">Homepage</a>
        </div>
    </div>
</form>

<script type="text/javascript" nonce="<?= $nonce ?>" src="/assets/javascript/utils.js"></script>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';