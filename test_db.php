<?php
// Test database connection and queries
require_once('./initialize.php');

echo "<h2>Database Connection Test</h2>";

// Test 1: Check connection
if($conn) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    die();
}

// Test 2: Check category_list table
echo "<h3>Category List Table:</h3>";
$cat_test = $conn->query("SELECT * FROM `category_list` WHERE `delete_flag` = 0");
if($cat_test) {
    echo "✅ Query successful. Found " . $cat_test->num_rows . " categories<br>";
    echo "<ul>";
    while($row = $cat_test->fetch_assoc()) {
        echo "<li>ID: {$row['id']}, Name: {$row['name']}, Delete Flag: {$row['delete_flag']}</li>";
    }
    echo "</ul>";
} else {
    echo "❌ Query failed: " . $conn->error . "<br>";
}

// Test 3: Check service_list table
echo "<h3>Service List Table:</h3>";
$svc_test = $conn->query("SELECT * FROM `service_list` WHERE `delete_flag` = 0");
if($svc_test) {
    echo "✅ Query successful. Found " . $svc_test->num_rows . " services<br>";
    echo "<ul>";
    while($row = $svc_test->fetch_assoc()) {
        echo "<li>ID: {$row['id']}, Name: {$row['name']}, Category IDs: {$row['category_ids']}, Fee: ₱{$row['fee']}</li>";
    }
    echo "</ul>";
} else {
    echo "❌ Query failed: " . $conn->error . "<br>";
}

// Test 4: Test the problematic query from services.php
echo "<h3>Filter Chips Query Test:</h3>";
$filter_test = $conn->query("SELECT * FROM `category_list` WHERE `delete_flag` = 0 ORDER BY `name` ASC");
if($filter_test) {
    echo "✅ Filter query successful. Found " . $filter_test->num_rows . " categories<br>";
} else {
    echo "❌ Filter query failed: " . $conn->error . "<br>";
}

echo "<br><a href='?page=services'>Go to Services Page</a>";
?>
