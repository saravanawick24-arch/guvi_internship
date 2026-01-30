<?php
// Redis configuration - Strict requirement
// Do NOT use PHP native sessions

try {
    if (!class_exists('Redis')) {
        throw new Exception("Redis extension not loaded");
    }

    $redisHost = getenv('REDIS_HOST') ?: "127.0.0.1";
    $redisPort = getenv('REDIS_PORT') ?: 6379;
    $redisPassword = getenv('REDIS_PASSWORD') ?: "";

    $redis = new Redis();
    
    if (!$redis->connect($redisHost, $redisPort)) {
        throw new Exception("Could not connect to Redis server");
    }
    
    if (!empty($redisPassword)) {
        $redis->auth($redisPassword);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "Redis Error: " . $e->getMessage()]));
}
?>
