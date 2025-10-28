<?php
/**
 * Appointments Model - Production Grade PDO Implementation
 */

// Handle direct POST requests to this file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../initialize.php';
    $appointmentsModel = new AppointmentsModel();
    
    // Handle form submission
    $id = $_POST['id'] ?? null;
    $data = [
        'user_id' => intval($_POST['user_id'] ?? 0),
        'service_id' => intval($_POST['service_id'] ?? 0),
        'schedule_date' => $_POST['schedule_date'] ?? '',
        'start_time' => $_POST['start_time'] ?? '',
        'fee' => floatval($_POST['fee'] ?? 0),
        'status' => $_POST['status'] ?? 'pending',
        'notes' => $_POST['notes'] ?? ''
    ];
    
    if (empty($id)) {
        // Create new appointment
        $result = $appointmentsModel->createAppointment($data);
    } else {
        // Update existing appointment
        $result = $appointmentsModel->updateAppointment($id, $data);
    }
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

class AppointmentsModel {
    private $pdo;
    
    public function __construct() {
        if (!function_exists('db')) {
            require_once __DIR__ . '/../initialize.php';
        }
        $this->pdo = db();
    }
    
    /**
     * Check capacity used for a specific date and time slot
     * @param string $date Date in Y-m-d format
     * @param int $slotId Time slot ID
     * @return int Number of appointments already booked
     */
    public function capacityUsed($date, $slotId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM appointments 
                WHERE schedule_date = ? 
                AND time_slot_id = ? 
                AND status IN ('pending', 'confirmed')
            ");
            $stmt->execute([$date, $slotId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Appointments::capacityUsed failed: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Create new appointment with unique code
     * @param array $data Appointment data
     * @return int|false Appointment ID if successful, false on failure
     */
    public function create($data) {
        try {
            $this->pdo->beginTransaction();
            
            // Generate unique appointment code
            $date = $data['schedule_date'];
            $dateCode = date('Ymd', strtotime($date));
            $attempts = 0;
            $maxAttempts = 10;
            
            do {
                $randomSuffix = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
                $code = "OVAS-{$dateCode}-{$randomSuffix}";
                
                // Check if code exists
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM appointments WHERE code = ?");
                $stmt->execute([$code]);
                $exists = $stmt->fetchColumn() > 0;
                $attempts++;
            } while ($exists && $attempts < $maxAttempts);
            
            if ($exists) {
                $this->pdo->rollBack();
                return false; // Unable to generate unique code
            }
            
            // Set defaults
            $data['code'] = $code;
            $data['status'] = $data['status'] ?? 'pending';
            $data['payment_status'] = $data['payment_status'] ?? 'unpaid';
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare("
                INSERT INTO appointments (user_id, pet_id, service_id, time_slot_id, 
                                        schedule_date, code, notes, status, payment_status,
                                        google_event_id, created_at, updated_at) 
                VALUES (:user_id, :pet_id, :service_id, :time_slot_id, 
                        :schedule_date, :code, :notes, :status, :payment_status,
                        :google_event_id, :created_at, :updated_at)
            ");
            
            $result = $stmt->execute([
                'user_id' => $data['user_id'],
                'pet_id' => $data['pet_id'],
                'service_id' => $data['service_id'],
                'time_slot_id' => $data['time_slot_id'],
                'schedule_date' => $data['schedule_date'],
                'code' => $data['code'],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'payment_status' => $data['payment_status'],
                'google_event_id' => $data['google_event_id'] ?? null,
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at']
            ]);
            
            if ($result) {
                $appointmentId = $this->pdo->lastInsertId();
                $this->pdo->commit();
                return $appointmentId;
            } else {
                $this->pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Appointments::create failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get appointments by user ID
     * @param int $userId User ID
     * @param string|null $status Optional status filter
     * @return array List of user appointments
     */
    public function byUser($userId, $status = null) {
        try {
            $sql = "
                SELECT a.id, a.code, a.schedule_date, a.notes, a.status, 
                       a.payment_status, a.google_event_id, a.created_at, a.updated_at,
                       s.name as service_name, s.fee as service_fee, s.duration as service_duration,
                       p.name as pet_name, p.species as pet_species, p.breed as pet_breed,
                       ts.start_time, ts.end_time,
                       CONCAT(ts.start_time, ' - ', ts.end_time) as time_range
                FROM appointments a
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN time_slots ts ON a.time_slot_id = ts.id
                WHERE a.user_id = ?
            ";
            
            $params = [$userId];
            
            if ($status) {
                $sql .= " AND a.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY a.schedule_date DESC, ts.start_time DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Appointments::byUser failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find appointment by ID
     * @param int $id Appointment ID
     * @param int|null $userId Optional user ID for ownership verification
     * @return array|null Appointment data if found, null otherwise
     */
    public function find($id, $userId = null) {
        try {
            $sql = "
                SELECT a.id, a.user_id, a.pet_id, a.service_id, a.time_slot_id,
                       a.schedule_date, a.code, a.notes, a.status, a.payment_status,
                       a.google_event_id, a.created_at, a.updated_at,
                       s.name as service_name, s.fee as service_fee, s.duration as service_duration,
                       p.name as pet_name, p.species as pet_species, p.breed as pet_breed,
                       p.age as pet_age, p.weight as pet_weight, p.medical_history,
                       ts.start_time, ts.end_time,
                       u.firstname, u.lastname, u.email, u.phone,
                       CONCAT(ts.start_time, ' - ', ts.end_time) as time_range
                FROM appointments a
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN time_slots ts ON a.time_slot_id = ts.id
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.id = ?
            ";
            
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND a.user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Appointments::find failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update appointment status
     * @param int $id Appointment ID
     * @param string $status New status
     * @param int|null $userId Optional user ID for ownership verification
     * @return bool Success status
     */
    public function updateStatus($id, $status, $userId = null) {
        try {
            $sql = "UPDATE appointments SET status = ?, updated_at = ? WHERE id = ?";
            $params = [$status, date('Y-m-d H:i:s'), $id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Appointments::updateStatus failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reschedule appointment
     * @param int $id Appointment ID
     * @param string $newDate New date in Y-m-d format
     * @param int $newSlotId New time slot ID
     * @param int|null $userId Optional user ID for ownership verification
     * @return bool Success status
     */
    public function reschedule($id, $newDate, $newSlotId, $userId = null) {
        try {
            $this->pdo->beginTransaction();
            
            // Check capacity for new slot
            $capacityUsed = $this->capacityUsed($newDate, $newSlotId);
            
            // Get max appointments for the slot
            $stmt = $this->pdo->prepare("SELECT max_appointments FROM time_slots WHERE id = ?");
            $stmt->execute([$newSlotId]);
            $maxAppointments = $stmt->fetchColumn();
            
            if ($capacityUsed >= $maxAppointments) {
                $this->pdo->rollBack();
                return false; // No capacity available
            }
            
            // Update appointment
            $sql = "
                UPDATE appointments 
                SET schedule_date = ?, time_slot_id = ?, updated_at = ?
                WHERE id = ?
            ";
            $params = [$newDate, $newSlotId, date('Y-m-d H:i:s'), $id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Appointments::reschedule failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all appointments (admin function)
     * @param array $filters Optional filters (status, date_from, date_to, service_id)
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array List of appointments
     */
    public function all($filters = [], $limit = 50, $offset = 0) {
        try {
            $sql = "
                SELECT a.id, a.code, a.schedule_date, a.status, a.payment_status,
                       a.created_at, a.updated_at,
                       s.name as service_name, s.fee as service_fee,
                       p.name as pet_name, p.species as pet_species,
                       u.firstname, u.lastname, u.email, u.phone,
                       ts.start_time, ts.end_time,
                       CONCAT(ts.start_time, ' - ', ts.end_time) as time_range
                FROM appointments a
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN users u ON a.user_id = u.id
                LEFT JOIN time_slots ts ON a.time_slot_id = ts.id
                WHERE 1=1
            ";
            
            $params = [];
            
            if (!empty($filters['status'])) {
                $sql .= " AND a.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND a.schedule_date >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND a.schedule_date <= ?";
                $params[] = $filters['date_to'];
            }
            
            if (!empty($filters['service_id'])) {
                $sql .= " AND a.service_id = ?";
                $params[] = $filters['service_id'];
            }
            
            $sql .= " ORDER BY a.schedule_date DESC, ts.start_time DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Appointments::all failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get upcoming appointments for reminders
     * @param string $reminderType Type of reminder (48h, 3h, 1h)
     * @return array List of appointments due for reminders
     */
    public function forReminders($reminderType) {
        try {
            $sql = "
                SELECT a.id, a.code, a.schedule_date, a.status,
                       s.name as service_name, s.duration as service_duration,
                       p.name as pet_name, p.species as pet_species,
                       u.firstname, u.lastname, u.email, u.phone,
                       ts.start_time, ts.end_time,
                       CONCAT(a.schedule_date, ' ', ts.start_time) as appointment_datetime
                FROM appointments a
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN pets p ON a.pet_id = p.id
                LEFT JOIN users u ON a.user_id = u.id
                LEFT JOIN time_slots ts ON a.time_slot_id = ts.id
                WHERE a.status IN ('pending', 'confirmed')
                AND CONCAT(a.schedule_date, ' ', ts.start_time) > NOW()
            ";
            
            // Add time-specific conditions based on reminder type
            switch ($reminderType) {
                case '48h':
                    $sql .= " AND CONCAT(a.schedule_date, ' ', ts.start_time) BETWEEN 
                             DATE_ADD(NOW(), INTERVAL 47 HOUR) AND 
                             DATE_ADD(NOW(), INTERVAL 49 HOUR)";
                    break;
                case '3h':
                    $sql .= " AND CONCAT(a.schedule_date, ' ', ts.start_time) BETWEEN 
                             DATE_ADD(NOW(), INTERVAL 2 HOUR 50 MINUTE) AND 
                             DATE_ADD(NOW(), INTERVAL 3 HOUR 10 MINUTE)";
                    break;
                case '1h':
                    $sql .= " AND CONCAT(a.schedule_date, ' ', ts.start_time) BETWEEN 
                             DATE_ADD(NOW(), INTERVAL 50 MINUTE) AND 
                             DATE_ADD(NOW(), INTERVAL 70 MINUTE)";
                    break;
            }
            
            $sql .= " ORDER BY a.schedule_date, ts.start_time";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Appointments::forReminders failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update Google Calendar event ID
     * @param int $id Appointment ID
     * @param string $eventId Google Calendar event ID
     * @return bool Success status
     */
    public function updateGoogleEventId($id, $eventId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE appointments 
                SET google_event_id = ?, updated_at = ?
                WHERE id = ?
            ");
            return $stmt->execute([$eventId, date('Y-m-d H:i:s'), $id]);
        } catch (PDOException $e) {
            error_log("Appointments::updateGoogleEventId failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count appointments by status
     * @param string|null $status Optional status filter
     * @return int Number of appointments
     */
    public function count($status = null) {
        try {
            $sql = "SELECT COUNT(*) FROM appointments";
            $params = [];
            
            if ($status) {
                $sql .= " WHERE status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Appointments::count failed: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get appointment statistics
     * @param string|null $dateFrom Optional start date filter
     * @param string|null $dateTo Optional end date filter
     * @return array Appointment statistics
     */
    public function getStats($dateFrom = null, $dateTo = null) {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid
                FROM appointments
                WHERE 1=1
            ";
            
            $params = [];
            
            if ($dateFrom) {
                $sql .= " AND schedule_date >= ?";
                $params[] = $dateFrom;
            }
            
            if ($dateTo) {
                $sql .= " AND schedule_date <= ?";
                $params[] = $dateTo;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Appointments::getStats failed: " . $e->getMessage());
            return [
                'total' => 0, 'pending' => 0, 'confirmed' => 0, 
                'completed' => 0, 'cancelled' => 0, 'paid' => 0
            ];
        }
    }
    
    /**
     * Create new appointment (for admin forms)
     * @param array $data Appointment data
     * @return array Result with status and message
     */
    public function createAppointment($data) {
        try {
            // Validate required fields
            if (empty($data['user_id']) || empty($data['service_id']) || empty($data['schedule_date']) || empty($data['start_time'])) {
                return ['status' => 'error', 'msg' => 'All required fields must be filled'];
            }
            
            // Generate unique appointment code
            $date = $data['schedule_date'];
            $dateCode = date('Ymd', strtotime($date));
            
            // Find next available number for this date
            $stmt = $this->pdo->prepare("SELECT MAX(CAST(SUBSTRING(code, -3) AS UNSIGNED)) FROM appointments WHERE code LIKE ?");
            $stmt->execute([$dateCode . '%']);
            $maxNum = $stmt->fetchColumn() ?: 0;
            $nextNum = str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
            $code = $dateCode . $nextNum;
            
            // Calculate end time based on service duration
            $serviceStmt = $this->pdo->prepare("SELECT duration_min FROM services WHERE id = ?");
            $serviceStmt->execute([$data['service_id']]);
            $duration = $serviceStmt->fetchColumn() ?: 30; // Default 30 minutes
            
            $endTime = date('H:i:s', strtotime($data['start_time'] . ' +' . $duration . ' minutes'));
            
            $stmt = $this->pdo->prepare("
                INSERT INTO appointments (user_id, service_id, schedule_date, start_time, end_time, fee, status, notes, code, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $result = $stmt->execute([
                $data['user_id'],
                $data['service_id'],
                $data['schedule_date'],
                $data['start_time'],
                $endTime,
                $data['fee'],
                $data['status'],
                $data['notes'],
                $code
            ]);
            
            if ($result) {
                return ['status' => 'success', 'msg' => 'Appointment created successfully', 'id' => $this->pdo->lastInsertId(), 'code' => $code];
            } else {
                return ['status' => 'error', 'msg' => 'Failed to create appointment'];
            }
            
        } catch (PDOException $e) {
            error_log("AppointmentsModel::createAppointment failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
    
    /**
     * Update appointment
     * @param int $id Appointment ID
     * @param array $data Appointment data
     * @return array Result with status and message
     */
    public function updateAppointment($id, $data) {
        try {
            // Validate required fields
            if (empty($data['user_id']) || empty($data['service_id']) || empty($data['schedule_date']) || empty($data['start_time'])) {
                return ['status' => 'error', 'msg' => 'All required fields must be filled'];
            }
            
            // Calculate end time based on service duration
            $serviceStmt = $this->pdo->prepare("SELECT duration_min FROM services WHERE id = ?");
            $serviceStmt->execute([$data['service_id']]);
            $duration = $serviceStmt->fetchColumn() ?: 30; // Default 30 minutes
            
            $endTime = date('H:i:s', strtotime($data['start_time'] . ' +' . $duration . ' minutes'));
            
            $stmt = $this->pdo->prepare("
                UPDATE appointments 
                SET user_id = ?, service_id = ?, schedule_date = ?, start_time = ?, end_time = ?, fee = ?, status = ?, notes = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $data['user_id'],
                $data['service_id'],
                $data['schedule_date'],
                $data['start_time'],
                $endTime,
                $data['fee'],
                $data['status'],
                $data['notes'],
                $id
            ]);
            
            if ($result) {
                return ['status' => 'success', 'msg' => 'Appointment updated successfully'];
            } else {
                return ['status' => 'error', 'msg' => 'Failed to update appointment'];
            }
            
        } catch (PDOException $e) {
            error_log("AppointmentsModel::updateAppointment failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
    
    /**
     * Delete appointment
     * @param int $id Appointment ID
     * @return array Result with status and message
     */
    public function deleteAppointment($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM appointments WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['status' => 'success', 'msg' => 'Appointment deleted successfully'];
            } else {
                return ['status' => 'error', 'msg' => 'Appointment not found'];
            }
            
        } catch (PDOException $e) {
            error_log("AppointmentsModel::deleteAppointment failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
}

// Create a global instance for backward compatibility
$Appointments = new AppointmentsModel();
?>