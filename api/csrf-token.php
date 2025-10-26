<?php
/**
 * CSRF Token Generation Endpoint
 * Provides fresh CSRF tokens for AJAX requests
 */

require_once '../initialize.php';
require_once '../classes/SecurityUtil.php';

header('Content-Type: application/json');

// Initialize secure session
if (!SecurityUtil::initSecureSession()) {
    http_response_code(401);
    echo json_encode(['error' => 'Session security violation']);
    exit;
}

// Generate and return CSRF token
$token = SecurityUtil::generateCSRFToken();

echo json_encode([
    'success' => true,
    'token' => $token
]);
?>