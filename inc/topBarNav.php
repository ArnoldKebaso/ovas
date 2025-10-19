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
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm" id="mainNav" style="z-index: 1030;">
  <div class="container">
    <!-- Brand -->
    <a href="./" class="navbar-brand d-flex align-items-center">
      <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="<?= $_settings->info('name') ?>" class="rounded-circle" style="height: 45px; width: 45px; object-fit: cover; margin-right: 0.75rem;">
      <span class="fw-bold text-primary"><?= $_settings->info('short_name') ?></span>
    </a>

    <!-- Mobile Toggler -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="# navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <a href="#home" class="nav-link active fw-semibold">Home</a>
        </li>
        <li class="nav-item">
          <a href="#services" class="nav-link fw-semibold">Services</a>
        </li>
        <li class="nav-item">
          <a href="#appointment" class="nav-link fw-semibold">Book Appointment</a>
        </li>
        <li class="nav-item">
          <a href="#about" class="nav-link fw-semibold">About Us</a>
        </li>
        <li class="nav-item">
          <a href="#contact" class="nav-link fw-semibold">Contact</a>
        </li>
        <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') != 1): ?>
        <li class="nav-item">
          <a href="./?page=profile" class="nav-link fw-semibold">My Profile</a>
        </li>
        <?php endif; ?>
      </ul>

      <!-- Right Side Actions -->
      <div class="d-flex align-items-center gap-3">
        <?php if($_settings->userdata('id') > 0): ?>
          <!-- Logged In User -->
          <div class="dropdown">
            <button class="btn btn-light rounded-pill dropdown-toggle d-flex align-items-center gap-2 shadow-sm" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?= validate_image($_settings->userdata('avatar')) ?>" alt="Avatar" class="rounded-circle" style="height: 32px; width: 32px; object-fit: cover;">
              <span class="fw-semibold"><?= !empty($_settings->userdata('firstname')) ? $_settings->userdata('firstname') : $_settings->userdata('username') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="./?page=profile"><i class="fas fa-user me-2 text-primary"></i>My Profile</a></li>
              <li><a class="dropdown-item" href="./?page=appointments"><i class="fas fa-calendar-alt me-2 text-info"></i>My Appointments</a></li>
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
            <a href="./admin" class="btn btn-outline-primary rounded-pill px-4">
              <i class="fas fa-user-shield me-1"></i> Admin
            </a>
            <a href="#appointment" class="btn btn-primary rounded-pill px-4 shadow-sm">
              <i class="fas fa-calendar-plus me-1"></i> Book Now
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<style>
  /* Enhanced Navigation Styles */
  #mainNav {
    padding: 0.75rem 0;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(10px);
  }
  
  #mainNav.scrolled {
    padding: 0.5rem 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
  }
  
  #mainNav .nav-link {
    color: #334155;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
    position: relative;
  }
  
  #mainNav .nav-link:hover {
    color: #2563eb;
  }
  
  #mainNav .nav-link.active {
    color: #2563eb;
    font-weight: 600;
  }
  
  #mainNav .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 1rem;
    right: 1rem;
    height: 3px;
    background: linear-gradient(90deg, #2563eb, #10b981);
    border-radius: 3px 3px 0 0;
  }
  
  .navbar-toggler:focus {
    box-shadow: none;
  }
  
  .dropdown-menu {
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border-radius: 0.75rem;
    margin-top: 0.5rem;
    padding: 0.5rem;
  }
  
  .dropdown-item {
    padding: 0.625rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .dropdown-item:hover {
    background-color: #f1f5f9;
    transform: translateX(4px);
  }
  
  /* Mobile Styles */
  @media (max-width: 991.98px) {
    #mainNav .navbar-collapse {
      background: white;
      padding: 1.5rem;
      margin-top: 1rem;
      border-radius: 1rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }
    
    #mainNav .navbar-nav {
      margin-bottom: 1rem;
    }
    
    #mainNav .nav-item {
      margin: 0.25rem 0;
    }
    
    #mainNav .nav-link.active::after {
      display: none;
    }
  }
  
  /* Add spacing for fixed navbar */
  body {
    padding-top: 80px;
  }
</style>