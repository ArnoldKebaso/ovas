<?php
/**
 * OVAS Session Authentication & Security Functions
 * Updated for production-grade security
 */

// Ensure session is started (handled in config.php)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get authenticated user data
 * @return array|null User data if authenticated, null otherwise
 */
function auth_user() {
    return $_SESSION['userdata'] ?? null;
}

/**
 * Get authenticated user ID
 * @return int|null User ID if authenticated, null otherwise
 */
function auth_id() {
    $user = auth_user();
    return $user ? (int)$user['id'] : null;
}

/**
 * Check if authenticated user is admin
 * @return bool True if user is admin, false otherwise
 */
function is_admin() {
    $user = auth_user();
    return $user && isset($user['login_type']) && (int)$user['login_type'] === 2;
}

/**
 * Require user to be logged in, redirect to login if not
 * @param string $redirect_url URL to redirect to after login
 */
function require_login($redirect_url = '') {
    if (!auth_user()) {
        $return_url = $redirect_url ?: $_SERVER['REQUEST_URI'];
        $_SESSION['return_url'] = $return_url;
        redirect('login.php');
        exit;
    }
    
    // Check for session hijacking via User-Agent validation
    validate_session_security();
}

/**
 * Generate CSRF token for forms
 * @return string CSRF token
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from request
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate session security (anti-hijacking)
 */
function validate_session_security() {
    if (!isset($_SESSION['user_agent_hash'])) {
        // First time after login, store the hash
        $_SESSION['user_agent_hash'] = generate_ua_hash();
        return;
    }
    
    // Check if User-Agent has changed significantly
    $current_hash = generate_ua_hash();
    if (!hash_equals($_SESSION['user_agent_hash'], $current_hash)) {
        // Potential session hijacking detected
        session_destroy();
        redirect('login.php?error=session_expired');
        exit;
    }
}

/**
 * Generate a hash of User-Agent for session validation
 * @return string Hash of User-Agent
 */
function generate_ua_hash() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return hash('sha256', $ua . config('JWT_SECRET', 'fallback-secret'));
}

/**
 * Set user session data after successful authentication
 * @param array $user_data User data to store in session
 */
function set_auth_session($user_data) {
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    $_SESSION['userdata'] = $user_data;
    $_SESSION['user_agent_hash'] = generate_ua_hash();
    $_SESSION['last_activity'] = time();
}

/**
 * Clear authentication session
 */
function clear_auth_session() {
    unset($_SESSION['userdata']);
    unset($_SESSION['user_agent_hash']);
    unset($_SESSION['csrf_token']);
    unset($_SESSION['last_activity']);
    session_destroy();
}

// Legacy admin authentication check
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
    $link = "https"; 
else
    $link = "http"; 
$link .= "://"; 
$link .= $_SERVER['HTTP_HOST']; 
$link .= $_SERVER['REQUEST_URI'];

// Only enforce admin auth on admin pages
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
    if(!strpos($link, 'login.php') && !strpos($link, 'registration.php') && (!isset($_SESSION['userdata']) || (isset($_SESSION['userdata']['login_type']) && $_SESSION['userdata']['login_type'] != 2)) ){
        redirect('login.php');
    }
    if(strpos($link, 'login.php') && isset($_SESSION['userdata']['login_type']) && $_SESSION['userdata']['login_type'] == 2){
        redirect('index.php');
    }
}
