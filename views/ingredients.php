<?php
$pageTitle = 'Ingredients';
require_once '../includes/auth.php';
require_once '../config/database.php';

// Get all ingredients from the database
function getAllIngredients() {
    $conn = getConnection();
    $sql = "SELECT * FROM ingredients ORDER BY name ASC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

$ingredients = getAllIngredients();
include '../partials/header.php';
?>

<div class="container">
    <h1 class="page-title">Ingredients List</h1>
    
    <?php if (isLoggedIn() && isAdmin()): ?>
    <div class="add-ingredient-section">
        <h2>Add New Ingredient</h2>
        <form action="<?php echo $base_url; ?>/views/add_ingredient.php" method="POST" class="ingredient-form">
            <div class="form-group">
                <label for="ingredient_name">Ingredient Name:</label>
                <input type="text" id="ingredient_name" name="name" required 
                       placeholder="Enter ingredient name">
            </div>
            <button type="submit" class="btn btn-primary">Add Ingredient</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="ingredients-grid">
        <?php if (!empty($ingredients)): ?>
            <?php foreach ($ingredients as $ingredient): ?>
                <div class="ingredient-card">
                    <h3><?php echo htmlspecialchars($ingredient['name']); ?></h3>
                    <?php if (isset($ingredient['recipe_count'])): ?>
                        <p>Used in <?php echo $ingredient['recipe_count']; ?> recipes</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-ingredients">No ingredients found. Start by adding some ingredients!</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
