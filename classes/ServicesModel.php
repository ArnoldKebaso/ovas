<?php
/**
 * Services Model - Production Grade PDO Implementation
 */

class ServicesModel {
    private $pdo;
    
    public function __construct() {
        if (!function_exists('db')) {
            require_once __DIR__ . '/../initialize.php';
        }
        $this->pdo = db();
    }
    
    /**
     * Get all active services
     * @return array List of active services
     */
    public function active() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description, fee, duration, category, 
                       image_path, status, created_at, updated_at
                FROM services 
                WHERE status = 1
                ORDER BY name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Services::active failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find service by ID
     * @param int $id Service ID
     * @return array|null Service data if found, null otherwise
     */
    public function find($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description, fee, duration, category, 
                       image_path, status, created_at, updated_at
                FROM services 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Services::find failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new service
     * @param array $data Service data
     * @return int|false Service ID if successful, false on failure
     */
    public function create($data) {
        try {
            // Set defaults
            $data['status'] = $data['status'] ?? 1; // 1 = active
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare("
                INSERT INTO services (name, description, fee, duration, category, 
                                    image_path, status, created_at, updated_at) 
                VALUES (:name, :description, :fee, :duration, :category, 
                        :image_path, :status, :created_at, :updated_at)
            ");
            
            $result = $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'fee' => $data['fee'],
                'duration' => $data['duration'] ?? 30, // Default 30 minutes
                'category' => $data['category'] ?? 'General',
                'image_path' => $data['image_path'] ?? null,
                'status' => $data['status'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at']
            ]);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Services::create failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update service data
     * @param int $id Service ID
     * @param array $data Data to update
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
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
                UPDATE services 
                SET " . implode(', ', $setParts) . " 
                WHERE id = ?
            ");
            
            return $stmt->execute($values);
            
        } catch (PDOException $e) {
            error_log("Services::update failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Toggle service status (active/inactive)
     * @param int $id Service ID
     * @return bool Success status
     */
    public function toggle($id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE services 
                SET status = CASE WHEN status = 1 THEN 0 ELSE 1 END,
                    updated_at = ?
                WHERE id = ?
            ");
            return $stmt->execute([date('Y-m-d H:i:s'), $id]);
        } catch (PDOException $e) {
            error_log("Services::toggle failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all services (admin function)
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array List of all services
     */
    public function all($limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description, fee, duration, category, 
                       image_path, status, created_at, updated_at,
                       (SELECT COUNT(*) FROM appointments WHERE service_id = services.id 
                        AND status IN ('pending', 'confirmed')) as active_appointments
                FROM services 
                ORDER BY name
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Services::all failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get services by category
     * @param string $category Service category
     * @return array List of services in category
     */
    public function byCategory($category) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description, fee, duration, category, 
                       image_path, status, created_at, updated_at
                FROM services 
                WHERE category = ? AND status = 1
                ORDER BY name
            ");
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Services::byCategory failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all service categories
     * @return array List of categories
     */
    public function getCategories() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT category
                FROM services 
                WHERE status = 1 AND category IS NOT NULL
                ORDER BY category
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Services::getCategories failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search services by name or description
     * @param string $query Search query
     * @return array List of matching services
     */
    public function search($query) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description, fee, duration, category, 
                       image_path, status, created_at, updated_at
                FROM services 
                WHERE status = 1 
                AND (name LIKE ? OR description LIKE ? OR category LIKE ?)
                ORDER BY 
                    CASE WHEN name LIKE ? THEN 1 ELSE 2 END,
                    name
                LIMIT 20
            ");
            $searchTerm = "%$query%";
            $exactTerm = "$query%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $exactTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Services::search failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get popular services (by appointment count)
     * @param int $limit Number of services to return
     * @return array List of popular services
     */
    public function popular($limit = 6) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.id, s.name, s.description, s.fee, s.duration, 
                       s.category, s.image_path, 
                       COUNT(a.id) as appointment_count
                FROM services s
                LEFT JOIN appointments a ON s.id = a.service_id 
                    AND a.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    AND a.status IN ('pending', 'confirmed', 'completed')
                WHERE s.status = 1
                GROUP BY s.id, s.name, s.description, s.fee, s.duration, 
                         s.category, s.image_path
                ORDER BY appointment_count DESC, s.name
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Services::popular failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total services
     * @param bool $activeOnly Whether to count only active services
     * @return int Total number of services
     */
    public function count($activeOnly = false) {
        try {
            $sql = "SELECT COUNT(*) FROM services";
            if ($activeOnly) {
                $sql .= " WHERE status = 1";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Services::count failed: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Delete service (soft delete)
     * @param int $id Service ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            // Check if service has any appointments
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM appointments 
                WHERE service_id = ? AND status IN ('pending', 'confirmed')
            ");
            $stmt->execute([$id]);
            $activeAppointments = $stmt->fetchColumn();
            
            if ($activeAppointments > 0) {
                return false; // Cannot delete service with active appointments
            }
            
            // Soft delete by setting status to 0
            $stmt = $this->pdo->prepare("
                UPDATE services 
                SET status = 0, updated_at = ?
                WHERE id = ?
            ");
            return $stmt->execute([date('Y-m-d H:i:s'), $id]);
            
        } catch (PDOException $e) {
            error_log("Services::delete failed: " . $e->getMessage());
            return false;
        }
    }
}

// Create a global instance for backward compatibility
$Services = new ServicesModel();
?>