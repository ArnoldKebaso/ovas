<?php
/**
 * Error Logging System
 * Centralized error handling and logging for the OVAS system
 */
class ErrorLogger {
    
    private $pdo;
    private $log_file;
    
    public function __construct() {
        $this->pdo = db();
        $this->log_file = __DIR__ . '/../logs/application.log';
        $this->ensureLogDirectory();
    }
    
    /**
     * Create logs directory if it doesn't exist
     */
    private function ensureLogDirectory() {
        $log_dir = dirname($this->log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
    }
    
    /**
     * Log error to both file and database
     */
    public function logError($level, $message, $context = [], $file = '', $line = 0) {
        $timestamp = date('Y-m-d H:i:s');
        $user_id = $_SESSION['userdata']['id'] ?? null;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Create log entry
        $log_entry = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context,
            'file' => $file,
            'line' => $line,
            'user_id' => $user_id,
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'request_uri' => $request_uri
        ];
        
        // Log to file
        $this->logToFile($log_entry);
        
        // Log to database for important errors
        if (in_array(strtolower($level), ['error', 'critical', 'emergency'])) {
            $this->logToDatabase($log_entry);
        }
        
        // Send notification for critical errors
        if (in_array(strtolower($level), ['critical', 'emergency'])) {
            $this->notifyAdmins($log_entry);
        }
    }
    
    /**
     * Log to file
     */
    private function logToFile($log_entry) {
        $log_line = sprintf(
            "[%s] %s: %s in %s:%d | IP: %s | User: %s | Context: %s\n",
            $log_entry['timestamp'],
            $log_entry['level'],
            $log_entry['message'],
            $log_entry['file'],
            $log_entry['line'],
            $log_entry['ip_address'],
            $log_entry['user_id'] ?? 'guest',
            json_encode($log_entry['context'])
        );
        
        file_put_contents($this->log_file, $log_line, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log to database
     */
    private function logToDatabase($log_entry) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO error_logs (
                    level, message, context, file, line, 
                    user_id, ip_address, user_agent, request_uri, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $log_entry['level'],
                $log_entry['message'],
                json_encode($log_entry['context']),
                $log_entry['file'],
                $log_entry['line'],
                $log_entry['user_id'],
                $log_entry['ip_address'],
                $log_entry['user_agent'],
                $log_entry['request_uri'],
                $log_entry['timestamp']
            ]);
            
        } catch (Exception $e) {
            // Fallback - log to file if database fails
            $fallback_entry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'ERROR',
                'message' => 'Database logging failed: ' . $e->getMessage(),
                'context' => $log_entry,
                'file' => __FILE__,
                'line' => __LINE__,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_id' => null
            ];
            $this->logToFile($fallback_entry);
        }
    }
    
    /**
     * Notify administrators of critical errors
     */
    private function notifyAdmins($log_entry) {
        // This could integrate with email notifications
        // For now, just mark in database for admin dashboard alert
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO admin_alerts (
                    type, title, message, severity, created_at
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                'error',
                'Critical System Error',
                $log_entry['message'] . ' in ' . $log_entry['file'] . ':' . $log_entry['line'],
                $log_entry['level'],
                $log_entry['timestamp']
            ]);
            
        } catch (Exception $e) {
            // If alert creation fails, log it
            $this->logToFile([
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'ERROR',
                'message' => 'Failed to create admin alert: ' . $e->getMessage(),
                'context' => [],
                'file' => __FILE__,
                'line' => __LINE__,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_id' => null
            ]);
        }
    }
    
    /**
     * Get recent errors for admin dashboard
     */
    public function getRecentErrors($limit = 50, $level = null) {
        try {
            $sql = "SELECT * FROM error_logs";
            $params = [];
            
            if ($level) {
                $sql .= " WHERE level = ?";
                $params[] = strtoupper($level);
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get error statistics
     */
    public function getErrorStats($days = 7) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    level,
                    COUNT(*) as count,
                    DATE(created_at) as date
                FROM error_logs 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY level, DATE(created_at)
                ORDER BY date DESC, level
            ");
            
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Clear old logs (older than specified days)
     */
    public function clearOldLogs($days = 30) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM error_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            
            $stmt->execute([$days]);
            return $stmt->rowCount();
            
        } catch (Exception $e) {
            $this->logError('error', 'Failed to clear old logs: ' . $e->getMessage());
            return 0;
        }
    }
}

/**
 * Global error handler function
 */
function handleError($level, $message, $file = '', $line = 0, $context = []) {
    static $logger = null;
    
    if ($logger === null) {
        $logger = new ErrorLogger();
    }
    
    $logger->logError($level, $message, $context, $file, $line);
}

/**
 * Global exception handler
 */
function handleException($exception) {
    handleError(
        'critical',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        [
            'trace' => $exception->getTraceAsString(),
            'code' => $exception->getCode()
        ]
    );
}

/**
 * Global fatal error handler
 */
function handleFatalError() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        handleError(
            'emergency',
            $error['message'],
            $error['file'],
            $error['line']
        );
    }
}

// Register global error handlers
set_error_handler('handleError');
set_exception_handler('handleException');
register_shutdown_function('handleFatalError');
?>