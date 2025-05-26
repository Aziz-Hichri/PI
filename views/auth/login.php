<?php
$pageTitle = 'Login';
require_once '../../config/connection.php';
require_once '../../includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . $base_url . '/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
      if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';    } else {
        if (login($email, $password)) {
            header('Location: ' . $base_url . '/index.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}

include '../../partials/header.php';
?>

<div class="form-container">    <h2>Login</h2>    <div id="error-message" class="error-message"></div>
    <form method="POST" action="<?php echo $base_url; ?>/views/auth/login.php" id="login-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include '../../partials/footer.php'; ?>
