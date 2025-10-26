<?php
/**
 * Notification Service
 * Handles email and SMS notifications for appointment confirmations and updates
 */

require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class NotificationService {
    private $mailer;
    private $smsEnabled = false;
    private $smsApiKey;
    private $smsPartnerId;
    private $smsShortcode;
    
    public function __construct() {
        $this->initializeMailer();
        $this->initializeSMS();
    }
    
    /**
     * Initialize PHPMailer with configuration
     */
    private function initializeMailer() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $_ENV['MAIL_PORT'] ?? 587;
            
            // Default sender
            $this->mailer->setFrom(
                $_ENV['MAIL_FROM'] ?? 'clinic@example.com',
                $_ENV['APP_NAME'] ?? 'OVAS Clinic'
            );
            
            // Set content type
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Mail configuration error: " . $e->getMessage());
        }
    }
    
    /**
     * Initialize SMS service configuration
     */
    private function initializeSMS() {
        $this->smsApiKey = $_ENV['TEXTSMS_API_KEY'] ?? '';
        $this->smsPartnerId = $_ENV['TEXTSMS_PARTNER_ID'] ?? '';
        $this->smsShortcode = $_ENV['TEXTSMS_SHORTCODE'] ?? '';
        
        $this->smsEnabled = !empty($this->smsApiKey) && !empty($this->smsPartnerId);
    }
    
    /**
     * Send appointment confirmation email
     * @param array $data Notification data
     * @return bool Success status
     */
    public function sendAppointmentConfirmation($data) {
        try {
            $user = $data['user'];
            $appointment = $data['appointment'];
            $service = $data['service'];
            $payment = $data['payment'];
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user['email'], $user['firstname'] . ' ' . $user['lastname']);
            
            $this->mailer->Subject = 'Appointment Confirmation - ' . $appointment['code'];
            
            $appointmentDate = date('l, F j, Y', strtotime($appointment['appointment_date']));
            $timeSlot = $this->getTimeSlotDisplay($appointment['time_slot_id']);
            $amountPaid = 'KSh ' . number_format($data['amount_paid'], 2);
            
            $this->mailer->Body = $this->getConfirmationEmailTemplate([
                'user_name' => $user['firstname'] . ' ' . $user['lastname'],
                'appointment_code' => $appointment['code'],
                'service_name' => $service['service'],
                'appointment_date' => $appointmentDate,
                'appointment_time' => $timeSlot,
                'amount_paid' => $amountPaid,
                'mpesa_receipt' => $data['mpesa_receipt'],
                'clinic_name' => $_ENV['APP_NAME'] ?? 'OVAS Clinic',
                'clinic_phone' => $_ENV['CLINIC_PHONE'] ?? '+254712345678',
                'clinic_email' => $_ENV['MAIL_FROM'] ?? 'clinic@example.com'
            ]);
            
            $emailSent = $this->mailer->send();
            
            // Send SMS confirmation if enabled
            if ($this->smsEnabled && !empty($user['phone'])) {
                $this->sendAppointmentConfirmationSMS($user, $appointment, $service);
            }
            
            return $emailSent;
            
        } catch (Exception $e) {
            error_log("Email confirmation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send payment failure notification
     * @param array $data Notification data
     * @return bool Success status
     */
    public function sendPaymentFailureNotification($data) {
        try {
            $user = $data['user'];
            $appointment = $data['appointment'];
            $service = $data['service'];
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user['email'], $user['firstname'] . ' ' . $user['lastname']);
            
            $this->mailer->Subject = 'Payment Failed - ' . $appointment['code'];
            
            $appointmentDate = date('l, F j, Y', strtotime($appointment['appointment_date']));
            $timeSlot = $this->getTimeSlotDisplay($appointment['time_slot_id']);
            
            $this->mailer->Body = $this->getPaymentFailureEmailTemplate([
                'user_name' => $user['firstname'] . ' ' . $user['lastname'],
                'appointment_code' => $appointment['code'],
                'service_name' => $service['service'],
                'appointment_date' => $appointmentDate,
                'appointment_time' => $timeSlot,
                'failure_reason' => $data['failure_reason'],
                'clinic_name' => $_ENV['APP_NAME'] ?? 'OVAS Clinic',
                'clinic_phone' => $_ENV['CLINIC_PHONE'] ?? '+254712345678',
                'rebook_url' => $_ENV['APP_URL'] . '/appointment.php?service_id=' . $service['id']
            ]);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Payment failure notification failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send appointment reminder
     * @param array $data Notification data
     * @return bool Success status
     */
    public function sendAppointmentReminder($data) {
        try {
            $user = $data['user'];
            $appointment = $data['appointment'];
            $service = $data['service'];
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($user['email'], $user['firstname'] . ' ' . $user['lastname']);
            
            $this->mailer->Subject = 'Appointment Reminder - Tomorrow';
            
            $appointmentDate = date('l, F j, Y', strtotime($appointment['appointment_date']));
            $timeSlot = $this->getTimeSlotDisplay($appointment['time_slot_id']);
            
            $this->mailer->Body = $this->getReminderEmailTemplate([
                'user_name' => $user['firstname'] . ' ' . $user['lastname'],
                'appointment_code' => $appointment['code'],
                'service_name' => $service['service'],
                'appointment_date' => $appointmentDate,
                'appointment_time' => $timeSlot,
                'clinic_name' => $_ENV['APP_NAME'] ?? 'OVAS Clinic',
                'clinic_phone' => $_ENV['CLINIC_PHONE'] ?? '+254712345678',
                'clinic_address' => $_ENV['CLINIC_ADDRESS'] ?? 'Nairobi, Kenya'
            ]);
            
            $emailSent = $this->mailer->send();
            
            // Send SMS reminder if enabled
            if ($this->smsEnabled && !empty($user['phone'])) {
                $this->sendAppointmentReminderSMS($user, $appointment, $service);
            }
            
            return $emailSent;
            
        } catch (Exception $e) {
            error_log("Appointment reminder failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send appointment confirmation SMS
     * @param array $user User data
     * @param array $appointment Appointment data
     * @param array $service Service data
     * @return bool Success status
     */
    private function sendAppointmentConfirmationSMS($user, $appointment, $service) {
        $appointmentDate = date('M j, Y', strtotime($appointment['appointment_date']));
        $timeSlot = $this->getTimeSlotDisplay($appointment['time_slot_id']);
        
        $message = "Appointment confirmed! Code: {$appointment['code']}, Service: {$service['service']}, Date: {$appointmentDate}, Time: {$timeSlot}. Thank you!";
        
        return $this->sendSMS($user['phone'], $message);
    }
    
    /**
     * Send appointment reminder SMS
     * @param array $user User data
     * @param array $appointment Appointment data
     * @param array $service Service data
     * @return bool Success status
     */
    private function sendAppointmentReminderSMS($user, $appointment, $service) {
        $appointmentDate = date('M j, Y', strtotime($appointment['appointment_date']));
        $timeSlot = $this->getTimeSlotDisplay($appointment['time_slot_id']);
        
        $message = "Reminder: You have an appointment tomorrow. Code: {$appointment['code']}, Time: {$timeSlot}. Please arrive 15 minutes early.";
        
        return $this->sendSMS($user['phone'], $message);
    }
    
    /**
     * Send SMS message
     * @param string $phone Phone number
     * @param string $message Message content
     * @return bool Success status
     */
    private function sendSMS($phone, $message) {
        if (!$this->smsEnabled) {
            return false;
        }
        
        try {
            // Format phone number
            $phone = $this->formatPhoneNumber($phone);
            if (!$phone) {
                return false;
            }
            
            // Prepare SMS API request (TextSMS format)
            $data = [
                'api_key' => $this->smsApiKey,
                'partner_id' => $this->smsPartnerId,
                'shortcode' => $this->smsShortcode,
                'message' => $message,
                'mobile' => $phone
            ];
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.textsms.co.ke/api/services/sendsms/',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                return isset($result['success']) && $result['success'];
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("SMS sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format phone number for SMS
     * @param string $phone
     * @return string|false
     */
    private function formatPhoneNumber($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            return '254' . substr($phone, 1);
        } elseif (strlen($phone) === 9) {
            return '254' . $phone;
        } elseif (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
            return $phone;
        }
        
        return false;
    }
    
    /**
     * Get time slot display from time slot ID
     * @param int $timeSlotId
     * @return string
     */
    private function getTimeSlotDisplay($timeSlotId) {
        try {
            $timeSlotsModel = new TimeSlotsModel();
            $timeSlot = $timeSlotsModel->find($timeSlotId);
            
            if ($timeSlot) {
                return date('g:i A', strtotime($timeSlot['start_time'])) . ' - ' . 
                       date('g:i A', strtotime($timeSlot['end_time']));
            }
            
            return 'Time slot not found';
            
        } catch (Exception $e) {
            return 'Time slot unavailable';
        }
    }
    
    /**
     * Get confirmation email template
     * @param array $data Template data
     * @return string HTML email content
     */
    private function getConfirmationEmailTemplate($data) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; background: #6c757d; color: white; }
                .appointment-details { background: white; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
                .highlight { color: #007bff; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Appointment Confirmed!</h1>
                </div>
                <div class='content'>
                    <p>Dear {$data['user_name']},</p>
                    
                    <p>Your appointment has been successfully confirmed and payment received. Here are your appointment details:</p>
                    
                    <div class='appointment-details'>
                        <h3>Appointment Details</h3>
                        <p><strong>Appointment Code:</strong> <span class='highlight'>{$data['appointment_code']}</span></p>
                        <p><strong>Service:</strong> {$data['service_name']}</p>
                        <p><strong>Date:</strong> {$data['appointment_date']}</p>
                        <p><strong>Time:</strong> {$data['appointment_time']}</p>
                        <p><strong>Amount Paid:</strong> {$data['amount_paid']}</p>
                        <p><strong>M-Pesa Receipt:</strong> {$data['mpesa_receipt']}</p>
                    </div>
                    
                    <h3>Important Instructions:</h3>
                    <ul>
                        <li>Please arrive 15 minutes before your scheduled time</li>
                        <li>Bring a valid ID for verification</li>
                        <li>If you need to reschedule, please contact us at least 24 hours in advance</li>
                        <li>Keep your appointment code for reference</li>
                    </ul>
                    
                    <p>If you have any questions, please contact us:</p>
                    <p>Phone: {$data['clinic_phone']}<br>Email: {$data['clinic_email']}</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 {$data['clinic_name']}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Get payment failure email template
     * @param array $data Template data
     * @return string HTML email content
     */
    private function getPaymentFailureEmailTemplate($data) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; background: #6c757d; color: white; }
                .appointment-details { background: white; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0; }
                .rebook-button { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Payment Failed</h1>
                </div>
                <div class='content'>
                    <p>Dear {$data['user_name']},</p>
                    
                    <p>We're sorry to inform you that your payment for the following appointment could not be processed:</p>
                    
                    <div class='appointment-details'>
                        <h3>Appointment Details</h3>
                        <p><strong>Appointment Code:</strong> {$data['appointment_code']}</p>
                        <p><strong>Service:</strong> {$data['service_name']}</p>
                        <p><strong>Date:</strong> {$data['appointment_date']}</p>
                        <p><strong>Time:</strong> {$data['appointment_time']}</p>
                        <p><strong>Reason:</strong> {$data['failure_reason']}</p>
                    </div>
                    
                    <p>Your appointment has been cancelled due to the payment failure. To reschedule your appointment, please try booking again.</p>
                    
                    <a href='{$data['rebook_url']}' class='rebook-button'>Book New Appointment</a>
                    
                    <p>If you continue to experience payment issues, please contact us:</p>
                    <p>Phone: {$data['clinic_phone']}</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 {$data['clinic_name']}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Get reminder email template
     * @param array $data Template data
     * @return string HTML email content
     */
    private function getReminderEmailTemplate($data) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ffc107; color: #333; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; background: #6c757d; color: white; }
                .appointment-details { background: white; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
                .highlight { color: #007bff; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Appointment Reminder</h1>
                </div>
                <div class='content'>
                    <p>Dear {$data['user_name']},</p>
                    
                    <p>This is a friendly reminder about your upcoming appointment tomorrow:</p>
                    
                    <div class='appointment-details'>
                        <h3>Appointment Details</h3>
                        <p><strong>Appointment Code:</strong> <span class='highlight'>{$data['appointment_code']}</span></p>
                        <p><strong>Service:</strong> {$data['service_name']}</p>
                        <p><strong>Date:</strong> {$data['appointment_date']}</p>
                        <p><strong>Time:</strong> {$data['appointment_time']}</p>
                    </div>
                    
                    <h3>Reminders:</h3>
                    <ul>
                        <li>Please arrive 15 minutes before your scheduled time</li>
                        <li>Bring a valid ID for verification</li>
                        <li>If you need to reschedule, please contact us immediately</li>
                    </ul>
                    
                    <p><strong>Clinic Address:</strong> {$data['clinic_address']}</p>
                    <p><strong>Contact:</strong> {$data['clinic_phone']}</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 {$data['clinic_name']}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>