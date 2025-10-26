<?php
/**
 * Automated Reminder System
 * CLI script for sending automated appointment reminders
 */

require_once __DIR__ . '/initialize.php';
require_once __DIR__ . '/classes/NotificationService.php';

class ReminderSystem {
    private $notificationService;
    private $db;
    
    public function __construct() {
        global $conn;
        $this->db = $conn;
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Run the reminder system
     */
    public function run() {
        $this->logMessage("Starting automated reminder system...");
        
        try {
            // Send 48-hour reminders
            $this->send48HourReminders();
            
            // Send 3-hour reminders
            $this->send3HourReminders();
            
            // Send 1-hour reminders
            $this->send1HourReminders();
            
            $this->logMessage("Reminder system completed successfully");
            
        } catch (Exception $e) {
            $this->logMessage("Error in reminder system: " . $e->getMessage());
        }
    }
    
    /**
     * Send 48-hour advance reminders
     */
    private function send48HourReminders() {
        $appointments = $this->getAppointmentsForReminder(48);
        $this->logMessage("Found " . count($appointments) . " appointments for 48-hour reminders");
        
        foreach ($appointments as $appointment) {
            if ($this->shouldSendReminder($appointment['id'], '48h')) {
                $this->sendReminder($appointment, '48h');
                $this->markReminderSent($appointment['id'], '48h');
            }
        }
    }
    
    /**
     * Send 3-hour advance reminders
     */
    private function send3HourReminders() {
        $appointments = $this->getAppointmentsForReminder(3);
        $this->logMessage("Found " . count($appointments) . " appointments for 3-hour reminders");
        
        foreach ($appointments as $appointment) {
            if ($this->shouldSendReminder($appointment['id'], '3h')) {
                $this->sendReminder($appointment, '3h');
                $this->markReminderSent($appointment['id'], '3h');
            }
        }
    }
    
    /**
     * Send 1-hour advance reminders
     */
    private function send1HourReminders() {
        $appointments = $this->getAppointmentsForReminder(1);
        $this->logMessage("Found " . count($appointments) . " appointments for 1-hour reminders");
        
        foreach ($appointments as $appointment) {
            if ($this->shouldSendReminder($appointment['id'], '1h')) {
                $this->sendReminder($appointment, '1h');
                $this->markReminderSent($appointment['id'], '1h');
            }
        }
    }
    
    /**
     * Get appointments that need reminders
     * @param int $hours Hours before appointment
     * @return array
     */
    private function getAppointmentsForReminder($hours) {
        $sql = "
            SELECT 
                a.id, a.code, a.schedule_date, a.notes, a.total_fee, a.status,
                u.name as user_name, u.email, u.phone,
                p.name as pet_name, p.species, p.breed,
                s.name as service_name,
                ts.start_time, ts.end_time
            FROM appointments a
            JOIN users u ON a.user_id = u.id
            JOIN pets p ON a.pet_id = p.id
            JOIN services s ON a.service_id = s.id
            JOIN time_slots ts ON a.time_slot_id = ts.id
            WHERE a.status IN ('confirmed', 'paid')
            AND CONCAT(a.schedule_date, ' ', ts.start_time) 
                BETWEEN NOW() + INTERVAL ? HOUR - INTERVAL 15 MINUTE
                AND NOW() + INTERVAL ? HOUR + INTERVAL 15 MINUTE
            ORDER BY a.schedule_date, ts.start_time
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hours, $hours]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if reminder should be sent (not already sent)
     * @param int $appointmentId
     * @param string $reminderType
     * @return bool
     */
    private function shouldSendReminder($appointmentId, $reminderType) {
        $sql = "
            SELECT COUNT(*) 
            FROM notification_logs 
            WHERE appointment_id = ? 
            AND type = 'reminder' 
            AND metadata LIKE ?
            AND sent_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$appointmentId, "%\"reminder_type\":\"$reminderType\"%"]);
        
