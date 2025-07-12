<?php
require_once __DIR__ . '/../vendor/autoload.php';

function getDB() {
    static $client = null;
    
    if ($client === null) {
        try {
            // Load environment variables
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
            
            $client = new MongoDB\Client($_ENV['MONGODB_URI']);
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw $e;
        }
    }
    
    return $client->selectDatabase($_ENV['DB_NAME']);
}
?>