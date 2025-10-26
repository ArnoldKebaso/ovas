<?php
/**
 * Payment Submission Handler
 * Initiates M-Pesa payments and creates pending appointment records
 */

require_once 'initialize.php';
require_once 'inc/sess_auth.php';
require_once 'classes/MpesaService.php';
require_once 'classes/AppointmentsModel.php';
require_once 'classes/PaymentsModel.php';
require_once 'classes/ServicesModel.php';
require_once 'classes/UsersModel.php';

// Set JSON response header
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Check if user is authenticated
$user = auth_user();
if (!$user) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Authentication required. Please login first.',
        'redirect_to_login' => true
    ]);
    exit;
}

// Validate required parameters
$requiredFields = ['service_id', 'date', 'time_slot_id'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
        ]);
        exit;
    }
}

$serviceId = (int)$_POST['service_id'];
$appointmentDate = $_POST['date'];
$timeSlotId = (int)$_POST['time_slot_id'];
$petId = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : null;

// Validate date format and ensure it's not in the past
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid date format'
    ]);
    exit;
}

$requestedDate = new DateTime($appointmentDate);
$today = new DateTime('today');
if ($requestedDate < $today) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Cannot book appointments for past dates'
    ]);
    exit;
}

// Check if it's Sunday (clinic closed)
$dayOfWeek = (int)date('N', strtotime($appointmentDate));
if ($dayOfWeek == 7) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Clinic is closed on Sundays'
    ]);
    exit;
}

try {
    // Initialize models
    $servicesModel = new ServicesModel();
    $appointmentsModel = new AppointmentsModel();
    $paymentsModel = new PaymentsModel();
    $usersModel = new UsersModel();
    $mpesaService = new MpesaService();
    
    // Verify service exists and is active
    $service = $servicesModel->find($serviceId);
    if (!$service || $service['status'] != 1) {
        http_response_code(404);
        echo json_encode([
            'success' => false, 
            'message' => 'Service not found or unavailable'
        ]);
        exit;
    }
    
    // Check time slot availability
    $capacityUsed = $appointmentsModel->capacityUsed($appointmentDate, $timeSlotId);
    $timeSlotsModel = new TimeSlotsModel();
    $timeSlot = $timeSlotsModel->find($timeSlotId);
    
    if (!$timeSlot || $timeSlot['status'] != 1) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Selected time slot is not available'
        ]);
        exit;
    }
    
    $availableSlots = (int)$timeSlot['max_appointments'] - $capacityUsed;
    if ($availableSlots <= 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false, 
            'message' => 'Selected time slot is fully booked. Please choose another time.'
        ]);
        exit;
    }
    
    // Get user details for phone number
    $userDetails = $usersModel->find($user['id']);
    if (!$userDetails) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'User account error. Please try logging in again.'
        ]);
        exit;
    }
    
    // Validate phone number
    if (empty($userDetails['phone'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Phone number is required for payment. Please update your profile.',
            'update_profile_required' => true
        ]);
        exit;
    }
    
    // Create appointment record with pending status
    $appointmentData = [
        'user_id' => $user['id'],
        'service_id' => $serviceId,
        'pet_id' => $petId,
        'appointment_date' => $appointmentDate,
        'time_slot_id' => $timeSlotId,
        'amount' => (float)$service['price'],
        'status' => 'pending_payment', // Will be updated after payment confirmation
        'notes' => $_POST['notes'] ?? ''
    ];
    
    $appointmentId = $appointmentsModel->create($appointmentData);
    if (!$appointmentId) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to create appointment record'
        ]);
        exit;
    }
    
    // Get the appointment code
    $appointment = $appointmentsModel->find($appointmentId);
    $appointmentCode = $appointment['code'];
    
    // Create initial payment record
    $paymentId = $paymentsModel->createInitiated(
        $appointmentId,
        (float)$service['price'],
        'mpesa'
    );
    
    if (!$paymentId) {
        // Rollback appointment creation
        $appointmentsModel->delete($appointmentId);
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to initialize payment'
        ]);
        exit;
    }
    
    // Initiate M-Pesa STK Push
    $mpesaResponse = $mpesaService->stkPush(
        $userDetails['phone'],
        (float)$service['price'],
        $appointmentCode,
        "OVAS Appointment - {$service['service']}"
    );
    
    if ($mpesaResponse['success']) {
        // Update payment record with M-Pesa details
        $updateData = [
            'mpesa_checkout_request_id' => $mpesaResponse['data']['checkout_request_id'],
            'mpesa_merchant_request_id' => $mpesaResponse['data']['merchant_request_id'],
            'status' => 'pending'
        ];
        $paymentsModel->update($paymentId, $updateData);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => $mpesaResponse['message'],
            'data' => [
                'appointment_id' => $appointmentId,
                'appointment_code' => $appointmentCode,
                'payment_id' => $paymentId,
                'amount' => (float)$service['price'],
                'checkout_request_id' => $mpesaResponse['data']['checkout_request_id'],
                'customer_message' => $mpesaResponse['data']['customer_message'],
                'service_name' => $service['service'],
                'appointment_date' => $appointmentDate,
                'time_slot' => $timeSlot['start_time'] . ' - ' . $timeSlot['end_time']
            ]
        ]);
        
    } else {
        // M-Pesa failed - clean up records
        $paymentsModel->markFailed($paymentId, $mpesaResponse['message']);
        $appointmentsModel->updateStatus($appointmentId, 'failed');
        
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $mpesaResponse['message'],
            'error_code' => $mpesaResponse['error_code'] ?? 'PAYMENT_FAILED',
            'appointment_code' => $appointmentCode // Keep for reference
        ]);
    }
    
} catch (Exception $e) {
    error_log("Payment submission error: " . $e->getMessage());
    
    // Clean up any created records
    if (isset($appointmentId)) {
        try {
            $appointmentsModel->updateStatus($appointmentId, 'failed');
        } catch (Exception $cleanup) {
            error_log("Cleanup error: " . $cleanup->getMessage());
        }
    }
    
    if (isset($paymentId)) {
        try {
            $paymentsModel->markFailed($paymentId, 'System error occurred');
        } catch (Exception $cleanup) {
            error_log("Payment cleanup error: " . $cleanup->getMessage());
        }
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while processing your payment. Please try again.'
    ]);
}
?>