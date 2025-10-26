<?php
/**
 * Simple Database Connection Test - PDO Only
 */

// Load the config and initialize
require_once 'config.php';

try {
    // Create PDO connection directly
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        config('DB_HOST', 'localhost'),
        config('DB_PORT', 3306),
        config('DB_NAME', 'ovas_db')
    );
    
    $pdo = new PDO($dsn, config('DB_USER', 'root'), config('DB_PASS', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "✓ PDO Database connection successful!\n\n";
    
    // Test fetching services
    $stmt = $pdo->query("SELECT id, name, description, fee FROM services WHERE status = 1 LIMIT 5");
    $services = $stmt->fetchAll();
    
    echo "✓ Services fetched successfully:\n";
    foreach ($services as $service) {
        echo sprintf("- %s (KES %d): %s\n", 
            $service['name'], 
            $service['fee'], 
            substr($service['description'], 0, 50) . '...'
        );
    }
    
    echo "\n✓ Test completed successfully!";
    
} catch (Exception $e) {
    echo "✗ Test failed: " . $e->getMessage();
}
?>