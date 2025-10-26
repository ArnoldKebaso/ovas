<?php
/**
 * Test Notifications API
 * Sends test notifications to verify system functionality
 */

header('Content-Type: application/json');
require_once '../initialize.php';
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

try {
    $notificationService = new NotificationService();
    $testResults = [];
    
    // Test email notification
    $testEmail = $_ENV['TEST_EMAIL'] ?? 'admin@ovas.local';
    $emailData = [
        'user_name' => 'Test User',
        'appointment_code' => 'TEST-001',
        'service_name' => 'Test Service',
        'pet_name' => 'Test Pet',
        'appointment_date' => date('l, F j, Y'),
        'appointment_time' => date('g:i A'),
        'clinic_address' => $_ENV['CLINIC_ADDRESS'] ?? 'OVAS Veterinary Clinic, Nairobi',
        'total_fee' => '2500.00'
    ];
    
    $emailSent = $notificationService->sendAppointmentConfirmation($testEmail, $emailData);
    $testResults['email'] = [
        'status' => $emailSent ? 'success' : 'failed',
        'recipient' => $testEmail,
        'message' => $emailSent ? 'Test email sent successfully' : 'Failed to send test email'
    ];
    
    // Test SMS notification (only if SMS is configured)
    $testPhone = $_ENV['TEST_PHONE'] ?? null;
    if ($testPhone) {
        $smsMessage = "OVAS TEST: This is a test message to verify SMS functionality. Code: TEST-001";
        $smsSent = $notificationService->sendSMS($testPhone, $smsMessage);
        
        $testResults['sms'] = [
            'status' => $smsSent ? 'success' : 'failed',
            'recipient' => $testPhone,
            'message' => $smsSent ? 'Test SMS sent successfully' : 'Failed to send test SMS'
        ];
    } else {
        $testResults['sms'] = [
            'status' => 'skipped',
            'recipient' => 'N/A',
            'message' => 'SMS test skipped - no test phone number configured'
        ];
    }
    
    // Log test notifications
    $logData = [
        'appointment_id' => null,
        'user_id' => $_SESSION['userdata']['id'] ?? null,
        'type' => 'test',
        'channel' => 'email,sms',
        'status' => 'sent',
        'recipient' => $testEmail,
        'metadata' => json_encode([
            'test_type' => 'system_verification',
            'admin_id' => $_SESSION['userdata']['id'] ?? null,
            'test_results' => $testResults
        ])
    ];
    
    $sql = "
        INSERT INTO notification_logs 
        (appointment_id, user_id, type, channel, status, recipient, metadata, sent_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $logData['appointment_id'],
        $logData['user_id'],
        $logData['type'],
        $logData['channel'],
        $logData['status'],
        $logData['recipient'],
        $logData['metadata']
    ]);
    
    // Determine overall success
    $overallSuccess = $testResults['email']['status'] === 'success' || 
                     $testResults['sms']['status'] === 'success';
    
    echo json_encode([
        'success' => $overallSuccess,
        'message' => 'Notification test completed',
        'results' => $testResults
    ]);
    
} catch (Exception $e) {
    error_log("Notification test failed: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Test failed: ' . $e->getMessage()
    ]);
}
?>