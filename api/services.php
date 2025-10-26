<?php
/**
 * Services API
 * Handles CRUD operations for services management
 */

header('Content-Type: application/json');
require_once '../initialize.php';
require_once '../classes/ServicesModel.php';

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
$servicesModel = new ServicesModel();

switch ($action) {
    case 'create':
        createService();
        break;
    case 'update':
        updateService();
        break;
    case 'delete':
        deleteService();
        break;
    case 'toggle_status':
        toggleServiceStatus();
        break;
    case 'get':
        getService();
        break;
    case 'list':
        listServices();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Create new service
 */
function createService() {
    global $servicesModel;
    
    try {
        // Validate required fields
        $requiredFields = ['name', 'description', 'duration_minutes', 'fee'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Prepare service data
        $serviceData = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'duration_minutes' => (int)$_POST['duration_minutes'],
            'fee' => (float)$_POST['fee'],
            'category' => trim($_POST['category'] ?? ''),
            'requirements' => trim($_POST['requirements'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
        ];
        
        // Validate data
        if ($serviceData['duration_minutes'] <= 0) {
            throw new Exception('Duration must be greater than 0 minutes');
        }
        
        if ($serviceData['fee'] < 0) {
            throw new Exception('Fee cannot be negative');
        }
        
        // Check for duplicate name
        if ($servicesModel->serviceNameExists($serviceData['name'])) {
            throw new Exception('A service with this name already exists');
        }
        
        // Create service
        $serviceId = $servicesModel->create($serviceData);
        
        if (!$serviceId) {
            throw new Exception('Failed to create service');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Service created successfully',
            'service_id' => $serviceId
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
 * Update existing service
 */
function updateService() {
    global $servicesModel;
    
    try {
        $serviceId = (int)($_POST['id'] ?? 0);
        
        if (!$serviceId) {
            throw new Exception('Service ID is required');
        }
        
        // Check if service exists
        $existingService = $servicesModel->find($serviceId);
        if (!$existingService) {
            throw new Exception('Service not found');
        }
        
        // Prepare update data
        $updateData = [];
        
        if (isset($_POST['name'])) {
            $name = trim($_POST['name']);
            if (empty($name)) {
                throw new Exception('Service name cannot be empty');
            }
            
            // Check for duplicate name (excluding current service)
            if ($servicesModel->serviceNameExists($name, $serviceId)) {
                throw new Exception('A service with this name already exists');
            }
            
            $updateData['name'] = $name;
        }
        
        if (isset($_POST['description'])) {
            $updateData['description'] = trim($_POST['description']);
        }
        
        if (isset($_POST['duration_minutes'])) {
            $duration = (int)$_POST['duration_minutes'];
            if ($duration <= 0) {
                throw new Exception('Duration must be greater than 0 minutes');
            }
            $updateData['duration_minutes'] = $duration;
        }
        
        if (isset($_POST['fee'])) {
            $fee = (float)$_POST['fee'];
            if ($fee < 0) {
                throw new Exception('Fee cannot be negative');
            }
            $updateData['fee'] = $fee;
        }
        
        if (isset($_POST['category'])) {
            $updateData['category'] = trim($_POST['category']);
        }
        
        if (isset($_POST['requirements'])) {
            $updateData['requirements'] = trim($_POST['requirements']);
        }
        
        if (isset($_POST['is_active'])) {
            $updateData['is_active'] = (int)$_POST['is_active'];
        }
        
        if (empty($updateData)) {
            throw new Exception('No data to update');
        }
        
        // Update service
        if (!$servicesModel->update($serviceId, $updateData)) {
            throw new Exception('Failed to update service');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Service updated successfully'
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
 * Delete service
 */
function deleteService() {
    global $servicesModel;
    
    try {
        $serviceId = (int)($_POST['id'] ?? 0);
        
        if (!$serviceId) {
            throw new Exception('Service ID is required');
        }
        
        // Check if service exists
        $service = $servicesModel->find($serviceId);
        if (!$service) {
            throw new Exception('Service not found');
        }
        
        // Check if service has appointments
        $hasAppointments = $servicesModel->hasAppointments($serviceId);
        if ($hasAppointments) {
            throw new Exception('Cannot delete service with existing appointments. Deactivate instead.');
        }
        
        // Delete service
        if (!$servicesModel->delete($serviceId)) {
            throw new Exception('Failed to delete service');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Service deleted successfully'
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
 * Toggle service status
 */
function toggleServiceStatus() {
    global $servicesModel;
    
    try {
        $serviceId = (int)($_POST['id'] ?? 0);
        $isActive = (int)($_POST['is_active'] ?? 0);
        
        if (!$serviceId) {
            throw new Exception('Service ID is required');
        }
        
        // Check if service exists
        $service = $servicesModel->find($serviceId);
        if (!$service) {
            throw new Exception('Service not found');
        }
        
        // Update status
        if (!$servicesModel->update($serviceId, ['is_active' => $isActive])) {
            throw new Exception('Failed to update service status');
        }
        
        $statusText = $isActive ? 'activated' : 'deactivated';
        
        echo json_encode([
            'success' => true,
            'message' => "Service $statusText successfully"
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
 * Get single service
 */
function getService() {
    global $servicesModel;
    
    try {
        $serviceId = (int)($_GET['id'] ?? 0);
        
        if (!$serviceId) {
            throw new Exception('Service ID is required');
        }
        
        $service = $servicesModel->find($serviceId);
        
        if (!$service) {
            throw new Exception('Service not found');
        }
        
        echo json_encode([
            'success' => true,
            'service' => $service
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
 * List all services
 */
function listServices() {
    global $servicesModel;
    
    try {
        $includeInactive = isset($_GET['include_inactive']) && $_GET['include_inactive'] == '1';
        $services = $servicesModel->getAllServices($includeInactive);
        
        echo json_encode([
            'success' => true,
            'services' => $services,
            'total' => count($services)
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>