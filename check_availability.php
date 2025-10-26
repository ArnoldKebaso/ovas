<?php
/**
 * Check Availability API
 * Returns available time slots for a specific service and date
 */

require_once 'initialize.php';
require_once 'inc/sess_auth.php';
require_once 'classes/TimeSlotsModel.php';
require_once 'classes/AppointmentsModel.php';
require_once 'classes/SecurityUtil.php';

// Set JSON response header
header('Content-Type: application/json');

// Initialize secure session
if (!SecurityUtil::initSecureSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session security violation']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting for availability checks
if (!SecurityUtil::checkRateLimit('availability_check', 20, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please wait a moment.']);
    exit;
}

// Check CSRF token
if (!SecurityUtil::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    SecurityUtil::logSecurityEvent('CSRF_VIOLATION', ['endpoint' => 'check_availability'], 'WARNING');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Validate required parameters
if (empty($_POST['service_id']) || empty($_POST['date'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Service ID and date are required'
    ]);
    exit;
}

$serviceId = (int)$_POST['service_id'];
$date = $_POST['date'];

// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid date format. Use YYYY-MM-DD'
    ]);
    exit;
}

// Check if date is in the past
$requestedDate = new DateTime($date);
$today = new DateTime('today');
if ($requestedDate < $today) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Cannot book appointments for past dates'
    ]);
    exit;
}

// Check if date is too far in the future (e.g., max 90 days)
$maxDate = new DateTime('+90 days');
if ($requestedDate > $maxDate) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Cannot book appointments more than 90 days in advance'
    ]);
    exit;
}

// Check if it's a Sunday (clinic closed)
$dayOfWeek = (int)date('N', strtotime($date)); // 1 (Monday) to 7 (Sunday)
if ($dayOfWeek == 7) { 
    echo json_encode([
        'success' => false, 
        'message' => 'Clinic is closed on Sundays',
        'is_closed' => true
    ]);
    exit;
}

try {
    $timeSlotsModel = new TimeSlotsModel();
    $appointmentsModel = new AppointmentsModel();
    
    // Get all active time slots
    $timeSlots = $timeSlotsModel->active();
    
    if (empty($timeSlots)) {
        echo json_encode([
            'success' => false, 
            'message' => 'No time slots available'
        ]);
        exit;
    }
    
    // Check availability for each time slot
    $availableSlots = [];
    foreach ($timeSlots as $slot) {
        $capacity = (int)$slot['max_appointments'];
        $used = $appointmentsModel->capacityUsed($date, $slot['id']);
        $available = $capacity - $used;
        
        $availableSlots[] = [
            'id' => (int)$slot['id'],
            'start_time' => $slot['start_time'],
            'end_time' => $slot['end_time'],
            'display_time' => date('g:i A', strtotime($slot['start_time'])) . ' - ' . 
                             date('g:i A', strtotime($slot['end_time'])),
            'max_appointments' => $capacity,
            'appointments_used' => $used,
            'appointments_available' => $available,
            'is_available' => $available > 0,
            'is_peak_hours' => isPeakHours($slot['start_time'])
        ];
    }
    
    // Sort by start time
    usort($availableSlots, function($a, $b) {
        return strcmp($a['start_time'], $b['start_time']);
    });
    
    // Group by availability status
    $availableCount = count(array_filter($availableSlots, function($slot) {
        return $slot['is_available'];
    }));
    
    echo json_encode([
        'success' => true,
        'data' => [
            'date' => $date,
            'service_id' => $serviceId,
            'day_of_week' => date('l', strtotime($date)),
            'total_slots' => count($availableSlots),
            'available_slots' => $availableCount,
            'slots' => $availableSlots
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Check availability failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Internal server error'
    ]);
}

/**
 * Check if time slot is during peak hours
 * @param string $startTime Time in HH:MM:SS format
 * @return bool True if peak hours, false otherwise
 */
function isPeakHours($startTime) {
    $hour = (int)date('H', strtotime($startTime));
    // Consider 9AM-12PM and 2PM-5PM as peak hours
    return ($hour >= 9 && $hour < 12) || ($hour >= 14 && $hour < 17);
}
?>
