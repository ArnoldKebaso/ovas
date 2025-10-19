<!-- ============================================ -->
<!-- MODERN SERVICES PAGE -->
<!-- ============================================ -->
</div><!-- Close index.php container to allow full-width sections -->

<style>
    /* Services Page Specific Styles */
    .services-hero {
        background: linear-gradient(135deg, #2563eb 0%, #10b981 100%);
        padding: 4rem 0 3rem;
        margin-top: -20px;
    }
    
    .search-bar-container {
        background: white;
        border-radius: 50px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        padding: 0.5rem;
    }
    
    .filter-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        border: 2px solid #e2e8f0;
        background: white;
        color: #64748b;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0.25rem;
    }
    
    .filter-chip:hover {
        border-color: #2563eb;
        color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    
    .filter-chip.active {
        background: linear-gradient(135deg, #2563eb, #10b981);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    
    .filter-chip i {
        margin-right: 0.5rem;
    }
    
    .service-card {
        position: relative;
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }
    
    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    .service-card-image {
        height: 200px;
        overflow: hidden;
        position: relative;
    }
    
    .service-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .service-card:hover .service-card-image img {
        transform: scale(1.1);
    }
    
    .service-card-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .service-icon-badge {
        position: absolute;
        bottom: -25px;
        left: 1.5rem;
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }
    
    .service-card-body {
        padding: 2.5rem 1.5rem 1.5rem;
    }
    
    .pet-type-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .pet-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        background: #f1f5f9;
        border-radius: 50px;
        font-size: 0.75rem;
        color: #64748b;
    }
    
    .pet-tag i {
        margin-right: 0.25rem;
        font-size: 0.7rem;
    }
    
    /* Modal Styles */
    .service-modal .modal-content {
        border-radius: 1.5rem;
        border: none;
        overflow: hidden;
    }
    
    .service-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #10b981);
        color: white;
        padding: 2rem;
        border: none;
    }
    
    .service-modal .modal-body {
        padding: 2rem;
    }
    
    .appointment-quick-form {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 2rem;
        margin-top: 2rem;
    }
    
    .price-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.5rem;
        font-weight: 700;
        color: #2563eb;
    }
    
    .price-tag small {
        font-size: 1rem;
        font-weight: 400;
        color: #64748b;
    }
    
    .feature-list {
        list-style: none;
        padding: 0;
    }
    
    .feature-list li {
        padding: 0.75rem 0;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .feature-list li:last-child {
        border-bottom: none;
    }
    
    .feature-list li i {
        color: #10b981;
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }
    
    .view-count {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        font-size: 0.875rem;
    }
</style>

<!-- Services Hero Section -->
<section class="services-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-3">Our Veterinary Services</h1>
                <p class="lead mb-4">Comprehensive care for your beloved pets. Expert veterinarians, modern equipment, and compassionate service.</p>
                <div class="d-flex gap-3">
                    <div class="text-center">
                        <div class="h2 fw-bold mb-0">50+</div>
                        <small>Services</small>
                    </div>
                    <div class="text-center">
                        <div class="h2 fw-bold mb-0">10</div>
                        <small>Specialists</small>
                    </div>
                    <div class="text-center">
                        <div class="h2 fw-bold mb-0">24/7</div>
                        <small>Emergency</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="<?= base_url ?>uploads/assets/services_hero.webp" alt="Our Services" class="img-fluid rounded-4 shadow-lg" 
                     onerror="this.src='<?= base_url ?>uploads/<?= $_settings->info('cover') ?>'">
            </div>
        </div>
    </div>
</section>

<!-- Search & Filter Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Search Bar -->
                <div class="search-bar-container mb-4" data-aos="fade-up">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="serviceSearch" class="form-control border-0 shadow-none" 
                               placeholder="Search services (e.g., vaccination, grooming, dental)...">
                        <button class="btn btn-primary rounded-pill px-4" type="button">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                    </div>
                </div>
                
                <!-- Filter Chips -->
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="filter-chip active" data-filter="all">
                        <i class="fas fa-th"></i> All Services
                    </div>
                    <?php 
                    $categories = $conn->query("SELECT * FROM `category_list` WHERE `delete_flag` = 0 ORDER BY `name` ASC");
                    $cat_icons = ['dog' => 'fa-dog', 'cat' => 'fa-cat', 'bird' => 'fa-dove', 'rabbit' => 'fa-paw', 'hamster' => 'fa-paw'];
                    if($categories):
                        while($cat = $categories->fetch_assoc()):
                        $icon = 'fa-paw';
                        foreach($cat_icons as $key => $val) {
                            if(stripos($cat['name'], $key) !== false) {
                                $icon = $val;
                                break;
                            }
                        }
                    ?>
                    <div class="filter-chip" data-filter="<?= strtolower($cat['name']) ?>">
                        <i class="fas <?= $icon ?>"></i> <?= $cat['name'] ?>
                    </div>
                    <?php 
                        endwhile;
                    endif; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid Section -->
<section class="py-5">
    <div class="container">
        <div class="row" id="servicesGrid">
            <?php
            // Get all services
            $services_qry = $conn->query("SELECT * FROM `service_list` WHERE `delete_flag` = 0 ORDER BY `name` ASC");
            
            // Build category lookup array
            $categories_qry = $conn->query("SELECT * FROM `category_list` WHERE `delete_flag` = 0");
            $cat_arr = [];
            if($categories_qry) {
                while($cat_row = $categories_qry->fetch_assoc()) {
                    $cat_arr[$cat_row['id']] = $cat_row['name'];
                }
            }
            
            // Define colors
            $colors = ['primary', 'success', 'info', 'warning', 'danger', 'purple', 'teal', 'indigo'];
            
            // Display services
            if($services_qry):
                while($service = $services_qry->fetch_assoc()):
                // Get first category for this service
                $category_ids = explode(',', $service['category_ids']);
                $first_cat_id = isset($category_ids[0]) ? trim($category_ids[0]) : 1;
                $category_name = isset($cat_arr[$first_cat_id]) ? $cat_arr[$first_cat_id] : 'General';
                
                // Build category names for data attribute
                $category_names = [];
                foreach($category_ids as $cat_id) {
                    $cat_id = trim($cat_id);
                    if(isset($cat_arr[$cat_id])) {
                        $category_names[] = strtolower($cat_arr[$cat_id]);
                    }
                }
                $categories_str = implode(' ', $category_names);
                
                // Determine color
                $colorIndex = $first_cat_id % count($colors);
                $color = $colors[$colorIndex];
                
                // Strip HTML tags from description for preview
                $description = strip_tags($service['description']);
            ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-category="<?= $categories_str ?>" data-service-id="<?= $service['id'] ?>">
                <div class="service-card card shadow-sm">
                    <!-- Service Image -->
                    <div class="service-card-image">
                        <img src="<?= base_url ?>uploads/services/<?= $service['id'] ?>.jpg" 
                             alt="<?= $service['name'] ?>"
                             onerror="this.src='<?= base_url ?>uploads/assets/service_placeholder.jpg'">
                        
                        <!-- Price Badge -->
                        <div class="service-card-badge">
                            <span class="text-<?= $color ?>">₱<?= number_format($service['fee'], 2) ?></span>
                        </div>
                        
                        <!-- Icon Badge -->
                        <div class="service-icon-badge bg-<?= $color ?> bg-gradient">
                            <i class="fas fa-stethoscope fa-lg text-white"></i>
                        </div>
                    </div>
                    
                    <!-- Service Content -->
                    <div class="service-card-body">
                        <h5 class="fw-bold mb-2"><?= $service['name'] ?></h5>
                        <p class="text-muted small mb-3">
                            <?= strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description ?>
                        </p>
                        
                        <!-- Category Tag -->
                        <div class="pet-type-tags">
                            <span class="pet-tag">
                                <i class="fas fa-folder"></i> <?= $category_name ?>
                            </span>
                            <span class="pet-tag">
                                <i class="fas fa-clock"></i> 30-60 min
                            </span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-outline-<?= $color ?> btn-sm rounded-pill flex-grow-1" 
                                    onclick="openServiceModal(<?= $service['id'] ?>, '<?= addslashes($service['name']) ?>', '<?= addslashes($description) ?>', <?= $service['fee'] ?>, '<?= addslashes($category_name) ?>', <?= $first_cat_id ?>)">
                                <i class="fas fa-info-circle me-1"></i> Details
                            </button>
                            <button class="btn btn-<?= $color ?> btn-sm rounded-pill flex-grow-1" 
                                    onclick="openAppointmentModal(<?= $service['id'] ?>, '<?= addslashes($service['name']) ?>', <?= $first_cat_id ?>)">
                                <i class="fas fa-calendar-plus me-1"></i> Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No services available at the moment. Please check back later.
                </div>
            </div>
            <?php 
            endif;
            ?>
        </div>
        
        <!-- No Results Message -->
        <div id="noResults" class="text-center py-5 d-none" data-aos="fade-up">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No services found</h4>
            <p class="text-muted">Try adjusting your search or filter</p>
        </div>
    </div>
</section>

<!-- Service Detail Modal -->
<div class="modal fade service-modal" id="serviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title fw-bold mb-2" id="serviceModalTitle"></h3>
                    <div class="d-flex gap-3">
                        <span class="badge bg-white bg-opacity-25" id="serviceCategory"></span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Service Image -->
                <div class="mb-4">
                    <img id="serviceModalImage" src="" alt="" class="img-fluid rounded-3 w-100" style="max-height: 300px; object-fit: cover;">
                </div>
                
                <!-- Price -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="price-tag">
                        ₱<span id="servicePrice">0.00</span>
                        <small>/ session</small>
                    </div>
                    <button class="btn btn-primary rounded-pill px-4" onclick="quickBookFromModal()">
                        <i class="fas fa-calendar-check me-2"></i> Book This Service
                    </button>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Description</h5>
                    <p class="text-muted" id="serviceDescription"></p>
                </div>
                
                <!-- What's Included -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">What's Included</h5>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Professional consultation</li>
                        <li><i class="fas fa-check-circle"></i> Complete examination</li>
                        <li><i class="fas fa-check-circle"></i> Health report</li>
                        <li><i class="fas fa-check-circle"></i> Follow-up support</li>
                    </ul>
                </div>
                
                <!-- Quick Appointment Form -->
                <div class="appointment-quick-form" id="quickAppointmentSection" style="display: none;">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i> Book Appointment
                    </h5>
                    <form id="quickAppointmentForm">
                        <input type="hidden" id="modal_service_id" name="service_id">
                        <input type="hidden" id="modal_category_id" name="category_id">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] = bin2hex(random_bytes(32)) ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Your Name *</label>
                                <input type="text" name="owner_name" class="form-control" value="<?= $_settings->userdata('firstname') ? $_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname') : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Number *</label>
                                <input type="tel" name="contact" class="form-control" value="<?= $_settings->userdata('contact') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email *</label>
                                <input type="email" name="email" class="form-control" value="<?= $_settings->userdata('email') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pet Name *</label>
                                <input type="text" name="pet_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Preferred Date *</label>
                                <input type="date" name="schedule" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pet Type</label>
                                <select name="pet_type" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="Dog">Dog</option>
                                    <option value="Cat">Cat</option>
                                    <option value="Bird">Bird</option>
                                    <option value="Rabbit">Rabbit</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Additional Notes</label>
                                <textarea name="remarks" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                                    <i class="fas fa-check-circle me-2"></i> Confirm Appointment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Modal (Standalone) -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-calendar-plus me-2"></i> Book Appointment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong>Service:</strong> <span id="appointmentServiceName"></span>
                    </div>
                </div>
                
                <form id="standaloneAppointmentForm" class="needs-validation" novalidate>
                    <input type="hidden" id="standalone_service_id" name="service_id">
                    <input type="hidden" id="standalone_category_id" name="category_id">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="text" name="website" style="display:none" tabindex="-1">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name *</label>
                            <input type="text" name="owner_name" class="form-control form-control-lg" value="<?= $_settings->userdata('firstname') ? $_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname') : '' ?>" required>
                            <div class="invalid-feedback">Please enter your name</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number *</label>
                            <input type="tel" name="contact" class="form-control form-control-lg" value="<?= $_settings->userdata('contact') ?>" required>
                            <div class="invalid-feedback">Please enter contact number</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address *</label>
                            <input type="email" name="email" class="form-control form-control-lg" value="<?= $_settings->userdata('email') ?>" required>
                            <div class="invalid-feedback">Please enter valid email</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="address" class="form-control form-control-lg" value="<?= $_settings->userdata('address') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pet Name *</label>
                            <input type="text" name="pet_name" class="form-control form-control-lg" required>
                            <div class="invalid-feedback">Please enter pet's name</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pet Type</label>
                            <select name="pet_type" class="form-select form-select-lg">
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
                            <label class="form-label fw-semibold">Breed</label>
                            <input type="text" name="breed" class="form-control form-control-lg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Age</label>
                            <input type="text" name="age" class="form-control form-control-lg" placeholder="e.g., 2 years">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Date *</label>
                            <input type="date" name="schedule" class="form-control form-control-lg" min="<?= date('Y-m-d') ?>" required>
                            <div class="invalid-feedback">Please select a date</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Additional Notes</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Any special concerns or requests..."></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3">
                                <span class="submit-text">
                                    <i class="fas fa-calendar-check me-2"></i> Book Appointment
                                </span>
                                <span class="spinner-border spinner-border-sm me-2 d-none"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i> <span id="successMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-circle me-2"></i> <span id="errorMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