        return $stmt->fetchColumn() == 0;
    }
    
    /**
     * Send reminder notification
     * @param array $appointment
     * @param string $reminderType
     */
    private function sendReminder($appointment, $reminderType) {
        try {
            $timeMap = [
                '48h' => '48 hours',
                '3h' => '3 hours',
                '1h' => '1 hour'
            ];
            
            $timeText = $timeMap[$reminderType] ?? $reminderType;
            $appointmentDateTime = $appointment['schedule_date'] . ' ' . $appointment['start_time'];
            
            // Prepare email data
            $emailData = [
                'user_name' => $appointment['user_name'],
                'appointment_code' => $appointment['code'],
                'service_name' => $appointment['service_name'],
                'pet_name' => $appointment['pet_name'],
                'appointment_date' => date('l, F j, Y', strtotime($appointment['schedule_date'])),
                'appointment_time' => date('g:i A', strtotime($appointment['start_time'])),
                'clinic_address' => $_ENV['CLINIC_ADDRESS'] ?? 'OVAS Veterinary Clinic, Nairobi',
                'reminder_time' => $timeText,
                'total_fee' => number_format($appointment['total_fee'], 2)
            ];
            
            // Send email reminder
            $emailSent = $this->notificationService->sendAppointmentReminder(
                $appointment['email'],
                $emailData,
                $reminderType
            );
            
            // Send SMS reminder
            $smsMessage = $this->buildSMSReminder($appointment, $timeText);
            $smsSent = $this->notificationService->sendSMS($appointment['phone'], $smsMessage);
            
            // Log the reminder
            $this->logNotification([
                'appointment_id' => $appointment['id'],
                'user_id' => null, // From appointment context
                'type' => 'reminder',
                'channel' => $emailSent && $smsSent ? 'email,sms' : ($emailSent ? 'email' : 'sms'),
                'status' => ($emailSent || $smsSent) ? 'sent' : 'failed',
                'recipient' => $appointment['email'],
                'metadata' => json_encode([
                    'reminder_type' => $reminderType,
                    'appointment_code' => $appointment['code'],
                    'email_status' => $emailSent ? 'sent' : 'failed',
                    'sms_status' => $smsSent ? 'sent' : 'failed'
                ])
            ]);
            
            $this->logMessage("Sent $reminderType reminder for appointment {$appointment['code']} to {$appointment['user_name']}");
            
        } catch (Exception $e) {
            $this->logMessage("Failed to send $reminderType reminder for appointment {$appointment['code']}: " . $e->getMessage());
        }
    }
    
    /**
     * Build SMS reminder message
     * @param array $appointment
     * @param string $timeText
     * @return string
     */
    private function buildSMSReminder($appointment, $timeText) {
        $date = date('M j, Y', strtotime($appointment['schedule_date']));
        $time = date('g:i A', strtotime($appointment['start_time']));
        
        return "OVAS REMINDER: Your appointment for {$appointment['pet_name']} ({$appointment['service_name']}) is in $timeText on $date at $time. Code: {$appointment['code']}. Please arrive 15 minutes early.";
    }
    
    /**
     * Mark reminder as sent (for tracking)
     * @param int $appointmentId
     * @param string $reminderType
     */
    private function markReminderSent($appointmentId, $reminderType) {
        // This is handled by logNotification method above
        // Additional tracking could be added here if needed
    }
    
    /**
     * Log notification to database
     * @param array $data
     */
    private function logNotification($data) {
        try {
            $sql = "
                INSERT INTO notification_logs 
                (appointment_id, user_id, type, channel, status, recipient, metadata, sent_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['appointment_id'],
                $data['user_id'],
                $data['type'],
                $data['channel'],
                $data['status'],
                $data['recipient'],
                $data['metadata']
            ]);
            
        } catch (Exception $e) {
            $this->logMessage("Failed to log notification: " . $e->getMessage());
        }
    }
    
    /**
     * Log message with timestamp
     * @param string $message
     */
    private function logMessage($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        // Log to file
        $logFile = __DIR__ . '/logs/reminders.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        
        // Also output to console if running in CLI
        if (php_sapi_name() === 'cli') {
            echo $logMessage;
        }
    }
}

// Run the reminder system if called directly
if (php_sapi_name() === 'cli' && isset($argv[0]) && basename($argv[0]) === 'reminder_system.php') {
    $reminderSystem = new ReminderSystem();
    $reminderSystem->run();
}
?>