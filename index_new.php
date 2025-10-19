<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="en" data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="100">
<?php require_once('inc/header.php'); ?>
<body class="layout-top-nav layout-fixed">
    <div class="wrapper">
        <?php require_once('inc/topBarNav.php'); ?>
        
        <!-- SECTION: Home (Hero) -->
        <section id="home" class="section-pad">
            <div class="hero-swiper swiper">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide">
                        <picture>
                            <source srcset="/uploads/assets/hero_01.webp" type="image/webp">
                            <img src="/uploads/assets/hero_01.jpg" alt="Veterinarian with happy dog at clinic" loading="eager" width="1920" height="900">
                        </picture>
                        <div class="hero-overlay"></div>
                        <div class="hero-content">
                            <div class="container">
                                <div class="row align-items-center min-vh-70">
                                    <div class="col-lg-8 col-xl-7">
                                        <h1 class="display-3 fw-bold text-white mb-4" data-aos="fade-up">
                                            Your Pet's Health, <br><span class="text-accent">Our Priority</span>
                                        </h1>
                                        <p class="lead text-white mb-5" data-aos="fade-up" data-aos-delay="100">
                                            Expert veterinary care with compassion. Book appointments online in seconds and give your beloved pets the care they deserve.
                                        </p>
                                        <div class="d-flex gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="200">
                                            <a href="#appointment" class="btn btn-primary btn-lg px-5 py-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                                                Book Appointment
                                            </a>
                                            <a href="#services" class="btn btn-outline-light btn-lg px-5 py-3">
                                                Our Services
                                            </a>
                                        </div>
                                        
                                        <!-- Stats Counter -->
                                        <div class="row mt-5 g-4">
                                            <div class="col-4">
                                                <div class="text-white">
                                                    <h3 class="display-5 fw-bold mb-0 counter" data-target="5000">0</h3>
                                                    <p class="mb-0 opacity-75">Happy Pets</p>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-white">
                                                    <h3 class="display-5 fw-bold mb-0 counter" data-target="15">0</h3>
                                                    <p class="mb-0 opacity-75">Years Experience</p>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-white">
                                                    <h3 class="display-5 fw-bold mb-0">24/7</h3>
                                                    <p class="mb-0 opacity-75">Support</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Slide 2 -->
                    <div class="swiper-slide">
                        <picture>
                            <source srcset="/uploads/assets/hero_02.webp" type="image/webp">
                            <img src="/uploads/assets/hero_02.jpg" alt="Cat checkup at veterinary clinic" loading="lazy" width="1920" height="900">
                        </picture>
                        <div class="hero-overlay"></div>
                        <div class="hero-content">
                            <div class="container">
                                <div class="row align-items-center min-vh-70">
                                    <div class="col-lg-8 col-xl-7">
                                        <h1 class="display-3 fw-bold text-white mb-4">
                                            Comprehensive Pet Care Services
                                        </h1>
                                        <p class="lead text-white mb-5">
                                            From routine checkups to emergency care, we're here for your furry family members.
                                        </p>
                                        <a href="#services" class="btn btn-primary btn-lg px-5 py-3">
                                            Explore Services
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Slide 3 -->
                    <div class="swiper-slide">
                        <picture>
                            <source srcset="/uploads/assets/hero_03.webp" type="image/webp">
                            <img src="/uploads/assets/hero_03.jpg" alt="Vet clinic reception with pet owner" loading="lazy" width="1920" height="900">
                        </picture>
                        <div class="hero-overlay"></div>
                        <div class="hero-content">
                            <div class="container">
                                <div class="row align-items-center min-vh-70">
                                    <div class="col-lg-8 col-xl-7">
                                        <h1 class="display-3 fw-bold text-white mb-4">
                                            Modern Facility, Caring Staff
                                        </h1>
                                        <p class="lead text-white mb-5">
                                            State-of-the-art equipment and experienced veterinarians dedicated to your pet's wellbeing.
                                        </p>
                                        <a href="#about" class="btn btn-primary btn-lg px-5 py-3">
                                            About Us
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
                
                <!-- Decorative Accent -->
                <img src="/uploads/assets/hero_accent.svg" alt="" class="hero-accent" role="presentation" aria-hidden="true">
            </div>
        </section>
        
        <!-- SECTION: Services -->
        <section id="services" class="section-pad bg-light">
            <div class="container">
                <!-- Section Header -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 class="heading-xl mb-3" data-aos="fade-up">Our Services</h2>
                        <p class="lead muted" data-aos="fade-up" data-aos-delay="100">
                            Comprehensive veterinary care for all your pets. From preventive care to specialized treatments.
                        </p>
                    </div>
                </div>
                
                <!-- Services Grid -->
                <div class="row g-4">
                    <!-- Service Card: Vaccination -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Vaccination</h5>
                                <p class="card-text small muted">Protect your pets from diseases with our comprehensive vaccination programs.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Pet vaccination service" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Card: Dental Care -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c-4.418 0-8 3.358-8 7.5S7.582 17 12 17s8-3.358 8-7.5S16.418 2 12 2z"/><path d="M8 17v3a3 3 0 0 0 3 3h2a3 3 0 0 0 3-3v-3"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Dental Care</h5>
                                <p class="card-text small muted">Professional teeth cleaning and oral health care for your pets.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Pet dental care" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Card: Grooming -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Grooming</h5>
                                <p class="card-text small muted">Professional grooming services to keep your pets looking and feeling great.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Pet grooming service" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Card: Surgery -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Surgery</h5>
                                <p class="card-text small muted">Advanced surgical procedures with experienced veterinary surgeons.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Veterinary surgery" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Card: Diagnostics -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Diagnostics</h5>
                                <p class="card-text small muted">Advanced diagnostic tools for accurate health assessments.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Pet diagnostics" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Card: Emergency Care -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="500">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Emergency Care</h5>
                                <p class="card-text small muted">24/7 emergency services for urgent pet health needs.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Emergency pet care" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Card: Deworming -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="600">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2v20"/><path d="M2 12h20"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Deworming</h5>
                                <p class="card-text small muted">Regular deworming programs to keep your pets healthy.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Pet deworming" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Card: Boarding -->
                    <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="700">
                        <div class="card card-hover h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="service-icon mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                                <h5 class="card-title mb-2">Pet Boarding</h5>
                                <p class="card-text small muted">Safe and comfortable boarding facilities for your pets.</p>
                                <img src="/uploads/assets/svc_photo_generic.webp" alt="Pet boarding facility" class="img-fluid rounded mt-3" width="200" height="200" loading="lazy">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- View More Link -->
                <div class="text-center mt-5" data-aos="fade-up">
                    <a href="services.php" class="btn btn-outline-primary btn-lg">
                        View All Services
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ms-2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>
        
        <!-- SECTION: Appointment -->
        <section id="appointment" class="section-pad">
            <div class="container">
                <div class="row align-items-center g-5">
                    <!-- Left Column: Illustration -->
                    <div class="col-lg-5" data-aos="fade-right">
                        <img src="/uploads/assets/appointment_header.svg" alt="Book appointment illustration" class="img-fluid" width="500" height="400" loading="lazy">
                    </div>
                    
                    <!-- Right Column: Form -->
                    <div class="col-lg-7" data-aos="fade-left">
                        <h2 class="heading-xl mb-4">Book an Appointment</h2>
                        <p class="lead muted mb-4">Schedule a visit for your pet. Fill in the details below and we'll confirm your appointment.</p>
                        
                        <form id="appointment-form" action="add_appointment.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            
                            <div class="row g-3">
                                <!-- Owner Name -->
                                <div class="col-md-6">
                                    <label for="owner_name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                           value="<?php echo $_SESSION['userdata']['name'] ?? ''; ?>" 
                                           <?php echo isset($_SESSION['userdata']['name']) ? 'readonly' : ''; ?> 
                                           required>
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo $_SESSION['userdata']['email'] ?? ''; ?>" 
                                           <?php echo isset($_SESSION['userdata']['email']) ? 'readonly' : ''; ?> 
                                           required>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                                
                                <!-- Contact -->
                                <div class="col-md-6">
                                    <label for="contact" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="contact" name="contact" 
                                           value="<?php echo $_SESSION['userdata']['phone'] ?? ''; ?>" 
                                           <?php echo isset($_SESSION['userdata']['phone']) ? 'readonly' : ''; ?> 
                                           required>
                                    <div class="invalid-feedback">Please enter your phone number.</div>
                                </div>
                                
                                <!-- Address -->
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" 
                                           value="<?php echo $_SESSION['userdata']['address'] ?? ''; ?>" 
                                           required>
                                    <div class="invalid-feedback">Please enter your address.</div>
                                </div>
                                
                                <!-- Pet Name -->
                                <div class="col-md-6">
                                    <label for="pet_name" class="form-label">Pet Name</label>
                                    <input type="text" class="form-control" id="pet_name" name="breed" placeholder="e.g., Max" required>
                                    <div class="invalid-feedback">Please enter your pet's name.</div>
                                </div>
                                
                                <!-- Pet Type -->
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Pet Type</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>Choose...</option>
                                        <?php 
                                        $categories = $conn->query("SELECT * FROM category_list WHERE delete_flag = 0 ORDER BY name ASC");
                                        while($row = $categories->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $row['id'] ?>"><?= ucwords($row['name']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select pet type.</div>
                                </div>
                                
                                <!-- Age -->
                                <div class="col-md-6">
                                    <label for="age" class="form-label">Pet Age</label>
                                    <input type="text" class="form-control" id="age" name="age" placeholder="e.g., 2 years" required>
                                    <div class="invalid-feedback">Please enter pet's age.</div>
                                </div>
                                
                                <!-- Service -->
                                <div class="col-md-6">
                                    <label for="service_id" class="form-label">Service Needed</label>
                                    <select class="form-select" id="service_id" name="service_ids" required>
                                        <option value="" selected disabled>Choose...</option>
                                        <?php 
                                        $services = $conn->query("SELECT * FROM service_list WHERE delete_flag = 0 ORDER BY name ASC");
                                        while($row = $services->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $row['id'] ?>"><?= ucwords($row['name']) ?> - ₱<?= number_format($row['fee'], 2) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a service.</div>
                                </div>
                                
                                <!-- Date -->
                                <div class="col-md-6">
                                    <label for="schedule" class="form-label">Preferred Date</label>
                                    <input type="date" class="form-control" id="schedule" name="schedule" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                    <div class="invalid-feedback">Please select a date.</div>
                                </div>
                                
                                <!-- Time Slot -->
                                <div class="col-md-6">
                                    <label for="time_slot" class="form-label">Preferred Time</label>
                                    <select class="form-select" id="time_slot" name="time_slot">
                                        <option value="09:00 AM">09:00 AM</option>
                                        <option value="10:00 AM">10:00 AM</option>
                                        <option value="11:00 AM">11:00 AM</option>
                                        <option value="01:00 PM">01:00 PM</option>
                                        <option value="02:00 PM">02:00 PM</option>
                                        <option value="03:00 PM">03:00 PM</option>
                                        <option value="04:00 PM">04:00 PM</option>
                                    </select>
                                </div>
                                
                                <!-- Notes -->
                                <div class="col-12">
                                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any specific concerns or requests?"></textarea>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <span class="submit-text">Book Appointment</span>
                                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- SECTION: About -->
        <section id="about" class="section-pad bg-light">
            <div class="container">
                <!-- Banner Image -->
                <div class="row mb-5">
                    <div class="col-12" data-aos="fade-up">
                        <div class="about-banner position-relative rounded overflow-hidden">
                            <img src="/uploads/assets/about_clinic.webp" alt="Our veterinary clinic interior" class="img-fluid w-100" width="1600" height="700" loading="lazy">
                            <div class="about-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <div class="text-center text-white">
                                    <h2 class="display-4 fw-bold mb-3">About Our Clinic</h2>
                                    <p class="lead">Dedicated to providing exceptional veterinary care since 2010</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- About Text -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center" data-aos="fade-up">
                        <p class="lead">
                            We are committed to enhancing the health and happiness of pets by providing a seamless, 
                            user-friendly platform for scheduling veterinary appointments. Our mission is to make pet 
                            care convenient for owners, offering access to a network of skilled and compassionate 
                            veterinarians. We understand how much your pets mean to you, and we aim to support their 
                            well-being through timely appointments, expert care, and a hassle-free experience.
                        </p>
                    </div>
                </div>
                
                <!-- KPI Counters -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                        <div class="card border-0 shadow-sm text-center p-4">
                            <div class="kpi-icon mx-auto mb-3 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                            </div>
                            <h3 class="display-5 fw-bold mb-0 counter" data-target="15">0</h3>
                            <p class="text-muted mb-0">Years of Experience</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card border-0 shadow-sm text-center p-4">
                            <div class="kpi-icon mx-auto mb-3 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <h3 class="display-5 fw-bold mb-0 counter" data-target="5000">0</h3>
                            <p class="text-muted mb-0">Happy Pets Treated</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card border-0 shadow-sm text-center p-4">
                            <div class="kpi-icon mx-auto mb-3 text-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            </div>
                            <h3 class="display-5 fw-bold mb-0 counter" data-target="4.9">0</h3>
                            <p class="text-muted mb-0">Average Rating (5★)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Team Section -->
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8 text-center">
                        <h2 class="heading-xl mb-3" data-aos="fade-up">Meet Our Team</h2>
                        <p class="lead muted" data-aos="fade-up" data-aos-delay="100">
                            Our experienced veterinarians and staff are dedicated to your pet's health
                        </p>
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- Team Member 1 -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="/uploads/assets/team_vet1.webp" alt="Dr. Sarah Johnson - Lead Veterinarian" class="card-img-top" width="400" height="500" loading="lazy">
                            <div class="card-body text-center">
                                <h5 class="card-title mb-1">Dr. Sarah Johnson</h5>
                                <p class="text-muted mb-3">Lead Veterinarian</p>
                                <p class="card-text small">15+ years of experience specializing in small animal care and surgery.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Team Member 2 -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="/uploads/assets/team_vet2.webp" alt="Dr. Michael Chen - Senior Veterinarian" class="card-img-top" width="400" height="500" loading="lazy">
                            <div class="card-body text-center">
                                <h5 class="card-title mb-1">Dr. Michael Chen</h5>
                                <p class="text-muted mb-3">Senior Veterinarian</p>
                                <p class="card-text small">Expert in exotic pets and emergency care with 12 years of experience.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Team Member 3 -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="/uploads/assets/team_vet3.webp" alt="Emily Rodriguez - Veterinary Nurse" class="card-img-top" width="400" height="500" loading="lazy">
                            <div class="card-body text-center">
                                <h5 class="card-title mb-1">Emily Rodriguez</h5>
                                <p class="text-muted mb-3">Veterinary Nurse</p>
                                <p class="card-text small">Compassionate care specialist with expertise in post-operative care.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- SECTION: Contact -->
        <section id="contact" class="section-pad">
            <div class="container">
                <div class="row align-items-center g-5">
                    <!-- Left Column: Form -->
                    <div class="col-lg-6" data-aos="fade-right">
                        <h2 class="heading-xl mb-4">Get In Touch</h2>
                        <p class="lead muted mb-4">Have questions? We're here to help. Send us a message and we'll respond as soon as possible.</p>
                        
                        <form id="contact-form" action="contact_us.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            
                            <!-- Honeypot for spam protection -->
                            <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">
                            
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label for="contact_name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" id="contact_name" name="fullname" required>
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="contact_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="contact_email" name="email" required>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                                
                                <!-- Phone -->
                                <div class="col-12">
                                    <label for="contact_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="contact_phone" name="contact" required>
                                    <div class="invalid-feedback">Please enter your phone number.</div>
                                </div>
                                
                                <!-- Message -->
                                <div class="col-12">
                                    <label for="contact_message" class="form-label">Message</label>
                                    <textarea class="form-control" id="contact_message" name="message" rows="5" required></textarea>
                                    <div class="invalid-feedback">Please enter your message.</div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <span class="submit-text">Send Message</span>
                                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Contact Channels -->
                        <div class="row g-3 mt-4">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="contact-icon me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted">Phone</p>
                                        <p class="mb-0 fw-semibold"><?= $_settings->info('contact') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="contact-icon me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted">Email</p>
                                        <p class="mb-0 fw-semibold"><?= $_settings->info('email') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="contact-icon me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted">Address</p>
                                        <p class="mb-0 fw-semibold"><?= $_settings->info('address') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Illustration & Map -->
                    <div class="col-lg-6" data-aos="fade-left">
                        <img src="/uploads/assets/contact_art.svg" alt="Contact us illustration" class="img-fluid mb-4" width="500" height="400" loading="lazy">
                        
                        <!-- Map Placeholder -->
                        <div class="map-container rounded overflow-hidden shadow">
                            <img src="/uploads/assets/contact_map_placeholder.svg" alt="Location map" class="img-fluid w-100" width="600" height="400" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <?php require_once('inc/footer.php'); ?>
    </div>
    
    <!-- Toast Notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    <span id="successMessage">Success!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                    <span id="errorMessage">An error occurred.</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</body>
</html>
