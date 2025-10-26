<?php
/**
 * Admin Appointments API
 * Handles admin appointment management operations
 */

header('Content-Type: application/json');
require_once '../initialize.php';
require_once '../classes/AppointmentsModel.php';
require_once '../classes/CalendarService.php';
require_once '../classes/NotificationService.php';

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

// Authentication check for admin functions
if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] != 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

$action = $_GET['action'] ?? '';
$appointmentsModel = new AppointmentsModel();
$calendarService = new CalendarService();
$notificationService = new NotificationService();

switch ($action) {
    case 'confirm':
        confirmAppointment();
        break;
    case 'complete':
        completeAppointment();
        break;
    case 'cancel':
        cancelAppointment();
        break;
    case 'delete':
        deleteAppointment();
        break;
    case 'send_notification':
        sendNotification();
        break;
    case 'get_stats':
        getAppointmentStats();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Confirm appointment
 */
function confirmAppointment() {
    global $appointmentsModel, $calendarService, $notificationService;
    
    try {
        $appointmentId = (int)($_POST['id'] ?? 0);
        
        if (!$appointmentId) {
            throw new Exception('Appointment ID is required');
        }
        
        // Get appointment details
        $appointment = $appointmentsModel->getAppointmentWithDetails($appointmentId);
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        if ($appointment['status'] !== 'pending') {
            throw new Exception('Only pending appointments can be confirmed');
        }
        
        // Start transaction
        global $conn;
        $conn->beginTransaction();
        
        // Update appointment status
        $updateData = [
            'status' => 'confirmed',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (!$appointmentsModel->update($appointmentId, $updateData)) {
            throw new Exception('Failed to confirm appointment');
        }
        
        // Create Google Calendar event if configured
        if ($calendarService->isConfigured()) {
            $updatedAppointment = $appointmentsModel->getAppointmentWithDetails($appointmentId);
            $eventId = $calendarService->createEvent($updatedAppointment);
            
            if ($eventId) {
                $appointmentsModel->update($appointmentId, ['google_event_id' => $eventId]);
            }
        }
        
        // Send confirmation notification
        sendAppointmentNotification($appointmentId, 'confirmed');
        
        // Log the action
        logAdminAction($appointmentId, 'confirmed', 'Appointment confirmed by admin');
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment confirmed successfully'
        ]);
        
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Complete appointment
 */
function completeAppointment() {
    global $appointmentsModel;
    
    try {
        $appointmentId = (int)($_POST['id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        
        if (!$appointmentId) {
            throw new Exception('Appointment ID is required');
        }
        
        // Get appointment details
        $appointment = $appointmentsModel->find($appointmentId);
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        if (!in_array($appointment['status'], ['confirmed', 'paid'])) {
            throw new Exception('Only confirmed or paid appointments can be completed');
        }
        
        // Update appointment status
        $updateData = [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($notes)) {
            $updateData['completion_notes'] = $notes;
        }
        
        if (!$appointmentsModel->update($appointmentId, $updateData)) {
            throw new Exception('Failed to complete appointment');
        }
        
        // Send completion notification
        sendAppointmentNotification($appointmentId, 'completed');
        
        // Log the action
        logAdminAction($appointmentId, 'completed', 'Appointment completed by admin');
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment marked as completed'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Cancel appointment
 */
function cancelAppointment() {
    global $appointmentsModel, $calendarService;
    
    try {
        $appointmentId = (int)($_POST['id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        
        if (!$appointmentId) {
            throw new Exception('Appointment ID is required');
        }
        
        // Get appointment details
        $appointment = $appointmentsModel->find($appointmentId);
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        if (in_array($appointment['status'], ['completed', 'cancelled'])) {
            throw new Exception('Cannot cancel completed or already cancelled appointments');
        }
        
        // Start transaction
        global $conn;
        $conn->beginTransaction();
        
        // Update appointment status
        $updateData = [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($reason)) {
            $updateData['cancellation_reason'] = $reason;
        }
        
        if (!$appointmentsModel->update($appointmentId, $updateData)) {
            throw new Exception('Failed to cancel appointment');
        }
        
        // Delete Google Calendar event
        if (!empty($appointment['google_event_id'])) {
            $calendarService->deleteEvent($appointment['google_event_id']);
        }
        
        // Process refund if payment was completed
        if ($appointment['payment_status'] === 'completed') {
            processRefund($appointment, 'admin_cancellation');
        }
        
        // Send cancellation notification
        sendAppointmentNotification($appointmentId, 'cancelled', ['reason' => $reason]);
        
        // Log the action
        logAdminAction($appointmentId, 'cancelled', "Appointment cancelled by admin. Reason: $reason");
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment cancelled successfully'
        ]);
        
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Delete appointment (only cancelled appointments)
 */
function deleteAppointment() {
    global $appointmentsModel;
    
    try {
        $appointmentId = (int)($_POST['id'] ?? 0);
        
        if (!$appointmentId) {
            throw new Exception('Appointment ID is required');
        }
        
        // Get appointment details
        $appointment = $appointmentsModel->find($appointmentId);
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        if ($appointment['status'] !== 'cancelled') {
            throw new Exception('Only cancelled appointments can be deleted');
        }
        
        // Delete appointment
        if (!$appointmentsModel->delete($appointmentId)) {
            throw new Exception('Failed to delete appointment');
        }
        
        // Log the action
        logAdminAction($appointmentId, 'deleted', 'Appointment permanently deleted by admin');
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment deleted successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Send notification for appointment
 */
function sendNotification() {
    global $appointmentsModel, $notificationService;
    
    try {
        $appointmentId = (int)($_POST['id'] ?? 0);
        $notificationType = $_POST['type'] ?? 'reminder';
        $customMessage = trim($_POST['message'] ?? '');
        
        if (!$appointmentId) {
            throw new Exception('Appointment ID is required');
        }
        
        // Get appointment details
        $appointment = $appointmentsModel->getAppointmentWithDetails($appointmentId);
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        // Send notification based on type
        switch ($notificationType) {
            case 'reminder':
                sendAppointmentNotification($appointmentId, 'reminder');
                break;
            case 'custom':
                if (empty($customMessage)) {
                    throw new Exception('Custom message is required');
                }
                sendCustomNotification($appointment, $customMessage);
                break;
            default:
                throw new Exception('Invalid notification type');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Notification sent successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Get appointment statistics
 */
function getAppointmentStats() {
    global $appointmentsModel;
    
    try {
        $period = $_GET['period'] ?? 'today'; // today, week, month, year
        
        $stats = $appointmentsModel->getAppointmentStats($period);
        
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Helper function to send appointment notifications
 */
function sendAppointmentNotification($appointmentId, $type, $extraData = []) {
    global $appointmentsModel, $notificationService;
    
    try {
        $appointment = $appointmentsModel->getAppointmentWithDetails($appointmentId);
        if (!$appointment) return false;
        
        switch ($type) {
            case 'confirmed':
                $notificationService->sendAppointmentConfirmation(
                    $appointment['user_email'],
                    $appointment
                );
                break;
            case 'completed':
                // Send completion notification with optional follow-up info
                $emailData = [
                    'user_name' => $appointment['user_name'],
                    'appointment_code' => $appointment['code'],
                    'service_name' => $appointment['service_name'],
                    'pet_name' => $appointment['pet_name'],
                    'completion_date' => date('l, F j, Y'),
                    'clinic_name' => $_ENV['CLINIC_NAME'] ?? 'OVAS Veterinary Clinic'
                ];
                // This would need a completion email template
                break;
            case 'cancelled':
                $notificationService->sendAppointmentCancellationNotification(
                    $appointment['user_email'],
                    array_merge($appointment, $extraData)
                );
                break;
            case 'reminder':
                $notificationService->sendAppointmentReminder(
                    $appointment['user_email'],
                    $appointment,
                    'admin'
                );
                break;
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Failed to send $type notification for appointment $appointmentId: " . $e->getMessage());
        return false;
    }
}

/**
 * Send custom notification
 */
function sendCustomNotification($appointment, $message) {
    global $notificationService;
    
    // Send custom email
    $subject = "OVAS: Message regarding your appointment ({$appointment['code']})";
    $customEmailData = [
        'user_name' => $appointment['user_name'],
        'appointment_code' => $appointment['code'],
        'custom_message' => $message,
        'clinic_name' => $_ENV['CLINIC_NAME'] ?? 'OVAS Veterinary Clinic'
    ];
    
    // This would need a custom message email template
    // For now, send as SMS
    $smsMessage = "OVAS: $message Appointment code: {$appointment['code']}";
    $notificationService->sendSMS($appointment['user_phone'], $smsMessage);
}

/**
 * Process refund for cancelled appointment
 */
function processRefund($appointment, $reason) {
    try {
        global $conn;
        
        $sql = "
            INSERT INTO refund_requests (appointment_id, amount, reason, status, created_at)
            VALUES (?, ?, ?, 'pending', NOW())
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $appointment['id'],
            $appointment['total_fee'],
            $reason
        ]);
        
        error_log("Refund request created for appointment {$appointment['id']} - Amount: KSh {$appointment['total_fee']}");
        
    } catch (Exception $e) {
        error_log("Failed to create refund request: " . $e->getMessage());
    }
}

/**
 * Log admin action
 */
function logAdminAction($appointmentId, $action, $description) {
    try {
        global $conn;
        
        $sql = "
            INSERT INTO admin_action_logs (appointment_id, admin_id, action, description, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $appointmentId,
            $_SESSION['userdata']['id'] ?? null,
            $action,
            $description
        ]);
        
    } catch (Exception $e) {
        error_log("Failed to log admin action: " . $e->getMessage());
    }
}
?>