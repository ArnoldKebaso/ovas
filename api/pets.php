<?php
/**
 * Pets API
 * Handles CRUD operations for pet management
 */

header('Content-Type: application/json');
require_once '../initialize.php';
require_once '../classes/PetsModel.php';

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

// Authentication check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$action = $_GET['action'] ?? '';
$petsModel = new PetsModel();
$userId = $_SESSION['user_id'];

switch ($action) {
    case 'create':
        createPet();
        break;
    case 'update':
        updatePet();
        break;
    case 'delete':
        deletePet();
        break;
    case 'get':
        getPet();
        break;
    case 'list':
        listUserPets();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Create new pet
 */
function createPet() {
    global $petsModel, $userId;
    
    try {
        // Validate required fields
        $requiredFields = ['name', 'species', 'age', 'gender'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate data types and ranges
        $age = (int)$_POST['age'];
        if ($age < 0 || $age > 30) {
            throw new Exception('Age must be between 0 and 30 years');
        }
        
        $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
        if ($weight !== null && ($weight < 0 || $weight > 200)) {
            throw new Exception('Weight must be between 0 and 200 kg');
        }
        
        // Validate species
        $validSpecies = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Other'];
        if (!in_array($_POST['species'], $validSpecies)) {
            throw new Exception('Invalid species selected');
        }
        
        // Validate gender
        $validGenders = ['male', 'female'];
        if (!in_array($_POST['gender'], $validGenders)) {
            throw new Exception('Invalid gender selected');
        }
        
        // Prepare pet data
        $petData = [
            'user_id' => $userId,
            'name' => trim($_POST['name']),
            'species' => $_POST['species'],
            'breed' => trim($_POST['breed'] ?? ''),
            'age' => $age,
            'weight' => $weight,
            'color' => trim($_POST['color'] ?? ''),
            'gender' => $_POST['gender'],
            'microchip_number' => trim($_POST['microchip_number'] ?? ''),
            'medical_notes' => trim($_POST['medical_notes'] ?? ''),
            'is_active' => 1
        ];
        
        // Validate string lengths
        if (strlen($petData['name']) > 100) {
            throw new Exception('Pet name cannot exceed 100 characters');
        }
        
        if (strlen($petData['breed']) > 100) {
            throw new Exception('Breed cannot exceed 100 characters');
        }
        
        if (strlen($petData['color']) > 50) {
            throw new Exception('Color cannot exceed 50 characters');
        }
        
        if (strlen($petData['microchip_number']) > 50) {
            throw new Exception('Microchip number cannot exceed 50 characters');
        }
        
        if (strlen($petData['medical_notes']) > 1000) {
            throw new Exception('Medical notes cannot exceed 1000 characters');
        }
        
        // Check for duplicate microchip number if provided
        if (!empty($petData['microchip_number'])) {
            if ($petsModel->microchipExists($petData['microchip_number'])) {
                throw new Exception('A pet with this microchip number is already registered');
            }
        }
        
        // Create pet
        $petId = $petsModel->create($petData);
        
        if (!$petId) {
            throw new Exception('Failed to register pet');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Pet registered successfully',
            'pet_id' => $petId
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
 * Update existing pet
 */
function updatePet() {
    global $petsModel, $userId;
    
    try {
        $petId = (int)($_POST['id'] ?? 0);
        
        if (!$petId) {
            throw new Exception('Pet ID is required');
        }
        
        // Check if pet exists and belongs to user
        $existingPet = $petsModel->find($petId);
        if (!$existingPet) {
            throw new Exception('Pet not found');
        }
        
        if ($existingPet['user_id'] != $userId) {
            throw new Exception('Access denied');
        }
        
        // Prepare update data
        $updateData = [];
        
        if (isset($_POST['name'])) {
            $name = trim($_POST['name']);
            if (empty($name)) {
                throw new Exception('Pet name cannot be empty');
            }
            if (strlen($name) > 100) {
                throw new Exception('Pet name cannot exceed 100 characters');
            }
            $updateData['name'] = $name;
        }
        
        if (isset($_POST['species'])) {
            $validSpecies = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Other'];
            if (!in_array($_POST['species'], $validSpecies)) {
                throw new Exception('Invalid species selected');
            }
            $updateData['species'] = $_POST['species'];
        }
        
        if (isset($_POST['breed'])) {
            $breed = trim($_POST['breed']);
            if (strlen($breed) > 100) {
                throw new Exception('Breed cannot exceed 100 characters');
            }
            $updateData['breed'] = $breed;
        }
        
        if (isset($_POST['age'])) {
            $age = (int)$_POST['age'];
            if ($age < 0 || $age > 30) {
                throw new Exception('Age must be between 0 and 30 years');
            }
            $updateData['age'] = $age;
        }
        
        if (isset($_POST['weight'])) {
            $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
            if ($weight !== null && ($weight < 0 || $weight > 200)) {
                throw new Exception('Weight must be between 0 and 200 kg');
            }
            $updateData['weight'] = $weight;
        }
        
        if (isset($_POST['color'])) {
            $color = trim($_POST['color']);
            if (strlen($color) > 50) {
                throw new Exception('Color cannot exceed 50 characters');
            }
            $updateData['color'] = $color;
        }
        
        if (isset($_POST['gender'])) {
            $validGenders = ['male', 'female'];
            if (!in_array($_POST['gender'], $validGenders)) {
                throw new Exception('Invalid gender selected');
            }
            $updateData['gender'] = $_POST['gender'];
        }
        
        if (isset($_POST['microchip_number'])) {
            $microchip = trim($_POST['microchip_number']);
            if (strlen($microchip) > 50) {
                throw new Exception('Microchip number cannot exceed 50 characters');
            }
            
            // Check for duplicate microchip number if changed
            if (!empty($microchip) && $microchip !== $existingPet['microchip_number']) {
                if ($petsModel->microchipExists($microchip)) {
                    throw new Exception('A pet with this microchip number is already registered');
                }
            }
            
            $updateData['microchip_number'] = $microchip;
        }
        
        if (isset($_POST['medical_notes'])) {
            $medicalNotes = trim($_POST['medical_notes']);
            if (strlen($medicalNotes) > 1000) {
                throw new Exception('Medical notes cannot exceed 1000 characters');
            }
            $updateData['medical_notes'] = $medicalNotes;
        }
        
        if (empty($updateData)) {
            throw new Exception('No data to update');
        }
        
        // Update pet
        if (!$petsModel->update($petId, $updateData)) {
            throw new Exception('Failed to update pet information');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Pet information updated successfully'
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
 * Delete pet
 */
function deletePet() {
    global $petsModel, $userId;
    
    try {
        $petId = (int)($_POST['id'] ?? 0);
        
        if (!$petId) {
            throw new Exception('Pet ID is required');
        }
        
        // Check if pet exists and belongs to user
        $pet = $petsModel->find($petId);
        if (!$pet) {
            throw new Exception('Pet not found');
        }
        
        if ($pet['user_id'] != $userId) {
            throw new Exception('Access denied');
        }
        
        // Check if pet has appointments
        $hasAppointments = $petsModel->hasAppointments($petId);
        if ($hasAppointments) {
            throw new Exception('Cannot delete pet with existing appointments. Please contact support.');
        }
        
        // Delete pet
        if (!$petsModel->delete($petId)) {
            throw new Exception('Failed to delete pet');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Pet deleted successfully'
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
 * Get single pet
 */
function getPet() {
    global $petsModel, $userId;
    
    try {
        $petId = (int)($_GET['id'] ?? 0);
        
        if (!$petId) {
            throw new Exception('Pet ID is required');
        }
        
        $pet = $petsModel->find($petId);
        
        if (!$pet) {
            throw new Exception('Pet not found');
        }
        
        if ($pet['user_id'] != $userId) {
            throw new Exception('Access denied');
        }
        
        echo json_encode([
            'success' => true,
            'pet' => $pet
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
 * List user's pets
 */
function listUserPets() {
    global $petsModel, $userId;
    
    try {
        $includeInactive = isset($_GET['include_inactive']) && $_GET['include_inactive'] == '1';
        $pets = $petsModel->getUserPets($userId, $includeInactive);
        
        echo json_encode([
            'success' => true,
            'pets' => $pets,
            'total' => count($pets)
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