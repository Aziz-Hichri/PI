<?php
$pageTitle = 'Recipes';
require_once '../includes/auth.php';
require_once '../includes/recipe_functions.php';

// Handle ingredient-based search
$recipes = [];
if (isset($_GET['ingredients']) && !empty($_GET['ingredients'])) {
    $ingredients_list = explode(',', trim($_GET['ingredients']));
    
    // Clean the ingredients list
    $ingredients_list = array_map('trim', array_filter($ingredients_list));
    
    // Make sure we have a valid array of ingredients
    if (!empty($ingredients_list)) {
        $recipes = findRecipesByIngredients($ingredients_list);
        
        // Debug information
        error_log("Searching for ingredients: " . implode(", ", $ingredients_list));
        error_log("Found " . count($recipes) . " matching recipes");
    }
} else {
    // Get all recipes if no ingredients specified
    $recipes = getAllRecipes();
}

include '../partials/header.php';
?>

<div class="container">
    <?php if (isLoggedIn() && isAdmin()): ?>
        <div style="margin: 1em 0;">
            <a href="<?php echo $base_url; ?>/views/add_recipe.php" class="btn btn-primary">Add Recipe</a>
        </div>
    <?php endif; ?>
    <div class="search-section">
        <h2 class="section-title">Find Recipes by Ingredients</h2>
        <form method="GET" action="<?php echo $base_url; ?>/views/recipes.php" class="ingredient-search-form">
            <div class="form-group search-input-group">
                <label for="ingredient-search">What ingredients do you have?</label>
                <div class="search-input-wrapper">
                    <input type="text" 
                           id="ingredient-search" 
                           placeholder="Type to search ingredients (e.g., chicken, tomatoes, pasta)..."
                           autocomplete="off">
                    <input type="hidden" name="ingredients" id="ingredients-hidden">
                    <?php if (!empty($_GET['ingredients'])): ?>
                        <script>
                            // Restore previous search
                            window.initialIngredients = <?php echo json_encode(explode(',', $_GET['ingredients'])); ?>;
                        </script>
                    <?php endif; ?>
                    <ul id="ingredient-suggestions" class="suggestions-list"></ul>
                </div>
            </div>
            <div id="selected-ingredients" class="selected-ingredients-grid">
                <!-- Selected ingredients will be added here dynamically -->
            </div>
            <button type="submit" class="btn">Find Matching Recipes</button>
            <?php if (!empty($recipes)): ?>
                <p class="search-results-count">
                    Found <?php echo count($recipes); ?> matching recipes
                </p>
            <?php endif; ?>
        </form>
    </div>

    <div class="recipes-section">
        <h2 class="section-title">Available Recipes</h2>
        <div class="recipe-grid">
            <?php if (empty($recipes)): ?>
                <p class="no-results">No recipes found. Try different ingredients!</p>
            <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card">
                        <div class="recipe-content">
                            <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($recipe['description'], 0, 100)) . '...'; ?></p>
                            <div class="recipe-meta">
                                <span>By <?php echo htmlspecialchars($recipe['creator']); ?></span>
                                <span><?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                                <?php if (isset($recipe['matching_ingredients'])): ?>
                                    <span class="matching-ingredients">
                                        <?php echo $recipe['matching_ingredients']; ?>/<?php echo $recipe['total_ingredients']; ?> ingredients match
                                    </span>
                                <?php endif; ?>                            </div>
                            <div class="recipe-actions">
                                <a href="<?php echo $base_url; ?>/views/recipe.php?id=<?php echo $recipe['id']; ?>" class="btn">View Recipe</a>
                                <?php if (isLoggedIn()): ?>
                                    <button class="favorite-btn <?php echo isFavorite($_SESSION['user_id'], $recipe['id']) ? 'favorited' : ''; ?>"
                                            data-recipe-id="<?php echo $recipe['id']; ?>">
                                        ❤
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
