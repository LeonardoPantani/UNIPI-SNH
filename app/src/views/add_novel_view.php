<?php
require_once __DIR__ . '/../libs/utils/config/constants.php';

$title = "Add novel";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Create a novel</h1>
<form action="<?= ADD_NOVEL_PATH ?>" method="POST">
    <input type="hidden" value="<?= $vars["token"] ?>" id="token" name="token">
    <div class="field">
        <div class="control">
            <label class="label" for="premium">Is it a premium novel?</label>
            <div class="buttons has-addons">
            <label class="toggle-button" id="nonPremiumLabel">
                <input class="is-hidden" type="radio" name="premium" value="0" required>
                Non premium
            </label>
            <label class="toggle-button" id="premiumLabel">
                <input class="is-hidden" type="radio" name="premium" value="1">
                Premium
            </label>
            </div>
        </div>
        <p class="help is-danger is-hidden" id="premium-message-error">Scegli un'opzione.</p>
    </div>

    <div class="field">
        <label class="label" for="title">Title</label>
        <div class="control has-icons-left has-icons-right">
            <input autocomplete="off" class="input" type="text" id="title" name="title" placeholder="<?= $vars["title_placeholder"] ?>" required>
            <span class="icon is-small is-left">
                <i class="fas fa-heading"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="title-icon-ok">
                <i class="fas fa-check"></i>
            </span>
            <span class="icon is-small is-right is-invisible" id="title-icon-error">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
        </div>
        <p class="help" id="title-message-error">Title can contain up to <?= $vars["title_maxlength"] ?> characters.</p>
    </div>

    <div class="field">
        <label class="label" for="novel_form">Type</label>
        <div class="control has-icons-left">
            <div class="select">
                <select id="novel_form" name="novel_form" required>
                    <option value="" disabled selected>-- Select an option --</option>
                    <option value="text">Text</option>
                    <option value="file">File</option>
                </select>
            </div>
            <div class="icon is-small is-left">
                <i class="fas fa-pen"></i>
            </div>
        </div>
        <p class="help is-danger is-hidden" id="novel_form-message-error">Choose an option.</p>
    </div>

    <div id="novel_text" class="field is-hidden">
        <label class="label" for="content">Content</label>
        <div class="control">
            <textarea class="textarea" id="content" name="content" placeholder="<?= $vars["content_placeholder"] ?>"></textarea>
        </div>
        <p class="help" id="content-message-error">Content can contain up to <?= $vars["content_maxlength"] ?> characters (currently using <span class="has-text-weight-bold" id="content-count-message-error">0</span>).</p>
    </div>

    <div id="novel_file" class="field is-hidden">
        <label class="label" for="file">File</label>
        <div class="file has-name">
            <label class="file-label">
                <input class="file-input input" type="file" id="file" name="file" accept="application/pdf" />
                <span class="file-cta">
                <span class="file-icon">
                    <i class="fas fa-upload"></i>
                </span>
                <span class="file-label">Choose a file...</span>
                </span>
                <span class="file-name" id="file-name-placeholder">No file added yet</span>
            </label>
        </div>
        <p class="help">Maximum supported file size is <strong>1 MB</strong>.</p>
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-primary" type="submit">Create</button>
        </div>
    </div>
</form>

<script type="text/javascript" nonce="<?= $nonce ?>" src="/assets/javascript/utils.js"></script>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';