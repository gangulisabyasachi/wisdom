<?php
// Database configuration
$host = 'localhost'; // Database host (use '127.0.0.1' if 'localhost' fails)
$username = 'wisdom'; // Default MySQL username for local development (change for production)
$password = 'wisdomwisdom'; // Default MySQL password for local development (change for production)
$dbname = 'wisdom'; // Database name from journal_portal.sql

// Create a MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to UTF-8 for proper encoding
$conn->set_charset('utf8mb4');

// Note: Do not close the connection here; let individual scripts close it when needed
// $conn->close();
?>