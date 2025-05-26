<?php
$pageTitle = 'Create Account';
require_once __DIR__ . '/../../config/database.php';

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . '/index.php');
    exit();
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Simple validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $conn = getConnection();
        
        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email or username is already taken';
        } else {
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
              if ($stmt->execute()) {
                $success = 'Account created successfully! You can now login.';
                header('Refresh: 2; URL=' . $base_url . '/views/auth/login.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include '../../partials/header.php';
?>

<div class="form-container">    <h2>Register</h2>    <div id="error-message" class="error-message"></div>
    <form method="POST" action="<?php echo $base_url; ?>/views/auth/register.php" id="register-form">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>
        <button type="submit" class="btn">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include '../../partials/footer.php'; ?>
