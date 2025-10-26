<?php
/**
 * Security Utility Class
 * Handles CSRF protection, session security, and input validation
 */
class SecurityUtil {
    
    /**
     * Generate CSRF token for forms
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token from form submission
     */
    public static function verifyCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !$token) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get CSRF token input field HTML
     */
    public static function getCSRFField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate and sanitize email
     */
    public static function sanitizeEmail($email) {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }
    
    /**
     * Validate phone number (basic validation)
     */
    public static function sanitizePhone($phone) {
        $phone = preg_replace('/[^0-9+\-\s()]/', '', trim($phone));
        return strlen($phone) >= 10 ? $phone : false;
    }
    
    /**
     * Initialize secure session with user agent tracking
     */
    public static function initSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session configuration
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_strict_mode', 1);
            
            session_start();
        }
        
        // Store user agent hash for session hijacking protection
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $userAgentHash = hash('sha256', $userAgent);
        
        if (!isset($_SESSION['user_agent_hash'])) {
            $_SESSION['user_agent_hash'] = $userAgentHash;
        } else if ($_SESSION['user_agent_hash'] !== $userAgentHash) {
            // User agent mismatch - possible session hijacking
            self::destroySession();
            return false;
        }
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } else if (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        return true;
    }
    
    /**
     * Destroy session securely
     */
    public static function destroySession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array();
            
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_destroy();
        }
    }
    
    /**
     * Rate limiting implementation
     */
    public static function checkRateLimit($action, $max_attempts = 5, $time_window = 300) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_' . $action;
        $now = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        // Remove old attempts outside time window
        $_SESSION[$key] = array_filter($_SESSION[$key], function($timestamp) use ($now, $time_window) {
            return ($now - $timestamp) < $time_window;
        });
        
        // Check if limit exceeded
        if (count($_SESSION[$key]) >= $max_attempts) {
            return false;
        }
        
        // Add current attempt
        $_SESSION[$key][] = $now;
        return true;
    }
    
    /**
     * Validate appointment capacity server-side
     */
    public static function validateAppointmentCapacity($service_id, $date, $time_slot_id, $db) {
        try {
            // Get service capacity
            $stmt = $db->prepare("SELECT capacity FROM services WHERE id = ?");
            $stmt->execute([$service_id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$service) {
                return false;
            }
            
            // Count existing appointments for this slot
            $stmt = $db->prepare("
                SELECT COUNT(*) as count 
                FROM appointments a
                JOIN time_slots ts ON a.time_slot_id = ts.id 
                WHERE a.service_id = ? 
                AND DATE(a.schedule_date) = ? 
                AND a.time_slot_id = ?
                AND a.status IN ('pending', 'confirmed')
            ");
            $stmt->execute([$service_id, $date, $time_slot_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] < $service['capacity'];
            
        } catch (Exception $e) {
            error_log("Capacity validation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($event, $details = [], $severity = 'INFO') {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'severity' => $severity,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? null,
            'details' => $details
        ];
        
        $log_file = __DIR__ . '/../logs/security.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
    }
}