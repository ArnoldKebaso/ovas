<?php
/**
 * Admin Session Authentication Check
 * Include this file at the top of all admin pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
function check_admin_auth() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        redirect_to_login();
        return false;
    }
    
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        redirect_to_login();
        return false;
    }
    
    if (!isset($_SESSION['status']) || $_SESSION['status'] != 1) {
        redirect_to_login();
        return false;
    }
    
    return true;
}

// Redirect to login page
function redirect_to_login() {
    header('Location: login.php');
    exit;
}

// Get current admin user data
function get_admin_user() {
    if (!check_admin_auth()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['id'] ?? null,
        'name' => $_SESSION['name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'phone' => $_SESSION['phone'] ?? '',
        'is_admin' => $_SESSION['is_admin'] ?? 0
    ];
}

// Admin logout function
function admin_logout() {
    session_start();
    session_destroy();
    header('Location: login.php');
    exit;
}

// Check authentication (call this on every admin page)
if (!check_admin_auth()) {
    exit; // Will redirect to login
}
?>