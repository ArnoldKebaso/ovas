<?php
/**
 * Payments Model - Production Grade PDO Implementation
 */

// Handle direct POST requests to this file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../initialize.php';
    $paymentsModel = new PaymentsModel();
    
    // Handle form submission
    $id = $_POST['id'] ?? null;
    $data = [
        'appointment_id' => intval($_POST['appointment_id'] ?? 0),
        'amount' => floatval($_POST['amount'] ?? 0),
        'payment_method' => $_POST['payment_method'] ?? '',
        'status' => $_POST['status'] ?? 'pending',
        'transaction_ref' => $_POST['transaction_ref'] ?? '',
        'notes' => $_POST['notes'] ?? ''
    ];
    
    if (empty($id)) {
        // Create new payment
        $result = $paymentsModel->createPayment($data);
    } else {
        // Update existing payment
        $result = $paymentsModel->updatePayment($id, $data);
    }
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

class PaymentsModel {
    private $pdo;
    
    public function __construct() {
        if (!function_exists('db')) {
            require_once __DIR__ . '/../initialize.php';
        }
        $this->pdo = db();
    }
    
    /**
     * Create payment record with initiated status
     * @param array $data Payment data
     * @return int|false Payment ID if successful, false on failure
     */
    public function createInitiated($data) {
        try {
            // Set defaults for initiated payment
            $data['status'] = 'initiated';
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = $this->pdo->prepare("
                INSERT INTO payments (user_id, amount, currency, payment_method, 
                                    status, mpesa_checkout_request_id, phone_number,
                                    created_at, updated_at) 
                VALUES (:user_id, :amount, :currency, :payment_method, 
                        :status, :mpesa_checkout_request_id, :phone_number,
                        :created_at, :updated_at)
            ");
            
            $result = $stmt->execute([
                'user_id' => $data['user_id'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'KES',
                'payment_method' => $data['payment_method'] ?? 'mpesa',
                'status' => $data['status'],
                'mpesa_checkout_request_id' => $data['mpesa_checkout_request_id'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at']
            ]);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Payments::createInitiated failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark payment as successful
     * @param int $id Payment ID
     * @param string $reference Payment reference/receipt number
     * @param array $rawPayload Raw payment provider response
     * @return bool Success status
     */
    public function markSuccess($id, $reference, $rawPayload = []) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE payments 
                SET status = 'completed',
                    mpesa_receipt_number = ?,
                    raw_response = ?,
                    completed_at = ?,
                    updated_at = ?
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $reference,
                json_encode($rawPayload),
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
                $id
            ]);
            
        } catch (PDOException $e) {
            error_log("Payments::markSuccess failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark payment as failed
     * @param int $id Payment ID
     * @param array $rawPayload Raw payment provider response
     * @return bool Success status
     */
    public function markFailed($id, $rawPayload = []) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE payments 
                SET status = 'failed',
                    raw_response = ?,
                    failed_at = ?,
                    updated_at = ?
                WHERE id = ?
            ");
            
            return $stmt->execute([
                json_encode($rawPayload),
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
                $id
            ]);
            
        } catch (PDOException $e) {
            error_log("Payments::markFailed failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Attach payment to appointment
     * @param int $paymentId Payment ID
     * @param int $appointmentId Appointment ID
     * @return bool Success status
     */
    public function attachAppointment($paymentId, $appointmentId) {
        try {
            $this->pdo->beginTransaction();
            
            // Update payment with appointment ID
            $stmt = $this->pdo->prepare("
                UPDATE payments 
                SET appointment_id = ?, updated_at = ?
                WHERE id = ?
            ");
            $result1 = $stmt->execute([$appointmentId, date('Y-m-d H:i:s'), $paymentId]);
            
            // Update appointment payment status
            $stmt = $this->pdo->prepare("
                UPDATE appointments 
                SET payment_status = 'paid', updated_at = ?
                WHERE id = ?
            ");
            $result2 = $stmt->execute([date('Y-m-d H:i:s'), $appointmentId]);
            
            if ($result1 && $result2) {
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Payments::attachAppointment failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find payment by ID
     * @param int $id Payment ID
     * @return array|null Payment data if found, null otherwise
     */
    public function find($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.id, p.user_id, p.appointment_id, p.amount, p.currency,
                       p.payment_method, p.status, p.mpesa_checkout_request_id,
                       p.mpesa_receipt_number, p.phone_number, p.raw_response,
                       p.created_at, p.completed_at, p.failed_at, p.updated_at,
                       u.firstname, u.lastname, u.email,
                       a.code as appointment_code, a.schedule_date,
                       s.name as service_name
                FROM payments p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN appointments a ON p.appointment_id = a.id
                LEFT JOIN services s ON a.service_id = s.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Payments::find failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find payment by M-Pesa checkout request ID
     * @param string $checkoutRequestId M-Pesa checkout request ID
     * @return array|null Payment data if found, null otherwise
     */
    public function findByCheckoutRequestId($checkoutRequestId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, user_id, appointment_id, amount, status, 
                       mpesa_checkout_request_id, created_at, updated_at
                FROM payments 
                WHERE mpesa_checkout_request_id = ?
            ");
            $stmt->execute([$checkoutRequestId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Payments::findByCheckoutRequestId failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get payments by user ID
     * @param int $userId User ID
     * @param string|null $status Optional status filter
     * @return array List of user payments
     */
    public function byUser($userId, $status = null) {
        try {
            $sql = "
                SELECT p.id, p.amount, p.currency, p.payment_method, p.status,
                       p.mpesa_receipt_number, p.created_at, p.completed_at, p.failed_at,
                       a.code as appointment_code, a.schedule_date,
                       s.name as service_name
                FROM payments p
                LEFT JOIN appointments a ON p.appointment_id = a.id
                LEFT JOIN services s ON a.service_id = s.id
                WHERE p.user_id = ?
            ";
            
            $params = [$userId];
            
            if ($status) {
                $sql .= " AND p.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY p.created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Payments::byUser failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all payments (admin function)
     * @param array $filters Optional filters (status, date_from, date_to, payment_method)
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array List of payments
     */
    public function all($filters = [], $limit = 50, $offset = 0) {
        try {
            $sql = "
                SELECT p.id, p.amount, p.currency, p.payment_method, p.status,
                       p.mpesa_receipt_number, p.phone_number, p.created_at, 
                       p.completed_at, p.failed_at,
                       u.firstname, u.lastname, u.email,
                       a.code as appointment_code, a.schedule_date,
                       s.name as service_name
                FROM payments p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN appointments a ON p.appointment_id = a.id
                LEFT JOIN services s ON a.service_id = s.id
                WHERE 1=1
            ";
            
            $params = [];
            
            if (!empty($filters['status'])) {
                $sql .= " AND p.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND DATE(p.created_at) >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND DATE(p.created_at) <= ?";
                $params[] = $filters['date_to'];
            }
            
            if (!empty($filters['payment_method'])) {
                $sql .= " AND p.payment_method = ?";
                $params[] = $filters['payment_method'];
            }
            
            $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Payments::all failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get payment statistics
     * @param string|null $dateFrom Optional start date filter
     * @param string|null $dateTo Optional end date filter
     * @return array Payment statistics
     */
    public function getStats($dateFrom = null, $dateTo = null) {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'initiated' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_revenue,
                    AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_payment
                FROM payments
                WHERE 1=1
            ";
            
            $params = [];
            
            if ($dateFrom) {
                $sql .= " AND DATE(created_at) >= ?";
                $params[] = $dateFrom;
            }
            
            if ($dateTo) {
                $sql .= " AND DATE(created_at) <= ?";
                $params[] = $dateTo;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            // Ensure numeric values
            $result['total_revenue'] = (float)$result['total_revenue'];
            $result['avg_payment'] = (float)$result['avg_payment'];
            
            return $result;
        } catch (PDOException $e) {
            error_log("Payments::getStats failed: " . $e->getMessage());
            return [
                'total' => 0, 'completed' => 0, 'failed' => 0, 'pending' => 0,
                'total_revenue' => 0.0, 'avg_payment' => 0.0
            ];
        }
    }
    
    /**
     * Update payment status
     * @param int $id Payment ID
     * @param string $status New status
     * @param array $additionalData Optional additional data to update
     * @return bool Success status
     */
    public function updateStatus($id, $status, $additionalData = []) {
        try {
            $updateData = array_merge($additionalData, [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Add timestamp based on status
            if ($status === 'completed' && !isset($updateData['completed_at'])) {
                $updateData['completed_at'] = date('Y-m-d H:i:s');
            } elseif ($status === 'failed' && !isset($updateData['failed_at'])) {
                $updateData['failed_at'] = date('Y-m-d H:i:s');
            }
            
            // Build dynamic SET clause
            $setParts = [];
            $values = [];
            
            foreach ($updateData as $field => $value) {
                if ($field !== 'id') {
                    $setParts[] = "$field = ?";
                    $values[] = $value;
                }
            }
            
            $values[] = $id; // Add ID for WHERE clause
            
            $stmt = $this->pdo->prepare("
                UPDATE payments 
                SET " . implode(', ', $setParts) . " 
                WHERE id = ?
            ");
            
            return $stmt->execute($values);
            
        } catch (PDOException $e) {
            error_log("Payments::updateStatus failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clean up old initiated payments (older than 1 hour)
     * @return int Number of payments cleaned up
     */
    public function cleanupExpiredPayments() {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE payments 
                SET status = 'expired', updated_at = ?
                WHERE status = 'initiated' 
                AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([date('Y-m-d H:i:s')]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Payments::cleanupExpiredPayments failed: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Find payment by M-Pesa checkout request ID
     * @param string $checkoutRequestId M-Pesa checkout request ID
     * @return array|false Payment record or false if not found
     */
    public function findByCheckoutRequestId($checkoutRequestId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM payments 
                WHERE mpesa_checkout_request_id = ?
                LIMIT 1
            ");
            $stmt->execute([$checkoutRequestId]);
            $result = $stmt->fetch();
            return $result ?: false;
        } catch (PDOException $e) {
            error_log("Payments::findByCheckoutRequestId failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get monthly revenue report
     * @param int $year Year for the report
     * @return array Monthly revenue data
     */
    public function getMonthlyRevenue($year) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    MONTH(completed_at) as month,
                    MONTHNAME(completed_at) as month_name,
                    COUNT(*) as payment_count,
                    SUM(amount) as total_revenue
                FROM payments 
                WHERE status = 'completed' 
                AND YEAR(completed_at) = ?
                GROUP BY MONTH(completed_at), MONTHNAME(completed_at)
                ORDER BY MONTH(completed_at)
            ");
            $stmt->execute([$year]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Payments::getMonthlyRevenue failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new payment (for admin forms)
     * @param array $data Payment data
     * @return array Result with status and message
     */
    public function createPayment($data) {
        try {
            // Validate required fields
            if (empty($data['appointment_id']) || empty($data['amount']) || empty($data['payment_method'])) {
                return ['status' => 'error', 'msg' => 'Appointment, amount, and payment method are required'];
            }
            
            // Get appointment details for user_id
            $apptStmt = $this->pdo->prepare("SELECT user_id FROM appointments WHERE id = ?");
            $apptStmt->execute([$data['appointment_id']]);
            $appointment = $apptStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$appointment) {
                return ['status' => 'error', 'msg' => 'Invalid appointment selected'];
            }
            
            $stmt = $this->pdo->prepare("
                INSERT INTO payments (user_id, appointment_id, amount, payment_method, status, transaction_ref, notes, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $result = $stmt->execute([
                $appointment['user_id'],
                $data['appointment_id'],
                $data['amount'],
                $data['payment_method'],
                $data['status'],
                $data['transaction_ref'],
                $data['notes']
            ]);
            
            if ($result) {
                // Update appointment status to paid if payment is completed
                if ($data['status'] === 'completed') {
                    $updateAppt = $this->pdo->prepare("UPDATE appointments SET status = 'paid' WHERE id = ?");
                    $updateAppt->execute([$data['appointment_id']]);
                }
                
                return ['status' => 'success', 'msg' => 'Payment created successfully', 'id' => $this->pdo->lastInsertId()];
            } else {
                return ['status' => 'error', 'msg' => 'Failed to create payment'];
            }
            
        } catch (PDOException $e) {
            error_log("PaymentsModel::createPayment failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
    
    /**
     * Update payment
     * @param int $id Payment ID
     * @param array $data Payment data
     * @return array Result with status and message
     */
    public function updatePayment($id, $data) {
        try {
            // Validate required fields
            if (empty($data['appointment_id']) || empty($data['amount']) || empty($data['payment_method'])) {
                return ['status' => 'error', 'msg' => 'Appointment, amount, and payment method are required'];
            }
            
            $stmt = $this->pdo->prepare("
                UPDATE payments 
                SET appointment_id = ?, amount = ?, payment_method = ?, status = ?, transaction_ref = ?, notes = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $data['appointment_id'],
                $data['amount'],
                $data['payment_method'],
                $data['status'],
                $data['transaction_ref'],
                $data['notes'],
                $id
            ]);
            
            if ($result) {
                // Update appointment status based on payment status
                if ($data['status'] === 'completed') {
                    $updateAppt = $this->pdo->prepare("UPDATE appointments SET status = 'paid' WHERE id = ?");
                    $updateAppt->execute([$data['appointment_id']]);
                } elseif ($data['status'] === 'failed') {
                    $updateAppt = $this->pdo->prepare("UPDATE appointments SET status = 'confirmed' WHERE id = ? AND status = 'paid'");
                    $updateAppt->execute([$data['appointment_id']]);
                }
                
                return ['status' => 'success', 'msg' => 'Payment updated successfully'];
            } else {
                return ['status' => 'error', 'msg' => 'Failed to update payment'];
            }
            
        } catch (PDOException $e) {
            error_log("PaymentsModel::updatePayment failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
    
    /**
     * Delete payment
     * @param int $id Payment ID
     * @return array Result with status and message
     */
    public function deletePayment($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM payments WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['status' => 'success', 'msg' => 'Payment deleted successfully'];
            } else {
                return ['status' => 'error', 'msg' => 'Payment not found'];
            }
            
        } catch (PDOException $e) {
            error_log("PaymentsModel::deletePayment failed: " . $e->getMessage());
            return ['status' => 'error', 'msg' => 'Database error occurred'];
        }
    }
}

// Create a global instance for backward compatibility
$Payments = new PaymentsModel();
?>