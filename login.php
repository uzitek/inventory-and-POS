<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Rate limiting
    if (!check_rate_limit($_SERVER['REMOTE_ADDR'], 'login', 5, 300)) {
        $error = "Too many login attempts. Please try again later.";
    } else {
        $username = sanitize_input($_POST['username']);
        $password = $_POST['password'];

        $user = authenticate_user($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            log_user_activity($user['id'], 'login');
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h1><?php echo COMPANY_NAME; ?> Inventory & POS</h1>
        <form action="login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>