<!-- ============================================ -->
<!-- MODERN SINGLE-PAGE HOMEPAGE -->
<!-- ============================================ -->
</div><!-- Close index.php container to allow full-width sections -->

<!-- Hero Section with Swiper Carousel -->
<section id="home" class="hero-section">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background-image: linear-gradient(135deg, rgba(37, 99, 235, 0.8), rgba(16, 185, 129, 0.6)), url('<?= base_url ?>uploads/assets/hero_01.webp');">
                    <div class="container">
                        <div class="row align-items-center" style="min-height: 70vh;">
                            <div class="col-lg-7" data-aos="fade-right">
                                <h1 class="hero-title display-3 fw-bold text-white mb-4">
                                    Professional <span class="text-warning">Veterinary Care</span> for Your Beloved Pets
                                </h1>
                                <p class="hero-subtitle lead text-white mb-4">
                                    Expert veterinarians, modern facilities, and compassionate care. Your pet's health is our priority.
                                </p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <a href="#appointment" class="btn btn-warning btn-lg rounded-pill px-5 py-3 shadow-lg">
                                        <i class="fas fa-calendar-plus me-2"></i> Book Appointment
                                    </a>
                                    <a href="#services" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3">
                                        <i class="fas fa-heart me-2"></i> Our Services
                                    </a>
                                </div>
                                <div class="row mt-5 g-4">
                                    <div class="col-4">
                                        <div class="stat-card text-white text-center">
                                            <h3 class="display-5 fw-bold counter" data-target="5000">0</h3>
                                            <p class="mb-0">Happy Pets</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-card text-white text-center">
                                            <h3 class="display-5 fw-bold counter" data-target="15">0</h3>
                                            <p class="mb-0">Years Experience</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-card text-white text-center">
                                            <h3 class="display-5 fw-bold counter" data-target="10">0</h3>
                                            <p class="mb-0">Expert Vets</p>
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
                <div class="hero-slide" style="background-image: linear-gradient(135deg, rgba(139, 92, 246, 0.8), rgba(236, 72, 153, 0.6)), url('<?= base_url ?>uploads/assets/hero_02.webp');">
                    <div class="container">
                        <div class="row align-items-center" style="min-height: 70vh;">
                            <div class="col-lg-7" data-aos="fade-right">
                                <h1 class="hero-title display-3 fw-bold text-white mb-4">
                                    24/7 Emergency <span class="text-danger">Pet Care</span> Services
                                </h1>
                                <p class="hero-subtitle lead text-white mb-4">
                                    Round-the-clock emergency services. We're here when your pet needs us most.
                                </p>
                                <div class="d-flex gap-3">
                                    <a href="tel:07123456789" class="btn btn-danger btn-lg rounded-pill px-5 py-3 shadow-lg">
                                        <i class="fas fa-phone-alt me-2"></i> Emergency: 07123456789
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background-image: linear-gradient(135deg, rgba(16, 185, 129, 0.8), rgba(59, 130, 246, 0.6)), url('<?= base_url ?>uploads/assets/hero_03.webp');">
                    <div class="container">
                        <div class="row align-items-center" style="min-height: 70vh;">
                            <div class="col-lg-7" data-aos="fade-right">
                                <h1 class="hero-title display-3 fw-bold text-white mb-4">
                                    Complete <span class="text-info">Wellness Programs</span> for Every Pet
                                </h1>
                                <p class="hero-subtitle lead text-white mb-4">
                                    Preventive care, nutrition counseling, and health monitoring tailored to your pet's needs.
                                </p>
                                <div class="d-flex gap-3">
                                    <a href="#services" class="btn btn-info btn-lg rounded-pill px-5 py-3 shadow-lg text-white">
                                        <i class="fas fa-clipboard-check me-2"></i> Explore Programs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-4 fw-bold text-primary mb-3">Our Veterinary Services</h2>
            <p class="lead text-muted">Comprehensive care for your furry, feathered, and scaly friends</p>
        </div>

        <div class="row g-4">
            <?php
            $services_data = [
                ['icon' => 'fa-syringe', 'title' => 'Vaccination', 'desc' => 'Complete immunization programs to protect your pets', 'color' => 'primary'],
                ['icon' => 'fa-pills', 'title' => 'Deworming', 'desc' => 'Regular deworming schedules for optimal health', 'color' => 'success'],
                ['icon' => 'fa-cut', 'title' => 'Grooming', 'desc' => 'Professional grooming and hygiene services', 'color' => 'info'],
                ['icon' => 'fa-tooth', 'title' => 'Dental Care', 'desc' => 'Complete oral health and dental cleaning', 'color' => 'warning'],
                ['icon' => 'fa-heartbeat', 'title' => 'Surgery', 'desc' => 'Advanced surgical procedures with expert care', 'color' => 'danger'],
                ['icon' => 'fa-microscope', 'title' => 'Diagnostics', 'desc' => 'State-of-the-art laboratory and imaging', 'color' => 'purple'],
                ['icon' => 'fa-home', 'title' => 'Pet Boarding', 'desc' => 'Safe and comfortable accommodation', 'color' => 'teal'],
                ['icon' => 'fa-stethoscope', 'title' => 'Check-ups', 'desc' => 'Regular health examinations and monitoring', 'color' => 'indigo']
            ];

            foreach ($services_data as $index => $service):
            ?>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <div class="service-card card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="service-icon mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-<?= $service['color'] ?> bg-gradient" style="width: 80px; height: 80px;">
                            <i class="fas <?= $service['icon'] ?> fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3"><?= $service['title'] ?></h5>
                        <p class="card-text text-muted"><?= $service['desc'] ?></p>
                        <a href="#appointment" class="btn btn-sm btn-outline-<?= $service['color'] ?> rounded-pill mt-2">
                            Book Now <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="./?page=services" class="btn btn-primary btn-lg rounded-pill px-5">
                <i class="fas fa-th-large me-2"></i> View All Services
            </a>
        </div>
    </div>
