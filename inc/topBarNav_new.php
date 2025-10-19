<style>
  .user-img{
        position: absolute;
        height: 27px;
        width: 27px;
        object-fit: cover;
        left: -7%;
        top: -12%;
  }
  .btn-rounded{
        border-radius: 50px;
  }
</style>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
  <div class="container">
    <!-- Brand -->
    <a href="./" class="navbar-brand d-flex align-items-center">
      <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="<?= $_settings->info('name') ?>" class="brand-image img-circle" style="height: 40px; width: 40px; object-fit: cover; margin-right: 0.75rem;">
      <span class="fw-bold"><?= $_settings->info('short_name') ?></span>
    </a>

    <!-- Mobile Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <a href="#home" class="nav-link active">Home</a>
        </li>
        <li class="nav-item">
          <a href="#services" class="nav-link">Services</a>
        </li>
        <li class="nav-item">
          <a href="#appointment" class="nav-link">Book Appointment</a>
        </li>
        <li class="nav-item">
          <a href="#about" class="nav-link">About Us</a>
        </li>
        <li class="nav-item">
          <a href="#contact" class="nav-link">Contact</a>
        </li>
        <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') != 1): ?>
        <li class="nav-item">
          <a href="./?page=profile" class="nav-link">My Profile</a>
        </li>
        <?php endif; ?>
      </ul>

      <!-- Right Side Actions -->
      <div class="d-flex align-items-center gap-3">
        <?php if($_settings->userdata('id') > 0): ?>
          <!-- Logged In User -->
          <div class="dropdown">
            <button class="btn btn-sm btn-light rounded-pill dropdown-toggle d-flex align-items-center gap-2" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?= validate_image($_settings->userdata('avatar')) ?>" alt="Avatar" class="rounded-circle" style="height: 28px; width: 28px; object-fit: cover;">
              <span><?= !empty($_settings->userdata('firstname')) ? $_settings->userdata('firstname') : $_settings->userdata('username') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="./?page=profile"><i class="fas fa-user me-2"></i>My Profile</a></li>
              <li><a class="dropdown-item" href="./?page=appointments"><i class="fas fa-calendar-alt me-2"></i>My Appointments</a></li>
              <li><hr class="dropdown-divider"></li>
              <?php if($_settings->userdata('login_type') == 1): ?>
                <li><a class="dropdown-item text-danger" href="<?= base_url.'classes/Login.php?f=logout' ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
              <?php else: ?>
                <li><a class="dropdown-item text-danger" href="<?= base_url.'classes/Login.php?f=client_logout' ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
              <?php endif; ?>
            </ul>
          </div>
        <?php else: ?>
          <!-- Guest User -->
          <div class="d-flex gap-2">
            <a href="./admin" class="btn btn-sm btn-outline-primary rounded-pill">Admin Login</a>
            <a href="#appointment" class="btn btn-sm btn-primary rounded-pill">Book Now</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Top Contact Bar (Optional - can be hidden on scroll) -->
<div class="top-contact-bar bg-gradient-primary text-white py-2 d-none d-lg-block" id="topContactBar" style="padding-top: 0; font-size: 0.875rem;">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex gap-4">
        <span><i class="fas fa-phone me-2"></i><?= $_settings->info('contact') ?></span>
        <span><i class="fas fa-envelope me-2"></i><?= $_settings->info('email') ?></span>
      </div>
      <div>
        <span><i class="fas fa-clock me-2"></i>Mon - Sat: 8:00 AM - 6:00 PM</span>
      </div>
    </div>
  </div>
</div>

<script>
  // Hide top contact bar on scroll
  let lastScroll = 0;
  const topBar = document.getElementById('topContactBar');
  
  window.addEventListener('scroll', function() {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 50 && topBar) {
      topBar.style.transform = 'translateY(-100%)';
      topBar.style.opacity = '0';
    } else if (topBar) {
      topBar.style.transform = 'translateY(0)';
      topBar.style.opacity = '1';
    }
    
    lastScroll = currentScroll;
  });
</script>

<style>
  /* Top Contact Bar Styling */
  .top-contact-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    transition: transform 0.3s ease, opacity 0.3s ease;
  }
  
  /* Adjust main navbar position when top bar is visible */
  #mainNav {
    top: 0;
    transition: top 0.3s ease;
  }
  
  /* Mobile navbar toggle styling */
  .navbar-toggler {
    border: none;
    padding: 0.5rem;
  }
  
  .navbar-toggler:focus {
    box-shadow: none;
  }
  
  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  }
  
  #mainNav.scrolled .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  }
  
  /* Dropdown menu styling */
  .dropdown-menu {
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    margin-top: 0.5rem;
  }
  
  .dropdown-item {
    padding: 0.625rem 1.25rem;
    transition: all 0.2s ease;
  }
  
  .dropdown-item:hover {
    background-color: #f8f9fa;
    padding-left: 1.5rem;
  }
  
  /* Responsive adjustments */
  @media (max-width: 991.98px) {
    .navbar-collapse {
      background: white;
      padding: 1rem;
      margin-top: 1rem;
      border-radius: 0.5rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-nav {
      margin-bottom: 1rem;
    }
    
    .nav-item {
      margin: 0.25rem 0;
    }
  }
</style>
