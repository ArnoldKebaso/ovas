<?php
/**
 * M-Pesa Integration Service
 * Handles STK Push payments with proper error handling and transaction tracking
 */

class MpesaService {
    private $consumerKey;
    private $consumerSecret;
    private $environment;
    private $businessShortCode;
    private $passkey;
    private $callbackUrl;
    private $baseUrl;
    
    public function __construct() {
        // Load M-Pesa configuration from environment
        $this->consumerKey = $_ENV['MPESA_CONSUMER_KEY'] ?? '';
        $this->consumerSecret = $_ENV['MPESA_CONSUMER_SECRET'] ?? '';
        $this->environment = $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox';
        $this->businessShortCode = $_ENV['MPESA_BUSINESS_SHORTCODE'] ?? '174379';
        $this->passkey = $_ENV['MPESA_PASSKEY'] ?? 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $this->callbackUrl = $_ENV['MPESA_CALLBACK_URL'] ?? $_ENV['APP_URL'] . '/mpesa_callback.php';
        
        // Set base URL based on environment
        $this->baseUrl = $this->environment === 'production' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';
    }
    
    /**
     * Generate OAuth access token
     * @return string|false Access token or false on failure
     */
    public function generateAccessToken() {
        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials',
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $credentials,
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        
        if ($error) {
            error_log("M-Pesa OAuth cURL Error: " . $error);
            return false;
        }
        
        if ($httpCode !== 200) {
            error_log("M-Pesa OAuth HTTP Error: " . $httpCode . " - " . $response);
            return false;
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['access_token'])) {
            error_log("M-Pesa OAuth Response Error: " . $response);
            return false;
        }
        
