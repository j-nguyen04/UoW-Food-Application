<?php

// Database connection parameters
$host = 'localhost'; // Database server
$dbname = 'solirestaurant'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

try {
    // Correct PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection Failed : " . $e->getMessage()); // stops the script execution and prints the error message
}
?>
