<?php
// Database connection settings
$dsn = 'mysql:host=localhost;dbname=grc';  // DSN (Data Source Name) containing the database host and name
$username = 'root';                         // Database username
$password = 'Mm44683151&&';                 // Database password

// Try to establish the connection
try {
    // Create a new PDO instance (connecting to the MySQL database)
    $conn = new PDO($dsn, $username, $password);

    // Set the PDO error mode to exception for easier debugging
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If the connection fails, display the error message
    echo 'Connection failed: ' . $e->getMessage();
}
?>

