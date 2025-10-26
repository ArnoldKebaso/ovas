<?php
/**
 * M-Pesa Callback Handler
 * Processes payment confirmations and updates appointment/payment status
 */

require_once 'initialize.php';
require_once 'classes/MpesaService.php';
require_once 'classes/AppointmentsModel.php';
require_once 'classes/PaymentsModel.php';
require_once 'classes/UsersModel.php';
require_once 'classes/ServicesModel.php';

// Set JSON response header
header('Content-Type: application/json');

// Log callback for debugging
$input = file_get_contents('php://input');
error_log("M-Pesa Callback Received: " . $input);

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Method not allowed']);
    exit;
}

try {
    // Decode callback data
    $callbackData = json_decode($input, true);
    
    if (!$callbackData) {
        error_log("M-Pesa Callback: Invalid JSON data");
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid JSON data']);
        exit;
    }
    
    // Initialize services
    $mpesaService = new MpesaService();
    $paymentsModel = new PaymentsModel();
    $appointmentsModel = new AppointmentsModel();
    $usersModel = new UsersModel();
    $servicesModel = new ServicesModel();
    
    // Validate callback structure
    if (!$mpesaService->isValidCallback($callbackData)) {
        error_log("M-Pesa Callback: Invalid callback structure");
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback structure']);
        exit;
    }
    
    // Extract payment details
    $paymentData = $mpesaService->extractCallbackData($callbackData);
    
    if (!$paymentData) {
        error_log("M-Pesa Callback: Failed to extract payment data");
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Failed to extract payment data']);
        exit;
    }
    
    // Find payment record by checkout request ID
    $payment = $paymentsModel->findByCheckoutRequestId($paymentData['checkout_request_id']);
    
    if (!$payment) {
        error_log("M-Pesa Callback: Payment record not found for checkout ID: " . $paymentData['checkout_request_id']);
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Payment record not found']);
        exit;
    }
    
    // Get associated appointment
    $appointment = $appointmentsModel->find($payment['appointment_id']);
    
    if (!$appointment) {
        error_log("M-Pesa Callback: Appointment not found for payment ID: " . $payment['id']);
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Appointment not found']);
        exit;
    }
    
    if ($paymentData['success']) {
        // Payment successful
        try {
            // Update payment record
            $paymentUpdateData = [
                'status' => 'completed',
                'mpesa_receipt_number' => $paymentData['mpesa_receipt_number'],
                'amount_paid' => $paymentData['amount'],
                'transaction_date' => formatMpesaDate($paymentData['transaction_date']),
                'phone_number' => $paymentData['phone_number'],
                'result_code' => $paymentData['result_code'],
                'result_desc' => $paymentData['result_desc'],
                'completed_at' => date('Y-m-d H:i:s')
            ];
            
            $paymentsModel->update($payment['id'], $paymentUpdateData);
            
            // Update appointment status to confirmed
            $appointmentsModel->updateStatus($appointment['id'], 'confirmed');
            
            // Log successful payment
            error_log("M-Pesa Payment Success: Appointment {$appointment['code']}, Receipt: {$paymentData['mpesa_receipt_number']}, Amount: {$paymentData['amount']}");
            
            // Send confirmation notifications
            try {
                sendPaymentConfirmation($appointment, $payment, $paymentData);
            } catch (Exception $e) {
                error_log("M-Pesa Callback: Notification error - " . $e->getMessage());
                // Don't fail the callback for notification errors
            }
            
            echo json_encode([
                'ResultCode' => 0, 
                'ResultDesc' => 'Payment processed successfully'
            ]);
            
        } catch (Exception $e) {
            error_log("M-Pesa Callback: Error processing successful payment - " . $e->getMessage());
            echo json_encode([
                'ResultCode' => 1, 
                'ResultDesc' => 'Error processing payment'
            ]);
        }
        
    } else {
        // Payment failed
        try {
            // Update payment record
            $paymentUpdateData = [
                'status' => 'failed',
                'result_code' => $paymentData['result_code'],
                'result_desc' => $paymentData['result_desc'],
                'failed_at' => date('Y-m-d H:i:s')
            ];
            
            $paymentsModel->update($payment['id'], $paymentUpdateData);
            
            // Update appointment status to cancelled
            $appointmentsModel->updateStatus($appointment['id'], 'cancelled');
            
            // Log failed payment
            error_log("M-Pesa Payment Failed: Appointment {$appointment['code']}, Reason: {$paymentData['result_desc']}");
            
            // Send failure notification
            try {
                sendPaymentFailureNotification($appointment, $paymentData);
            } catch (Exception $e) {
                error_log("M-Pesa Callback: Notification error - " . $e->getMessage());
            }
            
            echo json_encode([
                'ResultCode' => 0, 
                'ResultDesc' => 'Payment failure processed'
            ]);
            
        } catch (Exception $e) {
            error_log("M-Pesa Callback: Error processing failed payment - " . $e->getMessage());
            echo json_encode([
                'ResultCode' => 1, 
                'ResultDesc' => 'Error processing payment failure'
            ]);
        }
    }
    
} catch (Exception $e) {
    error_log("M-Pesa Callback Exception: " . $e->getMessage());
    echo json_encode([
        'ResultCode' => 1, 
        'ResultDesc' => 'Internal server error'
    ]);
}

