<?php
// Include auth functions
require_once __DIR__ . '/sess_auth.php';

// Safe settings
$short_name = $_settings->info('short_name') ?? 'VAP';
$logo       = $_settings->info('logo') ?? 'uploads/logo-1641262650.png';
$base       = base_url;

// Get authenticated user data
$auth_user = auth_user();
$is_logged_in = !empty($auth_user);
$user_name = $is_logged_in ? ($auth_user['firstname'] . ' ' . $auth_user['lastname']) : '';
$is_admin_user = is_admin();

// === REQUIRED URLS (your original mapping)
$hrefHome      = $base . '?page=home';
$hrefServices  = $base . '?page=services';
$hrefBook      = $base . '?page=home#appointment';
$hrefAbout     = $base . '?page=about_us';
$hrefContact   = $base . '?page=contact_us';
$hrefAdmin     = $base . 'admin';
?>

<!-- ========== NAVBAR (self-contained) ========== -->
<style>
  :root{ --ovnav-brand:#2563eb; --ovnav-accent:#10b981; --ovnav-ink:#0f172a; }

  .ovnav{
    --bg:rgba(255,255,255,.85); --border:rgba(15,23,42,.08);
    backdrop-filter:saturate(140%) blur(8px);
    background:var(--bg); border-bottom:1px solid var(--border);
    transition:background .2s ease, box-shadow .2s ease;
  }
  .ovnav.is-scrolled{ --bg:rgba(255,255,255,.95); box-shadow:0 8px 24px rgba(2,6,23,.08); }

  /* brand */
  .ovnav__logo{ width:34px;height:34px;object-fit:contain;border-radius:999px;background:rgba(37,99,235,.08);padding:4px; }
  .ovnav .navbar-brand{ color:var(--ovnav-ink); font-weight:600; }

  /* links */
  .ovnav .nav-link{
    color:var(--ovnav-ink); font-weight:500;
    padding:.75rem .8rem; position:relative; transition:color .15s ease;
  }
  .ovnav .nav-link:hover{ color:var(--ovnav-brand); }
  .ovnav .nav-link.active::after,
  .ovnav .nav-link:hover::after{
    content:""; position:absolute; left:10px; right:10px; bottom:6px; height:3px; border-radius:6px;
    background:linear-gradient(90deg,var(--ovnav-brand),var(--ovnav-accent));
  }

  /* buttons */
  .ovbtn{ display:inline-flex; align-items:center; gap:.5rem; }
  .ovbtn--pill{ border-radius:999px; padding:.55rem 1rem; }
  .ovbtn--soft{ border-radius:999px; padding:.5rem .9rem; background:rgba(37,99,235,.06); border:1px solid rgba(37,99,235,.3); }
  .ovbtn--soft:hover{ background:rgba(37,99,235,.12); }
  .ovnav-gap{ margin-left:.6rem; } /* space between Admin & Book Now */

  /* tiny icons (no external deps) */
  .ovic{ width:18px;height:18px;display:inline-block;background:currentColor;-webkit-mask-size:cover;mask-size:cover; }
  .ovic--user{ -webkit-mask-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>'); mask-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>'); }
  .ovic--cal{ -webkit-mask-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'); mask-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'); }

  /* toggler */
  .ovnav .navbar-toggler{ border:1px solid rgba(15,23,42,.2) !important; }
  .ovnav .navbar-toggler:focus{ box-shadow:0 0 0 .15rem rgba(37,99,235,.2); }

  /* spacer so content isn't hidden by fixed nav */
  .ovnav-spacer{ height:64px; } @media(min-width:992px){ .ovnav-spacer{ height:72px; } }
</style>

<nav id="ovnav" class="navbar navbar-expand-lg ovnav fixed-top">
  <div class="container">
    <!-- brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $hrefHome; ?>">
      <img src="<?php echo $base . $logo; ?>" alt="<?php echo htmlspecialchars($short_name); ?> logo" class="ovnav__logo">
      <span><?php echo htmlspecialchars($short_name); ?></span>
    </a>

    <!-- toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ovnavCollapse"
            aria-controls="ovnavCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- links -->
    <div class="collapse navbar-collapse" id="ovnavCollapse">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a href="<?php echo $base; ?>?page=home" class="nav-link fw-semibold" data-page="home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>?page=services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>?page=appointment">Book Appointment</a></li>
        <li class="nav-item"><a href="<?php echo $base; ?>?page=about_us" class="nav-link fw-semibold" data-page="about_us">About Us</a></li>
        <li class="nav-item"><a href="<?php echo $base; ?>?page=contact_us" class="nav-link fw-semibold" data-page="contact_us">Contact</a></li>

        <!-- admin + user menu + CTA -->
        <?php if ($is_logged_in): ?>
          <!-- User greeting -->
          <li class="nav-item dropdown ms-lg-3">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
               role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="ovic ovic--user me-1"></span>
              <span>Hi, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if ($is_admin_user): ?>
                <li><a class="dropdown-item" href="<?php echo $hrefAdmin; ?>">
                  <i class="fas fa-cog me-2"></i>Admin Panel
                </a></li>
                <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?php echo $base; ?>?page=my-appointments">
                <i class="fas fa-calendar me-2"></i>My Appointments
              </a></li>
              <li><a class="dropdown-item" href="<?php echo $base; ?>?page=my-pets">
                <i class="fas fa-paw me-2"></i>My Pets
              </a></li>
              <li><a class="dropdown-item" href="<?php echo $base; ?>?page=profile">
                <i class="fas fa-user me-2"></i>Profile
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="#" onclick="handleLogout()">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
              </a></li>
            </ul>
          </li>
        <?php else: ?>
          <!-- Login/Register buttons for guests -->
          <li class="nav-item ms-lg-3">
            <a class="btn ovbtn ovbtn--soft me-2" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
              <span class="ovic ovic--user"></span><span>Login</span>
            </a>
          </li>
        <?php endif; ?>
        
        <li class="nav-item ovnav-gap">
          <a class="btn btn-primary ovbtn ovbtn--pill" href="<?php echo $base; ?>?page=appointment">
            <span class="ovic ovic--cal"></span><span>Book Now</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Prevent overlap with fixed navbar -->
