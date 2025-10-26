<?php
/**
 * Email Templates System
 * Manages all email templates for appointment notifications
 */

class EmailTemplatesSystem {
    private $templatesPath;
    private $defaultData;
    
    public function __construct() {
        $this->templatesPath = __DIR__ . '/../templates/email/';
        $this->defaultData = [
            'clinic_name' => $_ENV['APP_NAME'] ?? 'OVAS Clinic',
            'clinic_phone' => $_ENV['CLINIC_PHONE'] ?? '+254712345678',
            'clinic_email' => $_ENV['MAIL_FROM'] ?? 'clinic@example.com',
            'clinic_address' => $_ENV['CLINIC_ADDRESS'] ?? 'Nairobi, Kenya',
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost/ovas',
            'year' => date('Y')
        ];
        
        // Create templates directory if it doesn't exist
        if (!is_dir($this->templatesPath)) {
            mkdir($this->templatesPath, 0755, true);
        }
    }
    
    /**
     * Get appointment confirmation template
     * @param array $data Template variables
     * @return string HTML template
     */
    public function getConfirmationTemplate($data = []) {
        $data = array_merge($this->defaultData, $data);
        
        return $this->loadTemplate('confirmation', $data) ?: $this->getDefaultConfirmationTemplate($data);
    }
    
    /**
     * Get payment failure template
     * @param array $data Template variables
     * @return string HTML template
     */
    public function getPaymentFailureTemplate($data = []) {
        $data = array_merge($this->defaultData, $data);
        
        return $this->loadTemplate('payment_failure', $data) ?: $this->getDefaultPaymentFailureTemplate($data);
    }
    
    /**
     * Get appointment reminder template
     * @param array $data Template variables
     * @return string HTML template
     */
    public function getReminderTemplate($data = []) {
        $data = array_merge($this->defaultData, $data);
        
        return $this->loadTemplate('reminder', $data) ?: $this->getDefaultReminderTemplate($data);
    }
    
    /**
     * Get cancellation template
     * @param array $data Template variables
     * @return string HTML template
     */
    public function getCancellationTemplate($data = []) {
        $data = array_merge($this->defaultData, $data);
        
        return $this->loadTemplate('cancellation', $data) ?: $this->getDefaultCancellationTemplate($data);
    }
    
    /**
     * Get rescheduling template
     * @param array $data Template variables
     * @return string HTML template
     */
    public function getReschedulingTemplate($data = []) {
        $data = array_merge($this->defaultData, $data);
        
        return $this->loadTemplate('rescheduling', $data) ?: $this->getDefaultReschedulingTemplate($data);
    }
    
    /**
     * Load template from file
     * @param string $templateName Template name
     * @param array $data Template variables
     * @return string|false Template content or false if not found
     */
    private function loadTemplate($templateName, $data) {
        $templateFile = $this->templatesPath . $templateName . '.html';
        
        if (file_exists($templateFile)) {
            $content = file_get_contents($templateFile);
            return $this->replacePlaceholders($content, $data);
        }
        
        return false;
    }
    
