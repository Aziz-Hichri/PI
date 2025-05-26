<?php
require_once './config/database.php';

// Connect to database
$conn = getConnection();

// First, let's make sure we have a test user if none exists
$conn->query("INSERT IGNORE INTO users (username, email, password) VALUES ('demo_user', 'demo@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "')");
$user_id = $conn->insert_id ?: 1; // Use the new user or default to ID 1

// Get all ingredient IDs and names for later use
$ingredients = [];
$result = $conn->query("SELECT id, name FROM ingredients");
while ($row = $result->fetch_assoc()) {
    $ingredients[$row['name']] = $row['id'];
}

// Delete existing recipes for clean testing
$conn->query("DELETE FROM recipe_ingredients");
$conn->query("DELETE FROM recipes");

// Function to add a recipe with its ingredients
function addRecipe($conn, $title, $description, $instructions, $prep_time, $cook_time, $servings, $user_id, $ingredients_list) {
    global $ingredients;
    
    // Insert recipe
    $stmt = $conn->prepare("INSERT INTO recipes (title, description, instructions, prep_time, cook_time, servings, created_by) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiii", $title, $description, $instructions, $prep_time, $cook_time, $servings, $user_id);
    $stmt->execute();
    
    $recipe_id = $conn->insert_id;
    
    // Insert recipe ingredients
    $stmt = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, unit) VALUES (?, ?, ?, ?)");
    
    foreach ($ingredients_list as $ingredient) {
        $ingredient_id = $ingredients[$ingredient['name']];
        $quantity = $ingredient['quantity'];
        $unit = $ingredient['unit'];
        
        $stmt->bind_param("iiss", $recipe_id, $ingredient_id, $quantity, $unit);
        $stmt->execute();
    }
    
    return $recipe_id;
}