/**
 * Format M-Pesa transaction date to MySQL datetime format
 * @param string $mpesaDate Format: 20231026143500
 * @return string MySQL datetime format
 */
function formatMpesaDate($mpesaDate) {
    if (strlen($mpesaDate) === 14) {
        return substr($mpesaDate, 0, 4) . '-' . 
               substr($mpesaDate, 4, 2) . '-' . 
               substr($mpesaDate, 6, 2) . ' ' . 
               substr($mpesaDate, 8, 2) . ':' . 
               substr($mpesaDate, 10, 2) . ':' . 
               substr($mpesaDate, 12, 2);
    }
    return date('Y-m-d H:i:s');
}

/**
 * Send payment confirmation notifications
 * @param array $appointment
 * @param array $payment
 * @param array $paymentData
 */
function sendPaymentConfirmation($appointment, $payment, $paymentData) {
    global $usersModel, $servicesModel;
    
    // Get user and service details
    $user = $usersModel->find($appointment['user_id']);
    $service = $servicesModel->find($appointment['service_id']);
    
    if (!$user || !$service) {
        error_log("M-Pesa Callback: Missing user or service data for notification");
        return;
    }
    
    // Prepare notification data
    $notificationData = [
        'user' => $user,
        'appointment' => $appointment,
        'service' => $service,
        'payment' => $payment,
        'mpesa_receipt' => $paymentData['mpesa_receipt_number'],
        'amount_paid' => $paymentData['amount'],
        'transaction_date' => $paymentData['transaction_date']
    ];
    
    // Send email notification (if NotificationService exists)
    if (class_exists('NotificationService')) {
        $notificationService = new NotificationService();
        $notificationService->sendAppointmentConfirmation($notificationData);
    }
    
    // Log for manual follow-up if automated notifications aren't set up
    error_log("Payment Confirmation Required: Send to {$user['email']} for appointment {$appointment['code']}");
}

/**
 * Send payment failure notification
 * @param array $appointment
 * @param array $paymentData
 */
function sendPaymentFailureNotification($appointment, $paymentData) {
    global $usersModel, $servicesModel;
    
    // Get user details
    $user = $usersModel->find($appointment['user_id']);
    $service = $servicesModel->find($appointment['service_id']);
    
    if (!$user || !$service) {
        return;
    }
    
    // Prepare notification data
    $notificationData = [
        'user' => $user,
        'appointment' => $appointment,
        'service' => $service,
        'failure_reason' => $paymentData['result_desc'],
        'result_code' => $paymentData['result_code']
    ];
    
    // Send email notification (if NotificationService exists)
    if (class_exists('NotificationService')) {
        $notificationService = new NotificationService();
        $notificationService->sendPaymentFailureNotification($notificationData);
    }
    
    // Log for manual follow-up
    error_log("Payment Failure Notification Required: Send to {$user['email']} for appointment {$appointment['code']} - Reason: {$paymentData['result_desc']}");
}
?>