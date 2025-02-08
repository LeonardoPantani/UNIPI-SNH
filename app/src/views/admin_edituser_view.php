<?php
require_once __DIR__ . '/../libs/utils/config/constants.php';

$title = "Edit User";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Edit User</h1>
<h2 class="subtitle"></h2>

<form action="<?= ADMIN_EDIT_USER_PATH ?>" method="POST">
    <div class="field">
        <label class="label" for="username">Username</label>
        <div class="control has-icons-left has-icons-right">
            <input autofocus pattern="<?= $vars["username_pattern"] ?>" class="input" type="text" id="username" name="username" minlength="<?= $vars["username_minlength"] ?>" maxlength="<?= $vars["username_maxlength"] ?>" required placeholder="Username">
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
    </div>

    <div class="field">
        <label class="label" for="role">Role</label>
        <div class="select">
            <select id="role" name="role">
                <option disabled selected>-- Select an option --</option>
                
                <? foreach($vars["roles"] as $role): ?>
                    <option value="<?= $role["id"] ?>"><?= $role["name"] ?></option>
                <? endforeach; ?>
            </select>
        </div>
    </div>


    <div class="field is-grouped">
        <div class="control">
            <button class="button is-primary" type="submit">Edit User</button>
        </div>
        <div class="control">
            <a class="button is-secondary" href="<?= ADMIN_PATH ?>">Go back to Admin Panel</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    const USERNAME_REGEX = /^<?= $vars["username_pattern"] ?>$/;
</script>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';