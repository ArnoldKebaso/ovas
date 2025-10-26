<?php
/**
 * Authentication Handler - Backend Endpoints
 * Handles register, login, logout with rate limiting and security
 */

require_once 'initialize.php';
require_once 'inc/sess_auth.php';
require_once 'classes/UsersModel.php';

// Get the action from POST data or GET parameter
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Rate limiting helper class
class RateLimiter {
    private $pdo;
    
    public function __construct() {
        $this->pdo = db();
    }
    
    /**
     * Check and record login attempt
     * @param string $identifier IP address or email
     * @param string $type Type of attempt (ip or email)
     * @return bool True if allowed, false if rate limited
     */
    public function checkAttempt($identifier, $type = 'ip') {
        try {
            $windowStart = date('Y-m-d H:i:s', strtotime('-5 minutes'));
            
            // Count recent attempts
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM login_attempts 
                WHERE identifier = ? AND attempt_type = ? 
                AND attempted_at > ?
            ");
            $stmt->execute([$identifier, $type, $windowStart]);
            $attempts = $stmt->fetchColumn();
            
            // Record this attempt
            $stmt = $this->pdo->prepare("
                INSERT INTO login_attempts (identifier, attempt_type, attempted_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$identifier, $type, date('Y-m-d H:i:s')]);
            
            // Allow max 5 attempts per 5 minutes
            return $attempts < 5;
            
        } catch (PDOException $e) {
            error_log("RateLimiter::checkAttempt failed: " . $e->getMessage());
            return true; // Allow on error to not break authentication
        }
    }
    
    /**
     * Clear successful login attempts
     * @param string $identifier
     * @param string $type
     */
    public function clearAttempts($identifier, $type = 'ip') {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM login_attempts 
                WHERE identifier = ? AND attempt_type = ?
            ");
            $stmt->execute([$identifier, $type]);
        } catch (PDOException $e) {
            error_log("RateLimiter::clearAttempts failed: " . $e->getMessage());
        }
    }
}

$rateLimiter = new RateLimiter();
$users = new UsersModel();

// Handle different actions
switch ($action) {
    case 'register':
        handleRegister();
        break;
    case 'login':
        handleLogin();
        break;
    case 'logout':
        handleLogout();
        break;
    default:
        // Return error for invalid action
        respondWithError('Invalid action', 400);
        break;
}

/**
 * Handle user registration
 */
function handleRegister() {
    global $users, $rateLimiter;
    
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
        respondWithError('Invalid CSRF token', 403);
        return;
    }
    
    // Rate limiting by IP
    $userIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!$rateLimiter->checkAttempt($userIP, 'registration')) {
        respondWithError('Too many registration attempts. Please try again in 5 minutes.', 429);
        return;
    }
    
    // Validate required fields
    $required = ['firstname', 'lastname', 'email', 'phone', 'password', 'confirm_password'];
    $errors = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    // Validate email format
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Validate password match
    if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $errors[] = 'Passwords do not match';
        }
        if (strlen($_POST['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
    }
    
    // Check if email already exists
    if (!empty($_POST['email']) && $users->emailExists($_POST['email'])) {
        $errors[] = 'Email address is already registered';
    }
    
    if (!empty($errors)) {
        respondWithError('Validation failed', 400, $errors);
        return;
    }
    
    // Create user
    $userData = [
        'firstname' => trim($_POST['firstname']),
        'lastname' => trim($_POST['lastname']),
        'email' => trim(strtolower($_POST['email'])),
        'phone' => trim($_POST['phone']),
        'password' => $_POST['password'],
        'login_type' => 1, // Customer
        'status' => 1 // Active
    ];
    
    $userId = $users->create($userData);
    
    if ($userId) {
        // Auto-login the user
        $userRecord = $users->find($userId);
        set_auth_session($userRecord);
        
        // Clear rate limiting for successful registration
        $rateLimiter->clearAttempts($userIP, 'registration');
        
        // Determine redirect URL
        $redirectUrl = $_SESSION['return_url'] ?? base_url . '?page=home';
        unset($_SESSION['return_url']);
        
        respondWithSuccess([
            'message' => 'Registration successful! Welcome to OVAS.',
            'redirect_url' => $redirectUrl,
            'user' => [
                'id' => $userRecord['id'],
                'name' => $userRecord['firstname'] . ' ' . $userRecord['lastname'],
                'email' => $userRecord['email']
            ]
        ]);
    } else {
        respondWithError('Registration failed. Please try again.', 500);
    }
}

/**
 * Handle user login
 */
function handleLogin() {
    global $users, $rateLimiter;
    
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
        respondWithError('Invalid CSRF token', 403);
        return;
    }
    
    // Validate required fields
    if (empty($_POST['email']) || empty($_POST['password'])) {
        respondWithError('Email and password are required', 400);
        return;
    }
    
    $email = trim(strtolower($_POST['email']));
    $password = $_POST['password'];
    $userIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Rate limiting by IP and email
    if (!$rateLimiter->checkAttempt($userIP, 'ip') || !$rateLimiter->checkAttempt($email, 'email')) {
        respondWithError('Too many login attempts. Please try again in 5 minutes.', 429);
        return;
    }
    
    // Verify credentials
    $userRecord = $users->verifyPassword($email, $password);
    
    if ($userRecord) {
        // Clear rate limiting for successful login
        $rateLimiter->clearAttempts($userIP, 'ip');
        $rateLimiter->clearAttempts($email, 'email');
        
        // Set user session
        set_auth_session($userRecord);
        
        // Determine redirect URL
        $redirectUrl = $_SESSION['return_url'] ?? base_url . '?page=home';
        unset($_SESSION['return_url']);
        
        // Admin users go to admin panel
        if ((int)$userRecord['login_type'] === 2) {
            $redirectUrl = base_url . 'admin/';
        }
        
        respondWithSuccess([
            'message' => 'Login successful! Welcome back.',
            'redirect_url' => $redirectUrl,
            'user' => [
                'id' => $userRecord['id'],
                'name' => $userRecord['firstname'] . ' ' . $userRecord['lastname'],
                'email' => $userRecord['email'],
                'is_admin' => (int)$userRecord['login_type'] === 2
            ]
        ]);
    } else {
        respondWithError('Invalid email or password', 401);
    }
}

/**
 * Handle user logout
 */
function handleLogout() {
    // Clear authentication session
    clear_auth_session();
    
    // Determine redirect URL
    $redirectUrl = $_GET['redirect'] ?? base_url . '?page=home';
    
    if (isAjaxRequest()) {
        respondWithSuccess([
            'message' => 'Logged out successfully',
            'redirect_url' => $redirectUrl
        ]);
    } else {
        // Handle direct logout request
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Check if request is AJAX
 * @return bool
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Send success response
 * @param array $data Response data
 */
function respondWithSuccess($data) {
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => true], $data));
    } else {
        // Handle form submission
        $_SESSION['flash_message'] = $data['message'] ?? 'Operation successful';
        $_SESSION['flash_type'] = 'success';
        $redirectUrl = $data['redirect_url'] ?? base_url . '?page=home';
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Send error response
 * @param string $message Error message
 * @param int $httpCode HTTP status code
 * @param array $errors Additional errors
 */
function respondWithError($message, $httpCode = 400, $errors = []) {
    if (isAjaxRequest()) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => $message];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        echo json_encode($response);
    } else {
        // Handle form submission
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = 'error';
        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
        }
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? base_url . '?page=home';
        header("Location: $redirectUrl");
        exit;
    }
}
?>