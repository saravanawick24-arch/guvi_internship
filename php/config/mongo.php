<?php
// MongoDB configuration - prioritize MongoDB over MySQL
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Check if MongoDB extension is loaded
    if (!extension_loaded('mongodb')) {
        throw new Exception('MongoDB extension not loaded');
    }
    
    $mongoUri = getenv('MONGO_URI') ?: "mongodb+srv://saravana24057_db_user:LNUxmkiWupaVY7RG@intern.zsnnkal.mongodb.net/";
    $client = new MongoDB\Client($mongoUri);
    
    // Select Database and Collection as per request
    $db = $client->guvi_internship;
    $profiles = $db->profiles;
    
    // Alias for backward compatibility with existing code that expects $collection
    $collection = $profiles;
    
    // Test connection by pinging
    $client->selectDatabase('admin')->command(['ping' => 1]);
    
    // Success - using MongoDB
    $usingMongoDB = true;
    
} catch (Exception $e) {
    // Strict Requirement: MongoDB must be available
    http_response_code(500);
    die(json_encode(["error" => "MongoDB Connection Failed: " . $e->getMessage()]));
}
?>