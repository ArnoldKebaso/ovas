<?php
require_once('./initialize.php');

// Set JSON header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Invalid request method'
    ]);
    exit;
}

// Honeypot check (spam protection)
if (!empty($_POST['website'])) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Spam detected'
    ]);
    exit;
}

// Validate CSRF token
session_start();
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Invalid security token. Please refresh and try again.'
    ]);
    exit;
}

// Get and sanitize input
$data = [
    'owner_name' => trim($_POST['owner_name'] ?? ''),
    'contact' => trim($_POST['contact'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
    'pet_name' => trim($_POST['pet_name'] ?? ''),
    'pet_type' => trim($_POST['pet_type'] ?? ''),
    'breed' => trim($_POST['breed'] ?? ''),
    'age' => trim($_POST['age'] ?? ''),
    'category_id' => intval($_POST['category_id'] ?? 0),
    'service_id' => intval($_POST['service_id'] ?? 0),
    'schedule' => trim($_POST['schedule'] ?? ''),
    'remarks' => trim($_POST['remarks'] ?? '')
];

// Validation
$errors = [];

if (empty($data['owner_name'])) {
    $errors[] = 'Owner name is required';
}

if (empty($data['contact'])) {
    $errors[] = 'Contact number is required';
}

if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($data['pet_name'])) {
    $errors[] = 'Pet name is required';
}

if ($data['category_id'] <= 0) {
    $errors[] = 'Please select a service category';
}

if ($data['service_id'] <= 0) {
    $errors[] = 'Please select a service';
}

if (empty($data['schedule'])) {
    $errors[] = 'Appointment date is required';
}

// Validate date is not in the past
if (!empty($data['schedule'])) {
    $selected_date = new DateTime($data['schedule']);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if ($selected_date < $today) {
        $errors[] = 'Cannot book appointments for past dates';
    }
}

// Return validation errors if any
if (!empty($errors)) {
    echo json_encode([
        'status' => 'error',
        'msg' => implode(', ', $errors),
        'errors' => $errors
    ]);
    exit;
}

try {
    // Check availability
    $schedule_date = date('Y-m-d', strtotime($data['schedule']));
    $max_appointments = 10; // Adjust based on clinic capacity
    
    $availability_check = $conn->query("SELECT COUNT(*) as total 
                                       FROM `appointment_list` 
                                       WHERE DATE(`schedule`) = '{$schedule_date}' 
                                       AND `delete_flag` = 0 
                                       AND `status` != 4");
    
    $availability = $availability_check->fetch_assoc();
    
    if (intval($availability['total']) >= $max_appointments) {
        echo json_encode([
            'status' => 'error',
            'msg' => 'This date is fully booked. Please select another date.'
        ]);
        exit;
    }
    
    // Get user_id if logged in
    $user_id = isset($_SESSION['userdata']['id']) ? intval($_SESSION['userdata']['id']) : null;
    
    // Prepare SQL statement
    $sql = "INSERT INTO `appointment_list` 
            (`user_id`, `owner_name`, `contact`, `email`, `address`, 
             `pet_name`, `pet_type`, `breed`, `age`, 
             `category_id`, `service_id`, `schedule`, `remarks`, `status`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'issssssssiiis',
        $user_id,
        $data['owner_name'],
        $data['contact'],
        $data['email'],
        $data['address'],
        $data['pet_name'],
        $data['pet_type'],
        $data['breed'],
        $data['age'],
        $data['category_id'],
        $data['service_id'],
        $data['schedule'],
        $data['remarks']
    );
    
    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id;
        
        // TODO: Send confirmation email
        // require_once('./sendemail.php');
        // sendAppointmentConfirmation($data['email'], $appointment_id);
        
        echo json_encode([
            'status' => 'success',
            'msg' => 'Appointment booked successfully! You will receive a confirmation email shortly.',
            'appointment_id' => $appointment_id,
            'redirect' => null // Set to profile page if logged in
        ]);
    } else {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log('Appointment booking error: ' . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'msg' => 'Failed to book appointment. Please try again or contact us directly.',
        'error' => $e->getMessage() // Remove in production
    ]);
}

$conn->close();
?>
