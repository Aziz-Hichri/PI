<?php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $user, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to MySQL server successfully<br>";

// Read SQL file
$sql = file_get_contents('database_setup.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Database and tables created successfully<br>";
    
    // Process all result sets
    do {
        // Store result
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

$conn->close();
echo "Setup complete!";
?>
