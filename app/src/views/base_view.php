<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? 'StoryForge - ' . $title : 'StoryForge'; ?></title>

    <!-- Bulma CSS -->
    <link rel="stylesheet" href="/assets/stylesheets/main.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="/assets/stylesheets/fontawesome/css/all.min.css">
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
                    <a class="navbar-item">
                        <span class="icon">
                            <i class="fa-solid fa-book"></i>
                        </span>
                        <span>Test</span>
                    </a>

                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link">
                            <span class="icon">
                                <i class="fa-solid fa-gear"></i>
                            </span>
                            <span>More</span>
                        </a>

                        <div class="navbar-dropdown">
                            <a class="navbar-item">
                                <span class="icon">
                                    <i class="fa-solid fa-question"></i>
                                </span>
                                <span>A</span>
                            </a>
                            <a class="navbar-item">
                                <span class="icon">
                                    <i class="fa-solid fa-money-bill"></i>
                                </span>
                                <span>B</span>
                            </a>
                            <a class="navbar-item">
                                <span class="icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <span>C</span>
                            </a>
                            <hr class="navbar-divider">
                            <a class="navbar-item">
                                <span class="icon">
                                    <i class="fa-solid fa-pen-nib"></i>
                                </span>
                                <span>D</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Navbar lato destro -->
                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <?php if (isset($_SESSION["user"])) { ?>
                                <a class="button is-primary" href="#TODO">
                                    <span class="icon">
                                        <i class="fa-solid fa-circle-user"></i>
                                    </span>
                                    <span><strong>TEST</strong></span>
                                </a>
                                <a class="button is-danger" id="logout-button">
                                    <span class="icon">
                                        <i class="fa-solid fa-door-open"></i>
                                    </span>
                                    <span>Log out</span>
                                </a>
                            <?php } else { ?>
                                <a class="button is-primary" href="./registration.php">
                                    <span class="icon">
                                        <i class="fa-solid fa-pen-fancy"></i>
                                    </span>
                                    <strong>Register</strong>
                                </a>
                                <a class="button is-light" href="./login.php">
                                    <span class="icon">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                    </span>
                                    <span>Log in</span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <?php if(isset($vars["flash"])) :?>
            <?php foreach($vars["flash"] as $key => $msg): ?>
                <?php $class = ($key === 'success') ? 'is-success' : 'is-danger' ?>

                <div class="container pt-5" style="width: 30%">
                    <div class="notification <?= $class ?>">
                        <button class="delete"></button>
                        <?= $msg ?>
                    </div>
                </div> 
            <?php endforeach; ?>
        <?php endif; ?>

        <section class="section">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-half">
                        <?= $content; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="content has-text-centered">
            <p>
                <strong>StoryForge</strong> by <i>Leonardo Pantani, Christian Sabella & Gioele Dimilta</i>.
                The <a href="https://github.com/LeonardoPantani/UNIPI-SNH">source code</a> is licensed <a href="https://www.gnu.org/licenses/gpl-3.0.html#license-text">GPL-3.0</a>.
            </p>
        </div>
    </footer>

    <script src="/assets/javascript/generic.js"></script>
    <script src="/assets/javascript/flash.js"></script>
    <?php if(file_exists(__DIR__ . "/../public/assets/javascript/".strtolower($title).".js")) { ?><script src="/assets/javascript/<?= strtolower($title) ?>.js"></script><?php } ?>
</body>

</html>