<?php
/**
 * Appointment Management API
 * Handles reschedule and cancel operations with proper validation
 */

header('Content-Type: application/json');
require_once '../initialize.php';
require_once '../classes/AppointmentsModel.php';
require_once '../classes/CalendarService.php';
require_once '../classes/NotificationService.php';
require_once '../classes/SecurityUtil.php';

// Initialize secure session
if (!SecurityUtil::initSecureSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session security violation']);
    exit;
}

// Rate limiting for appointment management
$action = $_GET['action'] ?? '';
if (!SecurityUtil::checkRateLimit('appointment_' . $action, 5, 300)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please wait before trying again.']);
    exit;
}

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!SecurityUtil::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        SecurityUtil::logSecurityEvent('CSRF_VIOLATION', ['endpoint' => 'appointment_management', 'action' => $action], 'WARNING');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

$appointmentsModel = new AppointmentsModel();
$calendarService = new CalendarService();
$notificationService = new NotificationService();

switch ($action) {
    case 'reschedule':
        handleReschedule();
        break;
    case 'cancel':
        handleCancel();
        break;
    case 'get_available_slots':
        getAvailableSlots();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Handle appointment rescheduling
 */
function handleReschedule() {
    global $appointmentsModel, $calendarService, $notificationService;
    
    try {
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $newDate = $_POST['new_date'] ?? '';
        $newTimeSlotId = (int)($_POST['new_time_slot_id'] ?? 0);
        $reason = $_POST['reason'] ?? '';
        
        // Validate inputs
        if (!$appointmentId || !$newDate || !$newTimeSlotId) {
            throw new Exception('Missing required fields');
        }
        
        // Get current appointment
        $appointment = $appointmentsModel->find($appointmentId);
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        // Check ownership (if user session exists)
        if (isset($_SESSION['user_id']) && $appointment['user_id'] != $_SESSION['user_id']) {
            throw new Exception('Access denied');
        }
        
        // Validate reschedule timing (must be at least 24 hours before original appointment)
        $originalDateTime = $appointment['schedule_date'] . ' ' . $appointment['start_time'];
        $hoursUntilAppointment = (strtotime($originalDateTime) - time()) / 3600;
        
        if ($hoursUntilAppointment < 24) {
            throw new Exception('Appointments can only be rescheduled at least 24 hours in advance');
        }
        
        // Validate new date (must be at least 24 hours from now)
        $newDateTime = $newDate . ' 00:00:00';
        $hoursUntilNewDate = (strtotime($newDateTime) - time()) / 3600;
        
        if ($hoursUntilNewDate < 24) {
            throw new Exception('New appointment date must be at least 24 hours from now');
        }
        
        // Check if new slot is available
        if (!$appointmentsModel->isTimeSlotAvailable($newDate, $newTimeSlotId, $appointmentId)) {
            throw new Exception('Selected time slot is not available');
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // Update appointment
        $updateData = [
            'schedule_date' => $newDate,
            'time_slot_id' => $newTimeSlotId,
            'status' => 'confirmed', // Reset status if needed
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (!$appointmentsModel->update($appointmentId, $updateData)) {
            throw new Exception('Failed to update appointment');
        }
        
        // Update Google Calendar event
        $updatedAppointment = $appointmentsModel->find($appointmentId);
        if ($appointment['google_event_id']) {
            $calendarService->updateEvent($updatedAppointment);
        }
        
        // Log the reschedule with reason
        logAppointmentAction($appointmentId, 'rescheduled', [
            'old_date' => $appointment['schedule_date'],
            'new_date' => $newDate,
            'old_time_slot_id' => $appointment['time_slot_id'],
            'new_time_slot_id' => $newTimeSlotId,
            'reason' => $reason
        ]);
        
        // Send notification
        sendRescheduleNotification($updatedAppointment, $appointment, $reason);
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment rescheduled successfully',
            'appointment' => $updatedAppointment
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
 * Handle appointment cancellation
 */
function handleCancel() {
    global $appointmentsModel, $calendarService, $notificationService;
    
    try {
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $reason = $_POST['reason'] ?? '';
        
        if (!$appointmentId) {
            throw new Exception('Appointment ID is required');
        }
        
        // Get current appointment
        $appointment = $appointmentsModel->find($appointmentId);
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        // Check ownership
        if (isset($_SESSION['user_id']) && $appointment['user_id'] != $_SESSION['user_id']) {
            throw new Exception('Access denied');
        }
        
        // Validate cancellation timing (must be at least 24 hours before appointment)
        $appointmentDateTime = $appointment['schedule_date'] . ' ' . $appointment['start_time'];
        $hoursUntilAppointment = (strtotime($appointmentDateTime) - time()) / 3600;
        
        if ($hoursUntilAppointment < 24) {
            throw new Exception('Appointments can only be cancelled at least 24 hours in advance');
        }
        
        // Check if already cancelled
        if ($appointment['status'] === 'cancelled') {
            throw new Exception('Appointment is already cancelled');
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // Update appointment status
        $updateData = [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (!$appointmentsModel->update($appointmentId, $updateData)) {
            throw new Exception('Failed to cancel appointment');
        }
        
        // Delete Google Calendar event
        if ($appointment['google_event_id']) {
            $calendarService->deleteEvent($appointment['google_event_id']);
        }
        
        // Process refund if payment was made
        if ($appointment['payment_status'] === 'completed') {
            processRefund($appointment);
        }
        
        // Log the cancellation
        logAppointmentAction($appointmentId, 'cancelled', [
            'reason' => $reason,
            'cancelled_by' => $_SESSION['user_id'] ?? 'system'
        ]);
        
        // Send notification
        sendCancellationNotification($appointment, $reason);
        
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
 * Get available time slots for a specific date
 */
function getAvailableSlots() {
    global $appointmentsModel;
    
    try {
        $date = $_GET['date'] ?? '';
        $excludeAppointmentId = (int)($_GET['exclude_appointment'] ?? 0);
        
        if (!$date) {
            throw new Exception('Date is required');
        }
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new Exception('Invalid date format');
        }
        
        // Check if date is in the future (at least 24 hours from now)
        $hoursUntilDate = (strtotime($date . ' 00:00:00') - time()) / 3600;
        if ($hoursUntilDate < 24) {
            throw new Exception('Date must be at least 24 hours from now');
        }
        
        $availableSlots = $appointmentsModel->getAvailableTimeSlots($date, $excludeAppointmentId);
        
        echo json_encode([
            'success' => true,
            'slots' => $availableSlots
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
 * Log appointment action
 */
function logAppointmentAction($appointmentId, $action, $metadata = []) {
    global $conn;
    
    try {
        $sql = "
            INSERT INTO appointment_logs (appointment_id, action, metadata, created_at)
            VALUES (?, ?, ?, NOW())
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $appointmentId,
            $action,
            json_encode($metadata)
        ]);
        
    } catch (Exception $e) {
        error_log("Failed to log appointment action: " . $e->getMessage());
    }
}

/**
 * Send reschedule notification
 */
function sendRescheduleNotification($newAppointment, $oldAppointment, $reason) {
    global $notificationService;
    
    try {
        // Get user data
        $usersModel = new UsersModel();
        $user = $usersModel->find($newAppointment['user_id']);
        
        if (!$user) return;
        
        // Get additional data
        $servicesModel = new ServicesModel();
        $petsModel = new PetsModel();
        $timeSlotsModel = new TimeSlotsModel();
        
        $service = $servicesModel->find($newAppointment['service_id']);
        $pet = $petsModel->find($newAppointment['pet_id']);
        $timeSlot = $timeSlotsModel->find($newAppointment['time_slot_id']);
        $oldTimeSlot = $timeSlotsModel->find($oldAppointment['time_slot_id']);
        
        $emailData = [
            'user_name' => $user['name'],
            'appointment_code' => $newAppointment['code'],
            'service_name' => $service['name'],
            'pet_name' => $pet['name'],
            'old_date' => date('l, F j, Y', strtotime($oldAppointment['schedule_date'])),
            'old_time' => date('g:i A', strtotime($oldTimeSlot['start_time'])),
            'new_date' => date('l, F j, Y', strtotime($newAppointment['schedule_date'])),
            'new_time' => date('g:i A', strtotime($timeSlot['start_time'])),
            'reason' => $reason,
            'clinic_address' => $_ENV['CLINIC_ADDRESS'] ?? 'OVAS Veterinary Clinic, Nairobi'
        ];
        
        $notificationService->sendAppointmentRescheduleNotification($user['email'], $emailData);
        
        // Send SMS
        $smsMessage = "OVAS: Your appointment for {$pet['name']} has been rescheduled to " . 
                     date('M j, Y g:i A', strtotime($newAppointment['schedule_date'] . ' ' . $timeSlot['start_time'])) . 
                     ". Code: {$newAppointment['code']}";
        $notificationService->sendSMS($user['phone'], $smsMessage);
        
    } catch (Exception $e) {
        error_log("Failed to send reschedule notification: " . $e->getMessage());
    }
}

/**
 * Send cancellation notification
 */
function sendCancellationNotification($appointment, $reason) {
    global $notificationService;
    
    try {
        // Get user data
        $usersModel = new UsersModel();
        $user = $usersModel->find($appointment['user_id']);
        
        if (!$user) return;
        
        // Get additional data
        $servicesModel = new ServicesModel();
        $petsModel = new PetsModel();
        $timeSlotsModel = new TimeSlotsModel();
        
        $service = $servicesModel->find($appointment['service_id']);
        $pet = $petsModel->find($appointment['pet_id']);
        $timeSlot = $timeSlotsModel->find($appointment['time_slot_id']);
        
        $emailData = [
            'user_name' => $user['name'],
            'appointment_code' => $appointment['code'],
            'service_name' => $service['name'],
            'pet_name' => $pet['name'],
            'appointment_date' => date('l, F j, Y', strtotime($appointment['schedule_date'])),
            'appointment_time' => date('g:i A', strtotime($timeSlot['start_time'])),
            'reason' => $reason,
            'refund_info' => $appointment['payment_status'] === 'completed' ? 'A refund will be processed within 3-5 business days.' : ''
        ];
        
        $notificationService->sendAppointmentCancellationNotification($user['email'], $emailData);
        
        // Send SMS
        $smsMessage = "OVAS: Your appointment for {$pet['name']} on " . 
                     date('M j, Y g:i A', strtotime($appointment['schedule_date'] . ' ' . $timeSlot['start_time'])) . 
                     " has been cancelled. Code: {$appointment['code']}";
        $notificationService->sendSMS($user['phone'], $smsMessage);
        
    } catch (Exception $e) {
        error_log("Failed to send cancellation notification: " . $e->getMessage());
    }
}

/**
 * Process refund for cancelled appointment
 */
function processRefund($appointment) {
    // This would integrate with M-Pesa or payment gateway refund API
    // For now, we'll log the refund request
    
    try {
        global $conn;
        
        $sql = "
            INSERT INTO refund_requests (appointment_id, amount, status, created_at)
            VALUES (?, ?, 'pending', NOW())
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $appointment['id'],
            $appointment['total_fee']
        ]);
        
        error_log("Refund request created for appointment {$appointment['id']} - Amount: KSh {$appointment['total_fee']}");
        
    } catch (Exception $e) {
        error_log("Failed to create refund request: " . $e->getMessage());
    }
}
?>