// Add sample recipes
$recipes = [
    [
        'title' => 'Classic Spaghetti Bolognese',
        'description' => 'A hearty Italian pasta dish with a meaty sauce.',
        'instructions' => "1. Heat olive oil in a large pot.\n2. Add onion, garlic, and sauté until soft.\n3. Add ground beef and cook until browned.\n4. Stir in tomatoes, salt, and pepper.\n5. Simmer for 30 minutes.\n6. Cook pasta according to package instructions.\n7. Serve sauce over pasta with cheese on top.",
        'prep_time' => 15,
        'cook_time' => 45,
        'servings' => 4,
        'ingredients' => [
            ['name' => 'pasta', 'quantity' => '500', 'unit' => 'g'],
            ['name' => 'ground beef', 'quantity' => '400', 'unit' => 'g'],
            ['name' => 'tomatoes', 'quantity' => '400', 'unit' => 'g'],
            ['name' => 'onion', 'quantity' => '1', 'unit' => 'medium'],
            ['name' => 'garlic', 'quantity' => '2', 'unit' => 'cloves'],
            ['name' => 'olive oil', 'quantity' => '2', 'unit' => 'tbsp'],
            ['name' => 'salt', 'quantity' => '1', 'unit' => 'tsp'],
            ['name' => 'black pepper', 'quantity' => '1/2', 'unit' => 'tsp'],
            ['name' => 'cheese', 'quantity' => '50', 'unit' => 'g']
        ]
    ],
    [
        'title' => 'Chicken Stir Fry',
        'description' => 'A quick and healthy Asian-inspired dish.',
        'instructions' => "1. Slice chicken into thin strips.\n2. Heat oil in a wok or large frying pan.\n3. Add chicken and stir-fry until golden.\n4. Add vegetables and stir-fry for 3-4 minutes.\n5. Add soy sauce and stir to combine.\n6. Serve hot over rice.",
        'prep_time' => 15,
        'cook_time' => 15,
        'servings' => 3,
        'ingredients' => [
            ['name' => 'chicken breast', 'quantity' => '300', 'unit' => 'g'],
            ['name' => 'rice', 'quantity' => '200', 'unit' => 'g'],
            ['name' => 'bell pepper', 'quantity' => '1', 'unit' => 'medium'],
            ['name' => 'carrot', 'quantity' => '1', 'unit' => 'large'],
            ['name' => 'onion', 'quantity' => '1', 'unit' => 'medium'],
            ['name' => 'garlic', 'quantity' => '2', 'unit' => 'cloves'],
            ['name' => 'olive oil', 'quantity' => '1', 'unit' => 'tbsp'],
            ['name' => 'salt', 'quantity' => '1/2', 'unit' => 'tsp']
        ]
    ],
    [
        'title' => 'Vegetable Omelette',
        'description' => 'A quick, protein-rich breakfast option.',
        'instructions' => "1. Whisk eggs in a bowl with salt and pepper.\n2. Heat butter in a non-stick pan.\n3. Add chopped vegetables and sauté for 2 minutes.\n4. Pour egg mixture over vegetables.\n5. Cook until set then fold in half.\n6. Serve hot with cheese sprinkled on top.",
        'prep_time' => 10,
        'cook_time' => 10,
        'servings' => 2,
        'ingredients' => [
            ['name' => 'eggs', 'quantity' => '4', 'unit' => 'large'],
            ['name' => 'bell pepper', 'quantity' => '1/2', 'unit' => 'medium'],
            ['name' => 'tomatoes', 'quantity' => '1', 'unit' => 'medium'],
            ['name' => 'onion', 'quantity' => '1/2', 'unit' => 'small'],
            ['name' => 'cheese', 'quantity' => '30', 'unit' => 'g'],
            ['name' => 'butter', 'quantity' => '1', 'unit' => 'tbsp'],
            ['name' => 'salt', 'quantity' => '1/4', 'unit' => 'tsp'],
            ['name' => 'black pepper', 'quantity' => '1/4', 'unit' => 'tsp']
        ]
    ],
    [
        'title' => 'Creamy Potato Soup',
        'description' => 'A comforting soup perfect for cold days.',
        'instructions' => "1. Melt butter in a large pot.\n2. Add onions and cook until soft.\n3. Add potatoes, salt, and pepper.\n4. Cover with water or stock and simmer until potatoes are tender.\n5. Add milk and bring to a gentle simmer.\n6. Blend until smooth if desired.\n7. Serve hot with fresh herbs.",
        'prep_time' => 15,
        'cook_time' => 30,
        'servings' => 4,
        'ingredients' => [
            ['name' => 'potato', 'quantity' => '500', 'unit' => 'g'],
            ['name' => 'onion', 'quantity' => '1', 'unit' => 'medium'],
            ['name' => 'butter', 'quantity' => '2', 'unit' => 'tbsp'],
            ['name' => 'milk', 'quantity' => '200', 'unit' => 'ml'],
            ['name' => 'salt', 'quantity' => '1', 'unit' => 'tsp'],
            ['name' => 'black pepper', 'quantity' => '1/2', 'unit' => 'tsp'],
            ['name' => 'garlic', 'quantity' => '2', 'unit' => 'cloves']
        ]
    ],
    [
        'title' => 'Simple Tomato Pasta',
        'description' => 'A quick and easy vegetarian pasta dish.',
        'instructions' => "1. Cook pasta according to package instructions.\n2. Heat olive oil in a pan.\n3. Add garlic and sauté until fragrant.\n4. Add tomatoes and cook for 5 minutes.\n5. Season with salt and pepper.\n6. Toss pasta with sauce.\n7. Serve with fresh herbs.",
        'prep_time' => 5,
        'cook_time' => 15,
        'servings' => 2,
        'ingredients' => [
            ['name' => 'pasta', 'quantity' => '200', 'unit' => 'g'],
            ['name' => 'tomatoes', 'quantity' => '400', 'unit' => 'g'],
            ['name' => 'garlic', 'quantity' => '2', 'unit' => 'cloves'],
            ['name' => 'olive oil', 'quantity' => '2', 'unit' => 'tbsp'],
            ['name' => 'salt', 'quantity' => '1/2', 'unit' => 'tsp'],
            ['name' => 'black pepper', 'quantity' => '1/4', 'unit' => 'tsp']
        ]
    ]
];

// Add all recipes to the database
foreach ($recipes as $recipe) {
    addRecipe(
        $conn,
        $recipe['title'],
        $recipe['description'],
        $recipe['instructions'],
        $recipe['prep_time'],
        $recipe['cook_time'],
        $recipe['servings'],
        $user_id,
        $recipe['ingredients']
    );
}

echo "✅ Added " . count($recipes) . " sample recipes to the database successfully!";
?>
