<?php
$title = "Create password";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Create a memorable password</h1>

<form action="<?= create_password_path($vars["code"]) ?>" method="POST">
    <div class="field">
        <label class="label">Verification code</label>
        <div class="control">
            <div class="is-flex is-justify-content-center minicode">
                <input type="text" class="code-input input is-size-4 has-text-centered has-width-3rem has-height-3rem is-capitalized mr-3" maxlength="1" data-index="0" value="<?= $vars["code"][0] ?>">
                <input type="text" class="code-input input is-size-4 has-text-centered has-width-3rem has-height-3rem is-capitalized mr-3" maxlength="1" data-index="1" value="<?= $vars["code"][1] ?>">
                <input type="text" class="code-input input is-size-4 has-text-centered has-width-3rem has-height-3rem is-capitalized mr-3" maxlength="1" data-index="2" value="<?= $vars["code"][2] ?>">
                <input type="text" class="code-input input is-size-4 has-text-centered has-width-3rem has-height-3rem is-capitalized mr-3" maxlength="1" data-index="3" value="<?= $vars["code"][3] ?>">
                <input type="text" class="code-input input is-size-4 has-text-centered has-width-3rem has-height-3rem is-capitalized"      maxlength="1" data-index="4" value="<?= $vars["code"][4] ?>">
            </div>
            <input type="hidden" name="code" id="code" value="<?= $vars["code"] ?>" disabled>
        </div>
        <p class="help code-field-message">To continue, please provide the <b>5 characters long</b> code that you received via e-mail.</p>
    </div>

    <div class="field is-invisible password-field">
        <label class="label" for="password">Password</label>
        <div class="control has-icons-left has-icons-right">
            <input 
                class="input" 
                type="password" 
                id="password" 
                name="password" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required 
                placeholder="Your new password">
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

    <div class="field is-invisible password-field">
        <label class="label" for="password_confirm">Confirm password</label>
        <div class="control has-icons-left has-icons-right">
            <input 
                class="input" 
                type="password" 
                id="password_confirm" 
                name="password_confirm" 
                minlength="<?= $vars["password_minlength"] ?>" 
                required 
                placeholder="Confirm new password">
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
            <button class="button is-primary is-disabled" type="submit" id="submit" disabled>Change password</button>
        </div>
    </div>
</form>

<script type="text/javascript" nonce="<?= $nonce ?>" src="/assets/javascript/utils.js"></script>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';