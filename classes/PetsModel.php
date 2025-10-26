<?php
/**
 * Pets Model - Production Grade PDO Implementation
 */

class PetsModel {
    private $pdo;
    
    public function __construct() {
        if (!function_exists('db')) {
            require_once __DIR__ . '/../initialize.php';
        }
        $this->pdo = db();
    }
    
    /**
     * Get all pets for a specific user
     * @param int $userId User ID
     * @return array List of pets
     */
    public function allByUser($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, species, breed, age, weight, color, 
                       medical_history, status, created_at, updated_at
                FROM pets 
                WHERE user_id = ? AND status = 1
                ORDER BY name
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Pets::allByUser failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new pet
     * @param array $data Pet data including user_id
     * @return int|false Pet ID if successful, false on failure
     */
    public function create($data) {
        try {
            // Set defaults
            $data['status'] = $data['status'] ?? 1; // 1 = active
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare("
                INSERT INTO pets (user_id, name, species, breed, age, weight, 
                                color, medical_history, status, created_at, updated_at) 
                VALUES (:user_id, :name, :species, :breed, :age, :weight, 
                        :color, :medical_history, :status, :created_at, :updated_at)
            ");
            
            $result = $stmt->execute([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'species' => $data['species'],
                'breed' => $data['breed'] ?? null,
                'age' => $data['age'] ?? null,
                'weight' => $data['weight'] ?? null,
                'color' => $data['color'] ?? null,
                'medical_history' => $data['medical_history'] ?? null,
                'status' => $data['status'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at']
            ]);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Pets::create failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find pet by ID
     * @param int $id Pet ID
     * @param int|null $userId Optional user ID for ownership verification
     * @return array|null Pet data if found, null otherwise
     */
    public function find($id, $userId = null) {
        try {
            $sql = "
                SELECT p.id, p.user_id, p.name, p.species, p.breed, p.age, 
                       p.weight, p.color, p.medical_history, p.status, 
                       p.created_at, p.updated_at,
                       u.firstname, u.lastname, u.email, u.phone
                FROM pets p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.id = ? AND p.status = 1
            ";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND p.user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Pets::find failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update pet data
     * @param int $id Pet ID
     * @param array $data Data to update
     * @param int|null $userId Optional user ID for ownership verification
     * @return bool Success status
     */
    public function update($id, $data, $userId = null) {
        try {
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            // Build dynamic SET clause
            $setParts = [];
            $values = [];
            
            foreach ($data as $field => $value) {
                if (!in_array($field, ['id', 'user_id', 'created_at'])) {
                    $setParts[] = "$field = ?";
                    $values[] = $value;
                }
            }
            
            $values[] = $id; // Add ID for WHERE clause
            
            $sql = "UPDATE pets SET " . implode(', ', $setParts) . " WHERE id = ? AND status = 1";
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $values[] = $userId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
            
        } catch (PDOException $e) {
            error_log("Pets::update failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete pet (set status to 0)
     * @param int $id Pet ID
     * @param int|null $userId Optional user ID for ownership verification
     * @return bool Success status
     */
    public function delete($id, $userId = null) {
        try {
            $sql = "UPDATE pets SET status = 0, updated_at = ? WHERE id = ?";
            $params = [date('Y-m-d H:i:s'), $id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Pets::delete failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get pets with their upcoming appointments
     * @param int $userId User ID
     * @return array List of pets with appointment counts
     */
    public function withAppointmentCounts($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.id, p.name, p.species, p.breed, p.age,
                       COUNT(a.id) as upcoming_appointments
                FROM pets p
                LEFT JOIN appointments a ON p.id = a.pet_id 
                    AND a.schedule_date >= CURDATE() 
                    AND a.status IN ('pending', 'confirmed')
                WHERE p.user_id = ? AND p.status = 1
                GROUP BY p.id, p.name, p.species, p.breed, p.age
                ORDER BY p.name
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Pets::withAppointmentCounts failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all pets (admin function)
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array List of all pets
     */
    public function all($limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.id, p.name, p.species, p.breed, p.age, p.weight, 
                       p.color, p.status, p.created_at, p.updated_at,
                       u.firstname, u.lastname, u.email, u.phone
                FROM pets p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.status = 1
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Pets::all failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total pets for a user
     * @param int $userId User ID
     * @return int Total number of pets
     */
    public function countByUser($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM pets 
                WHERE user_id = ? AND status = 1
            ");
            $stmt->execute([$userId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Pets::countByUser failed: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Search pets by name or species
     * @param string $query Search query
     * @param int|null $userId Optional user ID to restrict search
     * @return array List of matching pets
     */
    public function search($query, $userId = null) {
        try {
            $sql = "
                SELECT p.id, p.name, p.species, p.breed, p.age,
                       u.firstname, u.lastname, u.email
                FROM pets p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.status = 1 
                AND (p.name LIKE ? OR p.species LIKE ? OR p.breed LIKE ?)
            ";
            $params = ["%$query%", "%$query%", "%$query%"];
            
            if ($userId !== null) {
                $sql .= " AND p.user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " ORDER BY p.name LIMIT 20";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Pets::search failed: " . $e->getMessage());
            return [];
        }
    }
}

// Create a global instance for backward compatibility
$Pets = new PetsModel();
?>