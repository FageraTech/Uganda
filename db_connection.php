<?php
// Database configuration
$host = 'localhost';      // Your database host
$dbname = '9c_service';    // Your database name
$username = 'root';       // Your database username 
$password = '';           // Your database password 

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO attributes for proper error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // echo "Connected successfully"; // Uncomment for testing connection
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>