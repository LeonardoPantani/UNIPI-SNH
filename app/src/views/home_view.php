<?php
$title = "Homepage";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Welcome on the Homepage</h1>
<p class="subtitle">This is the main page of the app.</p>

<b>Users list</b>
<br>
<?php
if (count($vars["users"])) {
    foreach ($vars["users"] as &$value) {
        echo $value->getUsername() . "<br>";
    }
} else {
    echo "No users" . "<br>";
}
?>
<br>

<?php 
if(isset($_SESSION['user'])) {
    if (count($vars["novels"])) { 
?>
    <b>Novels list</b>
    <br>
    <?php
        foreach ($vars["novels"] as &$value) {
            $premium = ($value->getIsPremium()) ? "yes" : "no";

            echo "title: " . $value->getTitle() . "<br>";
            echo " &emsp;premium: " . $premium . "<br>"; 

            if($value instanceof App\Models\NovelText) {
                echo "&emsp;content: " . $value->getFormContent() . "<br>";
            } else if($value instanceof App\Models\NovelFile) {
                echo "&emsp;path: " . $value->getFormPath() . "<br>";
            } else {
                echo "Error: invalid instanceof";
            }
        }
    } else {
        echo "No novels" . "<br>";
    }

    echo "<br>";
}
?>


<b>$vars["session"] content:</b>
<br>
<?php var_dump($vars["session"]); ?>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';