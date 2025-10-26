<?php
// footer.php — Drop-in replacement
// Values below can be pulled from your settings if you have them.
// For now, they’re simple PHP fallbacks you can change anytime.
$brand      = isset($_settings) ? ($_settings->info('short_name') ?: 'VAP') : 'OVAS';
$phone      = isset($_settings) ? ($_settings->info('contact')     ?: '07123456789') : '07123456789';
$email      = isset($_settings) ? ($_settings->info('email')       ?: 'clinic@example.com') : 'clinic@example.com';
$address    = isset($_settings) ? ($_settings->info('address')     ?: 'Nairobi, Kenya') : 'Nairobi, Kenya';
$hours_line1= 'Mon – Sat: 8:00 AM – 6:00 PM';
$hours_line2= 'Sunday: Emergency Only';
?>
<style>
/*** ===== Site Footer (scoped) ===== ***/
.site-footer{position:relative; color:#eaf0ff; background:radial-gradient(1200px 700px at 10% -10%, #1649ff22, transparent), linear-gradient(180deg, #091b3a 0%, #07162f 100%);}
.site-footer .container{max-width:1200px; margin:0 auto; padding: clamp(28px, 6vw, 72px) 16px;}
.site-footer a{color:#eaf0ff; text-decoration:none}
.site-footer a:hover{opacity:.9; text-decoration:underline}

/* top grid */
.footer-grid{display:grid; grid-template-columns: 1.2fr 1fr 1fr 1.2fr; gap: clamp(18px, 2.8vw, 34px);}
@media (max-width: 980px){ .footer-grid{grid-template-columns:1fr 1fr} }
@media (max-width: 640px){ .footer-grid{grid-template-columns:1fr} }

/* brand */
.brand-wrap h3{margin:0 0 8px; font-size:22px; font-weight:900; letter-spacing:.3px}
.brand-wrap p{margin:0; color:#cfe1ff; line-height:1.6}
.logo-badge{display:inline-flex; align-items:center; gap:10px; padding:8px 12px; background:#0f2b63; border:1px solid #25407a; border-radius:999px; margin-bottom:10px; box-shadow:inset 0 0 0 1px #1b3a74}
.logo-dot{width:26px; height:26px; border-radius:8px; background:linear-gradient(135deg,#2f65f6,#22c1c3); display:grid; place-items:center; font-weight:900}

/* titles */
.ft-title{margin:0 0 8px; font-size:14px; font-weight:800; letter-spacing:.5px; text-transform:uppercase; color:#cfe1ff}
.ft-underline{width:48px; height:3px; background:linear-gradient(90deg,#2f65f6,#22c1c3); border-radius:99px; margin:6px 0 14px}

/* lists */
.ft-list{list-style:none; padding:0; margin:0}
.ft-list li{margin:8px 0}
.ft-list a{color:#eaf0ff; text-decoration:none}
.ft-list a:hover{opacity:.9}

/* service columns pairing */
.cols-2{display:grid; grid-template-columns:1fr 1fr; gap:6px 28px}
@media (max-width:640px){ .cols-2{grid-template-columns:1fr} }

/* contact lines */
.contact-line{display:flex; gap:10px; align-items:flex-start; padding:10px 0; border-top:1px dashed #244269}
.contact-line:first-child{border-top:0}
.icon{width:18px; margin-top:2px; color:#9fc2ff}

/* socials */
.socials{display:flex; gap:12px; margin-top:12px}
.soc{width:38px; height:38px; border-radius:12px; display:grid; place-items:center; color:#eaf0ff; background:#0f2b63; border:1px solid #25407a; transition:.2s}
.soc:hover{transform:translateY(-2px); background:#153478}

/* bottom bar */
.footer-bottom{border-top:1px solid #112448; margin-top: clamp(18px, 3.4vw, 40px);}
.bottom-row{display:flex; gap:14px; align-items:center; justify-content:space-between; padding:16px 0}
.bottom-links{display:flex; gap:16px; flex-wrap:wrap}
.badge-mini{display:inline-flex; align-items:center; gap:8px; background:#0f2b63; border:1px solid #25407a; padding:6px 10px; border-radius:999px; color:#cfe1ff}

/* small helper */
.muted{color:#cfe1ff; opacity:.9}
</style>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <!-- Brand / About -->
      <div class="brand-wrap">
        <div class="logo-badge"><span class="logo-dot">🐾</span> <strong><?php echo htmlspecialchars($brand) ?></strong></div>
        <p>Professional veterinary care for your beloved pets. We provide comprehensive health services with compassion and expertise.</p>
        <div class="socials">
          <a class="soc" href="#" aria-label="Facebook" title="Facebook">
            <!-- fb -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 20v-7h2.5l.5-3h-3V8.1c0-.9.3-1.5 1.6-1.5H17V4.1C16.6 4.1 15.7 4 14.7 4 12.4 4 11 5.2 11 7.7V10H8.5v3H11v7h2.5z"/></svg>
          </a>
          <a class="soc" href="#" aria-label="Instagram" title="Instagram">
            <!-- ig -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11zm0 2a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm5.8-.9a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
          </a>
          <a class="soc" href="#" aria-label="X / Twitter" title="X">
            <!-- x -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M4 3l7.2 9.1L4.6 21h2.4l5.7-6.8 4.6 6.8H20l-7.3-10L19.4 3h-2.4l-5 6-4.1-6H4z"/></svg>
          </a>
          <a class="soc" href="#" aria-label="WhatsApp" title="WhatsApp">
            <!-- wa -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 12a8 8 0 0 1-12 6.9L4 20l1.1-3.9A8 8 0 1 1 20 12zm-8-6a6 6 0 0 0-5.2 9l-.7 2.5 2.6-.7A6 6 0 1 0 12 6zm3.7 8.5c-.1.2-.6.4-.8.5-.2 0-.3.1-.5 0s-.4 0-.6-.1a9 9 0 0 1-3.2-2 8.6 8.6 0 0 1-2-3.2c0-.2 0-.4.1-.6l.3-.5c.1-.2.3-.4.5-.3h.6c.1 0 .5.1.5.4l.4 1c.1.2.1.4 0 .6l-.2.3-.2.3v.2c0 .1 0 .1.1.3l.3.4.4.4.4.3.3.1h.2c.1 0 .2 0 .3-.2l.4-.4c.1-.1.2-.1.3-.1h.5l.9.4c.2.1.3.2.3.3l.1.6z"/></svg>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div>
        <h4 class="ft-title">Quick Links</h4>
        <div class="ft-underline"></div>
        <ul class="ft-list">
          <li><a href="?page=home">Home</a></li>
          <li><a href="?page=services">Services</a></li>
          <li><a href="?page=book_appointment">Book Appointment</a></li>
          <li><a href="?page=about_us">About Us</a></li>
          <li><a href="?page=contact_us">Contact</a></li>
        </ul>
      </div>

      <!-- Our Services -->
      <div>
        <h4 class="ft-title">Our Services</h4>
        <div class="ft-underline"></div>
        <div class="cols-2">
          <ul class="ft-list">
            <li><a href="?page=services#vaccination">Vaccination</a></li>
            <li><a href="?page=services#grooming">Grooming</a></li>
            <li><a href="?page=services#surgery">Surgery</a></li>
          </ul>
          <ul class="ft-list">
            <li><a href="?page=services#deworming">Deworming</a></li>
            <li><a href="?page=services#dental">Dental Care</a></li>
          </ul>
        </div>
      </div>

      <!-- Contact Info -->
      <div>
        <h4 class="ft-title">Contact Info</h4>
        <div class="ft-underline"></div>

        <div class="contact-line">
          <span class="icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#9fc2ff"><path d="M12 2l9 7v13h-6v-7H9v7H3V9l9-7z"/></svg>
          </span>
          <div>
            <div class="muted">Address</div>
            <strong><?php echo htmlspecialchars($address) ?></strong>
          </div>
        </div>

        <div class="contact-line">
          <span class="icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#9fc2ff"><path d="M6.6 10.8a15.7 15.7 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.4.6 3.7.6.6 0 1 .4 1 1v3.6c0 .6-.4 1-1 1A18.9 18.9 0 0 1 3 5c0-.6.4-1 1-1h3.6c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.7.1.4 0 .8-.3 1.1l-2.3 2z"/></svg>
          </span>
          <div>
            <div class="muted">Phone</div>
            <a href="tel:<?php echo preg_replace('/\s+/', '', $phone) ?>"><?php echo htmlspecialchars($phone) ?></a>
          </div>
        </div>

        <div class="contact-line">
          <span class="icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#9fc2ff"><path d="M12 13L2 6V5l10 7 10-7v1l-10 7z"/><path d="M2 7v12h20V7l-10 7L2 7z"/></svg>
          </span>
          <div>
            <div class="muted">Email</div>
            <a href="mailto:<?php echo htmlspecialchars($email) ?>"><?php echo htmlspecialchars($email) ?></a>
          </div>
        </div>

        <div class="contact-line">
          <span class="icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#9fc2ff"><path d="M12 7V3M12 21v-4M7 12H3m18 0h-4M5.6 5.6l2.8 2.8m7.2 7.2 2.8 2.8M5.6 18.4l2.8-2.8m7.2-7.2 2.8-2.8"/></svg>
          </span>
          <div>
            <div class="muted">Hours</div>
            <div><?php echo $hours_line1 ?></div>
            <div><?php echo $hours_line2 ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom bar -->
    <div class="footer-bottom">
      <div class="bottom-row">
        <div class="badge-mini">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="#9fc2ff" aria-hidden="true"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 11h5v2h-7V7h2v6z"/></svg>
          <span id="year"><?php echo date('Y') ?></span> © <?php echo htmlspecialchars($brand) ?>. All rights reserved.
        </div>
        <div class="bottom-links">
          <a href="?page=privacy">Privacy</a>
          <span>•</span>
          <a href="?page=terms">Terms</a>
          <span>•</span>
          <a href="?page=contact_us">Support</a>
        </div>
      </div>
    </div>
  </div>
</footer>
