<?php
require_once('./initialize.php');

header('Content-Type: application/json');

// Check if date is provided
if (!isset($_GET['date']) || empty($_GET['date'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Date is required',
        'available_slots' => null
    ]);
    exit;
}

$date = $_GET['date'];

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid date format. Use YYYY-MM-DD',
        'available_slots' => null
    ]);
    exit;
}

// Check if date is in the past
$selected_date = new DateTime($date);
$today = new DateTime();
$today->setTime(0, 0, 0);

if ($selected_date < $today) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Cannot book appointments for past dates',
        'available_slots' => 0
    ]);
    exit;
}

try {
    // Count appointments for the selected date
    // Assuming max 10 appointments per day (adjust as needed)
    $max_appointments_per_day = 10;
    
    $qry = $conn->query("SELECT COUNT(*) as total 
                         FROM `appointment_list` 
                         WHERE DATE(`schedule`) = '{$date}' 
                         AND `delete_flag` = 0 
                         AND `status` != 4");
    
    $result = $qry->fetch_assoc();
    $booked_slots = intval($result['total']);
    $available_slots = max(0, $max_appointments_per_day - $booked_slots);
    
    // Check if it's a weekend (optional - adjust based on clinic schedule)
    $day_of_week = date('N', strtotime($date)); // 1 (Monday) to 7 (Sunday)
    
    if ($day_of_week == 7) { // Sunday
        echo json_encode([
            'status' => 'warning',
            'message' => 'Clinic is closed on Sundays',
            'available_slots' => 0,
            'is_closed' => true
        ]);
        exit;
    }
    
    // Return availability information
    echo json_encode([
        'status' => 'success',
        'available_slots' => $available_slots,
        'booked_slots' => $booked_slots,
        'max_slots' => $max_appointments_per_day,
        'date' => $date,
        'day_of_week' => date('l', strtotime($date)),
        'is_available' => $available_slots > 0
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to check availability',
        'error' => $e->getMessage(),
        'available_slots' => null
    ]);
}
?>
