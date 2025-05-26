<?php
require_once __DIR__ . '/config/database.php';

function fixFavoritesTable() {
    $conn = getConnection();
    
    // Drop and recreate favorites table
    $sql = "DROP TABLE IF EXISTS favorites";
    $conn->query($sql);
    
    $sql = "CREATE TABLE favorites (
        user_id INT NOT NULL,
        recipe_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, recipe_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql)) {
        echo "Favorites table fixed successfully!\n";
    } else {
        echo "Error fixing favorites table: " . $conn->error . "\n";
    }
}

// Execute the fix
fixFavoritesTable();