    /**
     * Replace placeholders in template
     * @param string $content Template content
     * @param array $data Data to replace
     * @return string Processed template
     */
    private function replacePlaceholders($content, $data) {
        foreach ($data as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        return $content;
    }
    
    /**
     * Save template to file
     * @param string $templateName Template name
     * @param string $content Template content
     * @return bool Success status
     */
    public function saveTemplate($templateName, $content) {
        $templateFile = $this->templatesPath . $templateName . '.html';
        return file_put_contents($templateFile, $content) !== false;
    }
    
    /**
     * Get default confirmation template
     * @param array $data Template data
     * @return string HTML template
     */
    private function getDefaultConfirmationTemplate($data) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Appointment Confirmed</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f4f4f4; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 10px; 
                    overflow: hidden; 
                    box-shadow: 0 0 20px rgba(0,0,0,0.1); 
                }
                .header { 
                    background: linear-gradient(135deg, #007bff, #0056b3); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 300; 
                }
                .content { 
                    padding: 30px 20px; 
                }
                .appointment-card { 
                    background: #f8f9fa; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin: 20px 0; 
                    border-left: 4px solid #28a745; 
                }
                .appointment-card h3 { 
                    margin-top: 0; 
                    color: #28a745; 
                }
                .detail-row { 
                    display: flex; 
                    justify-content: space-between; 
                    padding: 8px 0; 
                    border-bottom: 1px solid #dee2e6; 
                }
                .detail-row:last-child { 
                    border-bottom: none; 
                }
                .detail-label { 
                    font-weight: 600; 
                    color: #495057; 
                }
                .detail-value { 
                    color: #007bff; 
                    font-weight: 500; 
                }
                .instructions { 
                    background: #e3f2fd; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin: 20px 0; 
                }
                .instructions h3 { 
                    margin-top: 0; 
                    color: #1976d2; 
                }
                .instructions ul { 
                    margin: 10px 0; 
                    padding-left: 20px; 
                }
                .instructions li { 
                    margin: 8px 0; 
                }
                .contact-info { 
                    background: #f1f3f4; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin: 20px 0; 
                    text-align: center; 
                }
                .footer { 
                    background: #6c757d; 
                    color: white; 
                    text-align: center; 
                    padding: 20px; 
                    font-size: 14px; 
                }
                .success-badge { 
                    background: #28a745; 
                    color: white; 
                    padding: 5px 15px; 
                    border-radius: 20px; 
                    font-size: 12px; 
                    font-weight: 600; 
                    text-transform: uppercase; 
                    letter-spacing: 1px; 
                }
                @media (max-width: 600px) {
                    .container { 
                        margin: 0; 
                        border-radius: 0; 
                    }
                    .detail-row { 
                        flex-direction: column; 
                    }
                    .detail-value { 
                        margin-top: 5px; 
                    }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Appointment Confirmed!</h1>
                    <span class='success-badge'>Payment Successful</span>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$data['user_name']}</strong>,</p>
                    
                    <p>Great news! Your appointment has been successfully confirmed and your payment has been processed. We look forward to seeing you at our clinic.</p>
                    
                    <div class='appointment-card'>
                        <h3>📋 Appointment Details</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Appointment Code:</span>
                            <span class='detail-value'>{$data['appointment_code']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Service:</span>
                            <span class='detail-value'>{$data['service_name']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Date:</span>
                            <span class='detail-value'>{$data['appointment_date']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Time:</span>
                            <span class='detail-value'>{$data['appointment_time']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Amount Paid:</span>
                            <span class='detail-value'>{$data['amount_paid']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>M-Pesa Receipt:</span>
                            <span class='detail-value'>{$data['mpesa_receipt']}</span>
                        </div>
                    </div>
                    
                    <div class='instructions'>
                        <h3>📝 Important Instructions</h3>
                        <ul>
                            <li><strong>Arrive Early:</strong> Please arrive 15 minutes before your scheduled time</li>
                            <li><strong>Bring ID:</strong> A valid identification document is required</li>
                            <li><strong>Rescheduling:</strong> Contact us at least 24 hours in advance if you need to reschedule</li>
                            <li><strong>Keep Your Code:</strong> Your appointment code <strong>{$data['appointment_code']}</strong> is required for check-in</li>
                            <li><strong>Payment Receipt:</strong> Keep your M-Pesa receipt <strong>{$data['mpesa_receipt']}</strong> for your records</li>
                        </ul>
                    </div>
                    
                    <div class='contact-info'>
                        <h3>📞 Need Help?</h3>
                        <p>If you have any questions or need to make changes to your appointment, please contact us:</p>
                        <p><strong>Phone:</strong> {$data['clinic_phone']}<br>
                        <strong>Email:</strong> {$data['clinic_email']}</p>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$data['year']} {$data['clinic_name']}. All rights reserved.</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Get default payment failure template
     * @param array $data Template data
     * @return string HTML template
     */
    private function getDefaultPaymentFailureTemplate($data) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Payment Failed</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
                .content { padding: 30px 20px; }
                .appointment-card { background: #f8d7da; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #dc3545; }
                .rebook-button { background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
                .footer { background: #6c757d; color: white; text-align: center; padding: 20px; font-size: 14px; }
                .error-badge { background: #dc3545; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>❌ Payment Failed</h1>
                    <span class='error-badge'>Action Required</span>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$data['user_name']}</strong>,</p>
                    
                    <p>We're sorry to inform you that we were unable to process your payment for the following appointment:</p>
                    
                    <div class='appointment-card'>
                        <h3>📋 Appointment Details</h3>
                        <p><strong>Appointment Code:</strong> {$data['appointment_code']}</p>
                        <p><strong>Service:</strong> {$data['service_name']}</p>
                        <p><strong>Date:</strong> {$data['appointment_date']}</p>
                        <p><strong>Time:</strong> {$data['appointment_time']}</p>
                        <p><strong>Reason:</strong> {$data['failure_reason']}</p>
                    </div>
                    
                    <p><strong>What happens next?</strong></p>
                    <p>Your appointment has been automatically cancelled due to the payment failure. To secure your preferred time slot, please book a new appointment.</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$data['rebook_url']}' class='rebook-button'>📅 Book New Appointment</a>
                    </div>
                    
                    <p>If you continue to experience payment issues, please contact our support team:</p>
                    <p><strong>Phone:</strong> {$data['clinic_phone']}</p>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$data['year']} {$data['clinic_name']}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Get default reminder template
     * @param array $data Template data
     * @return string HTML template
     */
    private function getDefaultReminderTemplate($data) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Appointment Reminder</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #ffc107, #e0a800); color: #333; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
                .content { padding: 30px 20px; }
                .appointment-card { background: #fff3cd; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #ffc107; }
                .footer { background: #6c757d; color: white; text-align: center; padding: 20px; font-size: 14px; }
                .reminder-badge { background: #ffc107; color: #333; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔔 Appointment Reminder</h1>
                    <span class='reminder-badge'>Tomorrow</span>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$data['user_name']}</strong>,</p>
                    
                    <p>This is a friendly reminder about your upcoming appointment tomorrow:</p>
                    
                    <div class='appointment-card'>
                        <h3>📋 Your Appointment</h3>
                        <p><strong>Appointment Code:</strong> {$data['appointment_code']}</p>
                        <p><strong>Service:</strong> {$data['service_name']}</p>
                        <p><strong>Date:</strong> {$data['appointment_date']}</p>
                        <p><strong>Time:</strong> {$data['appointment_time']}</p>
                    </div>
                    
                    <h3>📝 Reminders:</h3>
                    <ul>
                        <li><strong>Arrive 15 minutes early</strong> for check-in</li>
                        <li><strong>Bring a valid ID</strong> for verification</li>
                        <li><strong>Contact us immediately</strong> if you need to reschedule</li>
                    </ul>
                    
                    <p><strong>📍 Clinic Address:</strong> {$data['clinic_address']}</p>
                    <p><strong>📞 Contact:</strong> {$data['clinic_phone']}</p>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$data['year']} {$data['clinic_name']}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Get default cancellation template
     * @param array $data Template data
     * @return string HTML template
     */
    private function getDefaultCancellationTemplate($data) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Appointment Cancelled</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #6c757d, #5a6268); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
                .content { padding: 30px 20px; }
                .appointment-card { background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #6c757d; }
                .footer { background: #6c757d; color: white; text-align: center; padding: 20px; font-size: 14px; }
                .cancelled-badge { background: #6c757d; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🚫 Appointment Cancelled</h1>
                    <span class='cancelled-badge'>Cancelled</span>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$data['user_name']}</strong>,</p>
                    
                    <p>Your appointment has been successfully cancelled:</p>
                    
                    <div class='appointment-card'>
                        <h3>📋 Cancelled Appointment</h3>
                        <p><strong>Appointment Code:</strong> {$data['appointment_code']}</p>
                        <p><strong>Service:</strong> {$data['service_name']}</p>
                        <p><strong>Date:</strong> {$data['appointment_date']}</p>
                        <p><strong>Time:</strong> {$data['appointment_time']}</p>
                        <p><strong>Cancellation Reason:</strong> {$data['cancellation_reason']}</p>
                    </div>
                    
                    <p>If you need to book a new appointment, please visit our booking page or contact us directly.</p>
                    
                    <p><strong>📞 Contact:</strong> {$data['clinic_phone']}</p>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$data['year']} {$data['clinic_name']}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Get default rescheduling template
     * @param array $data Template data
     * @return string HTML template
     */
    private function getDefaultReschedulingTemplate($data) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Appointment Rescheduled</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
                .content { padding: 30px 20px; }
                .appointment-card { background: #d1ecf1; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #17a2b8; }
                .footer { background: #6c757d; color: white; text-align: center; padding: 20px; font-size: 14px; }
                .rescheduled-badge { background: #17a2b8; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔄 Appointment Rescheduled</h1>
                    <span class='rescheduled-badge'>Updated</span>
                </div>
                
                <div class='content'>
                    <p>Dear <strong>{$data['user_name']}</strong>,</p>
                    
                    <p>Your appointment has been successfully rescheduled. Here are your new appointment details:</p>
                    
                    <div class='appointment-card'>
                        <h3>📋 New Appointment Details</h3>
                        <p><strong>Appointment Code:</strong> {$data['appointment_code']}</p>
                        <p><strong>Service:</strong> {$data['service_name']}</p>
                        <p><strong>New Date:</strong> {$data['new_appointment_date']}</p>
                        <p><strong>New Time:</strong> {$data['new_appointment_time']}</p>
                        <p><strong>Previous Date:</strong> <s>{$data['old_appointment_date']}</s></p>
                        <p><strong>Previous Time:</strong> <s>{$data['old_appointment_time']}</s></p>
                    </div>
                    
                    <p>Please make note of your new appointment time. We look forward to seeing you!</p>
                    
                    <p><strong>📞 Contact:</strong> {$data['clinic_phone']}</p>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$data['year']} {$data['clinic_name']}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>