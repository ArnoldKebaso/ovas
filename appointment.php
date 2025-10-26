<?php
require_once 'initialize.php';
require_once 'inc/sess_auth.php';

// Get service ID from URL if provided
$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : null;

// Generate CSRF token for this session
$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_settings->info('title') ?? 'OVAS' ?> - Book Appointment</title>
    <?php require_once('inc/header.php') ?>
    <style>
        .wizard-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .wizard-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .wizard-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #dee2e6;
            z-index: 1;
        }
        .wizard-step {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            font-weight: bold;
            color: #6c757d;
        }
        .wizard-step.active {
            border-color: #007bff;
            background: #007bff;
            color: white;
        }
        .wizard-step.completed {
            border-color: #28a745;
            background: #28a745;
            color: white;
        }
        .wizard-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            min-height: 400px;
        }
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .service-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .service-card:hover, .service-card.selected {
            border-color: #007bff;
            background: #f8f9fa;
        }
        .time-slot {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
            display: inline-block;
            transition: all 0.3s;
        }
        .time-slot:hover, .time-slot.selected {
            border-color: #007bff;
            background: #007bff;
            color: white;
        }
        .time-slot.unavailable {
            background: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            border-color: #dee2e6;
        }
        .wizard-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .loading-spinner {
            text-align: center;
            padding: 40px;
        }
        .alert-banner {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="layout-top-nav layout-fixed">
    <div class="wrapper">
        <?php require_once('inc/topBarNav.php') ?>
        
        <div class="content-wrapper">
            <div class="content py-5">
                <div class="container-fluid">
                    <div class="wizard-container">
                        <!-- Progress Steps -->
                        <div class="wizard-steps">
                            <div class="wizard-step active" id="step-indicator-1">1</div>
                            <div class="wizard-step" id="step-indicator-2">2</div>
                            <div class="wizard-step" id="step-indicator-3">3</div>
                            <div class="wizard-step" id="step-indicator-4">4</div>
                        </div>
                        
                        <div class="wizard-content">
                            <!-- Step 1: Service Selection -->
                            <div class="step-content active" id="step-1">
                                <h3 class="mb-4">Select a Service</h3>
                                <div id="services-list">
                                    <div class="loading-spinner">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading services...</span>
                                        </div>
                                        <p class="mt-2">Loading available services...</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 2: Authentication Check -->
                            <div class="step-content" id="step-2">
                                <h3 class="mb-4">Account Verification</h3>
                                <div id="auth-check">
                                    <div class="loading-spinner">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Checking authentication...</span>
                                        </div>
                                        <p class="mt-2">Verifying your account...</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 3: Date & Time Selection -->
                            <div class="step-content" id="step-3">
                                <h3 class="mb-4">Select Date & Time</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="appointment-date" class="form-label">Select Date:</label>
                                        <input type="date" id="appointment-date" class="form-control" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+90 days')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <div id="service-summary" class="alert alert-info" style="display: none;">
                                            <!-- Service details will be populated here -->
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h5>Available Time Slots:</h5>
                                    <div id="time-slots-container">
                                        <p class="text-muted">Please select a date to view available time slots.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 4: Payment & Confirmation -->
                            <div class="step-content" id="step-4">
                                <h3 class="mb-4">Payment & Confirmation</h3>
                                <div id="booking-summary">
                                    <!-- Booking summary will be populated here -->
                                </div>
                                <div class="mt-4">
                                    <div class="alert alert-info">
                                        <h5>Payment Information</h5>
                                        <p>You will be charged <strong id="final-amount">KSh 0.00</strong> for this appointment.</p>
                                        <p>Payment will be processed via M-Pesa STK Push to your registered phone number.</p>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="terms-accept" required>
                                        <label class="form-check-label" for="terms-accept">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Navigation Buttons -->
                            <div class="wizard-navigation">
                                <button type="button" class="btn btn-secondary" id="prev-btn" style="display: none;">Previous</button>
                                <button type="button" class="btn btn-primary" id="next-btn">Next</button>
                                <button type="button" class="btn btn-success" id="confirm-btn" style="display: none;">Confirm & Pay</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php require_once('inc/footer.php') ?>
    </div>
    
    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Appointment Booking Terms</h6>
                    <ul>
                        <li>Appointments must be cancelled at least 24 hours in advance</li>
                        <li>Late cancellations may incur a fee</li>
                        <li>Payment is required to confirm your appointment</li>
                        <li>Rescheduling is subject to availability</li>
                        <li>Please arrive 15 minutes before your scheduled time</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Appointment Booking Wizard
        class AppointmentWizard {
            constructor() {
                this.currentStep = 1;
                this.maxSteps = 4;
                this.selectedService = null;
                this.selectedDate = null;
                this.selectedTimeSlot = null;
                this.csrfToken = '<?= $csrfToken ?>';
                this.preselectedService = <?= $serviceId ? $serviceId : 'null' ?>;
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.loadServices();
            }
            
            setupEventListeners() {
                // Navigation buttons
                $('#next-btn').on('click', () => this.nextStep());
                $('#prev-btn').on('click', () => this.prevStep());
                $('#confirm-btn').on('click', () => this.confirmBooking());
                
                // Date selection
                $('#appointment-date').on('change', (e) => {
                    this.selectedDate = e.target.value;
                    this.loadTimeSlots();
                });
                
                // Service selection delegation
                $(document).on('click', '.service-card', (e) => {
                    $('.service-card').removeClass('selected');
                    $(e.currentTarget).addClass('selected');
                    this.selectedService = parseInt($(e.currentTarget).data('service-id'));
                });
                
                // Time slot selection delegation
                $(document).on('click', '.time-slot:not(.unavailable)', (e) => {
                    $('.time-slot').removeClass('selected');
                    $(e.currentTarget).addClass('selected');
                    this.selectedTimeSlot = parseInt($(e.currentTarget).data('slot-id'));
                });
            }
            
            async loadServices() {
                try {
                    const response = await fetch('services.php');
                    const html = await response.text();
                    
                    // Extract services data from the page
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const serviceElements = tempDiv.querySelectorAll('.card');
                    
                    let servicesHtml = '';
                    serviceElements.forEach((element, index) => {
                        // Extract service info from the HTML structure
                        const title = element.querySelector('.card-title')?.textContent?.trim() || 'Service';
                        const description = element.querySelector('.card-text')?.textContent?.trim() || '';
                        
                        servicesHtml += `
                            <div class="service-card ${this.preselectedService === (index + 1) ? 'selected' : ''}" data-service-id="${index + 1}">
                                <h5>${title}</h5>
                                <p class="text-muted">${description}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">Available</span>
                                    <small class="text-muted">Duration: 30 minutes</small>
                                </div>
                            </div>
                        `;
                        
                        if (this.preselectedService === (index + 1)) {
                            this.selectedService = index + 1;
                        }
                    });
                    
                    $('#services-list').html(servicesHtml);
                    
                } catch (error) {
                    console.error('Error loading services:', error);
                    $('#services-list').html(`
                        <div class="alert alert-danger">
                            <h5>Error Loading Services</h5>
                            <p>Unable to load available services. Please refresh the page or try again later.</p>
                        </div>
                    `);
                }
            }
            
            async checkAuthentication() {
                try {
                    const response = await fetch('auth_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=check&csrf_token=${this.csrfToken}`
                    });
                    
                    const result = await response.json();
                    
                    if (result.success && result.authenticated) {
                        $('#auth-check').html(`
                            <div class="alert alert-success">
                                <h5>Welcome back, ${result.user.firstname}!</h5>
                                <p>Your account is verified and ready for booking.</p>
                            </div>
                        `);
                        return true;
                    } else {
                        $('#auth-check').html(`
                            <div class="alert alert-warning">
                                <h5>Login Required</h5>
                                <p>You need to be logged in to book an appointment.</p>
                                <button type="button" class="btn btn-primary" onclick="$('#loginModal').modal('show')">Login</button>
                                <button type="button" class="btn btn-outline-primary" onclick="$('#registerModal').modal('show')">Register</button>
                            </div>
                        `);
                        return false;
                    }
                } catch (error) {
                    console.error('Auth check error:', error);
                    $('#auth-check').html(`
                        <div class="alert alert-danger">
                            <h5>Authentication Error</h5>
                            <p>Unable to verify your login status. Please try again.</p>
                        </div>
                    `);
                    return false;
                }
            }
            
            async loadTimeSlots() {
                if (!this.selectedDate || !this.selectedService) return;
                
                const timeSlotsContainer = document.getElementById('time-slots-container');
                ovasUI.showLoading(timeSlotsContainer, 'Loading available time slots...');
                
                try {
                    const result = await ovasAPI.loadAvailability(this.selectedService, this.selectedDate);
                    
                    if (result.success) {
                        let slotsHtml = '';
                        result.data.slots.forEach(slot => {
                            const availableClass = slot.is_available ? '' : 'unavailable';
                            const availableText = slot.is_available ? 
                                `${slot.appointments_available} available` : 
                                'Fully booked';
                            
                            slotsHtml += `
                                <div class="time-slot ${availableClass}" data-slot-id="${slot.id}" ${!slot.is_available ? 'title="This time slot is fully booked"' : ''}>
                                    <div><strong>${slot.display_time}</strong></div>
                                    <small>${availableText}</small>
                                </div>
                            `;
                        });
                        
                        $('#time-slots-container').html(slotsHtml || '<p class="text-muted">No time slots available for this date.</p>');
                    } else {
                        $('#time-slots-container').html(`
                            <div class="alert alert-warning">
                                <p>${result.message}</p>
                            </div>
                        `);
                    }
                } catch (error) {
                    console.error('Error loading time slots:', error);
                    timeSlotsContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <p>Error loading time slots. Please try again.</p>
                        </div>
                    `;
                } finally {
                    ovasUI.hideLoading(timeSlotsContainer);
                }
            }
            
            async loadServiceDetails() {
                if (!this.selectedService) return;
                
                try {
                    const result = await ovasAPI.getServiceDetails(this.selectedService);
                    
                    if (result.success) {
                        const service = result.data;
                        document.getElementById('service-summary').innerHTML = `
                            <h6>${service.name}</h6>
                            <p><strong>Fee:</strong> ${ovasUI.formatCurrency(service.fee)}</p>
                            <p><strong>Duration:</strong> ${service.duration_minutes} minutes</p>
                        `;
                        
                        document.getElementById('final-amount').textContent = ovasUI.formatCurrency(service.fee);
                    }
                    }
                } catch (error) {
                    console.error('Error loading service details:', error);
                }
            }
            
            nextStep() {
                if (this.currentStep === 1 && !this.selectedService) {
                    alert('Please select a service first.');
                    return;
                }
                
                if (this.currentStep === 3 && (!this.selectedDate || !this.selectedTimeSlot)) {
                    alert('Please select both date and time slot.');
                    return;
                }
                
                if (this.currentStep < this.maxSteps) {
                    this.currentStep++;
                    this.updateWizard();
                    
                    // Load content for the new step
                    if (this.currentStep === 2) {
                        this.checkAuthentication();
                    } else if (this.currentStep === 3) {
                        this.loadServiceDetails();
                    } else if (this.currentStep === 4) {
                        this.showBookingSummary();
                    }
                }
            }
            
            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    this.updateWizard();
                }
            }
            
            updateWizard() {
                // Update step indicators
                for (let i = 1; i <= this.maxSteps; i++) {
                    const indicator = $(`#step-indicator-${i}`);
                    const content = $(`#step-${i}`);
                    
                    if (i < this.currentStep) {
                        indicator.removeClass('active').addClass('completed');
                        content.removeClass('active');
                    } else if (i === this.currentStep) {
                        indicator.removeClass('completed').addClass('active');
                        content.addClass('active');
                    } else {
                        indicator.removeClass('active completed');
                        content.removeClass('active');
                    }
                }
                
                // Update navigation buttons
                $('#prev-btn').toggle(this.currentStep > 1);
                $('#next-btn').toggle(this.currentStep < this.maxSteps);
                $('#confirm-btn').toggle(this.currentStep === this.maxSteps);
            }
            
            showBookingSummary() {
                // Create booking summary
                const summary = `
                    <div class="card">
                        <div class="card-header">
                            <h5>Booking Summary</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Service:</strong> Service ${this.selectedService}</p>
                            <p><strong>Date:</strong> ${new Date(this.selectedDate).toLocaleDateString()}</p>
                            <p><strong>Time:</strong> Time Slot ${this.selectedTimeSlot}</p>
                            <p><strong>Duration:</strong> 30 minutes</p>
                            <hr>
                            <p class="h5"><strong>Total Fee:</strong> <span id="summary-amount">Loading...</span></p>
                        </div>
                    </div>
                `;
                
                $('#booking-summary').html(summary);
                this.loadServiceDetails(); // This will update the amount
            }
            
            async confirmBooking() {
                if (!$('#terms-accept').is(':checked')) {
                    alert('Please accept the terms and conditions.');
                    return;
                }
                
                try {
                    // Show loading state
                    $('#confirm-btn').prop('disabled', true).text('Processing...');
                    
                    // Submit booking request
                    const response = await fetch('submit_payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `service_id=${this.selectedService}&date=${this.selectedDate}&time_slot_id=${this.selectedTimeSlot}&csrf_token=${this.csrfToken}`
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Redirect to success page or show success message
                        window.location.href = `success_msg.php?appointment_code=${result.appointment_code}`;
                    } else {
                        alert(result.message || 'Booking failed. Please try again.');
                        $('#confirm-btn').prop('disabled', false).text('Confirm & Pay');
                    }
                } catch (error) {
                    console.error('Booking error:', error);
                    alert('An error occurred while processing your booking. Please try again.');
                    $('#confirm-btn').prop('disabled', false).text('Confirm & Pay');
                }
            }
        }
        
        // Initialize wizard when page loads
        $(document).ready(function() {
            new AppointmentWizard();
        });
    </script>
    
    <!-- Include OVAS API Client -->
    <script src="<?php echo base_url ?>assets/js/ovas-api.js"></script>
</body>
</html>