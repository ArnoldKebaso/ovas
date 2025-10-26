<?php
/**
 * topBarNav.php — safe, styled, drop-in navbar
 * Fixes: infinite load from recursive includes + missing styles.
 */
if (defined('OVAS_TOPBAR_RENDERED')) { return; } // <-- Prevents recursion / multiple renders
define('OVAS_TOPBAR_RENDERED', true);

// Active page (used for highlighting)
$active = isset($_GET['page']) ? trim($_GET['page']) : 'home';

// Brand / routes (change if your router uses different slugs)
$brand = 'VAP';
$routes = [
  'home'            => '?page=home',
  'services'        => '?page=services',
  'book_appointment'=> '?page=book_appointment',
  'about_us'        => '?page=about_us',
  'contact_us'      => '?page=contact_us',
  'admin'           => 'admin/login.php',
];
?>
<style>
/* ===== Top bar (scoped) ===== */
#ovas-topbar { position:sticky; top:0; z-index:1000; }
.ovas-nav {
  background:#ffffff;
  border-bottom:1px solid #eef2f7;
  box-shadow:0 0 0 0 rgba(0,0,0,0);
  transition: box-shadow .2s ease, border-color .2s ease, background-color .2s ease;
}
.ovas-nav.stuck {
  box-shadow:0 8px 22px rgba(15,23,42,.08);
  border-color:#e8eef8;
}
.ovas-nav .inner {
  max-width:1200px; margin:0 auto; padding:10px 16px;
  display:flex; align-items:center; justify-content:space-between; gap:14px;
}

/* Brand */
.ovas-brand {
  display:flex; align-items:center; gap:10px; text-decoration:none; color:#111827;
}
.ovas-logo {
  width:34px; height:34px; border-radius:10px;
  display:grid; place-items:center; color:#fff;
  background:linear-gradient(135deg,#2f65f6,#22c1c3);
  box-shadow:0 6px 14px rgba(47,101,246,.25);
  font-weight:900; font-size:16px;
}
.ovas-name { font-weight:900; letter-spacing:.3px; }

/* Menu */
.ovas-menu { display:flex; align-items:center; gap:8px; }
.ovas-list {
  list-style:none; margin:0; padding:0; display:flex; gap:6px;
}
.ovas-link {
  display:inline-block; text-decoration:none; color:#1f2937;
  padding:10px 12px; border-radius:12px; font-weight:600;
}
.ovas-link:hover { background:#f5f7ff; }
.ovas-link.active {
  color:#1f4ef0; background:#eef3ff; border:1px solid #dfe7ff;
}

/* Actions */
.ovas-actions { display:flex; align-items:center; gap:10px; }
.btn {
  display:inline-flex; align-items:center; gap:8px;
  border:0; cursor:pointer; font-weight:800; border-radius:999px;
  text-decoration:none; padding:10px 14px; transition:.18s ease;
}
.btn-outline {
  background:#fff; color:#1f4ef0; border:2px solid #dfe7ff;
}
.btn-outline:hover { background:#f3f6ff; }
.btn-primary {
  background:linear-gradient(180deg,#2f65f6,#1f57f4); color:#fff;
  box-shadow:0 10px 22px rgba(47,101,246,.22);
}
.btn-primary:hover { transform:translateY(-1px); box-shadow:0 14px 28px rgba(47,101,246,.28); }

/* Icons (simple emoji fallback — replace with your icon font if you want) */
.ico { font-style:normal; }

/* Hamburger (mobile) */
.ovas-burger {
  display:none; border:0; background:#fff; width:42px; height:42px;
  border-radius:12px; border:1px solid #e6ecf7; cursor:pointer;
}
.ovas-burger span, .ovas-burger::before, .ovas-burger::after {
  content:""; display:block; height:2px; background:#111827; margin:6px 10px; border-radius:2px;
}
.ovas-burger::before{ margin-top:12px }
.ovas-burger::after{ margin-bottom:12px }

/* Responsive */
@media (max-width: 980px) {
  .ovas-list { display:none; position:absolute; left:0; right:0; top:64px; background:#fff;
    padding:10px; border-bottom:1px solid #eef2f7; box-shadow:0 8px 22px rgba(15,23,42,.06); }
  .ovas-list.open { display:block; }
  .ovas-list li { padding:4px }
  .ovas-burger { display:block; }
}
</style>

<div id="ovas-topbar">
  <nav class="ovas-nav" id="ovasNav">
    <div class="inner">
      <!-- Brand -->
      <a class="ovas-brand" href="<?php echo $routes['home'] ?>">
        <span class="ovas-logo">🐾</span>
        <span class="ovas-name"><?php echo htmlspecialchars($brand) ?></span>
      </a>

      <!-- Menu -->
      <div class="ovas-menu">
        <ul class="ovas-list" id="ovasMenuList">
          <li><a class="ovas-link <?php echo $active==='home'?'active':''?>" href="<?php echo $routes['home']?>">Home</a></li>
          <li><a class="ovas-link <?php echo $active==='services'?'active':''?>" href="<?php echo $routes['services']?>">Services</a></li>
          <li><a class="ovas-link <?php echo $active==='book_appointment'?'active':''?>" href="<?php echo $routes['book_appointment']?>">Book Appointment</a></li>
          <li><a class="ovas-link <?php echo $active==='about_us'?'active':''?>" href="<?php echo $routes['about_us']?>">About Us</a></li>
          <li><a class="ovas-link <?php echo $active==='contact_us'?'active':''?>" href="<?php echo $routes['contact_us']?>">Contact</a></li>
        </ul>

        <!-- Actions (Admin + Book Now) -->
        <div class="ovas-actions">
          <a class="btn btn-outline" href="<?php echo $routes['admin'] ?>">
            <span class="ico">👥</span> Admin
          </a>
          <a class="btn btn-primary" href="<?php echo $routes['book_appointment'] ?>">
            <span class="ico">📅</span> Book Now
          </a>
          <button class="ovas-burger" id="ovasBurger" aria-label="Toggle menu"></button>
        </div>
      </div>
    </div>
  </nav>
</div>

<script>
// Sticky shadow
(function(){
  const nav = document.getElementById('ovasNav');
  const onScroll = () => nav.classList.toggle('stuck', window.scrollY > 6);
  onScroll(); window.addEventListener('scroll', onScroll, {passive:true});
})();

// Mobile toggle
(function(){
  const btn = document.getElementById('ovasBurger');
  const list = document.getElementById('ovasMenuList');
  if(!btn || !list) return;
  btn.addEventListener('click', () => list.classList.toggle('open'));
  // Close on link click (mobile)
  list.querySelectorAll('a').forEach(a => a.addEventListener('click', () => list.classList.remove('open')));
})();
</script>
