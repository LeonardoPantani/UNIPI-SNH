<?php
$title = "Add novel";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Create a novel</h1>
<form action="./add_novel.php" method="POST">
    <div class="field">
        <label class="label" for="premium">It is a premium novel?</label>
        <div class="control">
            <label class="radio">
                <input type="radio" name="premium" value="1">
                Yes
            </label>
            <label class="radio">
                <input type="radio" name="premium" value="0">
                No
            </label>
        </div>
    </div>

    <div class="field">
        <label class="label" for="title">Title</label>
        <input class="input" type="text" id="title" name="title" placeholder="Novel title">
    </div>

    <div class="field">
        <label class="label" for="novel_form">Type</label>
        <div class="select">
            <select id="novel_form" name="novel_form">
                <option disabled selected>-- Select an option --</option>
                <option value="text">Text</option>
                <option value="file">File</option>
            </select>
        </div>
    </div>

    <div id="novel_text" class="field" style="display: none">
        <label class="label" for="content">Content</label>
        <input class="input" type="text" id="content" name="content" placeholder="Novel content">
    </div>

    <div id="novel_file" class="field" style="display: none">
        <label class="label" for="file">File</label>
        <input class="input" type="file" id="file" name="file" accept="application/pdf" placeholder="Novel file">
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-primary" type="submit">Create</button>
        </div>
    </div>
</form>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';