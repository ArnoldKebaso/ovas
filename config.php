<?php
/**
 * OVAS Configuration File - Updated for Production Grade System
 */
ob_start();

// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

/**
 * Configuration helper function
 * @param string $key Environment key
 * @param mixed $default Default value if not found
 * @return mixed
 */
function config($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Set timezone from environment
date_default_timezone_set(config('TIMEZONE', 'Africa/Nairobi'));

// Configure session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (config('APP_ENV') === 'production')
    ]);
    session_start();
}

// Error reporting based on environment
if (config('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Define application constants
define('BASE_URL', config('BASE_URL', 'http://localhost/ovas/'));
define('APP_ROOT', __DIR__);
define('BOOKING_FEE', (int)config('BOOKING_FEE', 500));

// Do not bootstrap DB or classes here to avoid circular includes.
// initialize.php is responsible for DB constants, connections and $_settings.

function redirect($url=''){
	if(!empty($url))
	echo '<script>location.href="'.base_url .$url.'"</script>';
}
function validate_image($file){
	if(!empty($file)){
			// exit;
        $ex = explode('?',$file);
        $file = $ex[0];
        $param =  isset($ex[1]) ? '?'.$ex[1]  : '';
		if(is_file(base_app.$file)){
			return base_url.$file.$param;
		}else{
			return base_url.'dist/img/no-image-available.png';
		}
	}else{
		return base_url.'dist/img/no-image-available.png';
	}
}
function isMobileDevice(){
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}
ob_end_flush();
?>
