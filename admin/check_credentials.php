<?php
/**
 * Admin Credentials Helper
 * Use this script to view/reset admin credentials
 */

require_once '../config.php';

echo "<h2>OVAS Admin Credentials Helper</h2>";

try {
    // Check current admin users
    $stmt = $conn->prepare("SELECT id, name, email, is_admin, status, created_at FROM users WHERE is_admin = 1");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Admin Users:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Created</th><th>Action</th></tr>";
    
    foreach ($admins as $admin) {
        $statusText = $admin['status'] ? 'Active' : 'Disabled';
        echo "<tr>";
        echo "<td>{$admin['id']}</td>";
        echo "<td>{$admin['name']}</td>";
        echo "<td>{$admin['email']}</td>";
        echo "<td>{$statusText}</td>";
        echo "<td>{$admin['created_at']}</td>";
        echo "<td><a href='?reset_password={$admin['id']}'>Reset Password</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Handle password reset
    if (isset($_GET['reset_password'])) {
        $userId = (int)$_GET['reset_password'];
        $newPassword = 'admin123'; // Simple password for testing
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ? AND is_admin = 1");
        if ($stmt->execute([$hashedPassword, $userId])) {
            echo "<div style='background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "<strong>Success!</strong> Password reset for admin ID $userId<br>";
            echo "<strong>New Password:</strong> $newPassword";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "<strong>Error!</strong> Failed to reset password.";
            echo "</div>";
        }
    }
    
    echo "<h3>Default Credentials (from database):</h3>";
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Email:</strong> admin@ovas.test<br>";
    echo "<strong>Password:</strong> password<br>";
    echo "<small>If the above doesn't work, use the 'Reset Password' link above to set password to 'admin123'</small>";
    echo "</div>";
    
    echo "<h3>Quick Actions:</h3>";
    echo "<a href='?create_test_admin=1' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;'>Create Test Admin</a><br><br>";
    
    // Create test admin
    if (isset($_GET['create_test_admin'])) {
        $testEmail = 'test@admin.com';
        $testPassword = 'admin123';
        $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, is_admin, status) VALUES (?, ?, ?, 1, 1)");
            if ($stmt->execute(['Test Admin', $testEmail, $hashedPassword])) {
                echo "<div style='background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>Success!</strong> Test admin created<br>";
                echo "<strong>Email:</strong> $testEmail<br>";
                echo "<strong>Password:</strong> $testPassword";
                echo "</div>";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "<div style='background: #fff3cd; color: #856404; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>Notice:</strong> Test admin already exists. Use email: $testEmail, password: $testPassword";
                echo "</div>";
            } else {
                throw $e;
            }
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>Database Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
}
table {
    margin: 10px 0;
}
th, td {
    padding: 8px 12px;
    text-align: left;
}
th {
    background: #f8f9fa;
}
a {
    color: #007bff;
}
</style>