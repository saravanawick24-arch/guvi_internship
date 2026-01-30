<?php

$servername = getenv('DB_HOST') ?: "127.0.0.1";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";
$dbname = getenv('DB_NAME') ?: "guvi_intern";
$port = getenv('DB_PORT') ?: 3307;

try {
    // 1. Connect without Database first to ensure we can create it
    // We suppress warnings just in case, though the exception is what we catch
    $conn = new mysqli($servername, $username, $password, null, $port);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit(json_encode([
        "status" => "error", 
        "message" => "Database Connection Failed: " . $e->getMessage(),
        "details" => "Ensure MySQL is running on port $port"
    ]));
}

if($conn->connect_error){
    http_response_code(500);
    exit(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// 2. Create Database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    http_response_code(500);
    exit(json_encode(["status" => "error", "message" => "Error creating database: " . $conn->error]));
}

// 3. Select the Database
$conn->select_db($dbname);

// 4. Auto-Existent Tables (Self-Healing)
$usersTable = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!$conn->query($usersTable)) {
    http_response_code(500);
    exit(json_encode(["status" => "error", "message" => "Error creating users table: " . $conn->error]));
}

$profilesTable = "CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    age VARCHAR(10),
    dob DATE,
    contact VARCHAR(20),
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
)";
if (!$conn->query($profilesTable)) {
    http_response_code(500);
    exit(json_encode(["status" => "error", "message" => "Error creating profiles table: " . $conn->error]));
}

?>