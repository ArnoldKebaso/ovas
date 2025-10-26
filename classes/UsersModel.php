<?php
/**
 * Users Model - Production Grade PDO Implementation
 */

class UsersModel {
    private $pdo;
    
    public function __construct() {
        if (!function_exists('db')) {
            require_once __DIR__ . '/../initialize.php';
        }
        $this->pdo = db();
    }
    
    /**
     * Find user by email
     * @param string $email User email
     * @return array|null User data if found, null otherwise
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, firstname, lastname, email, phone, password, 
                       login_type, status, created_at, updated_at 
                FROM users 
                WHERE email = ? AND status = 1
            ");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Users::findByEmail failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new user
     * @param array $data User data
     * @return int|false User ID if successful, false on failure
     */
    public function create($data) {
        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            // Set defaults
            $data['login_type'] = $data['login_type'] ?? 1; // 1 = customer, 2 = admin
            $data['status'] = $data['status'] ?? 1; // 1 = active
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare("
                INSERT INTO users (firstname, lastname, email, phone, password, 
                                 login_type, status, created_at, updated_at) 
                VALUES (:firstname, :lastname, :email, :phone, :password, 
                        :login_type, :status, :created_at, :updated_at)
            ");
            
            $result = $stmt->execute([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'login_type' => $data['login_type'],
                'status' => $data['status'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at']
            ]);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Users::create failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by ID
     * @param int $id User ID
     * @return array|null User data if found, null otherwise
     */
    public function find($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, firstname, lastname, email, phone, 
                       login_type, status, created_at, updated_at 
                FROM users 
                WHERE id = ? AND status = 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Users::find failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update user data
     * @param int $id User ID
     * @param array $data Data to update
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
            // Hash password if being updated
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            // Build dynamic SET clause
            $setParts = [];
            $values = [];
            
            foreach ($data as $field => $value) {
                if ($field !== 'id' && $field !== 'created_at') {
                    $setParts[] = "$field = ?";
                    $values[] = $value;
                }
            }
            
            $values[] = $id; // Add ID for WHERE clause
            
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET " . implode(', ', $setParts) . " 
                WHERE id = ? AND status = 1
            ");
            
            return $stmt->execute($values);
            
        } catch (PDOException $e) {
            error_log("Users::update failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all pet owners (non-admin users)
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array List of pet owners
     */
    public function allOwners($limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.firstname, u.lastname, u.email, u.phone, 
                       u.status, u.created_at, u.updated_at,
                       COUNT(p.id) as pet_count
                FROM users u
                LEFT JOIN pets p ON u.id = p.user_id AND p.status = 1
                WHERE u.login_type = 1 AND u.status = 1
                GROUP BY u.id, u.firstname, u.lastname, u.email, u.phone, 
                         u.status, u.created_at, u.updated_at
                ORDER BY u.lastname, u.firstname
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Users::allOwners failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if email exists (for registration validation)
     * @param string $email Email to check
     * @param int|null $excludeId User ID to exclude from check (for updates)
     * @return bool True if email exists, false otherwise
     */
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
            $params = [$email];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Users::emailExists failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify user password
     * @param string $email User email
     * @param string $password Plain text password
     * @return array|false User data if valid, false otherwise
     */
    public function verifyPassword($email, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, firstname, lastname, email, phone, password, 
                       login_type, status, created_at, updated_at 
                FROM users 
                WHERE email = ? AND status = 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Remove password from returned data
                unset($user['password']);
                return $user;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Users::verifyPassword failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total count of owners
     * @return int Total number of owners
     */
    public function countOwners() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM users 
                WHERE login_type = 1 AND status = 1
            ");
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Users::countOwners failed: " . $e->getMessage());
            return 0;
        }
    }
}

// Create a global instance for backward compatibility
$Users = new UsersModel();
?>