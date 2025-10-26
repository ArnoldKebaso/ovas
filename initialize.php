<?php
/**
 * OVAS Initialization File - Updated for Production Grade System
 */

// Only require config if not already loaded
if (!function_exists('config')) {
    require_once __DIR__ . '/config.php';
}

// Initialize error logging as early as possible
require_once __DIR__ . '/classes/ErrorLogger.php';

$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');

// Legacy constants for backward compatibility
if(!defined('base_url')) define('base_url', config('BASE_URL', 'http://localhost/ovas/'));
if(!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/' );
if(!defined('dev_data')) define('dev_data',$dev_data);

// Database constants from environment
if(!defined('DB_SERVER')) define('DB_SERVER', config('DB_HOST', 'localhost'));
if(!defined('DB_USERNAME')) define('DB_USERNAME', config('DB_USER', 'root'));
if(!defined('DB_PASSWORD')) define('DB_PASSWORD', config('DB_PASS', ''));
if(!defined('DB_NAME')) define('DB_NAME', config('DB_NAME', 'ovas_db'));

/**
 * PDO Database Connection Singleton
 * @return PDO
 */
function db() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
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
        } catch (PDOException $e) {
            handleError('critical', "Database connection failed: " . $e->getMessage(), __FILE__, __LINE__);
            if (config('APP_ENV') !== 'production') {
                throw $e;
            }
            die("Database connection failed. Please check configuration.");
        }
    }
    
    return $pdo;
}

// Load legacy classes after constants are defined
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');

// Legacy database connection for backward compatibility
$db = new DBConnection;
$conn = $db->conn;
?>