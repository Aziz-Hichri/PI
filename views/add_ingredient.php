<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Make sure $base_url is available
if (!isset($base_url)) {
    require_once '../config/connection.php';
}

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . $base_url . '/views/auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $error = 'Ingredient name is required';
    } else {
        $conn = getConnection();
        
        // Check if ingredient already exists
        $stmt = $conn->prepare("SELECT id FROM ingredients WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'This ingredient already exists';
        } else {
            // Insert new ingredient
            $stmt = $conn->prepare("INSERT INTO ingredients (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Ingredient added successfully';
            } else {
                $error = 'Error adding ingredient';
            }
        }
    }
}

// Redirect back to ingredients page
header('Location: ' . $base_url . '/views/ingredients.php');
exit();
