<?php
$title = "Forgot Password";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Forgot password</h1>
<h2 class="subtitle">No problem! Enter your email to change it.</h2>

<form action="./forgot_password.php" method="POST">
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
        <p class="help is-danger is-invisible" id="email-message-error">Invalid email.</p>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button class="button is-primary" type="submit">Send email</button>
        </div>
    </div>
</form>

<script type="text/javascript">
    const EMAIL_REGEX = <?= $vars["email_pattern"] ?>;
</script>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';