<div class="ovnav-spacer"></div>

<script>
(function(){
  const nav = document.getElementById('ovnav');
  if (!nav) return;

  // Blur/solid toggle
  const onScroll = () => {
    if (window.scrollY > 8) nav.classList.add('is-scrolled');
    else nav.classList.remove('is-scrolled');
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive:true });

  // Enhance active state only on the homepage (no interference with links)
  const isHome = /[?&]page=home(?![^#])/i.test(window.location.search);
  if (!isHome) return;

  // If in home: keep 'active' in sync for hash sections (#appointment, #about, #contact).
  const map = {
    '#home': document.querySelector('#home'),
    '#services': document.querySelector('#services'),
    '#appointment': document.querySelector('#appointment'),
    '#about': document.querySelector('#about'),
    '#contact': document.querySelector('#contact'),
  };
  const links = Array.from(nav.querySelectorAll('.nav-link'));
  const byId = {
    '#home': links.find(a=>a.getAttribute('href').endsWith('?page=home')) || null,
    '#services': links.find(a=>a.getAttribute('href').endsWith('#services')) || null,
    '#appointment': links.find(a=>a.getAttribute('href').endsWith('#appointment')) || null,
    '#about': links.find(a=>a.getAttribute('href').endsWith('#about')) || null,
    '#contact': links.find(a=>a.getAttribute('href').endsWith('#contact')) || null
  };

  const sections = Object.entries(map).filter(([,el])=>!!el);
  if (!sections.length) return;

  const io = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      const id = '#' + entry.target.id;
      const link = byId[id];
      if (entry.isIntersecting && link){
        links.forEach(l=>l.classList.remove('active'));
        link.classList.add('active');
      }
    });
  }, { rootMargin:'-10% 0px -40% 0px', threshold:[0.5] });

  sections.forEach(([,el])=> io.observe(el));
})();

// Logout functionality
function handleLogout() {
  if (confirm('Are you sure you want to logout?')) {
    // Create form for CSRF protection
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo $base; ?>auth_handler.php';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?php echo csrf_token(); ?>';
    form.appendChild(csrfInput);
    
    // Add action
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'logout';
    form.appendChild(actionInput);
    
    document.body.appendChild(form);
    form.submit();
  }
}
</script>

<!-- Login/Register Modal -->
<?php if (!$is_logged_in): ?>
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <ul class="nav nav-tabs nav-fill w-100" id="authTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-pane" 
                    type="button" role="tab" aria-controls="login-pane" aria-selected="true">Login</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-pane" 
                    type="button" role="tab" aria-controls="register-pane" aria-selected="false">Register</button>
          </li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="tab-content" id="authTabsContent">
          <!-- Login Tab -->
          <div class="tab-pane fade show active" id="login-pane" role="tabpanel" aria-labelledby="login-tab">
            <form id="loginForm" action="<?php echo $base; ?>auth_handler.php" method="POST">
              <input type="hidden" name="action" value="login">
              <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
              
              <div class="mb-3">
                <label for="loginEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="loginEmail" name="email" required>
              </div>
              
              <div class="mb-3">
                <label for="loginPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="loginPassword" name="password" required>
              </div>
              
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
            </form>
          </div>
          
          <!-- Register Tab -->
          <div class="tab-pane fade" id="register-pane" role="tabpanel" aria-labelledby="register-tab">
            <form id="registerForm" action="<?php echo $base; ?>auth_handler.php" method="POST">
              <input type="hidden" name="action" value="register">
              <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
              
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="registerFirstname" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="registerFirstname" name="firstname" required>
                </div>
                <div class="col-md-6">
                  <label for="registerLastname" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="registerLastname" name="lastname" required>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="registerEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="registerEmail" name="email" required>
              </div>
              
              <div class="mb-3">
                <label for="registerPhone" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="registerPhone" name="phone" required>
              </div>
              
              <div class="mb-3">
                <label for="registerPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="registerPassword" name="password" required minlength="6">
                <div class="form-text">Password must be at least 6 characters long.</div>
              </div>
              
              <div class="mb-3">
                <label for="registerConfirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="registerConfirmPassword" name="confirm_password" required>
              </div>
              
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Register</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['flash_message'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11000;">
  <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto text-<?php echo $_SESSION['flash_type'] ?? 'info'; ?>">
        <?php echo $_SESSION['flash_type'] === 'error' ? 'Error' : 'Success'; ?>
      </strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
      <?php if (isset($_SESSION['flash_errors'])): ?>
        <ul class="mb-0 mt-2">
          <?php foreach ($_SESSION['flash_errors'] as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php 
unset($_SESSION['flash_message']);
unset($_SESSION['flash_type']);
unset($_SESSION['flash_errors']);
endif; 
?>
<!-- ========== /NAVBAR ========== -->
