<?php
/**
 * Get Service Details API
 * Returns service information including fee and duration
 */

require_once 'initialize.php';
require_once 'inc/sess_auth.php';
require_once 'classes/ServicesModel.php';

// Set JSON response header
header('Content-Type: application/json');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate required parameters - support both 'id' and 'service_id' for compatibility
$serviceId = null;
if (!empty($_GET['service_id'])) {
    $serviceId = (int)$_GET['service_id'];
} elseif (!empty($_GET['id'])) {
    $serviceId = (int)$_GET['id'];
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Service ID is required'
    ]);
    exit;
}

try {
    $servicesModel = new ServicesModel();
    $service = $servicesModel->find($serviceId);
    
    if (!$service) {
        http_response_code(404);
        echo json_encode([
            'success' => false, 
            'message' => 'Service not found'
        ]);
        exit;
    }
    
    // Format the service data for booking wizard
    $serviceData = [
        'id' => (int)$service['id'],
        'name' => $service['service'],
        'description' => strip_tags($service['description']),
        'fee' => (float)$service['price'],
        'fee_formatted' => 'KSh ' . number_format((float)$service['price'], 2),
        'duration_minutes' => 30, // Default duration - could be added to database
        'duration_formatted' => '30 minutes',
        'category' => $service['category'] ?? 'General',
        'status' => (int)$service['status'] === 1 ? 'active' : 'inactive',
        'image_path' => !empty($service['image_path']) ? $service['image_path'] : null,
        'created_at' => $service['date_created'],
        'updated_at' => $service['date_updated']
    ];
    
    // Add booking requirements
    $serviceData['booking_requirements'] = [
        'requires_login' => true,
        'advance_booking_hours' => 2, // Minimum 2 hours advance booking
        'max_advance_days' => 90,     // Maximum 90 days advance booking
        'cancellation_hours' => 24,   // 24 hours cancellation policy
        'deposit_required' => false   // No deposit required for now
    ];
    
    // Add clinic operational info
    $serviceData['clinic_info'] = [
        'closed_days' => ['Sunday'],
        'operating_hours' => '8:00 AM - 6:00 PM',
        'timezone' => 'Africa/Nairobi'
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $serviceData,
        // Legacy format for backward compatibility
        'status' => 'success',
        'service' => [
            'id' => $service['id'],
            'name' => $service['service'],
            'description' => strip_tags($service['description']),
            'fee' => $service['price'],
            'category_name' => $service['category'] ?? 'General'
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Get service details failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Internal server error'
    ]);
}
?>
