/**
 * OVAS Frontend API Client
 * Modern JavaScript API interactions for dynamic content loading
 */

class OvasAPI {
    constructor() {
        this.baseUrl = window.location.origin + '/ovas';
        this.csrfToken = this.getCSRFToken();
    }

    /**
     * Get CSRF token from meta tag or form
     */
    getCSRFToken() {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            return metaToken.getAttribute('content');
        }
        
        const hiddenToken = document.querySelector('input[name="csrf_token"]');
        if (hiddenToken) {
            return hiddenToken.value;
        }
        
        // Generate new token if none found
        return this.generateCSRFToken();
    }

    /**
     * Generate new CSRF token
     */
    async generateCSRFToken() {
        try {
            const response = await fetch(`${this.baseUrl}/api/csrf-token.php`);
            const data = await response.json();
            return data.token;
        } catch (error) {
            console.error('Failed to generate CSRF token:', error);
            return '';
        }
    }

    /**
     * Make authenticated API request
     */
    async makeRequest(url, options = {}) {
        const defaultOptions = {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            }
        };

        // Add CSRF token to form data for POST requests
        if (options.method === 'POST' && options.body instanceof FormData) {
            options.body.append('csrf_token', this.csrfToken);
        }

        const mergedOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, mergedOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            
            return await response.text();
        } catch (error) {
            console.error('API Request failed:', error);
            throw error;
        }
    }

    /**
     * Load available time slots for appointment booking
     */
    async loadAvailability(serviceId, date) {
        const formData = new FormData();
        formData.append('service_id', serviceId);
        formData.append('date', date);

        return this.makeRequest(`${this.baseUrl}/check_availability.php`, {
            method: 'POST',
            body: formData
        });
    }

    /**
     * Get service details including pricing and duration
     */
    async getServiceDetails(serviceId) {
        return this.makeRequest(`${this.baseUrl}/get_service_details.php?service_id=${encodeURIComponent(serviceId)}`);
    }

    /**
     * Submit appointment booking
     */
    async submitAppointment(appointmentData) {
        const formData = new FormData();
        
        Object.keys(appointmentData).forEach(key => {
            formData.append(key, appointmentData[key]);
        });

        return this.makeRequest(`${this.baseUrl}/submit_appointment.php`, {
            method: 'POST',
            body: formData
        });
    }

    /**
     * Reschedule appointment
     */
    async rescheduleAppointment(appointmentId, newDate, newTimeSlot) {
        const formData = new FormData();
        formData.append('appointment_id', appointmentId);
        formData.append('new_date', newDate);
        formData.append('new_time_slot', newTimeSlot);

        return this.makeRequest(`${this.baseUrl}/api/appointment_management.php?action=reschedule`, {
            method: 'POST',
            body: formData
        });
    }

    /**
     * Cancel appointment
     */
    async cancelAppointment(appointmentId, reason = '') {
        const formData = new FormData();
        formData.append('appointment_id', appointmentId);
        formData.append('reason', reason);

        return this.makeRequest(`${this.baseUrl}/api/appointment_management.php?action=cancel`, {
            method: 'POST',
            body: formData
        });
    }

    /**
     * Get user appointments
     */
    async getUserAppointments(status = '', limit = 10) {
        const params = new URLSearchParams();
        if (status) params.append('status', status);
        if (limit) params.append('limit', limit);

        return this.makeRequest(`${this.baseUrl}/api/appointments.php?${params.toString()}`);
    }

    /**
     * Get user pets
     */
    async getUserPets() {
        return this.makeRequest(`${this.baseUrl}/api/pets.php`);
    }

    /**
     * Add new pet
     */
    async addPet(petData) {
        const formData = new FormData();
        
        Object.keys(petData).forEach(key => {
            formData.append(key, petData[key]);
        });

        return this.makeRequest(`${this.baseUrl}/api/pets.php`, {
            method: 'POST',
            body: formData
        });
    }

    /**
     * Update pet information
     */
    async updatePet(petId, petData) {
        const formData = new FormData();
        formData.append('pet_id', petId);
        
        Object.keys(petData).forEach(key => {
            formData.append(key, petData[key]);
        });

        return this.makeRequest(`${this.baseUrl}/api/pets.php?action=update`, {
            method: 'POST',
            body: formData
        });
    }

    /**
     * Delete pet
     */
    async deletePet(petId) {
        const formData = new FormData();
        formData.append('pet_id', petId);

        return this.makeRequest(`${this.baseUrl}/api/pets.php?action=delete`, {
            method: 'POST',
            body: formData
        });
    }

    /**
     * Get dashboard statistics
     */
    async getDashboardStats() {
        return this.makeRequest(`${this.baseUrl}/api/dashboard-stats.php`);
    }

    /**
     * Get services by category
     */
    async getServicesByCategory(categoryId) {
        return this.makeRequest(`${this.baseUrl}/api/services.php?category_id=${encodeURIComponent(categoryId)}`);
    }

    /**
     * Contact form submission
     */
    async submitContactForm(contactData) {
        const formData = new FormData();
        
        Object.keys(contactData).forEach(key => {
            formData.append(key, contactData[key]);
        });

        return this.makeRequest(`${this.baseUrl}/contact_submit.php`, {
            method: 'POST',
            body: formData
        });
    }
}