// Services Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('serviceSearch');
    const serviceCards = document.querySelectorAll('[data-category]');
    const noResults = document.getElementById('noResults');
    
    searchInput?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        let visibleCount = 0;
        
        serviceCards.forEach(card => {
            const title = card.querySelector('h5').textContent.toLowerCase();
            const description = card.querySelector('.text-muted').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        noResults.classList.toggle('d-none', visibleCount > 0);
    });
    
    // Filter functionality
    const filterChips = document.querySelectorAll('.filter-chip');
    
    filterChips.forEach(chip => {
        chip.addEventListener('click', function() {
            // Remove active from all chips
            filterChips.forEach(c => c.classList.remove('active'));
            // Add active to clicked chip
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            let visibleCount = 0;
            
            serviceCards.forEach(card => {
                const category = card.dataset.category.toLowerCase();
                
                if (filter === 'all' || category.includes(filter)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            noResults.classList.toggle('d-none', visibleCount > 0);
        });
    });
});

// Open service detail modal
function openServiceModal(serviceId, serviceName, serviceDesc, serviceFee, categoryName, categoryId) {
    document.getElementById('serviceModalTitle').textContent = serviceName;
    document.getElementById('serviceCategory').textContent = categoryName;
    document.getElementById('servicePrice').textContent = parseFloat(serviceFee).toFixed(2);
    document.getElementById('serviceDescription').textContent = serviceDesc;
    document.getElementById('serviceModalImage').src = '<?= base_url ?>uploads/services/' + serviceId + '.jpg';
    document.getElementById('serviceModalImage').onerror = function() {
        this.src = '<?= base_url ?>uploads/assets/service_placeholder.jpg';
    };
    document.getElementById('modal_service_id').value = serviceId;
    document.getElementById('modal_category_id').value = categoryId;
    
    const modal = new bootstrap.Modal(document.getElementById('serviceModal'));
    modal.show();
}

// Open appointment modal directly
function openAppointmentModal(serviceId, serviceName, categoryId) {
    document.getElementById('appointmentServiceName').textContent = serviceName;
    document.getElementById('standalone_service_id').value = serviceId;
    document.getElementById('standalone_category_id').value = categoryId;
    
    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();
}

// Show quick book section in modal
function quickBookFromModal() {
    const quickSection = document.getElementById('quickAppointmentSection');
    quickSection.style.display = 'block';
    quickSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Handle quick appointment form submission (in modal)
document.getElementById('quickAppointmentForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
    
    try {
        const formData = new FormData(this);
        const response = await fetch('<?= base_url ?>submit_appointment.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('serviceModal')).hide();
            showToast('success', result.msg || 'Appointment booked successfully!');
            this.reset();
            document.getElementById('quickAppointmentSection').style.display = 'none';
        } else {
            showToast('error', result.msg || 'Failed to book appointment');
        }
    } catch (error) {
        showToast('error', 'An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Confirm Appointment';
    }
});

// Handle standalone appointment form submission
document.getElementById('standaloneAppointmentForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const submitText = submitBtn.querySelector('.submit-text');
    const spinner = submitBtn.querySelector('.spinner-border');
    
    submitBtn.disabled = true;
    submitText.classList.add('d-none');
    spinner.classList.remove('d-none');
    
    try {
        const formData = new FormData(this);
        const response = await fetch('<?= base_url ?>submit_appointment.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('appointmentModal')).hide();
            showToast('success', result.msg || 'Appointment booked successfully!');
            this.reset();
            this.classList.remove('was-validated');
        } else {
            showToast('error', result.msg || 'Failed to book appointment');
        }
    } catch (error) {
        showToast('error', 'An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitText.classList.remove('d-none');
        spinner.classList.add('d-none');
    }
});

// Toast notification function
function showToast(type, message) {
    const toastEl = type === 'success' ? document.getElementById('successToast') : document.getElementById('errorToast');
    const messageEl = type === 'success' ? document.getElementById('successMessage') : document.getElementById('errorMessage');
    
    if (toastEl && messageEl) {
        messageEl.textContent = message;
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }
}
</script>

<!-- Reopen container for index.php structure -->
<div class="container d-none">