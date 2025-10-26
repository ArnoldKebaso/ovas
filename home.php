<?php
// HOME (single-file: PHP + HTML + CSS + JS) — drop-in replacement
$short_name = isset($_settings) ? ($_settings->info('short_name') ?: 'VAP') : 'VAP';
$phone      = isset($_settings) ? ($_settings->info('contact') ?: '07123456789') : '07123456789';
$base_url   = './';

// Hero images (replace with your local files later if you want)
$hero_images = [
  'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=1600&auto=format&fit=crop', // vet with dog
  // 'https://images.unsplash.com/photo-1558944351-c12a1fda8c49?q=80&w=1600&auto=format&fit=crop',
  'https://unsplash.com/photos/brown-tabby-cat-7GX5aICb5i4',
  'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=1600&auto=format&fit=crop'  // puppy smiling
];
?>
<style>
  :root{
    --brand:#2563eb;          /* blue */
    --brand-2:#22c1c3;        /* teal */
    --text:#1f2937;
    --muted:#6b7280;
    --bg:#f5f7fb;
    --card:#ffffff;
    --ring: 0 10px 30px rgba(37,99,235,.15);
    --ring-soft: 0 12px 20px rgba(31,41,55,.06);
    --radius: 18px;
  }
  .page{background:#fff;}

  /* ===== HERO ===== */
  .hero{
    position:relative; overflow:hidden; border-bottom:1px solid #edf2ff;
    background: linear-gradient(135deg, rgba(37,99,235,.06), rgba(34,193,195,.08));
  }
  .hero-slider{ position:relative; height: 64vh; min-height: 520px; max-height: 800px; }
  .hero-slide{
    position:absolute; inset:0; background-size:cover; background-position:center;
    opacity:0; transform: scale(1.05); transition: opacity 900ms ease, transform 1600ms ease;
  }
  .hero-slide.is-active{ opacity:1; transform: scale(1); }
  .hero-scrim{
    position:absolute; inset:0;
    background: radial-gradient(1200px 600px at 20% 40%, rgba(0,0,0,.35), rgba(0,0,0,.55));
    mix-blend-multiply;
  }
  .hero-inner{
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    padding: clamp(20px, 4vw, 48px);
  }
  .hero-card{
    width:min(1100px, 96%); color:#fff; display:grid; grid-template-columns: 1.1fr 0.9fr; gap:28px;
    align-items:center;
  }
  @media (max-width: 980px){ .hero-card{ grid-template-columns: 1fr; } }

  .hero-copy h1{
    font-size: clamp(36px, 5vw, 64px); line-height:1.05; margin: 0 0 12px;
    font-weight:800; letter-spacing:.2px;
  }
  .hero-copy .accent{ color:#ff6b6b; }
  .hero-copy p{ margin: 6px 0 22px; color: #eaf2ff; font-size: clamp(16px, 1.7vw, 18px); }

  .hero-actions{ display:flex; gap:12px; flex-wrap:wrap; }
  .btn{
    display:inline-flex; align-items:center; gap:10px; border-radius: 999px;
    padding: 12px 18px; font-weight:700; border:2px solid transparent; cursor:pointer;
    transition: transform .12s ease, filter .12s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
  }
  .btn:active{ transform: translateY(1px) scale(.99); }
  .btn-primary{ color:#fff; background:var(--brand);
    box-shadow: var(--ring); }
  .btn-primary:hover{ filter:brightness(1.06); }
  .btn-ghost{ color:#fff; background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.28); }
  .btn-ghost:hover{ background:rgba(255,255,255,.16); }

  .hero-badge{
    display:inline-flex; align-items:center; gap:8px;
    background: rgba(255,255,255,.14); color:#fff; padding:8px 12px; border-radius:999px;
    font-weight:600; margin-bottom:10px; backdrop-filter: blur(4px);
  }
  .hero-badge svg{ width:18px; height:18px; }

  .hero-panel{
    background: rgba(255,255,255,.92); color:var(--text);
    border-radius: var(--radius); padding: 18px; box-shadow: var(--ring-soft);
    backdrop-filter: blur(6px);
  }
  .hero-panel h3{ margin:8px 0 6px; font-size:20px; }
  .hero-quick{
    display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px;
  }
  .chip{
    display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:14px;
    background:#fff; box-shadow: var(--ring-soft); transition: transform .15s ease, box-shadow .2s ease;
  }
  .chip:hover{ transform: translateY(-2px); box-shadow: 0 16px 28px rgba(31,41,55,.08); }
  .chip svg{ width:18px; height:18px; color:var(--brand); }

  .dots{ position:absolute; bottom:14px; left:50%; transform:translateX(-50%); display:flex; gap:8px; }
  .dot{
    width:8px; height:8px; border-radius:999px; background: rgba(255,255,255,.55);
    cursor:pointer; transition: transform .15s ease, background .15s ease;
  }
  .dot.is-active{ background:#fff; transform: scale(1.25); }

  /* ===== Sections ===== */
  .section{ padding: clamp(36px, 5vw, 72px) 16px; background:#fff; }
  .container{ max-width:1180px; margin:0 auto; }
  .section-header{ text-align:center; margin-bottom:28px; }
  .section h2{ font-size: clamp(28px, 3.2vw, 42px); color:var(--brand); margin:0 0 8px; }
  .section p.lead{ color:var(--muted); max-width:760px; margin:0 auto; }

  /* Services cards (sample) */
  .grid{ display:grid; grid-template-columns: repeat(4,1fr); gap:18px; }
  @media (max-width: 1100px){ .grid{ grid-template-columns: repeat(3,1fr); } }
  @media (max-width: 780px){ .grid{ grid-template-columns: repeat(2,1fr); } }
  @media (max-width: 520px){ .grid{ grid-template-columns: 1fr; } }

  .card{
    background: var(--card); border-radius: 20px; padding: 18px;
    box-shadow: var(--ring-soft); transition: transform .16s ease, box-shadow .22s ease, border-color .2s ease;
    border:1px solid #edf1f7;
  }
  .card:hover{ transform: translateY(-6px); box-shadow: 0 22px 38px rgba(31,41,55,.10); }
  .card-icon{
    width:46px; height:46px; border-radius:12px; background: linear-gradient(135deg, var(--brand), var(--brand-2));
    display:flex; align-items:center; justify-content:center; color:#fff; box-shadow: var(--ring);
  }
  .card h4{ margin:12px 0 6px; }
  .card p{ color:var(--muted); font-size:14.5px; }

  /* Appointment teaser card */
  .cta{
    background: linear-gradient(135deg, #2563eb, #22c1c3);
    color:#fff; border-radius: 22px; padding: 22px; display:flex; align-items:center; justify-content:space-between;
    gap:16px; box-shadow: var(--ring);
  }
  .cta small{ opacity:.9; }
  .cta .btn{ background:#fff; color:var(--brand); }
  .cta .btn:hover{ filter:brightness(.98); }

  /* Reveal animation (no library) */
  .reveal{ opacity:0; transform: translateY(14px) scale(.98); transition: opacity .6s ease, transform .6s ease; }
  .reveal.show{ opacity:1; transform: translateY(0) scale(1); }
</style>

<div class="page">

  <!-- =================== HERO =================== -->
  <section class="hero" id="home">
    <div class="hero-slider" id="heroSlider">
      <?php foreach($hero_images as $i => $src): ?>
      <div class="hero-slide<?php echo $i===0 ? ' is-active' : '' ?>" style="background-image:url('<?php echo $src ?>')"></div>
      <?php endforeach; ?>
      <div class="hero-scrim"></div>

      <div class="hero-inner">
        <div class="hero-card">
          <div class="hero-copy reveal">
            <span class="hero-badge">
              <!-- phone icon -->
              <svg viewBox="0 0 24 24" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.1 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.31 1.78.57 2.63a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.45-1.14a2 2 0 0 1 2.11-.45c.85.26 1.73.45 2.63.57A2 2 0 0 1 22 16.92Z" stroke="currentColor" stroke-width="2"/></svg>
              24/7 Emergency: <?php echo htmlspecialchars($phone) ?>
            </span>
            <h1>Compassionate <span class="accent">Pet Care</span> When It Matters</h1>
            <p>Experienced veterinarians, modern diagnostics, and flexible booking — so your furry friend gets the best, fast.</p>
            <div class="hero-actions">
              <a href="<?php echo $base_url ?>?page=home#appointment" class="btn btn-primary">Book an Appointment</a>
              <a href="<?php echo $base_url ?>?page=services" class="btn btn-ghost">Explore Services</a>
            </div>
          </div>

          <div class="hero-panel reveal">
            <h3>Why choose <?php echo htmlspecialchars($short_name) ?>?</h3>
            <div class="hero-quick">
              <div class="chip">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 2v20M2 12h20" stroke="currentColor" stroke-width="2"/></svg>
                Flexible scheduling
              </div>
              <div class="chip">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M8 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Expert veterinarians
              </div>
              <div class="chip">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4-8 4-8-4 8-4Zm0 10l8 4-8 4-8-4 8-4Z" stroke="currentColor" stroke-width="2"/></svg>
                Modern lab & imaging
              </div>
              <div class="chip">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 7h18M6 3v4m12-4v4M6 21h12a3 3 0 0 0 3-3V7H3v11a3 3 0 0 0 3 3Z" stroke="currentColor" stroke-width="2"/></svg>
                Same-day visits
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- dots -->
      <div class="dots" id="heroDots"></div>
    </div>
  </section>

  <!-- =================== SERVICES (teaser) =================== -->
  <section class="section" id="services">
    <div class="container">
      <div class="section-header">
        <h2 class="reveal">Our Veterinary Services</h2>
        <p class="lead reveal">Comprehensive care for your furry, feathered, and scaly friends.</p>
      </div>

      <div class="grid">
        <?php
          // Get services from database
          require_once 'classes/ServicesModel.php';
          $servicesModel = new ServicesModel();
          $popularServices = $servicesModel->popular(6); // Get 6 most popular services
          
          foreach($popularServices as $svc):
        ?>
        <div class="card reveal">
          <div class="card-icon">
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 6v8m-4-4h8" stroke="#fff" stroke-width="2"/></svg>
          </div>
          <h4><?php echo htmlspecialchars($svc['name']); ?></h4>
          <p><?php echo htmlspecialchars($svc['description']); ?></p>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted">KES <?php echo number_format($svc['fee']); ?></small>
            <small class="text-muted"><?php echo $svc['duration']; ?> min</small>
          </div>
          <div style="margin-top:10px">
            <a href="<?php echo $base_url ?>appointment.php?service_id=<?php echo $svc['id']; ?>" 
               class="btn btn-primary" style="padding:10px 14px; font-weight:600;">Book Now →</a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:22px" class="reveal">
        <div class="cta">
          <div>
            <strong>Want the full list?</strong><br>
            <small>See pricing, packages, and add-ons tailored for your pet.</small>
          </div>
          <a href="<?php echo $base_url ?>?page=services" class="btn">View All Services</a>
        </div>
      </div>
    </div>
  </section>

  <!-- =================== APPOINTMENT ANCHOR (your existing form section) =================== -->
  <section class="section" id="appointment">
    <div class="container">
      <div class="section-header">
        <h2 class="reveal">Book an Appointment</h2>
        <p class="lead reveal">Schedule a visit with our experienced veterinarians.</p>
      </div>

      <!-- If you already have a form include/partial, you can include it here.
           For drop-in safety, we provide a minimal placeholder container. -->
      <div id="appointmentFormHost" class="reveal" style="background:#fff;border:1px solid #edf1f7;border-radius:22px;box-shadow:var(--ring-soft);padding:18px;">
        <?php
        // If your original project had a form chunk, you can require it:
        // require_once('partials/appointment_form.php');
        ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <input placeholder="Full Name *" style="padding:12px;border:1px solid #e6eaf2;border-radius:12px;">
          <input placeholder="Contact Number *" style="padding:12px;border:1px solid #e6eaf2;border-radius:12px;">
          <input placeholder="Email Address *" style="padding:12px;border:1px solid #e6eaf2;border-radius:12px;">
          <input placeholder="Pet Name *" style="padding:12px;border:1px solid #e6eaf2;border-radius:12px;">
          <textarea placeholder="Reason for visit" rows="3" style="grid-column:1/-1;padding:12px;border:1px solid #e6eaf2;border-radius:12px;"></textarea>
        </div>
        <div style="margin-top:12px;display:flex;gap:10px;justify-content:flex-end;">
          <button class="btn btn-ghost" type="button">Clear</button>
          <button class="btn btn-primary" type="button" onclick="alert('Demo: Submit appointment')">Submit</button>
        </div>
      </div>
    </div>
  </section>

  <!-- =================== ABOUT =================== -->
  <section class="section" id="about" style="background:var(--bg)">
    <div class="container">
      <div class="section-header">
        <h2 class="reveal">About Us</h2>
        <p class="lead reveal">We blend compassion with modern veterinary medicine to deliver exceptional outcomes.</p>
      </div>
      <div class="reveal" style="display:grid;grid-template-columns:1.2fr 1fr;gap:18px;">
        <div class="card">
          <p>From wellness to emergency care, our dedicated team provides personalized attention to every patient. We’re proud of our 4.9★ client rating and our commitment to same-day visits.</p>
          <ul style="margin:10px 0 0 16px; color:var(--muted)">
            <li>AAHA-aligned quality standards</li>
            <li>In-house diagnostics & imaging</li>
            <li>Friendly, transparent communication</li>
          </ul>
        </div>
        <div class="card" style="display:grid;place-items:center;background-image:url('https://images.unsplash.com/photo-1494256997604-768d1f608cac?q=80&w=1200&auto=format&fit=crop');background-size:cover;background-position:center;min-height:220px;"></div>
      </div>
    </div>
  </section>

  <!-- =================== CONTACT =================== -->
  <section class="section" id="contact">
    <div class="container">
      <div class="section-header">
        <h2 class="reveal">Contact</h2>
        <p class="lead reveal">Questions? Call us anytime at <strong><?php echo htmlspecialchars($phone) ?></strong>.</p>
      </div>
      <div class="reveal" style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div class="card">
          <h4>Find us</h4>
          <p class="lead" style="font-size:15px">123 Pet Street, Nairobi</p>
          <div style="height:220px;border-radius:16px;background:#eef4ff;display:grid;place-items:center;color:#6b7aa5;">Map placeholder</div>
        </div>
        <div class="card">
          <h4>Quick message</h4>
          <input placeholder="Your Name" style="padding:12px;border:1px solid #e6eaf2;border-radius:12px;width:100%;margin-bottom:10px;">
          <input placeholder="Email" style="padding:12px;border:1px solid #e6eaf2;border-radius:12px;width:100%;margin-bottom:10px;">
          <textarea rows="4" placeholder="Message" style="padding:12px;border:1px solid #e6eaf2;border-radius:12px;width:100%;"></textarea>
          <div style="margin-top:10px;text-align:right;"><button class="btn btn-primary" type="button" onclick="alert('Demo: Message sent')">Send</button></div>
        </div>
      </div>
    </div>
  </section>

</div>

<script>
/* ===== Simple slider ===== */
(function(){
  const slides = Array.from(document.querySelectorAll('.hero-slide'));
  const dotsWrap = document.getElementById('heroDots');
  if (!slides.length || !dotsWrap) return;

  let idx = 0, timer = null;
  slides.forEach((_, i)=>{
    const d = document.createElement('div');
    d.className = 'dot' + (i===0 ? ' is-active' : '');
    d.addEventListener('click', ()=>go(i, true));
    dotsWrap.appendChild(d);
  });
  const dots = Array.from(dotsWrap.children);

  function go(n, manual){
    slides[idx].classList.remove('is-active');
    dots[idx].classList.remove('is-active');
    idx = (n + slides.length) % slides.length;
    slides[idx].classList.add('is-active');
    dots[idx].classList.add('is-active');
    if (manual) restart();
  }
  function next(){ go(idx+1, false); }
  function start(){ timer = setInterval(next, 4500); }
  function stop(){ clearInterval(timer); }
  function restart(){ stop(); start(); }

  // Pause on hover
  const slider = document.getElementById('heroSlider');
  slider.addEventListener('mouseenter', stop);
  slider.addEventListener('mouseleave', start);

  start();
})();

/* ===== Smooth scroll for same-page anchors ===== */
document.addEventListener('click', (e)=>{
  const a = e.target.closest('a[href*="#"]');
  if(!a) return;
  const href = a.getAttribute('href');
  if(!href || href.startsWith('http')) return;
  const id = href.split('#')[1];
  if(!id) return;
  const el = document.getElementById(id);
  if(!el) return;
  e.preventDefault();
  window.scrollTo({ top: el.getBoundingClientRect().top + window.pageYOffset - 70, behavior:'smooth' });
});

/* ===== Reveal on view (IntersectionObserver) ===== */
(function(){
  const els = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver(entries=>{
    entries.forEach(ent=>{
      if(ent.isIntersecting){
        ent.target.classList.add('show');
        io.unobserve(ent.target);
      }
    });
  }, { threshold:.12 });
  els.forEach(el=>io.observe(el));
})();
</script>
