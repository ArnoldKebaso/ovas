<?php
// Quick database check
try {
    // First check if MySQL is accessible
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    echo "✅ MySQL server is accessible\n";
    
    // Check if ovas_db exists
    $stmt = $pdo->query('SHOW DATABASES');
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Available databases:\n";
    foreach ($databases as $db) {
        echo "  - $db\n";
    }
    
    if (in_array('ovas_db', $databases)) {
        echo "✅ ovas_db database exists\n";
        
        // Test connection to ovas_db
        $pdo = new PDO('mysql:host=localhost;dbname=ovas_db', 'root', '');
        echo "✅ Connection to ovas_db successful\n";
        
        // Check tables
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "📋 Tables in ovas_db:\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
        
    } else {
        echo "❌ ovas_db database does NOT exist\n";
        echo "💡 You need to create the database first\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>