-- Create database
CREATE DATABASE IF NOT EXISTS wasteless_kitchen;
USE wasteless_kitchen;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ingredients table
CREATE TABLE IF NOT EXISTS ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recipes table
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructions TEXT NOT NULL,
    prep_time INT NOT NULL,
    cook_time INT NOT NULL,
    servings INT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Recipe ingredients table
CREATE TABLE IF NOT EXISTS recipe_ingredients (
    recipe_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

-- Favorites table
CREATE TABLE IF NOT EXISTS favorites (
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- Insert some sample ingredients
INSERT INTO ingredients (name, category) VALUES
('chicken breast', 'meat'),
('rice', 'grains'),
('tomatoes', 'vegetables'),
('onion', 'vegetables'),
('garlic', 'vegetables'),
('olive oil', 'pantry'),
('salt', 'pantry'),
('black pepper', 'pantry'),
('pasta', 'grains'),
('ground beef', 'meat'),
('carrot', 'vegetables'),
('celery', 'vegetables'),
('milk', 'dairy'),
('eggs', 'dairy'),
('flour', 'pantry'),
('butter', 'dairy'),
('cheese', 'dairy'),
('bell pepper', 'vegetables'),
('lemon', 'fruits'),
('potato', 'vegetables');
