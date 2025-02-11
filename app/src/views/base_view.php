<?php
    require_once __DIR__ . '/../libs/utils/config/constants.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? 'StoryForge - ' . $title : 'StoryForge'; ?></title>

    <!-- Bulma CSS -->
    <link rel="stylesheet" href="/assets/stylesheets/bulma.min.css">

    <!-- Minireset -->
    <link rel="stylesheet" href="/assets/stylesheets/minireset.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="/assets/stylesheets/fontawesome/css/all.min.css">

    <!-- Notification -->
    <link rel="stylesheet" href="/assets/stylesheets/notification.css">
</head>

<body>
    <header>
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    <img src="/assets/images/logo_extended.webp" alt="Logo">
                </a>

                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="test">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="test" class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item" href="<?= ADD_NOVEL_PATH ?>">
                        <span class="icon">
                            <i class="fa-solid fa-pen-nib"></i>
                        </span>
                        <span>Create Novel</span>
                    </a>

                    <? if(isset($_SESSION["user"]) && $_SESSION["role"] == "admin") { ?>
                        <a class="navbar-item" href="<?= ADMIN_PATH ?>">
                            <span class="icon">
                                <i class="fa-solid fa-toolbox"></i>
                            </span>
                            <span>Admin Panel</span>
                        </a>
                    <?php } ?>
                </div>
                

                <!-- Navbar lato destro -->
                <div class="navbar-end">
                    <?php if (isset($_SESSION["user"])) { ?>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <span class="icon">
                                    <i class="fa-solid fa-circle-user"></i>
                                </span>
                                <span><?= $_SESSION["username"] ?></span>
                            </a>

                            <div class="navbar-dropdown">
                                <a class="navbar-item" href="<?= SETTINGS_PATH ?>">
                                    <span class="icon">
                                        <i class="fa-solid fa-gear"></i>
                                    </span>
                                    <span>Settings</span>
                                </a>
                            </div>
                        </div>
                        <div class="navbar-item">
                            <a class="button is-danger" id="logout-button">
                                <span class="icon">
                                    <i class="fa-solid fa-door-open"></i>
                                </span>
                                <span>Log out</span>
                            </a>
                        </div>
                    <?php } else { ?>
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-primary" href="<?= REGISTRATION_PATH ?>">
                                <span class="icon">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <strong>Register</strong>
                            </a>
                            <a class="button is-light" href="<?= LOGIN_PATH ?>">
                                <span class="icon">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                </span>
                                <span>Log in</span>
                            </a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </nav>
    </header>

    <section class="upper-notification">
        <?php foreach($vars["flash"] as $key => $msg):
            $class = match ($key) {
                'success'   => 'is-success',
                'error'     => 'is-danger',
                'warning'   => 'is-warning',
                'info'      => 'is-info',
                default     => 'is-dark'
            };
        ?>

        <div class="notification <?= $class ?>">
            <button class="delete"></button>
            <?= $msg ?>
        </div>

        <?php endforeach; ?>
    </section>

    <main class="hero is-fullheight pt-5">
        <section class="section">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-three-quarters">
                        <?= $content; ?>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer mt-auto">
            <div class="content has-text-centered">
                <p>
                    <strong>StoryForge</strong> by <i>Leonardo Pantani, Christian Sabella & Gioele Dimilta</i>.
                    The <a href="https://github.com/LeonardoPantani/UNIPI-SNH">source code</a> is licensed <a href="https://www.gnu.org/licenses/gpl-3.0.html#license-text">GPL-3.0</a>.
                </p>
            </div>
        </footer>
    </main>

    <script type="text/javascript" nonce="<?= $nonce ?>" src="/assets/javascript/generic.js"></script>
    <script type="text/javascript" nonce="<?= $nonce ?>" src="/assets/javascript/flash.js"></script>
    <?php 
        $filename = str_replace(" ", "_", strtolower($title));
        if(file_exists(__DIR__ . "/../public/assets/javascript/".$filename.".js")) { 
    ?>
        <script type="text/javascript" nonce="<?= $nonce ?>" src="/assets/javascript/<?= $filename ?>.js"></script>
    <?php } ?>
</body>

</html>