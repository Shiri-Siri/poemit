<?php
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}
$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-content">
           <a href="<?php echo BASE_URL; ?>" class="navbar-brand">
             <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="PoemIT! Logo" class="logo-image">
            </a>
            
            <div class="navbar-menu">
                <?php if ($current_user): ?>
                    <a href="<?php echo BASE_URL; ?>/write" class="nav-link">Write</a>
                    <a href="<?php echo BASE_URL; ?>/profile?user=<?php echo $current_user['username']; ?>" class="nav-link">Profile</a>
                    <a href="<?php echo BASE_URL; ?>/logout" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login" class="nav-link">Login</a>
                    <a href="<?php echo BASE_URL; ?>/signup" class="btn btn-primary btn-sm">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main>
