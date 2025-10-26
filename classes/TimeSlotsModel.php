<?php
/**
 * TimeSlots Model - Production Grade PDO Implementation
 */

class TimeSlotsModel {
    private $pdo;
    
    public function __construct() {
        if (!function_exists('db')) {
            require_once __DIR__ . '/../initialize.php';
        }
        $this->pdo = db();
    }
    
    /**
     * Get all active time slots
     * @return array List of active time slots
     */
    public function active() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, start_time, end_time, max_appointments, status, 
                       created_at, updated_at
                FROM time_slots 
                WHERE status = 1
                ORDER BY start_time
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("TimeSlots::active failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find time slot by ID
     * @param int $id Time slot ID
     * @return array|null Time slot data if found, null otherwise
     */
    public function find($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, start_time, end_time, max_appointments, status, 
                       created_at, updated_at
                FROM time_slots 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("TimeSlots::find failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get available time slots for a specific date
     * @param string $date Date in Y-m-d format
     * @param int|null $serviceId Optional service ID for service-specific slots
     * @return array List of available time slots with availability info
     */
    public function availableForDate($date, $serviceId = null) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ts.id, ts.start_time, ts.end_time, ts.max_appointments,
                       COUNT(a.id) as current_bookings,
                       (ts.max_appointments - COUNT(a.id)) as available_spots,
                       CASE 
                           WHEN COUNT(a.id) >= ts.max_appointments THEN 0 
                           ELSE 1 
                       END as is_available
                FROM time_slots ts
                LEFT JOIN appointments a ON ts.id = a.time_slot_id 
                    AND a.schedule_date = ? 
                    AND a.status IN ('pending', 'confirmed')
                WHERE ts.status = 1
                GROUP BY ts.id, ts.start_time, ts.end_time, ts.max_appointments
                ORDER BY ts.start_time
            ");
            $stmt->execute([$date]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("TimeSlots::availableForDate failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if a specific time slot is available on a date
     * @param int $timeSlotId Time slot ID
     * @param string $date Date in Y-m-d format
     * @return bool True if available, false otherwise
     */
    public function isAvailable($timeSlotId, $date) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ts.max_appointments, COUNT(a.id) as current_bookings
                FROM time_slots ts
                LEFT JOIN appointments a ON ts.id = a.time_slot_id 
                    AND a.schedule_date = ? 
                    AND a.status IN ('pending', 'confirmed')
                WHERE ts.id = ? AND ts.status = 1
                GROUP BY ts.id, ts.max_appointments
            ");
            $stmt->execute([$date, $timeSlotId]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return false; // Time slot doesn't exist or is inactive
            }
            
            return $result['current_bookings'] < $result['max_appointments'];
        } catch (PDOException $e) {
            error_log("TimeSlots::isAvailable failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get time slots with booking statistics
     * @param string|null $date Optional specific date, defaults to today
     * @return array Time slots with booking statistics
     */
    public function withBookingStats($date = null) {
        try {
            if ($date === null) {
                $date = date('Y-m-d');
            }
            
            $stmt = $this->pdo->prepare("
                SELECT ts.id, ts.start_time, ts.end_time, ts.max_appointments,
                       COUNT(a.id) as total_bookings,
                       COUNT(CASE WHEN a.status = 'confirmed' THEN 1 END) as confirmed_bookings,
                       COUNT(CASE WHEN a.status = 'pending' THEN 1 END) as pending_bookings,
                       COUNT(CASE WHEN a.status = 'cancelled' THEN 1 END) as cancelled_bookings,
                       (ts.max_appointments - COUNT(CASE WHEN a.status IN ('pending', 'confirmed') THEN 1 END)) as available_spots
                FROM time_slots ts
                LEFT JOIN appointments a ON ts.id = a.time_slot_id AND a.schedule_date = ?
                WHERE ts.status = 1
                GROUP BY ts.id, ts.start_time, ts.end_time, ts.max_appointments
                ORDER BY ts.start_time
            ");
            $stmt->execute([$date]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("TimeSlots::withBookingStats failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new time slot
     * @param array $data Time slot data
     * @return int|false Time slot ID if successful, false on failure
     */
    public function create($data) {
        try {
            // Set defaults
            $data['status'] = $data['status'] ?? 1; // 1 = active
            $data['max_appointments'] = $data['max_appointments'] ?? 5; // Default 5 appointments per slot
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare("
                INSERT INTO time_slots (start_time, end_time, max_appointments, status, created_at, updated_at) 
                VALUES (:start_time, :end_time, :max_appointments, :status, :created_at, :updated_at)
            ");
            
            $result = $stmt->execute([
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'max_appointments' => $data['max_appointments'],
                'status' => $data['status'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at']
            ]);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("TimeSlots::create failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update time slot data
     * @param int $id Time slot ID
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
                UPDATE time_slots 
                SET " . implode(', ', $setParts) . " 
                WHERE id = ?
            ");
            
            return $stmt->execute($values);
            
        } catch (PDOException $e) {
            error_log("TimeSlots::update failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Toggle time slot status (active/inactive)
     * @param int $id Time slot ID
     * @return bool Success status
     */
    public function toggle($id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE time_slots 
                SET status = CASE WHEN status = 1 THEN 0 ELSE 1 END,
                    updated_at = ?
                WHERE id = ?
            ");
            return $stmt->execute([date('Y-m-d H:i:s'), $id]);
        } catch (PDOException $e) {
            error_log("TimeSlots::toggle failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all time slots (admin function)
     * @return array List of all time slots
     */
    public function all() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, start_time, end_time, max_appointments, status, 
                       created_at, updated_at,
                       (SELECT COUNT(*) FROM appointments WHERE time_slot_id = time_slots.id 
                        AND schedule_date >= CURDATE() AND status IN ('pending', 'confirmed')) as active_bookings
                FROM time_slots 
                ORDER BY start_time
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("TimeSlots::all failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get time slots formatted for display
     * @return array Time slots with formatted time display
     */
    public function forDisplay() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, start_time, end_time, max_appointments, status,
                       TIME_FORMAT(start_time, '%h:%i %p') as start_time_12h,
                       TIME_FORMAT(end_time, '%h:%i %p') as end_time_12h,
                       CONCAT(TIME_FORMAT(start_time, '%h:%i %p'), ' - ', TIME_FORMAT(end_time, '%h:%i %p')) as time_range
                FROM time_slots 
                WHERE status = 1
                ORDER BY start_time
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("TimeSlots::forDisplay failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total time slots
     * @param bool $activeOnly Whether to count only active time slots
     * @return int Total number of time slots
     */
    public function count($activeOnly = false) {
        try {
            $sql = "SELECT COUNT(*) FROM time_slots";
            if ($activeOnly) {
                $sql .= " WHERE status = 1";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("TimeSlots::count failed: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Delete time slot (only if no active appointments)
     * @param int $id Time slot ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            // Check if time slot has any future appointments
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM appointments 
                WHERE time_slot_id = ? 
                AND schedule_date >= CURDATE() 
                AND status IN ('pending', 'confirmed')
            ");
            $stmt->execute([$id]);
            $futureAppointments = $stmt->fetchColumn();
            
            if ($futureAppointments > 0) {
                return false; // Cannot delete time slot with future appointments
            }
            
            // Soft delete by setting status to 0
            $stmt = $this->pdo->prepare("
                UPDATE time_slots 
                SET status = 0, updated_at = ?
                WHERE id = ?
            ");
            return $stmt->execute([date('Y-m-d H:i:s'), $id]);
            
        } catch (PDOException $e) {
            error_log("TimeSlots::delete failed: " . $e->getMessage());
            return false;
        }
    }
}

// Create a global instance for backward compatibility
$TimeSlots = new TimeSlotsModel();
?>