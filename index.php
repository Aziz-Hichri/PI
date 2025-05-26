<<<<<<< HEAD
<?php
$pageTitle = 'Home';
require_once './includes/auth.php';
require_once './includes/recipe_functions.php';

// Get some featured recipes
$featured_recipes = getAllRecipes(6);

include './partials/header.php';
?>

<div class="hero">
    <div class="hero-content">
        <h1>Turn Your Ingredients into Delicious Meals</h1>
        <p>Discover personalized recipes based on what's already in your kitchen. Reduce waste, save money, and cook amazing dishes!</p>        <?php if (!isLoggedIn()): ?>
            <div class="hero-cta">
                <a href="<?php echo $base_url; ?>/views/auth/register.php" class="btn">Start Cooking</a>
                <a href="<?php echo $base_url; ?>/views/auth/login.php" class="btn btn-outline">Sign In</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<section class="featured-recipes">
    <h2 class="section-title">Featured Recipes</h2>
    <div class="recipe-grid">
        <?php foreach ($featured_recipes as $recipe): ?>
            <div class="recipe-card">
                <div class="recipe-content">
                    <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($recipe['description'], 0, 100)) . '...'; ?></p>                    <div class="recipe-meta">
                        <span>By <?php echo htmlspecialchars($recipe['creator']); ?></span>
                        <span><?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                    </div>
                    <a href="<?php echo $base_url; ?>/views/recipes.php?id=<?php echo $recipe['id']; ?>" class="btn">View Recipe</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps-grid">
        <div class="step">
            <h3>1. Enter Your Ingredients</h3>
            <p>List the ingredients you have in your kitchen</p>
        </div>
        <div class="step">
            <h3>2. Get Personalized Recipes</h3>
            <p>We'll suggest recipes you can make with your ingredients</p>
        </div>
        <div class="step">
            <h3>3. Start Cooking!</h3>
            <p>Follow our easy step-by-step instructions</p>
        </div>
    </div>
</section>

<section class="search-by-ingredients">
    <div class="container">        <h2>Find Recipes by Ingredients</h2>
        <p>Have ingredients but don't know what to cook? Let us help!</p>
        <form action="<?php echo $base_url; ?>/views/search_ingredients.php" method="GET" class="ingredient-search-form">
            <div class="search-container">
                <input type="text" name="ingredients" placeholder="Enter ingredients (e.g., chicken, rice, tomatoes)" class="search-input">
                <button type="submit" class="btn btn-primary">Find Recipes</button>
            </div>
        </form>
    </div>
</section>

<?php include './partials/footer.php'; ?>
=======
<?php
$pageTitle = 'Home';
require_once './includes/auth.php';
require_once './includes/recipe_functions.php';

// Get some featured recipes
$featured_recipes = getAllRecipes(6);

include './partials/header.php';
?>

<div class="hero">
    <div class="hero-content">
        <h1>Turn Your Ingredients into Delicious Meals</h1>
        <p>Discover personalized recipes based on what's already in your kitchen. Reduce waste, save money, and cook amazing dishes!</p>        <?php if (!isLoggedIn()): ?>
            <div class="hero-cta">
                <a href="<?php echo $base_url; ?>/views/auth/register.php" class="btn">Start Cooking</a>
                <a href="<?php echo $base_url; ?>/views/auth/login.php" class="btn btn-outline">Sign In</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<section class="featured-recipes">
    <h2 class="section-title">Featured Recipes</h2>
    <div class="recipe-grid">
        <?php foreach ($featured_recipes as $recipe): ?>
            <div class="recipe-card">
                <div class="recipe-content">
                    <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($recipe['description'], 0, 100)) . '...'; ?></p>                    <div class="recipe-meta">
                        <span>By <?php echo htmlspecialchars($recipe['creator']); ?></span>
                        <span><?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                    </div>
                    <a href="<?php echo $base_url; ?>/views/recipes.php?id=<?php echo $recipe['id']; ?>" class="btn">View Recipe</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps-grid">
        <div class="step">
            <h3>1. Enter Your Ingredients</h3>
            <p>List the ingredients you have in your kitchen</p>
        </div>
        <div class="step">
            <h3>2. Get Personalized Recipes</h3>
            <p>We'll suggest recipes you can make with your ingredients</p>
        </div>
        <div class="step">
            <h3>3. Start Cooking!</h3>
            <p>Follow our easy step-by-step instructions</p>
        </div>
    </div>
</section>

<section class="search-by-ingredients">
    <div class="container">        <h2>Find Recipes by Ingredients</h2>
        <p>Have ingredients but don't know what to cook? Let us help!</p>
        <form action="<?php echo $base_url; ?>/views/search_ingredients.php" method="GET" class="ingredient-search-form">
            <div class="search-container">
                <input type="text" name="ingredients" placeholder="Enter ingredients (e.g., chicken, rice, tomatoes)" class="search-input">
                <button type="submit" class="btn btn-primary">Find Recipes</button>
            </div>
        </form>
    </div>
</section>

<?php include './partials/footer.php'; ?>
>>>>>>> 6ab2d7ddd5da84c339af9fe1ab391baca9c9092c
