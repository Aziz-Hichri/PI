<?php
$pageTitle = 'My Profile';
require_once '../includes/auth.php';
require_once '../config/database.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: ' . $base_url . '/views/auth/login.php');
    exit();
}

// Get user information
$user = getCurrentUser();

// Get user's favorite recipes count
$conn = getConnection();
$stmt = $conn->prepare("SELECT COUNT(*) as favorite_count FROM favorites WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$favorite_count = $stmt->get_result()->fetch_assoc()['favorite_count'];

include '../partials/header.php';
?>

<div class="container">
    <div class="profile-container">
        <div class="profile-sidebar">
            <h2>Profile Information</h2>
            <div class="profile-info">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Saved Recipes:</strong> <?php echo $favorite_count; ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            </div>
        </div>
        
        <div class="profile-main">
            <h2>Recent Activity</h2>
            <div class="activity-section">
                <h3>Recently Saved Recipes</h3>
                <?php
                // Get user's recent favorites
                $sql = "SELECT r.*, u.username as creator 
                        FROM favorites f
                        JOIN recipes r ON f.recipe_id = r.id
                        JOIN users u ON r.created_by = u.id
                        WHERE f.user_id = ?
                        ORDER BY f.created_at DESC
                        LIMIT 5";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $recent_favorites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                
                if (!empty($recent_favorites)): ?>
                    <div class="recipe-grid">
                        <?php foreach ($recent_favorites as $recipe): ?>
                            <div class="recipe-card">
                                <div class="recipe-content">
                                    <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                                    <div class="recipe-meta">
                                        <span>By <?php echo htmlspecialchars($recipe['creator']); ?></span>
                                        <span><?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                                    </div>
                                    <a href="<?php echo $base_url; ?>/views/recipe.php?id=<?php echo $recipe['id']; ?>" class="btn">View Recipe</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">No saved recipes yet. <a href="<?php echo $base_url; ?>/views/recipes.php">Browse recipes</a> to save your favorites!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