        return $result['access_token'];
    }
    
    /**
     * Generate password for STK Push
     * @param string $timestamp
     * @return string
     */
    private function generatePassword($timestamp) {
        return base64_encode($this->businessShortCode . $this->passkey . $timestamp);
    }
    
    /**
     * Validate phone number format
     * @param string $phone
     * @return string|false Formatted phone or false if invalid
     */
    private function formatPhoneNumber($phone) {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle different formats
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            // Convert 0712345678 to 254712345678
            return '254' . substr($phone, 1);
        } elseif (strlen($phone) === 9) {
            // Convert 712345678 to 254712345678
            return '254' . $phone;
        } elseif (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
            // Already in correct format
            return $phone;
        }
        
        return false;
    }
    
    /**
     * Initiate STK Push payment
     * @param string $phone Customer phone number
     * @param float $amount Payment amount
     * @param string $accountReference Account reference (appointment code)
     * @param string $transactionDesc Transaction description
     * @return array Response array with success status and data
     */
    public function stkPush($phone, $amount, $accountReference, $transactionDesc = 'OVAS Appointment Payment') {
        try {
            // Validate inputs
            $formattedPhone = $this->formatPhoneNumber($phone);
            if (!$formattedPhone) {
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Use format: 0712345678',
                    'error_code' => 'INVALID_PHONE'
                ];
            }
            
            if ($amount < 1) {
                return [
                    'success' => false,
                    'message' => 'Amount must be at least KSh 1.00',
                    'error_code' => 'INVALID_AMOUNT'
                ];
            }
            
            // Get access token
            $accessToken = $this->generateAccessToken();
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with M-Pesa. Please try again.',
                    'error_code' => 'AUTH_FAILED'
                ];
            }
            
            // Generate timestamp and password
            $timestamp = date('YmdHis');
            $password = $this->generatePassword($timestamp);
            
            // Prepare request payload
            $payload = [
                'BusinessShortCode' => $this->businessShortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => round($amount),
                'PartyA' => $formattedPhone,
                'PartyB' => $this->businessShortCode,
                'PhoneNumber' => $formattedPhone,
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => $transactionDesc
            ];
            
            // Make STK Push request
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->baseUrl . '/mpesa/stkpush/v1/processrequest',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 60
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);
            
            if ($error) {
                error_log("M-Pesa STK Push cURL Error: " . $error);
                return [
                    'success' => false,
                    'message' => 'Network error occurred. Please try again.',
                    'error_code' => 'NETWORK_ERROR'
                ];
            }
            
            $result = json_decode($response, true);
            
            // Log the full response for debugging
            error_log("M-Pesa STK Push Response: " . $response);
            
            if ($httpCode === 200 && isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'message' => 'Payment request sent successfully. Please check your phone.',
                    'data' => [
                        'merchant_request_id' => $result['MerchantRequestID'],
                        'checkout_request_id' => $result['CheckoutRequestID'],
                        'response_code' => $result['ResponseCode'],
                        'response_description' => $result['ResponseDescription'],
                        'customer_message' => $result['CustomerMessage'] ?? 'Please check your phone for the payment prompt.'
                    ]
                ];
            } else {
                $errorMessage = $result['ResponseDescription'] ?? $result['errorMessage'] ?? 'Payment request failed';
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'error_code' => 'MPESA_ERROR',
                    'mpesa_response' => $result
                ];
            }
            
        } catch (Exception $e) {
            error_log("M-Pesa STK Push Exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
                'error_code' => 'EXCEPTION'
            ];
        }
    }
    
    /**
     * Query STK Push transaction status
     * @param string $checkoutRequestId
     * @return array Response array with transaction status
     */
    public function queryTransaction($checkoutRequestId) {
        try {
            $accessToken = $this->generateAccessToken();
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with M-Pesa',
                    'error_code' => 'AUTH_FAILED'
                ];
            }
            
            $timestamp = date('YmdHis');
            $password = $this->generatePassword($timestamp);
            
            $payload = [
                'BusinessShortCode' => $this->businessShortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId
            ];
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->baseUrl . '/mpesa/stkpushquery/v1/query',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                return [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to query transaction status',
                    'error_code' => 'QUERY_FAILED'
                ];
            }
            
        } catch (Exception $e) {
            error_log("M-Pesa Query Exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Query failed',
                'error_code' => 'EXCEPTION'
            ];
        }
    }
    
    /**
     * Validate callback data structure
     * @param array $callbackData
     * @return bool
     */
    public function isValidCallback($callbackData) {
        return isset($callbackData['Body']['stkCallback']) &&
               isset($callbackData['Body']['stkCallback']['CheckoutRequestID']) &&
               isset($callbackData['Body']['stkCallback']['ResultCode']);
    }
    
    /**
     * Extract payment details from callback
     * @param array $callbackData
     * @return array|null
     */
    public function extractCallbackData($callbackData) {
        if (!$this->isValidCallback($callbackData)) {
            return null;
        }
        
        $stkCallback = $callbackData['Body']['stkCallback'];
        
        $result = [
            'checkout_request_id' => $stkCallback['CheckoutRequestID'],
            'merchant_request_id' => $stkCallback['MerchantRequestID'],
            'result_code' => $stkCallback['ResultCode'],
            'result_desc' => $stkCallback['ResultDesc'],
            'success' => $stkCallback['ResultCode'] === 0,
            'mpesa_receipt_number' => null,
            'amount' => null,
            'transaction_date' => null,
            'phone_number' => null
        ];
        
        // Extract additional data if payment was successful
        if ($result['success'] && isset($stkCallback['CallbackMetadata']['Item'])) {
            foreach ($stkCallback['CallbackMetadata']['Item'] as $item) {
                switch ($item['Name']) {
                    case 'Amount':
                        $result['amount'] = $item['Value'];
                        break;
                    case 'MpesaReceiptNumber':
                        $result['mpesa_receipt_number'] = $item['Value'];
                        break;
                    case 'TransactionDate':
                        $result['transaction_date'] = $item['Value'];
                        break;
                    case 'PhoneNumber':
                        $result['phone_number'] = $item['Value'];
                        break;
                }
            }
        }
        
        return $result;
    }
}
?>