</section>

<!-- Appointment Booking Section -->
<section id="appointment" class="py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0" data-aos="fade-right">
                <h2 class="display-4 fw-bold text-primary mb-4">Book an Appointment</h2>
                <p class="lead text-muted mb-4">
                    Schedule a visit with our experienced veterinarians. We'll take great care of your pet!
                </p>
                
                <div class="feature-list">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-check text-primary"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold">Flexible Scheduling</h6>
                            <p class="text-muted mb-0">Choose a time that works best for you</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-md text-success"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold">Expert Veterinarians</h6>
                            <p class="text-muted mb-0">Experienced and certified professionals</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="icon-box bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shield-alt text-info"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold">Safe & Clean Facilities</h6>
                            <p class="text-muted mb-0">Modern equipment and hygienic environment</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <form action="<?= base_url ?>submit_appointment.php" method="POST" class="needs-validation" novalidate>
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] = bin2hex(random_bytes(32)) ?>">
                            <!-- Honeypot -->
                            <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                            <div class="row g-3">
                                <!-- Owner Information -->
                                <div class="col-12">
                                    <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-user me-2"></i> Owner Information
                                    </h5>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="owner_name" class="form-label fw-semibold">Full Name *</label>
                                    <input type="text" class="form-control form-control-lg" id="owner_name" name="owner_name" 
                                           value="<?= $_settings->userdata('firstname') ? $_settings->userdata('firstname').' '.$_settings->userdata('lastname') : '' ?>" 
                                           <?= $_settings->userdata('id') > 0 ? 'readonly' : '' ?> required>
                                    <?php if($_settings->userdata('id') > 0): ?>
                                    <button type="button" class="btn btn-sm btn-link edit-toggle" data-target="owner_name">Edit</button>
                                    <?php endif; ?>
                                    <div class="invalid-feedback">Please enter your full name.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="contact" class="form-label fw-semibold">Contact Number *</label>
                                    <input type="tel" class="form-control form-control-lg" id="contact" name="contact" 
                                           value="<?= $_settings->userdata('contact') ? $_settings->userdata('contact') : '' ?>" 
                                           <?= $_settings->userdata('id') > 0 ? 'readonly' : '' ?> required>
                                    <?php if($_settings->userdata('id') > 0): ?>
                                    <button type="button" class="btn btn-sm btn-link edit-toggle" data-target="contact">Edit</button>
                                    <?php endif; ?>
                                    <div class="invalid-feedback">Please enter your contact number.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email Address *</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                           value="<?= $_settings->userdata('email') ? $_settings->userdata('email') : '' ?>" 
                                           <?= $_settings->userdata('id') > 0 ? 'readonly' : '' ?> required>
                                    <?php if($_settings->userdata('id') > 0): ?>
                                    <button type="button" class="btn btn-sm btn-link edit-toggle" data-target="email">Edit</button>
                                    <?php endif; ?>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="address" class="form-label fw-semibold">Address</label>
                                    <input type="text" class="form-control form-control-lg" id="address" name="address" 
                                           value="<?= $_settings->userdata('address') ? $_settings->userdata('address') : '' ?>">
                                </div>

                                <!-- Pet Information -->
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-success border-bottom pb-2 mb-3">
                                        <i class="fas fa-paw me-2"></i> Pet Information
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <label for="pet_name" class="form-label fw-semibold">Pet Name *</label>
                                    <input type="text" class="form-control form-control-lg" id="pet_name" name="pet_name" required>
                                    <div class="invalid-feedback">Please enter your pet's name.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="pet_type" class="form-label fw-semibold">Pet Type</label>
                                    <select class="form-select form-select-lg" id="pet_type" name="pet_type">
                                        <option value="">Choose...</option>
                                        <option value="Dog">Dog</option>
                                        <option value="Cat">Cat</option>
                                        <option value="Bird">Bird</option>
                                        <option value="Rabbit">Rabbit</option>
                                        <option value="Hamster">Hamster</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="breed" class="form-label fw-semibold">Breed</label>
                                    <input type="text" class="form-control form-control-lg" id="breed" name="breed">
                                </div>

                                <div class="col-md-6">
                                    <label for="age" class="form-label fw-semibold">Age</label>
                                    <input type="text" class="form-control form-control-lg" id="age" name="age" placeholder="e.g., 2 years">
                                </div>

                                <!-- Service Selection -->
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-info border-bottom pb-2 mb-3">
                                        <i class="fas fa-clipboard-list me-2"></i> Service Details
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <label for="category_id" class="form-label fw-semibold">Service Category *</label>
                                    <select class="form-select form-select-lg" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>Choose...</option>
                                        <?php
                                        $categories = $conn->query("SELECT * FROM `category_list` WHERE `delete_flag` = 0 AND `status` = 1 ORDER BY `name` ASC");
                                        while($row = $categories->fetch_assoc()):
                                        ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a category.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="service_id" class="form-label fw-semibold">Service *</label>
                                    <select class="form-select form-select-lg" id="service_id" name="service_id" required>
                                        <option value="" selected disabled>Choose category first...</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a service.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="schedule" class="form-label fw-semibold">Preferred Date *</label>
                                    <input type="date" class="form-control form-control-lg" id="schedule" name="schedule" 
                                           min="<?= date('Y-m-d') ?>" required>
                                    <div class="invalid-feedback">Please select a date.</div>
                                </div>

                                <div class="col-12">
                                    <label for="remarks" class="form-label fw-semibold">Additional Notes</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                              placeholder="Any special concerns or requests..."></textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3">
                                        <span class="submit-text">
                                            <i class="fas fa-calendar-check me-2"></i> Book Appointment
                                        </span>
                                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h2 class="display-4 fw-bold text-primary mb-3">About Our Clinic</h2>
                <p class="lead text-muted">
                    <?= $_settings->info('name') ?> - Your trusted partner in pet healthcare since 2010
                </p>
            </div>
        </div>

        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <img src="<?= base_url ?>uploads/assets/about_clinic.webp" alt="Clinic Interior" 
                     class="img-fluid rounded-4 shadow-lg" loading="lazy" 
                     onerror="this.src='<?= base_url ?>uploads/<?= $_settings->info('cover') ?>'">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h3 class="fw-bold mb-4">Why Choose Us?</h3>
                <p class="text-muted mb-4">
                    We are committed to enhancing the health and happiness of pets by providing a seamless, 
                    user-friendly platform for scheduling veterinary appointments. Our mission is to make pet care 
                    convenient for owners, offering access to a network of skilled and compassionate veterinarians.
                </p>
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">Certified Vets</h6>
                                <p class="text-muted small mb-0">Licensed professionals</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-hospital text-success fa-2x"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">Modern Equipment</h6>
                                <p class="text-muted small mb-0">Latest technology</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-clock text-info fa-2x"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">24/7 Emergency</h6>
                                <p class="text-muted small mb-0">Always available</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-heart text-warning fa-2x"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">Compassionate Care</h6>
                                <p class="text-muted small mb-0">We love animals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Stats -->
        <div class="row g-4 text-center mt-5" data-aos="fade-up">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h2 class="display-4 fw-bold text-primary counter" data-target="5000">0</h2>
                        <p class="text-muted mb-0">Happy Clients</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-award fa-3x text-success"></i>
                        </div>
                        <h2 class="display-4 fw-bold text-success counter" data-target="15">0</h2>
                        <p class="text-muted mb-0">Years of Service</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-star fa-3x text-warning"></i>
                        </div>
                        <h2 class="display-4 fw-bold text-warning counter" data-target="4.9">0</h2>
                        <p class="text-muted mb-0">Average Rating</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5 bg-white">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold text-primary mb-3">Get in Touch</h2>
                <p class="lead text-muted">Have questions? We'd love to hear from you.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-right">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Contact Information</h5>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-phone text-primary"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-semibold mb-1">Phone</h6>
                                <p class="text-muted mb-0"><?= $_settings->info('contact') ?></p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-envelope text-success"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-semibold mb-1">Email</h6>
                                <p class="text-muted mb-0"><?= $_settings->info('email') ?></p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-map-marker-alt text-info"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-semibold mb-1">Location</h6>
                                <p class="text-muted mb-0"><?= $_settings->info('address') ?></p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-semibold mb-1">Business Hours</h6>
                                <p class="text-muted mb-0">Mon - Sat: 8:00 AM - 6:00 PM</p>
                                <p class="text-muted mb-0">Sunday: Emergency Only</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8" data-aos="fade-left">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Send us a Message</h5>
                        <form action="<?= base_url ?>contact_us.php" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="contact_name" class="form-label fw-semibold">Name *</label>
                                    <input type="text" class="form-control form-control-lg" id="contact_name" name="name" required>
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_email" class="form-label fw-semibold">Email *</label>
                                    <input type="email" class="form-control form-control-lg" id="contact_email" name="email" required>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                                <div class="col-12">
                                    <label for="contact_subject" class="form-label fw-semibold">Subject *</label>
                                    <input type="text" class="form-control form-control-lg" id="contact_subject" name="subject" required>
                                    <div class="invalid-feedback">Please enter a subject.</div>
                                </div>
                                <div class="col-12">
                                    <label for="contact_message" class="form-label fw-semibold">Message *</label>
                                    <textarea class="form-control" id="contact_message" name="message" rows="5" required></textarea>
                                    <div class="invalid-feedback">Please enter your message.</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">
                                        <i class="fas fa-paper-plane me-2"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Toast Notifications -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="successMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="errorMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Reopen container for index.php structure -->
<div class="container d-none">
</div>