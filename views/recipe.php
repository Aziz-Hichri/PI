<?php
$pageTitle = 'Recipe Details';
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/recipe_functions.php';

// Get recipe ID from URL
$recipe_id = $_GET['id'] ?? null;

if (!$recipe_id) {
    header('Location: ' . $base_url . '/views/recipes.php');
    exit();
}

// Get recipe details
$conn = getConnection();
$sql = "SELECT r.*, u.username as creator 
        FROM recipes r 
        JOIN users u ON r.created_by = u.id 
        WHERE r.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();

if (!$recipe) {
    header('Location: ' . $base_url . '/views/recipes.php');
    exit();
}

// Get recipe ingredients
$sql = "SELECT i.name, ri.quantity, ri.unit 
        FROM recipe_ingredients ri
        JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE ri.recipe_id = ?
        ORDER BY i.name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = htmlspecialchars($recipe['title']);
include '../partials/header.php';
?>

<div class="container">
    <div class="recipe-header">
        <a href="<?php echo $base_url; ?>/views/recipes.php" class="back-link">← Back to Recipes</a>
        <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
        
        <!-- Recipe meta information -->
        <div class="recipe-meta">
            <span class="meta-item"><i class="far fa-user"></i> By <?php echo htmlspecialchars($recipe['creator']); ?></span>
            <span class="meta-item"><i class="far fa-clock"></i> Prep: <?php echo $recipe['prep_time']; ?> mins</span>
            <span class="meta-item"><i class="fas fa-fire"></i> Cook: <?php echo $recipe['cook_time']; ?> mins</span>
            <span class="meta-item"><i class="fas fa-users"></i> Serves: <?php echo $recipe['servings']; ?></span>
            <span class="meta-item"><i class="far fa-clock"></i> Total Time: <?php echo ($recipe['prep_time'] + $recipe['cook_time']); ?> mins</span>
        </div>

        <!-- Favorite button for logged-in users -->
        <?php if (isLoggedIn()): ?>
            <button class="favorite-btn <?php echo isFavorite($_SESSION['user_id'], $recipe['id']) ? 'favorited' : ''; ?>"
                    data-recipe-id="<?php echo $recipe['id']; ?>">
                <span class="heart-icon">❤</span>
                <span class="favorite-text"><?php echo isFavorite($_SESSION['user_id'], $recipe['id']) ? 'Saved' : 'Save Recipe'; ?></span>
            </button>
        <?php endif; ?>
    </div>

    <div class="recipe-content">
        <!-- Recipe description -->
        <?php if (!empty($recipe['description'])): ?>
            <div class="recipe-section recipe-description">
                <p><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
            </div>
        <?php endif; ?>

        <!-- Ingredients section -->
        <div class="recipe-section recipe-ingredients">
            <h2>Ingredients</h2>
            <ul class="ingredients-list">
                <?php foreach ($ingredients as $ingredient): ?>
                    <li>
                        <span class="ingredient-amount">
                            <?php echo htmlspecialchars($ingredient['quantity']) . ' ' . htmlspecialchars($ingredient['unit']); ?>
                        </span>
                        <span class="ingredient-name">
                            <?php echo htmlspecialchars($ingredient['name']); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Instructions section -->
        <div class="recipe-section recipe-instructions">
            <h2>Instructions</h2>
            <ol class="instructions-list">
                <?php 
                $steps = explode("\n", $recipe['instructions']);
                foreach ($steps as $index => $step): 
                    $step = trim($step);
                    if (!empty($step)):
                ?>
                    <li>
                        <div class="step-number"><?php echo $index + 1; ?></div>
                        <div class="step-text"><?php echo htmlspecialchars($step); ?></div>
                    </li>
                <?php 
                    endif;
                endforeach; 
                ?>
            </ol>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>