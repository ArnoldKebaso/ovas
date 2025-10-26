<?php
// Safe fallbacks from System Settings
$short_name = $_settings->info('short_name') ?? 'VAP';
$sys_name   = $_settings->info('name') ?? 'Veterinary Appointment System';
$address    = $_settings->info('address') ?? 'Nairobi';
$email      = $_settings->info('email') ?? 'pet@gmail.com';
$phone      = $_settings->info('contact') ?? '07123456789';
$year       = date('Y');

// Optional logo path from settings (fallback to uploads)
$logo = $_settings->info('logo') ?? 'uploads/logo-1641262650.png';
?>

<footer class="ovas-footer text-light mt-auto">
  <div class="ovas-footer__top">
    <div class="container">
      <div class="row g-4 justify-content-between">
        <!-- Brand / About -->
        <div class="col-12 col-lg-4">
          <div class="d-flex align-items-center gap-3 mb-3">
            <img src="<?php echo base_url.$logo; ?>" alt="<?php echo htmlspecialchars($short_name); ?> logo" class="ovas-footer__logo">
            <h5 class="mb-0"><?php echo htmlspecialchars($short_name); ?></h5>
          </div>
          <p class="text-opacity-75 mb-4">
            Professional veterinary care for your beloved pets.
            We provide comprehensive health services with compassion and expertise.
          </p>

          <div class="d-flex gap-2">
            <!-- Social buttons (free, CDN SVGs from SimpleIcons) -->
            <a class="ovas-social" href="#" aria-label="Facebook">
              <img src="https://cdn.simpleicons.org/facebook/ffffff" alt="Facebook" width="18" height="18">
            </a>
            <a class="ovas-social" href="#" aria-label="Instagram">
              <img src="https://cdn.simpleicons.org/instagram/ffffff" alt="Instagram" width="18" height="18">
            </a>
            <a class="ovas-social" href="#" aria-label="X">
              <img src="https://cdn.simpleicons.org/x/ffffff" alt="X (Twitter)" width="18" height="18">
            </a>
            <a class="ovas-social" href="#" aria-label="WhatsApp">
              <img src="https://cdn.simpleicons.org/whatsapp/ffffff" alt="WhatsApp" width="18" height="18">
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-6 col-lg-2">
          <h6 class="ovas-footer__title">Quick Links</h6>
          <ul class="ovas-links">
            <li><a href="<?php echo base_url ?>?page=home">Home</a></li>
            <li><a href="<?php echo base_url ?>?page=services">Services</a></li>
            <li><a href="<?php echo base_url ?>?page=appointment">Book Appointment</a></li>
            <li><a href="<?php echo base_url ?>?page=about_us">About Us</a></li>
            <li><a href="<?php echo base_url ?>?page=contact_us">Contact</a></li>
          </ul>
        </div>

        <!-- Our Services -->
        <div class="col-6 col-lg-3">
          <h6 class="ovas-footer__title">Our Services</h6>
          <ul class="ovas-links ovas-links--two">
            <li><a href="<?php echo base_url ?>?page=services#vaccination">Vaccination</a></li>
            <li><a href="<?php echo base_url ?>?page=services#deworming">Deworming</a></li>
            <li><a href="<?php echo base_url ?>?page=services#grooming">Grooming</a></li>
            <li><a href="<?php echo base_url ?>?page=services#dental">Dental Care</a></li>
            <li><a href="<?php echo base_url ?>?page=services#surgery">Surgery</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="col-12 col-lg-3">
          <h6 class="ovas-footer__title">Contact Info</h6>
          <ul class="ovas-contact list-unstyled">
            <li>
              <span class="ovas-contact__label">Address</span>
              <span class="ovas-contact__value"><?php echo htmlspecialchars($address); ?></span>
            </li>
            <li>
              <span class="ovas-contact__label">Phone</span>
              <a class="ovas-contact__value" href="tel:<?php echo preg_replace('/\\D+/', '', $phone); ?>">
                <?php echo htmlspecialchars($phone); ?>
              </a>
            </li>
            <li>
              <span class="ovas-contact__label">Email</span>
              <a class="ovas-contact__value" href="mailto:<?php echo htmlspecialchars($email); ?>">
                <?php echo htmlspecialchars($email); ?>
              </a>
            </li>
            <li class="mt-2">
              <span class="ovas-contact__label">Hours</span>
              <span class="ovas-contact__value">Mon – Sat: 8:00 AM – 6:00 PM<br>Sunday: Emergency Only</span>
            </li>
          </ul>
        </div>
      </div> <!-- /row -->
    </div>
  </div>

  <div class="ovas-footer__bottom">
    <div class="container d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
      <div class="small text-opacity-75">
        © <?php echo $year; ?> <?php echo htmlspecialchars(trim($sys_name)); ?>. All rights reserved.
      </div>
      <div class="d-flex align-items-center gap-3 small">
        <a class="ovas-policy" href="#">Privacy Policy</a>
        <span class="text-opacity-50">•</span>
        <a class="ovas-policy" href="#">Terms of Service</a>
        <span class="text-opacity-50">•</span>
        <a class="ovas-policy" href="<?php echo base_url ?>admin" target="_blank" rel="noopener">Admin</a>
      </div>
    </div>
  </div>
