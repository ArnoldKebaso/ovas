<?php
// Safe settings
$short_name = $_settings->info('short_name') ?? 'VAP';
$logo       = $_settings->info('logo') ?? 'uploads/logo-1641262650.png';
$base       = base_url;

// === REQUIRED URLS (your original mapping)
$hrefHome      = $base . '?page=home';
$hrefServices  = $base . '?page=services';
$hrefBook      = $base . '?page=home#appointment';
$hrefAbout     = $base . '?page=home#about';
$hrefContact   = $base . '?page=home#contact';
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
        <li class="nav-item"><a class="nav-link" href="<?php echo $hrefHome; ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $hrefServices; ?>">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $hrefBook; ?>">Book Appointment</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $hrefAbout; ?>">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $hrefContact; ?>">Contact</a></li>

        <!-- admin + CTA -->
        <li class="nav-item ms-lg-3">
          <a class="btn ovbtn ovbtn--soft" href="<?php echo $hrefAdmin; ?>">
            <span class="ovic ovic--user"></span><span>Admin</span>
          </a>
        </li>
        <li class="nav-item ovnav-gap">
          <a class="btn btn-primary ovbtn ovbtn--pill" href="<?php echo $hrefBook; ?>">
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
</script>
<!-- ========== /NAVBAR ========== -->
