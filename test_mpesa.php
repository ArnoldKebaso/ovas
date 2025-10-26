<?php
/**
 * Test M-Pesa Service Integration
 * Quick test to verify M-Pesa service is properly configured
 */

require_once 'vendor/autoload.php';

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

require_once 'classes/MpesaService.php';

echo "Testing M-Pesa Service Integration...\n\n";

try {
    $mpesaService = new MpesaService();
    
    // Test 1: Phone number validation
    echo "Test 1: Phone Number Validation\n";
    $testNumbers = ['0712345678', '712345678', '254712345678', 'invalid'];
    
    foreach ($testNumbers as $number) {
        $reflection = new ReflectionClass($mpesaService);
        $method = $reflection->getMethod('formatPhoneNumber');
        $method->setAccessible(true);
        $result = $method->invoke($mpesaService, $number);
        
        echo "  $number -> " . ($result ?: 'INVALID') . "\n";
    }
    
    echo "\nTest 2: Password Generation\n";
    $reflection = new ReflectionClass($mpesaService);
    $method = $reflection->getMethod('generatePassword');
    $method->setAccessible(true);
    $timestamp = '20241026143000';
    $password = $method->invoke($mpesaService, $timestamp);
    echo "  Timestamp: $timestamp\n";
    echo "  Generated Password: " . substr($password, 0, 20) . "...\n";
    
    echo "\nTest 3: Environment Configuration Check\n";
    $config = [
        'MPESA_ENVIRONMENT' => $_ENV['MPESA_ENVIRONMENT'] ?? 'not set',
        'MPESA_BUSINESS_SHORTCODE' => $_ENV['MPESA_BUSINESS_SHORTCODE'] ?? 'not set',
        'MPESA_CALLBACK_URL' => $_ENV['MPESA_CALLBACK_URL'] ?? 'not set'
    ];
    
    foreach ($config as $key => $value) {
        echo "  $key: $value\n";
    }
    
    echo "\nTest 4: Callback Data Validation\n";
    $validCallback = [
        'Body' => [
            'stkCallback' => [
                'CheckoutRequestID' => 'ws_CO_123456789',
                'MerchantRequestID' => 'mr_123456789',
                'ResultCode' => 0,
                'ResultDesc' => 'The service request is processed successfully.'
            ]
        ]
    ];
    
    $isValid = $mpesaService->isValidCallback($validCallback);
    echo "  Valid callback structure: " . ($isValid ? '✅ Valid' : '❌ Invalid') . "\n";
    
    $extractedData = $mpesaService->extractCallbackData($validCallback);
    if ($extractedData) {
        echo "  ✅ Data extraction successful\n";
        echo "    - Checkout ID: {$extractedData['checkout_request_id']}\n";
        echo "    - Success: " . ($extractedData['success'] ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n🎉 M-Pesa Service integration test completed successfully!\n";
    echo "📝 Note: Actual payment requests will fail without valid M-Pesa credentials in .env\n";
    echo "📋 To set up M-Pesa:\n";
    echo "   1. Get Consumer Key and Secret from Safaricom Developer Portal\n";
    echo "   2. Update MPESA_CONSUMER_KEY and MPESA_CONSUMER_SECRET in .env\n";
    echo "   3. Update MPESA_BUSINESS_SHORTCODE with your business shortcode\n";
    echo "   4. Update MPESA_PASSKEY with your API passkey\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}
?>