</footer>
<style>
 /* ===== OVAS Footer ===== */
.ovas-footer{
  --footer-bg: #0f172a;      /* slate-900 */
  --footer-bg-grad: radial-gradient(1200px 600px at 20% -10%, rgba(37,99,235,.15), transparent),
                     radial-gradient(900px 500px at 80% 10%, rgba(16,185,129,.12), transparent);
  --footer-text: #e5e7eb;    /* gray-200 */
  --footer-muted: #94a3b8;   /* slate-400 */
  --footer-border: rgba(148,163,184,.15);

  color: var(--footer-text);
  background: var(--footer-bg);
  position: relative;
}

.ovas-footer__top{
  padding: 56px 0 28px;
  background-image: var(--footer-bg-grad);
  border-top: 1px solid var(--footer-border);
}

.ovas-footer__bottom{
  padding: 16px 0 20px;
  border-top: 1px solid var(--footer-border);
  background: rgba(2,6,23,.35);
  backdrop-filter: saturate(120%) blur(6px);
}

/* Brand */
.ovas-footer__logo{
  width: 44px; height: 44px;
  object-fit: contain;
  border-radius: 999px;
  background: rgba(255,255,255,.06);
  padding: 6px;
}

/* Column headings */
.ovas-footer__title{
  font-weight: 600;
  letter-spacing: .2px;
  margin-bottom: 14px;
  position: relative;
}
.ovas-footer__title::after{
  content:"";
  display:block;
  width:36px; height:3px;
  border-radius: 6px;
  margin-top: 8px;
  background: linear-gradient(90deg,#22d3ee,#22c55e);
}

/* Link lists */
.ovas-links{
  list-style: none;
  margin: 0;
  padding: 0;
  /* Perfect vertical rhythm */
  display: grid;
  gap: 10px;
}
.ovas-links--two{
  grid-template-columns: 1fr;
}
@media (min-width: 992px){
  .ovas-links--two{
    grid-template-columns: repeat(2,minmax(0,1fr));
    column-gap: 16px;
  }
}

.ovas-links a{
  color: var(--footer-text);
  text-decoration: none;
  opacity: .8;
  transition: color .2s ease, opacity .2s ease, transform .2s ease;
}
.ovas-links a:hover{
  color: #93c5fd; /* light blue */
  opacity: 1;
  transform: translateX(2px);
}

/* Contact info */
.ovas-contact li{
  padding: 8px 0;
  border-bottom: 1px dashed var(--footer-border);
}
.ovas-contact li:last-child{ border-bottom: 0; }
.ovas-contact__label{
  display:block;
  font-size: .8rem;
  color: var(--footer-muted);
}
.ovas-contact__value{
  color: var(--footer-text);
  text-decoration: none;
  word-break: break-word;
}
.ovas-contact a.ovas-contact__value:hover{ color:#93c5fd; }

/* Social buttons */
.ovas-social{
  --size: 40px;
  width: var(--size);
  height: var(--size);
  display: inline-flex;
  align-items: center;
  padding: 8px;
  justify-content: center;
  border-radius: 999px;
  background: rgba(255,255,255,.08);
  border: 1px solid var(--footer-border);
  transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
}
.ovas-social img{ display:block; }
.ovas-social:hover{
  transform: translateY(-2px) scale(1.03);
  background: linear-gradient(135deg, rgba(59,130,246,.25), rgba(16,185,129,.25));
  box-shadow: 0 10px 18px rgba(2,6,23,.35);
}

/* Policy links */
.ovas-policy{
  color: var(--footer-text);
  opacity: .8;
  text-decoration: none;
  transition: opacity .2s ease, color .2s ease;
}
.ovas-policy:hover{
  opacity: 1;
  color: #93c5fd;
}

/* Spacing helpers (footer context) */
.ovas-footer p{ margin-bottom: 0; }
.ovas-footer .text-opacity-75{ color: rgba(229,231,235,.75) !important; }
.ovas-footer .text-opacity-50{ color: rgba(229,231,235,.5) !important; }

/* Tighter on phones */
@media (max-width: 575.98px){
  .ovas-footer__top{ padding: 40px 0 20px; }
}
