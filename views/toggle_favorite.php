<?php
require_once '../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Please log in to save recipes'
    ]);
    exit();
}

// Get recipe ID from request body
$data = json_decode(file_get_contents('php://input'), true);
$recipeId = $data['recipe_id'] ?? null;

if (!$recipeId) {
    echo json_encode([
        'success' => false,
        'error' => 'Recipe ID is required'
    ]);
    exit();
}

// Connect to database
$conn = getConnection();

try {
    // Check if recipe exists first
    $check_recipe = $conn->prepare("SELECT id FROM recipes WHERE id = ?");
    $check_recipe->bind_param("i", $recipeId);
    $check_recipe->execute();
    if ($check_recipe->get_result()->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Recipe not found'
        ]);
        exit();
    }
    
    // Check if already favorited
    $check_favorite = $conn->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $check_favorite->bind_param("ii", $_SESSION['user_id'], $recipeId);
    $check_favorite->execute();
    
    if ($check_favorite->get_result()->num_rows > 0) {
        // Remove favorite
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $recipeId);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'action' => 'removed'
        ]);
    } else {
        // Add favorite
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $_SESSION['user_id'], $recipeId);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'action' => 'added'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error updating favorites: ' . $e->getMessage()
    ]);
}


