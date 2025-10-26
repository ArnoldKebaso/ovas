<?php
/**
 * Google Calendar Service
 * Handles appointment event creation, updates, and deletions
 */

require_once 'vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class CalendarService {
    private $service;
    private $calendarId;
    private $timezone = 'Africa/Nairobi';
    
    public function __construct() {
        $this->calendarId = $_ENV['GOOGLE_CALENDAR_ID'] ?? '';
        $this->initializeService();
    }
    
    /**
     * Initialize Google Calendar service
     */
    private function initializeService() {
        try {
            $credentialsPath = $_ENV['GOOGLE_CREDENTIALS_JSON_PATH'] ?? 'storage/google/credentials.json';
            
            if (!file_exists($credentialsPath)) {
                error_log("Google Calendar: Credentials file not found at $credentialsPath");
                return;
            }
            
            $client = new Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(Calendar::CALENDAR);
            $client->setApplicationName('OVAS Veterinary Appointment System');
            
            $this->service = new Calendar($client);
            
        } catch (Exception $e) {
            error_log("Google Calendar initialization failed: " . $e->getMessage());
            $this->service = null;
        }
    }
    
    /**
     * Check if Google Calendar is properly configured
     * @return bool
     */
    public function isConfigured() {
        return $this->service !== null && !empty($this->calendarId);
    }
    
    /**
     * Create calendar event for appointment
     * @param array $appointment Appointment data with related info
     * @return string|false Event ID or false on failure
     */
    public function createEvent($appointment) {
        if (!$this->isConfigured()) {
            error_log("Google Calendar: Service not configured");
            return false;
        }
        
        try {
            // Get additional data
            $user = $this->getUserData($appointment['user_id']);
            $service = $this->getServiceData($appointment['service_id']);
            $pet = $this->getPetData($appointment['pet_id']);
            $timeSlot = $this->getTimeSlotData($appointment['time_slot_id']);
            
            if (!$user || !$service || !$pet || !$timeSlot) {
                error_log("Google Calendar: Missing required data for appointment " . $appointment['id']);
                return false;
            }
            
            // Create event
            $event = new Event([
                'summary' => "{$service['name']} - {$pet['name']} ({$user['name']})",
                'description' => $this->buildEventDescription($appointment, $service, $pet, $user),
                'start' => [
                    'dateTime' => $this->formatDateTime($appointment['schedule_date'], $timeSlot['start_time']),
                    'timeZone' => $this->timezone,
                ],
                'end' => [
                    'dateTime' => $this->formatDateTime($appointment['schedule_date'], $timeSlot['end_time']),
                    'timeZone' => $this->timezone,
                ],
                'attendees' => [
                    ['email' => $user['email']],
                ],
                'location' => $_ENV['CLINIC_ADDRESS'] ?? 'OVAS Veterinary Clinic, Nairobi',
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 1440], // 24 hours
                        ['method' => 'popup', 'minutes' => 60],   // 1 hour
                    ],
                ],
            ]);
            
            $createdEvent = $this->service->events->insert($this->calendarId, $event);
            
            error_log("Google Calendar: Event created successfully - ID: " . $createdEvent->getId());
            return $createdEvent->getId();
            
        } catch (Exception $e) {
            error_log("Google Calendar: Failed to create event - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing calendar event
     * @param array $appointment Updated appointment data
     * @return bool Success status
     */
    public function updateEvent($appointment) {
        if (!$this->isConfigured() || empty($appointment['google_event_id'])) {
            return false;
        }
        
        try {
            // Get existing event
            $event = $this->service->events->get($this->calendarId, $appointment['google_event_id']);
            
            // Get updated data
            $user = $this->getUserData($appointment['user_id']);
            $service = $this->getServiceData($appointment['service_id']);
            $pet = $this->getPetData($appointment['pet_id']);
            $timeSlot = $this->getTimeSlotData($appointment['time_slot_id']);
            
            if (!$user || !$service || !$pet || !$timeSlot) {
                error_log("Google Calendar: Missing data for update of appointment " . $appointment['id']);
                return false;
            }
            
            // Update event properties
            $event->setSummary("{$service['name']} - {$pet['name']} ({$user['name']})");
            $event->setDescription($this->buildEventDescription($appointment, $service, $pet, $user));
            
            $event->setStart([
                'dateTime' => $this->formatDateTime($appointment['schedule_date'], $timeSlot['start_time']),
                'timeZone' => $this->timezone,
            ]);
            
            $event->setEnd([
                'dateTime' => $this->formatDateTime($appointment['schedule_date'], $timeSlot['end_time']),
                'timeZone' => $this->timezone,
            ]);
            
            $this->service->events->update($this->calendarId, $event->getId(), $event);
            
            error_log("Google Calendar: Event updated successfully - ID: " . $appointment['google_event_id']);
            return true;
            
        } catch (Exception $e) {
            error_log("Google Calendar: Failed to update event - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete calendar event
     * @param string $eventId Google Calendar event ID
     * @return bool Success status
     */
    public function deleteEvent($eventId) {
        if (!$this->isConfigured() || empty($eventId)) {
            return false;
        }
        
        try {
            $this->service->events->delete($this->calendarId, $eventId);
            error_log("Google Calendar: Event deleted successfully - ID: $eventId");
            return true;
            
        } catch (Exception $e) {
            error_log("Google Calendar: Failed to delete event - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format date and time for Google Calendar
     * @param string $date Date in Y-m-d format
     * @param string $time Time in H:i:s format
     * @return string ISO 8601 datetime string
     */
    private function formatDateTime($date, $time) {
        $datetime = new DateTime("$date $time", new DateTimeZone($this->timezone));
        return $datetime->format('c'); // ISO 8601 format
    }
    
    /**
     * Build event description
     * @param array $appointment
     * @param array $service
     * @param array $pet
     * @param array $user
     * @return string
     */
    private function buildEventDescription($appointment, $service, $pet, $user) {
        $description = "OVAS Veterinary Appointment\n\n";
        $description .= "Appointment Code: {$appointment['code']}\n";
        $description .= "Service: {$service['name']}\n";
        $description .= "Pet: {$pet['name']} ({$pet['species']})\n";
        $description .= "Owner: {$user['name']}\n";
        $description .= "Contact: {$user['phone']}\n";
        $description .= "Email: {$user['email']}\n";
        
        if (!empty($pet['breed'])) {
            $description .= "Breed: {$pet['breed']}\n";
        }
        
        if (!empty($pet['age'])) {
            $description .= "Age: {$pet['age']}\n";
        }
        
        if (!empty($appointment['notes'])) {
            $description .= "\nNotes: {$appointment['notes']}\n";
        }
        
        $description .= "\nFee: KSh " . number_format($appointment['total_fee'], 2);
        $description .= "\nStatus: " . ucfirst($appointment['status']);
        
        return $description;
    }
    
    /**
     * Get user data
     * @param int $userId
     * @return array|false
     */
    private function getUserData($userId) {
        try {
            $usersModel = new UsersModel();
            return $usersModel->find($userId);
        } catch (Exception $e) {
            error_log("Calendar: Failed to get user data - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get service data
     * @param int $serviceId
     * @return array|false
     */
    private function getServiceData($serviceId) {
        try {
            $servicesModel = new ServicesModel();
            return $servicesModel->find($serviceId);
        } catch (Exception $e) {
            error_log("Calendar: Failed to get service data - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get pet data
     * @param int $petId
     * @return array|false
     */
    private function getPetData($petId) {
        try {
            $petsModel = new PetsModel();
            return $petsModel->find($petId);
        } catch (Exception $e) {
            error_log("Calendar: Failed to get pet data - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get time slot data
     * @param int $timeSlotId
     * @return array|false
     */
    private function getTimeSlotData($timeSlotId) {
        try {
            $timeSlotsModel = new TimeSlotsModel();
            return $timeSlotsModel->find($timeSlotId);
        } catch (Exception $e) {
            error_log("Calendar: Failed to get time slot data - " . $e->getMessage());
            return false;
        }
    }
}
?>