/**
 * UI Helper Functions
 */
class OvasUI {
    constructor(api) {
        this.api = api;
        this.loadingStates = new Map();
    }

    /**
     * Show loading state for element
     */
    showLoading(element, text = 'Loading...') {
        const originalContent = element.innerHTML;
        this.loadingStates.set(element, originalContent);
        
        element.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <span>${text}</span>
            </div>
        `;
        element.disabled = true;
    }

    /**
     * Hide loading state for element
     */
    hideLoading(element) {
        const originalContent = this.loadingStates.get(element);
        if (originalContent) {
            element.innerHTML = originalContent;
            this.loadingStates.delete(element);
        }
        element.disabled = false;
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" id="${toastId}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        // Remove toast element after it hides
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    /**
     * Populate time slots dropdown
     */
    populateTimeSlots(container, timeSlots) {
        container.innerHTML = '';
        
        if (!timeSlots || timeSlots.length === 0) {
            container.innerHTML = '<option value="">No available time slots</option>';
            return;
        }

        container.innerHTML = '<option value="">Select a time slot</option>';
        
        timeSlots.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot.id;
            option.textContent = `${slot.start_time} - ${slot.end_time}`;
            option.disabled = !slot.available;
            
            if (!slot.available) {
                option.textContent += ' (Booked)';
            }
            
            container.appendChild(option);
        });
    }

    /**
     * Format currency
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES'
        }).format(amount);
    }

    /**
     * Format date for display
     */
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-KE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    /**
     * Format time for display
     */
    formatTime(timeString) {
        return new Date(`2000-01-01 ${timeString}`).toLocaleTimeString('en-KE', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

/**
 * Form Validation Helper
 */
class FormValidator {
    constructor() {
        this.rules = {};
    }

    /**
     * Add validation rule
     */
    addRule(field, validator, message) {
        if (!this.rules[field]) {
            this.rules[field] = [];
        }
        this.rules[field].push({ validator, message });
    }

    /**
     * Validate form
     */
    validate(formData) {
        const errors = {};

        Object.keys(this.rules).forEach(field => {
            const value = formData.get(field) || formData[field];
            
            this.rules[field].forEach(rule => {
                if (!rule.validator(value)) {
                    if (!errors[field]) {
                        errors[field] = [];
                    }
                    errors[field].push(rule.message);
                }
            });
        });

        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }

    /**
     * Common validators
     */
    static validators = {
        required: (value) => value && value.toString().trim() !== '',
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        phone: (value) => /^[\+]?[0-9\-\(\)\s]+$/.test(value) && value.replace(/\D/g, '').length >= 10,
        minLength: (min) => (value) => value && value.length >= min,
        maxLength: (max) => (value) => !value || value.length <= max
    };
}

// Initialize global instances
const ovasAPI = new OvasAPI();
const ovasUI = new OvasUI(ovasAPI);

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { OvasAPI, OvasUI, FormValidator };
}