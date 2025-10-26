<?php
/**
 * Test Models - Users, Pets, Services
 */

require_once 'config.php';
require_once 'classes/UsersModel.php';
require_once 'classes/PetsModel.php';  
require_once 'classes/ServicesModel.php';

echo "=== OVAS Models Testing ===\n\n";

try {
    // Test Services Model
    echo "1. Testing Services Model:\n";
    $services = new ServicesModel();
    $activeServices = $services->active();
    echo "   Active services found: " . count($activeServices) . "\n";
    
    if (count($activeServices) > 0) {
        $firstService = $activeServices[0];
        echo "   First service: " . $firstService['name'] . " (KES " . $firstService['fee'] . ")\n";
    }
    
    // Test Users Model  
    echo "\n2. Testing Users Model:\n";
    $users = new UsersModel();
    $owners = $users->allOwners(5);
    echo "   Pet owners found: " . count($owners) . "\n";
    
    // Test Pets Model
    echo "\n3. Testing Pets Model:\n";
    $pets = new PetsModel();
    if (count($owners) > 0) {
        $firstOwnerId = $owners[0]['id'];
        $ownerPets = $pets->allByUser($firstOwnerId);
        echo "   Pets for first owner: " . count($ownerPets) . "\n";
    } else {
        echo "   No owners found to test pets\n";
    }
    
    echo "\n✓ All model tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Model test failed: " . $e->getMessage() . "\n";
}
?>