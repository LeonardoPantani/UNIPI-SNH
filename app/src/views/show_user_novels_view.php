<?php
require_once __DIR__ . '/../libs/utils/config/constants.php';

$title = "Show Novels";
ob_start();
// CODICE DELLA PAGINA INIZIA QUI
?>
<h1 class="title">My Novels</h1>

<?php if(count($vars["novels_text"]) > 0 || count($vars["novels_file"]) > 0) : ?>
    <p class="subtitle">Manage your novels.</p>

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
                    <?php if(strlen($novel_title) > 30): ?>
                        <b><?= substr($novel_title, 0, 30) . "..." ?></b>
                    <?php else: ?>
                        <b><?= $novel_title ?></b>
                    <?php endif; ?>
                </p>
            </div>
            <?php 
                    endforeach;
                else:
            ?>
                <p>No text novels found.</p>
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
                        <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-download fa-lg"></i>
                        </a>
                    </span>
                    <?php if(strlen($novel_title) > 30): ?>
                        <b><?= substr($novel_title, 0, 30) . "..." ?></b>
                    <?php else: ?>
                        <b><?= $novel_title ?></b>
                    <?php endif; ?>
                </p>
            </div>
            <?php 
                    endforeach;
                else:
            ?>
                <p>No file novels found.</p>
            <?php
                endif; 
            ?>
        </div>
    </div>
<?php else : ?>
    <p class="subtitle">Your novels list is empty. <a href="<?= ADD_NOVEL_PATH ?>">Add new ones!</a></p>
<?php endif; ?>

<?php
// CODICE DELLA PAGINA FINISCE QUI
$content = ob_get_clean();
include __DIR__ . '/base_view.php';