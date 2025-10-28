<?php
/**
 * Users Model - Production Grade PDO Implementation
 */

// Handle direct POST requests to this file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../initialize.php';
    $usersModel = new UsersModel();
    
    // Handle form submission
    $id = $_POST['id'] ?? null;
    $data = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'is_admin' => intval($_POST['is_admin'] ?? 0),
        'status' => intval($_POST['status'] ?? 1)
    ];
    
    // Handle password
    if (!empty($_POST['password'])) {
        $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    
    if (empty($id)) {
        // Create new user
        $result = $usersModel->createUser($data);
    } else {
        // Update existing user
        $result = $usersModel->updateUser($id, $data);
    }
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

class UsersModel {
    private $pdo;
    
    public function __construct() {
        if (!function_exists('db')) {
            require_once __DIR__ . '/../initialize.php';
        }
        $this->pdo = db();
    }
    /**
     * Get all users
     * @return array List of all users
     */
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, email, phone, address, is_admin, status, created_at, updated_at
                FROM users 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UsersModel::getAllUsers failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user by ID
     * @param int $id User ID
     * @return array|null User data if found, null otherwise
     */
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, email, phone, address, is_admin, status, created_at, updated_at
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UsersModel::getUserById failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find user by email
     * @param string $email User email
     * @return array|null User data if found, null otherwise
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, email, phone, address, password_hash, is_admin, status, created_at, updated_at
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UsersModel::findByEmail failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new user
     * @param array $data User data
     * @return array Result with status and message
     */
    public function createUser($data) {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['email'])) {
                return ['status' => 'error', 'msg' => 'Name and email are required'];
            }
            
            // Check if email already exists
            if ($this->findByEmail($data['email'])) {
                return ['status' => 'error', 'msg' => 'Email already exists'];
            }
            
            // Validate password for new users
            if (empty($data['password_hash'])) {
                return ['status' => 'error', 'msg' => 'Password is required'];
            }
            
            $stmt = $this->pdo->prepare("
                INSERT INTO users (name, email, phone, address, password_hash, is_admin, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['password_hash'],
                $data['is_admin'] ?? 0,
                $data['status'] ?? 1
            ]);
            
            if ($result) {
                return ['status' => 'success', 'msg' => 'User created successfully', 'id' => $this->pdo->lastInsertId()];
            } else {
                return ['status' => 'error', 'msg' => 'Failed to create user'];
            }
            
        } catch (PDOException $e) {
            error_log("UsersModel::createUser failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
    
    /**
     * Update user
     * @param int $id User ID
     * @param array $data User data
     * @return array Result with status and message
     */
    public function updateUser($id, $data) {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['email'])) {
                return ['status' => 'error', 'msg' => 'Name and email are required'];
            }
            
            // Check if email already exists for another user
            $existingUser = $this->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                return ['status' => 'error', 'msg' => 'Email already exists'];
            }
            
            // Build update query
            $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, is_admin = ?, status = ?, updated_at = NOW()";
            $params = [
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['is_admin'] ?? 0,
                $data['status'] ?? 1
            ];
            
            // Add password update if provided
            if (!empty($data['password_hash'])) {
                $sql .= ", password_hash = ?";
                $params[] = $data['password_hash'];
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return ['status' => 'success', 'msg' => 'User updated successfully'];
            } else {
                return ['status' => 'error', 'msg' => 'Failed to update user'];
            }
            
        } catch (PDOException $e) {
            error_log("UsersModel::updateUser failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
    
    /**
     * Delete user
     * @param int $id User ID
     * @return array Result with status and message
     */
    public function deleteUser($id) {
        try {
            // Don't allow deletion of admin users
            $user = $this->getUserById($id);
            if ($user && $user['is_admin']) {
                return ['status' => 'error', 'msg' => 'Cannot delete admin users'];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['status' => 'success', 'msg' => 'User deleted successfully'];
            } else {
                return ['status' => 'error', 'msg' => 'User not found or cannot be deleted'];
            }
            
        } catch (PDOException $e) {
            error_log("UsersModel::deleteUser failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
    
    /**
     * Toggle user status
     * @param int $id User ID
     * @return array Result with status and message
     */
    public function toggleStatus($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET status = CASE WHEN status = 1 THEN 0 ELSE 1 END WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                return ['status' => 'success', 'msg' => 'User status updated successfully'];
            } else {
                return ['status' => 'error', 'msg' => 'Failed to update user status'];
            }
            
        } catch (PDOException $e) {
            error_log("UsersModel::toggleStatus failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
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