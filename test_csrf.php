<?php
/**
 * CSRF Token Test
 */

require_once 'config.php';
require_once 'inc/sess_auth.php';

// Test CSRF token generation
echo "Testing CSRF Token Generation:\n";
echo "Token 1: " . csrf_token() . "\n";
echo "Token 2: " . csrf_token() . "\n";
echo "Tokens should be identical: " . (csrf_token() === csrf_token() ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test CSRF token verification
echo "Testing CSRF Token Verification:\n";
$valid_token = csrf_token();
$invalid_token = "invalid_token_12345";

echo "Valid token verification: " . (csrf_verify($valid_token) ? "✓ PASS" : "✗ FAIL") . "\n";
echo "Invalid token verification: " . (csrf_verify($invalid_token) ? "✗ FAIL" : "✓ PASS") . "\n\n";

// Test auth helper functions
echo "Testing Auth Helper Functions:\n";
echo "auth_user(): " . (auth_user() ? "User logged in" : "No user") . "\n";
echo "auth_id(): " . (auth_id() ?: "No ID") . "\n";
echo "is_admin(): " . (is_admin() ? "Yes" : "No") . "\n\n";

echo "✓ CSRF and Auth tests completed!";
?>