<?php
$pageTitle = 'Saved Recipes';
require_once '../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . '/views/auth/login.php');
    exit();
}

// Get user's favorite recipes
$conn = getConnection();
try {
    $sql = "SELECT r.*, u.username as creator 
            FROM favorites f
            JOIN recipes r ON f.recipe_id = r.id
            JOIN users u ON r.created_by = u.id
            WHERE f.user_id = ?
            ORDER BY r.title ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $favorites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Error loading favorites: " . $e->getMessage();
    $favorites = [];
}

include '../partials/header.php';
?>

<div class="container">    <div class="favorites-header">
        <h1>My Saved Recipes</h1>
        <a href="<?php echo $base_url; ?>/views/recipes.php" class="btn-secondary">Browse All Recipes</a>
    </div>

    <?php if (empty($favorites)): ?>
        <div class="empty-state">
            <div class="empty-state-content">
                <h2>No Saved Recipes Yet</h2>
                <p>Start saving your favorite recipes to access them quickly!</p>
                <a href="<?php echo $base_url; ?>/views/recipes.php" class="btn">Discover Recipes</a>
            </div>
        </div>
    <?php else: ?>
        <div class="recipe-grid">
            <?php foreach ($favorites as $recipe): ?>
                <div class="recipe-card">
                    <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                    
                    <div class="card-meta">
                        <span class="author">By <?php echo htmlspecialchars($recipe['creator']); ?></span>
                        <span class="time">
                            Total Time: <?php echo ($recipe['prep_time'] + $recipe['cook_time']); ?> mins
                        </span>                    </div>
                    
                    <div class="card-actions">
                        <a href="<?php echo $base_url; ?>/views/recipe.php?id=<?php echo $recipe['id']; ?>" class="btn">View Recipe</a>
                        <button class="remove-favorite-btn" 
                                data-recipe-id="<?php echo $recipe['id']; ?>"
                                title="Remove from saved recipes">
                            Remove
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../partials/footer.php'; ?>
