<?php
// Only define constants if they don't already exist
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'wasteless_kitchen');

// Global connection variable
$conn = null;

// Function to get database connection
function getConnection() {
    global $conn;
    
    // If connection already exists, return it
    if ($conn !== null) {
        return $conn;
    }
    
    // Create new connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Create initial connection for database setup
$setup_conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($setup_conn->connect_error) {
    die("Connection failed: " . $setup_conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($setup_conn->query($sql) === FALSE) {
    die("Error creating database: " . $setup_conn->error);
}

// Select the database
$setup_conn->select_db(DB_NAME);

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS ingredients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        category VARCHAR(50),
        description TEXT
    )",
    
    "CREATE TABLE IF NOT EXISTS recipes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        instructions TEXT,
        prep_time INT,
        cook_time INT,
        servings INT,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS recipe_ingredients (
        recipe_id INT,
        ingredient_id INT,
        quantity VARCHAR(50),
        unit VARCHAR(30),
        PRIMARY KEY (recipe_id, ingredient_id),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id),
        FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS favorites (
        user_id INT,
        recipe_id INT,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, recipe_id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id)
    )"
];

foreach ($tables as $sql) {
    if ($setup_conn->query($sql) === FALSE) {
        die("Error creating table: " . $setup_conn->error);
    }
}
?>
