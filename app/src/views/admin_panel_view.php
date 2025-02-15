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
    <div class="column is-one-third">
      <div class="card">
          <header class="card-header">
              <p class="card-header-title">Edit User Role</p>
              <span class="card-header-icon">
                  <i class="fas fa-wand-sparkles" aria-hidden="true"></i>
              </span>
          </header>
          <div class="card-content">
              <div class="content">
                This service allows you to change the role of a user.
              </div>
          </div>
          <footer class="card-footer">
              <a href="<?= ADMIN_EDIT_USER_PATH ?>" class="card-footer-item">Click to access this service</a>
          </footer>
      </div>
    </div>
    <div class="column is-one-third">
      <div class="card">
          <header class="card-header">
              <p class="card-header-title">Coming Soon</p>
              <span class="card-header-icon">
                  <i class="fas fa-terminal" aria-hidden="true"></i>
              </span>
          </header>
          <div class="card-content">
              <div class="content">
                This service will be implemented in a future release.
              </div>
          </div>
          <footer class="card-footer">
            <span class="card-footer-item has-background-inherit">This service is not active</span>
          </footer>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/base_view.php';