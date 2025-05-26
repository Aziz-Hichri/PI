<?php 
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>WasteLess Kitchen - <?php echo $pageTitle ?? 'Home'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/css/style.css">
    <script>
        // Pass PHP configuration to JavaScript
        window.appConfig = {
            baseUrl: '<?php echo $base_url; ?>'
        };
    </script>
    <script src="<?php echo $base_url; ?>/public/js/main.js" defer></script>
</head>
<body>    <nav class="navbar">
        <div class="container nav-content">
            <a href="<?php echo $base_url; ?>/" class="logo">WasteLess Kitchen</a>
            <div class="nav-links">
                <a href="<?php echo $base_url; ?>/">Home</a>
                <a href="<?php echo $base_url; ?>/views/recipes.php">Recipes</a>
                <a href="<?php echo $base_url; ?>/views/ingredients.php">Ingredients</a>
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo $base_url; ?>/views/favorites.php">My Favorites</a>
                    <a href="<?php echo $base_url; ?>/views/profile.php">Profile</a>
                    <a href="<?php echo $base_url; ?>/views/auth/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>/views/auth/login.php">Login</a>
                    <a href="<?php echo $base_url; ?>/views/auth/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container"><?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <ul>
        <?php if (isLoggedIn() && isAdmin()): ?>
            <!-- Add more admin links here if needed -->
        <?php endif; ?>
    </ul>
