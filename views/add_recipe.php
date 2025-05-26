<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Restrict to admin only
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo '<h2>Access Denied</h2><p>You do not have permission to access this page.</p>';
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $prep_time = intval($_POST['prep_time'] ?? 0);
    $cook_time = intval($_POST['cook_time'] ?? 0);
    $servings = intval($_POST['servings'] ?? 1);
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($instructions) || $prep_time < 0 || $cook_time < 0 || $servings < 1) {
        $error = 'Please fill in all required fields correctly.';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO recipes (title, description, instructions, prep_time, cook_time, servings, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiii", $title, $description, $instructions, $prep_time, $cook_time, $servings, $created_by);
        if ($stmt->execute()) {
            $success = 'Recipe added successfully!';
        } else {
            $error = 'Error adding recipe.';
        }
    }
}

include '../partials/header.php';
?>
<div class="container">
    <h1 class="page-title">Add New Recipe</h1>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" action="" class="recipe-form">
        <div class="form-group">
            <label for="title">Title*</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <div class="form-group">
            <label for="instructions">Instructions*</label>
            <textarea id="instructions" name="instructions" required></textarea>
        </div>
        <div class="form-group">
            <label for="prep_time">Prep Time (minutes)*</label>
            <input type="number" id="prep_time" name="prep_time" min="0" required>
        </div>
        <div class="form-group">
            <label for="cook_time">Cook Time (minutes)*</label>
            <input type="number" id="cook_time" name="cook_time" min="0" required>
        </div>
        <div class="form-group">
            <label for="servings">Servings*</label>
            <input type="number" id="servings" name="servings" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Recipe</button>
    </form>
</div>
<?php include '../partials/footer.php'; ?>
