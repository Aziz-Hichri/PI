<?php
require_once __DIR__ . '/../config/database.php';

function getAllRecipes($limit = 10, $offset = 0) {
    $conn = getConnection();
    $sql = "SELECT r.*, u.username as creator 
            FROM recipes r 
            JOIN users u ON r.created_by = u.id 
            ORDER BY r.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getRecipeById($id) {
    $conn = getConnection();
    $sql = "SELECT r.*, u.username as creator 
            FROM recipes r 
            JOIN users u ON r.created_by = u.id 
            WHERE r.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getRecipeIngredients($recipe_id) {
    $conn = getConnection();
    $sql = "SELECT i.*, ri.quantity, ri.unit 
            FROM recipe_ingredients ri 
            JOIN ingredients i ON ri.ingredient_id = i.id 
            WHERE ri.recipe_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function createRecipe($title, $description, $instructions, $prep_time, $cook_time, $servings, $user_id, $ingredients) {
    $conn = getConnection();
    $conn->begin_transaction();
    
    try {
        // Insert recipe
        $stmt = $conn->prepare("INSERT INTO recipes (title, description, instructions, prep_time, cook_time, servings, created_by) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiii", $title, $description, $instructions, $prep_time, $cook_time, $servings, $user_id);
        $stmt->execute();
        
        $recipe_id = $conn->insert_id;
        
        // Insert ingredients
        $stmt = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, unit) VALUES (?, ?, ?, ?)");
        foreach ($ingredients as $ingredient) {
            $stmt->bind_param("iiss", $recipe_id, $ingredient['id'], $ingredient['quantity'], $ingredient['unit']);
            $stmt->execute();
        }
        
        $conn->commit();
        return $recipe_id;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function searchRecipesByIngredients($ingredient_ids) {
    $conn = getConnection();
    $placeholders = str_repeat('?,', count($ingredient_ids) - 1) . '?';
    
    $sql = "SELECT r.*, u.username as creator, COUNT(ri.ingredient_id) as matching_ingredients
            FROM recipes r
            JOIN users u ON r.created_by = u.id
            JOIN recipe_ingredients ri ON r.id = ri.recipe_id
            WHERE ri.ingredient_id IN ($placeholders)
            GROUP BY r.id
            ORDER BY matching_ingredients DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($ingredient_ids)), ...$ingredient_ids);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function toggleFavorite($user_id, $recipe_id) {
    $conn = getConnection();
    
    // Check if already favorited
    $stmt = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        // Remove from favorites
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        return $stmt->execute();
    } else {
        // Add to favorites
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $recipe_id);
        return $stmt->execute();
    }
}

function getUserFavorites($user_id) {
    $conn = getConnection();
    $sql = "SELECT r.*, u.username as creator 
            FROM favorites f
            JOIN recipes r ON f.recipe_id = r.id
            JOIN users u ON r.created_by = u.id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function isFavorite($user_id, $recipe_id) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND recipe_id = ? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function findRecipesByIngredients($ingredients) {
    $conn = getConnection();
    
    // Clean and prepare ingredients array
    $ingredients = array_map('trim', array_filter($ingredients));
    if (empty($ingredients)) {
        return [];
    }

    // Debug - log what ingredients we're searching for
    error_log("Searching for ingredients: " . implode(", ", $ingredients));
    
    // Build the ingredient search conditions for LIKE queries
    $conditions = [];
    $params = [];
    $types = "";
    
    foreach ($ingredients as $ingredient) {
        $conditions[] = "LOWER(i.name) LIKE ?";
        $params[] = "%" . strtolower($ingredient) . "%";
        $types .= "s";
    }
    
    $conditionsSql = implode(' OR ', $conditions);
    
    // This query:
    // 1. Uses LIKE to find partial matches of ingredient names
    // 2. Counts matching ingredients for each recipe
    // 3. Gets total ingredients for each recipe
    // 4. Calculates a match score
    // 5. Orders by match score and number of matching ingredients
    $sql = "WITH recipe_matches AS (
        SELECT 
            r.id,
            COUNT(DISTINCT CASE WHEN $conditionsSql THEN i.id END) as matching_count,
            COUNT(DISTINCT i.id) as total_ingredients
        FROM recipes r
        JOIN recipe_ingredients ri ON r.id = ri.recipe_id
        JOIN ingredients i ON ri.ingredient_id = i.id
        GROUP BY r.id
    )
    SELECT 
        r.*,
        u.username as creator,
        rm.matching_count as matching_ingredients,
        rm.total_ingredients,
        (rm.matching_count * 100.0 / rm.total_ingredients) as match_percentage
    FROM recipes r
    JOIN users u ON r.created_by = u.id
    JOIN recipe_matches rm ON r.id = rm.id
    WHERE rm.matching_count > 0
    ORDER BY match_percentage DESC, matching_ingredients DESC
    LIMIT 50";

    try {
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Debug - log how many results we found
        error_log("Found " . count($results) . " recipes matching the ingredients");
        
        return $results;
    } catch (Exception $e) {
        error_log("Error finding recipes by ingredients: " . $e->getMessage());
        return [];
    }
}
?>
