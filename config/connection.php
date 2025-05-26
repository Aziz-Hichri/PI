<?php
// Base URL for the application
$base_url = '/PI';

// Include database configuration
require_once __DIR__ . '/database.php';

// Get database connection
$conn = getConnection();

// Return the connection for use in other files
return $conn;
?>
