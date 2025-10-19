<!-- ============================================ -->
<!-- CONTACT US PAGE -->
<!-- ============================================ -->
<?php include 'sendemail.php'; ?>
</div><!-- Close index.php container to allow full-width sections -->

<!-- Hero Section -->
<section class="py-5 bg-gradient" style="background: linear-gradient(135deg, #2563eb 0%, #10b981 100%); min-height: 40vh;">
    <div class="container">
        <div class="row align-items-center" style="min-height: 30vh;">
            <div class="col-lg-8 mx-auto text-center text-white" data-aos="fade-up">
                <h1 class="display-3 fw-bold mb-4">Contact <?= $_settings->info('name') ?></h1>
                <p class="lead fs-4">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </div>
</section>

<!--Alert messages start-->
<?php if(isset($alert) && !empty($alert)): ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <?php echo $alert; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<!--Alert messages end-->

<!-- Contact Information Cards -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row g-4">
            <!-- Phone Card -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-phone fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Call Us</h5>
                        <p class="text-muted mb-3">Speak directly with our team</p>
                        <a href="tel:<?= $_settings->info('contact') ?>" class="btn btn-outline-primary btn-sm">
                            <?= $_settings->info('contact') ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Email Card -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-envelope fa-2x text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Email Us</h5>
                        <p class="text-muted mb-3">Send us a message anytime</p>
                        <a href="mailto:<?= $_settings->info('email') ?>" class="btn btn-outline-success btn-sm">
                            <?= $_settings->info('email') ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Location Card -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-map-marker-alt fa-2x text-info"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Visit Us</h5>
                        <p class="text-muted mb-3">Come to our clinic</p>
                        <p class="small text-muted mb-0"><?= $_settings->info('address') ?></p>
                    </div>
                </div>
            </div>

            <!-- Hours Card -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Business Hours</h5>
                        <p class="text-muted mb-3">We're here to help</p>
                        <p class="small text-muted mb-1">Mon - Sat: 8:00 AM - 6:00 PM</p>
                        <p class="small text-muted mb-0">Sunday: Emergency Only</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold text-primary mb-3">Send Us a Message</h2>
                    <p class="lead text-muted">
                        Have a question or need to schedule an appointment? Fill out the form below and we'll get back to you shortly.
                    </p>
                </div>
                
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <form class="contact needs-validation" action="" method="post" novalidate>
                            <div class="row g-4">
                                <!-- Name Field -->
                                <div class="col-md-6">
                                    <label for="contact_name" class="form-label fw-semibold">Full Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control form-control-lg border-start-0 ps-0" 
                                               id="contact_name" 
                                               name="name" 
                                               placeholder="Your full name"
                                               required>
                                        <div class="invalid-feedback">Please provide your name.</div>
                                    </div>
                                </div>

                                <!-- Email Field -->
                                <div class="col-md-6">
                                    <label for="contact_email" class="form-label fw-semibold">Email Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control form-control-lg border-start-0 ps-0" 
                                               id="contact_email" 
                                               name="email" 
                                               placeholder="your.email@example.com"
                                               required>
                                        <div class="invalid-feedback">Please provide a valid email address.</div>
                                    </div>
                                </div>

                                <!-- Phone Field -->
                                <div class="col-md-6">
                                    <label for="contact_phone" class="form-label fw-semibold">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input type="tel" 
                                               class="form-control form-control-lg border-start-0 ps-0" 
                                               id="contact_phone" 
                                               name="phone" 
                                               placeholder="Your phone number">
                                    </div>
                                </div>

                                <!-- Subject Field -->
                                <div class="col-md-6">
                                    <label for="contact_subject" class="form-label fw-semibold">Subject *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-tag text-muted"></i>
                                        </span>
                                        <select class="form-select form-select-lg border-start-0 ps-0" 
                                                id="contact_subject" 
                                                name="subject" 
                                                required>
                                            <option value="">Choose a subject</option>
                                            <option value="General Inquiry">General Inquiry</option>
                                            <option value="Appointment Request">Appointment Request</option>
                                            <option value="Emergency">Emergency</option>
                                            <option value="Billing Question">Billing Question</option>
                                            <option value="Feedback">Feedback</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a subject.</div>
                                    </div>
                                </div>

                                <!-- Message Field -->
                                <div class="col-12">
                                    <label for="contact_message" class="form-label fw-semibold">Your Message *</label>
                                    <textarea class="form-control form-control-lg" 
                                              id="contact_message" 
                                              name="message" 
                                              rows="6" 
                                              placeholder="Please describe your inquiry or let us know how we can help you..."
                                              required></textarea>
                                    <div class="invalid-feedback">Please enter your message.</div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="d-flex gap-3 justify-content-center">
                                        <button type="submit" name="submit" class="btn btn-primary btn-lg rounded-pill px-5">
                                            <i class="fas fa-paper-plane me-2"></i> Send Message
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                                            <i class="fas fa-redo me-2"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Emergency Contact Section -->
<section class="py-5 bg-danger">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center text-white" data-aos="fade-up">
                <h2 class="display-6 fw-bold mb-3">Emergency? Call Now!</h2>
                <p class="lead mb-4">
                    For urgent veterinary emergencies, don't wait. Call us immediately for prompt assistance.
                </p>
                <a href="tel:<?= $_settings->info('contact') ?>" class="btn btn-light btn-lg rounded-pill px-5">
                    <i class="fas fa-phone me-2"></i> Emergency: <?= $_settings->info('contact') ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="display-5 fw-bold text-primary mb-3">Frequently Asked Questions</h2>
                    <p class="lead text-muted">Quick answers to common questions</p>
                </div>
                
                <div class="accordion accordion-flush" id="faqAccordion" data-aos="fade-up" data-aos-delay="100">
                    <!-- FAQ 1 -->
                    <div class="accordion-item border-0 mb-3 rounded shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What are your business hours?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We're open Monday through Saturday from 8:00 AM to 6:00 PM. On Sundays, we provide emergency services only. 
                                For after-hours emergencies, please call our main number and follow the prompts for emergency care.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 2 -->
                    <div class="accordion-item border-0 mb-3 rounded shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do I schedule an appointment?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can schedule an appointment by calling us, using our online booking form, or visiting our clinic in person. 
                                We recommend booking in advance, especially for routine checkups and wellness visits.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 3 -->
                    <div class="accordion-item border-0 mb-3 rounded shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Do you handle emergencies?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, we provide 24/7 emergency veterinary services. For urgent situations, call our main number immediately. 
                                Our emergency team is trained to handle critical cases and provide immediate care when your pet needs it most.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 4 -->
                    <div class="accordion-item border-0 rounded shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                What should I bring to my pet's first visit?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Please bring any previous medical records, vaccination history, current medications, and a list of questions you may have. 
                                If your pet is new to our clinic, allow extra time for paperwork and to discuss your pet's health history with our team.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Prevent form resubmission on refresh
if(window.history.replaceState){
    window.history.replaceState(null, null, window.location.href);
}
</script>

<!-- Reopen container for index.php structure -->
<div class="container d-none">
</div>