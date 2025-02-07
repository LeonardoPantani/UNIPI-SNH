<?php
$title = "Admin Panel";
ob_start();
?>
<h1 class="title">Admin Panel</h1>

<article class="message is-danger">
  <div class="message-body">
    Any disclosure, distribution, or unauthorized use of the Admin Panel's content and functionalities, except by authorized StoryForge personnel, is strictly prohibited. Non-compliance may result in legal action and financial penalties.
  </div>
</article>

<div class="container">
  <div class="columns is-multiline">
    <?php foreach($vars["admin_services"] as $s): ?>
      <div class="column is-one-third">
        <div class="card">
            <header class="card-header">
                <p class="card-header-title"><?= $s->getName() ?></p>
                <span class="card-header-icon">
                    <i class="fas fa-<?= $s->getIcon() ?>" aria-hidden="true"></i>
                </span>
            </header>
            <div class="card-content">
                <div class="content">
                    <?= $s->getDescription() ?>
                </div>
            </div>
            <footer class="card-footer">
                <? if($s->getUrl() != ""): ?>
                    <a href="/storyforge/admin/<?= $s->getUrl() ?>_service.php" class="card-footer-item">Click to access this service</a>
                <? else: ?>
                    <span class="card-footer-item has-text-white">This service is not active</span>
                <? endif; ?>
            </footer>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/base_view.php';