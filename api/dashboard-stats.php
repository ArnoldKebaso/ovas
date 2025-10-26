<?php
/**
 * Dashboard Statistics API
 * Provides real-time dashboard data for logged-in users
 */

require_once '../initialize.php';
require_once '../inc/sess_auth.php';
require_once '../classes/AppointmentsModel.php';
require_once '../classes/PetsModel.php';
require_once '../classes/SecurityUtil.php';

header('Content-Type: application/json');

// Initialize secure session
if (!SecurityUtil::initSecureSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session security violation']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $appointmentsModel = new AppointmentsModel();
    $petsModel = new PetsModel();

    // Get user's appointments
    $appointments = $appointmentsModel->getUserAppointments($userId);
    
    // Calculate statistics
    $stats = [
        'total_appointments' => count($appointments),
        'upcoming_appointments' => count(array_filter($appointments, function($apt) {
            return in_array($apt['status'], ['confirmed', 'paid']) && 
                   strtotime($apt['schedule_date'] . ' ' . $apt['start_time']) > time();
        })),
        'completed_appointments' => count(array_filter($appointments, function($apt) {
            return $apt['status'] === 'completed';
        })),
        'pending_appointments' => count(array_filter($appointments, function($apt) {
            return $apt['status'] === 'pending';
        })),
        'cancelled_appointments' => count(array_filter($appointments, function($apt) {
            return $apt['status'] === 'cancelled';
        })),
        'total_pets' => count($petsModel->getUserPets($userId))
    ];

    // Add recent activity
    $recentAppointments = array_slice(array_filter($appointments, function($apt) {
        return strtotime($apt['schedule_date'] . ' ' . $apt['start_time']) > time();
    }), 0, 5);

    echo json_encode([
        'success' => true,
        'data' => [
            'stats' => $stats,
            'recent_appointments' => $recentAppointments,
            'last_updated' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    handleError('error', 'Dashboard stats API error: ' . $e->getMessage(), __FILE__, __LINE__);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch dashboard data'
    ]);
}
?>