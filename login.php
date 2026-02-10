<?php
require_once 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please provide valid email and password';
    } else {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT id, username, email, password_hash FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            redirect('/');
        } else {
            $error = 'Please provide valid email and password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to continue sharing your poetry</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo escape($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="<?php echo escape($_POST['email'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            </div>
        </div>
    </div>
</body>
</html>
