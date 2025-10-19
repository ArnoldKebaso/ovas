<?php
require_once('initialize.php');

header('Content-Type: application/json');

// Check if service ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Service ID is required'
    ]);
    exit;
}

$service_id = intval($_GET['id']);

// Fetch service details
$qry = $conn->query("
    SELECT * FROM `service_list` 
    WHERE id = '{$service_id}' 
    AND delete_flag = 0
");

if ($qry && $qry->num_rows > 0) {
    $service = $qry->fetch_assoc();
    
    // Get category names
    $category_ids = explode(',', $service['category_ids']);
    $first_cat_id = isset($category_ids[0]) ? trim($category_ids[0]) : 1;
    
    $cat_qry = $conn->query("SELECT name FROM `category_list` WHERE id = '{$first_cat_id}'");
    $category_name = 'General';
    if($cat_qry && $cat_qry->num_rows > 0) {
        $cat_row = $cat_qry->fetch_assoc();
        $category_name = $cat_row['name'];
    }
    
    echo json_encode([
        'status' => 'success',
        'service' => [
            'id' => $service['id'],
            'name' => $service['name'],
            'description' => strip_tags($service['description']),
            'fee' => $service['fee'],
            'category_id' => $first_cat_id,
            'category_name' => $category_name
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Service not found'
    ]);
}
?>
