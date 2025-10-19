<?php
require_once('./initialize.php');

header('Content-Type: application/json');

// Check if category_id is provided
if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category ID is required'
    ]);
    exit;
}

$category_id = intval($_GET['category_id']);

try {
    // Get services for the selected category
    $qry = $conn->query("SELECT id, name, fee, description 
                         FROM `service_list` 
                         WHERE `category_id` = '{$category_id}' 
                         AND `delete_flag` = 0 
                         AND `status` = 1
                         ORDER BY `name` ASC");
    
    $services = [];
    
    if ($qry && $qry->num_rows > 0) {
        while ($row = $qry->fetch_assoc()) {
            $services[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'fee' => floatval($row['fee']),
                'description' => $row['description']
            ];
        }
    }
    
    echo json_encode($services);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch services',
        'error' => $e->getMessage()
    ]);
}
?>
