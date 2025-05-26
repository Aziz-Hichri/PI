<?php
require_once __DIR__ . '/../config/database.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if search query is provided
$searchQuery = $_GET['q'] ?? '';
if (strlen($searchQuery) < 2) {
    echo json_encode([]);
    exit();
}

// Connect to database and search ingredients
$conn = getConnection();
$searchQuery = "%$searchQuery%";

$sql = "SELECT id, name FROM ingredients WHERE name LIKE ? ORDER BY name LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $searchQuery);
$stmt->execute();

// Get results and return as JSON
$result = $stmt->get_result();
$ingredients = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($ingredients);
