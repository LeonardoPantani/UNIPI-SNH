<?php
$title = "Show Novels";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">Novels</h1>

<?php if(count($vars["novels_text"]) > 0 || count($vars["novels_file"]) > 0) : ?>
    <p class="subtitle">Discover new novels from other users.</p>

    <div class="columns pt-5">
        <div class="column is-6">
            <div class="icon-text pb-3">
                <span class="icon is-medium">
                    <i class="fas fa-file-lines fa-2xl"></i>
                </span>
                <h1 class="subtitle is-3">Text novels</h1>
            </div>
            
            <?php
                if (count($vars["novels_text"]) > 0) :
                    foreach ($vars["novels_text"] as $novel) :
                        $url = $novel["url"];
                        $premium = ($novel["isPremium"]) ? "PREMIUM" : "NON PREMIUM";
                        $novel_title = $novel["title"];
            ?>
            <div class="icon-text">
                <p>
                    <span class="icon is-medium">
                        <?php if($novel["isPremium"]): ?>    
                            <i class="fas fa-star fa-lg"></i>
                        <?php else: ?>
                            <i class="far fa-star fa-lg"></i>
                        <?php endif; ?>
                    </span>
                    <span class="icon is-medium">
                        <a href="<?= $url ?>">
                            <i class="fas fa-arrow-up-right-from-square fa-lg"></i>
                        </a>
                    </span>
                    <b><?= $novel_title ?></b>
                </p>
            </div>
            <?php 
                    endforeach;
                else:
            ?>
                <p>Empty list</p>
            <?php
                endif; 
            ?>
        </div>

        <div class="column is-6">
            <div class="icon-text pb-3">
                <span class="icon is-medium">
                    <i class="fas fa-file-pdf fa-2xl"></i>
                </span>
                <h1 class="subtitle is-3">File novels</h1>
            </div>

            <?php
                if (count($vars["novels_file"]) > 0) :
                    foreach ($vars["novels_file"] as $novel) :
                        $url = $novel["url"];
                        $premium = ($novel["isPremium"]) ? "PREMIUM" : "NON PREMIUM";
                        $novel_title = $novel["title"];
            ?>
            <div class="icon-text">
                <p>
                    <span class="icon is-medium">
                        <?php if($novel["isPremium"]): ?>    
                            <i class="fas fa-star fa-lg"></i>
                        <?php else: ?>
                            <i class="far fa-star fa-lg"></i>
                        <?php endif; ?>
                    </span>
                    <span class="icon is-medium">
                        <a href="<?= $url ?>">
                            <i class="fas fa-download fa-lg"></i>
                        </a>
                    </span>
                    <b><?= $novel_title ?></b>
                </p>
            </div>
            <?php 
                    endforeach;
                else:
            ?>
                <p>Empty list</p>
            <?php
                endif; 
            ?>
        </div>
    </div>
<?php else : ?>
    <p class="subtitle">We're sorry! Our novels list is empty.</p>
<?php endif; ?>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';