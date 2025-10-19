<!-- ============================================ -->
<!-- ABOUT US PAGE -->
<!-- ============================================ -->
</div><!-- Close index.php container to allow full-width sections -->

<!-- Hero Section -->
<section class="py-5 bg-gradient" style="background: linear-gradient(135deg, #2563eb 0%, #10b981 100%); min-height: 40vh;">
    <div class="container">
        <div class="row align-items-center" style="min-height: 30vh;">
            <div class="col-lg-8 mx-auto text-center text-white" data-aos="fade-up">
                <h1 class="display-3 fw-bold mb-4">About <?= $_settings->info('name') ?></h1>
                <p class="lead fs-4">Your trusted partner in comprehensive veterinary care since 2010</p>
            </div>
        </div>
    </div>
</section>

<!-- Main About Content -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <img src="<?= base_url ?>uploads/assets/about_clinic.webp" alt="<?= $_settings->info('name') ?> Clinic Interior" 
                     class="img-fluid rounded-4 shadow-lg" loading="lazy" 
                     onerror="this.src='<?= base_url ?>uploads/<?= $_settings->info('cover') ?>'">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="display-5 fw-bold text-primary mb-4">Our Story</h2>
                <p class="lead text-muted mb-4">
                    Founded in 2010, <?= $_settings->info('name') ?> has been at the forefront of veterinary excellence, 
                    providing compassionate and comprehensive care for pets across our community.
                </p>
                <p class="text-muted mb-4">
                    We believe that every pet deserves the highest quality medical attention, delivered with 
                    compassion and expertise. Our state-of-the-art facility, combined with our team of 
                    experienced veterinarians, ensures your beloved companions receive the best possible care.
                </p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-award text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Certified Excellence</h6>
                                <small class="text-muted">Licensed professionals</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-heart text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Compassionate Care</h6>
                                <small class="text-muted">We love animals</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission, Vision, Values -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-3">Our Mission & Values</h2>
                <p class="lead text-muted">
                    We're committed to enhancing the health and happiness of pets through exceptional veterinary care.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Mission -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-bullseye fa-2x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Our Mission</h4>
                        <p class="text-muted">
                            To provide exceptional veterinary care through advanced medical practices, 
                            compassionate service, and a commitment to the health and well-being of every pet we treat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Vision -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-eye fa-2x text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Our Vision</h4>
                        <p class="text-muted">
                            To be the leading veterinary practice in our community, known for innovation, 
                            excellence, and creating lasting relationships between pets, families, and our team.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Values -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-handshake fa-2x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Our Values</h4>
                        <p class="text-muted">
                            Compassion, integrity, excellence, and continuous learning guide everything we do. 
                            We treat every pet as if they were our own family member.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Team -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-3">Meet Our Expert Team</h2>
                <p class="lead text-muted">
                    Our experienced veterinarians and support staff are dedicated to providing the highest quality care.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Team Member 1 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-user-md fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Dr. Sarah Johnson</h5>
                        <p class="text-primary fw-semibold mb-2">Chief Veterinarian</p>
                        <p class="text-muted small mb-3">
                            15+ years experience in small animal medicine and surgery. 
                            Specialized in internal medicine and emergency care.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge bg-light text-dark">DVM</span>
                            <span class="badge bg-light text-dark">Emergency Care</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Member 2 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-user-md fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Dr. Michael Chen</h5>
                        <p class="text-success fw-semibold mb-2">Senior Veterinarian</p>
                        <p class="text-muted small mb-3">
                            Specialized in orthopedic surgery and rehabilitation therapy. 
                            10+ years helping pets recover from injuries.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge bg-light text-dark">Surgery</span>
                            <span class="badge bg-light text-dark">Orthopedics</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Member 3 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="bg-info bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-user-md fa-3x text-info"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Dr. Emily Rodriguez</h5>
                        <p class="text-info fw-semibold mb-2">Veterinary Specialist</p>
                        <p class="text-muted small mb-3">
                            Expert in exotic pet care and dermatology. 
                            Passionate about preventive care and nutrition counseling.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge bg-light text-dark">Exotic Pets</span>
                            <span class="badge bg-light text-dark">Dermatology</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Facilities & Equipment -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-3">State-of-the-Art Facilities</h2>
                <p class="lead text-muted">
                    Modern equipment and advanced technology ensure the best possible care for your pets.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-hospital fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Modern Surgery Suite</h5>
                    <p class="text-muted small">
                        Fully equipped operating rooms with advanced monitoring systems
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-x-ray fa-2x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Digital Radiology</h5>
                    <p class="text-muted small">
                        High-resolution imaging for accurate diagnostics
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-microscope fa-2x text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-2">In-House Laboratory</h5>
                    <p class="text-muted small">
                        Quick test results for faster diagnosis and treatment
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-bed fa-2x text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Recovery Rooms</h5>
                    <p class="text-muted small">
                        Comfortable spaces for post-surgery monitoring and care
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5 bg-primary">
    <div class="container py-4">
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-white">
                    <h2 class="display-4 fw-bold counter" data-target="5000">0</h2>
                    <p class="mb-0">Happy Pets Treated</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="text-white">
                    <h2 class="display-4 fw-bold counter" data-target="15">0</h2>
                    <p class="mb-0">Years of Service</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="text-white">
                    <h2 class="display-4 fw-bold counter" data-target="10">0</h2>
                    <p class="mb-0">Expert Veterinarians</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="text-white">
                    <h2 class="display-4 fw-bold counter" data-target="24">0</h2>
                    <p class="mb-0">Hours Emergency Care</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-4">Ready to Experience Our Care?</h2>
                <p class="lead text-muted mb-4">
                    Schedule an appointment today and see why thousands of pet owners trust us with their beloved companions.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="./?page=home#appointment" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="fas fa-calendar-plus me-2"></i> Book Appointment
                    </a>
                    <a href="./?page=contact" class="btn btn-outline-primary btn-lg rounded-pill px-5">
                        <i class="fas fa-phone me-2"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reopen container for index.php structure -->
<div class="container d-none">
</div>