<?php
// ABOUT US — Single-file, drop-in (PHP + HTML + CSS + JS)
// Uses $_settings when present; safe fallbacks otherwise.
$short_name = isset($_settings) ? ($_settings->info('short_name') ?: 'VAP') : 'VAP';
$phone      = isset($_settings) ? ($_settings->info('contact')     ?: '07123456789') : '07123456789';
$email      = isset($_settings) ? ($_settings->info('email')       ?: 'pet@gmail.com') : 'pet@gmail.com';
$address    = isset($_settings) ? ($_settings->info('address')     ?: 'Nairobi') : 'Nairobi';
$base_url   = './';
?>
<style>
  :root{
    --brand:#2563eb; --brand2:#22c1c3; --ink:#0f172a; --muted:#64748b;
    --bg:#f5f7fb; --card:#ffffff; --border:#e6edf7;
    --ring:0 10px 28px rgba(37,99,235,.15); --ring-soft:0 10px 22px rgba(2,6,23,.06);
    --r:22px;
  }
  .page{background:#fff; color:var(--ink);}

  /* ===== HERO ===== */
  .hero-wrap{ background: linear-gradient(135deg, #2563eb, #22c1c3); color:#fff; }
  .hero{ max-width:1180px; margin:0 auto; padding: clamp(36px, 7vw, 96px) 16px 56px; text-align:center; }
  .hero h1{ margin:0; font-size: clamp(32px, 5vw, 64px); line-height:1.06; font-weight:800; }
  .hero p{ opacity:.9; margin:10px auto 0; max-width:760px; }

  /* ===== Sections ===== */
  .section{ padding: clamp(36px, 6vw, 84px) 16px; }
  .container{ max-width:1180px; margin:0 auto; }
  .section h2{ text-align:center; font-size: clamp(28px, 3.4vw, 42px); margin:0 0 8px; color:var(--brand); }
  .lead{ text-align:center; color:var(--muted); max-width:780px; margin:6px auto 24px; }

  /* ===== Story ===== */
  .story{ display:grid; grid-template-columns: 1.05fr .95fr; gap:22px; align-items:center; }
  @media (max-width: 980px){ .story{ grid-template-columns:1fr; } }
  .story-visual{
    position:relative; border-radius: var(--r); overflow:hidden; box-shadow: var(--ring-soft);
  }
  .story-visual img{ width:100%; height:100%; object-fit:cover; display:block; }
  .story-badges{ position:absolute; left:12px; bottom:12px; display:flex; gap:10px; flex-wrap:wrap; }
  .badge{
    display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px;
    background: rgba(255,255,255,.92); color:#1f2937; font-weight:700; box-shadow: var(--ring-soft);
  }
  .story-text p{ color:#475569; line-height:1.7; }
  .tick{ color:#16a34a; font-weight:800; margin-right:8px; }
  .key-points{ display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:12px; }
  @media (max-width:720px){ .key-points{ grid-template-columns:1fr; } }
  .chip{
    display:flex; gap:10px; align-items:center; padding:10px 12px; border:1px solid var(--border);
    background:#fff; border-radius: 14px; box-shadow: var(--ring-soft);
  }
  .dot{ width:16px; height:16px; border-radius:50%; }
  .dot.blue{ background:#2563eb; } .dot.green{ background:#16a34a; }

  /* ===== Mission & Values ===== */
  .grid-3{ display:grid; grid-template-columns: repeat(3,1fr); gap:18px; }
  @media (max-width:980px){ .grid-3{ grid-template-columns:1fr 1fr; } }
  @media (max-width:680px){ .grid-3{ grid-template-columns:1fr; } }
  .card{ background:var(--card); border:1px solid var(--border); border-radius: var(--r); padding:18px;
         box-shadow: var(--ring-soft); }
  .icon-wrap{ width:64px; height:64px; border-radius:18px; display:grid; place-items:center; color:#fff;
              background:linear-gradient(135deg, var(--brand), var(--brand2)); box-shadow:var(--ring); }
  .card h3{ margin:10px 0 6px; }
  .card p{ color:#64748b; }

  /* ===== Team ===== */
  .team{ display:grid; grid-template-columns: repeat(3,1fr); gap:18px; }
  @media (max-width:980px){ .team{ grid-template-columns:1fr 1fr; } }
  @media (max-width:680px){ .team{ grid-template-columns:1fr; } }
  .person{ background:#fff; border:1px solid var(--border); border-radius: var(--r); box-shadow: var(--ring-soft); overflow:hidden; }
  .person .photo{ aspect-ratio: 4/3; background:#eef2f9; }
  .person .photo img{ width:100%; height:100%; object-fit:cover; display:block; }
  .person .body{ padding:14px 16px 16px; }
  .person .name{ font-weight:800; }
  .badge-mini{ font-size:12px; color:#2563eb; font-weight:700; }
  .skills{ margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; }
  .pill{ padding:6px 10px; border:1px solid var(--border); border-radius:999px; background:#fff; color:#475569; font-size:12.5px; }

  /* ===== Facilities (icons row) ===== */
  .soft{ background:var(--bg); }
  .facilities{ display:grid; grid-template-columns: repeat(4,1fr); gap:18px; margin-top:18px; }
  @media (max-width:980px){ .facilities{ grid-template-columns:1fr 1fr; } }
  @media (max-width:560px){ .facilities{ grid-template-columns:1fr; } }
  .facility{ background:#fff; border:1px solid var(--border); border-radius: var(--r); padding:18px; box-shadow: var(--ring-soft); text-align:center; }
  .bubble{ width:64px; height:64px; border-radius:50%; margin:0 auto 10px; display:grid; place-items:center; color:#fff;
           background:linear-gradient(135deg, var(--brand), var(--brand2)); box-shadow: var(--ring); }

  /* ===== Stats Bar ===== */
  .stats{ background:#2563eb; color:#fff; }
  .stats-inner{ max-width:1180px; margin:0 auto; padding: 24px 16px; display:grid; grid-template-columns: repeat(4,1fr); gap:10px; text-align:center; }
  @media (max-width:780px){ .stats-inner{ grid-template-columns:1fr 1fr; } }
  .stat b{ display:block; font-size:40px; font-weight:800; }

  /* ===== CTA ===== */
  .cta{ text-align:center; padding: 36px 16px; }
  .cta .btn{ display:inline-flex; align-items:center; gap:10px; padding:12px 18px; font-weight:800; border-radius:999px; border:2px solid transparent; cursor:pointer; }
  .btn-primary{ background:#2563eb; color:#fff; box-shadow:var(--ring); }
  .btn-outline{ background:#fff; color:#2563eb; border-color:#2563eb; }

  /* Reveal animation */
  .reveal{ opacity:0; transform: translateY(14px); transition: opacity .6s ease, transform .6s ease; }
  .reveal.show{ opacity:1; transform:none; }
</style>

<div class="page">

  <!-- ============== HERO ============== -->
  <section class="hero-wrap">
    <div class="hero">
      <h1>About <?php echo htmlspecialchars($short_name) ?></h1>
      <p>Your trusted partner in comprehensive veterinary care since 2010</p>
    </div>
  </section>

  <!-- ============== OUR STORY ============== -->
  <section class="section">
    <div class="container story">
      <div class="story-visual reveal">
        <img src="https://images.unsplash.com/photo-1529778873920-4da4926a72c2?q=80&w=1600&auto=format&fit=crop" alt="Clinic with pets">
        <div class="story-badges">
          <span class="badge">Certified Excellence</span>
          <span class="badge">Compassionate Care</span>
        </div>
      </div>
      <div class="story-text reveal">
        <h2>Our Story</h2>
        <p>Founded in 2010, <?php echo htmlspecialchars($short_name) ?> has been at the forefront of veterinary excellence—combining compassion with modern diagnostics to deliver comprehensive care for pets across our community.</p>
        <p>We believe every pet deserves the highest-quality medical attention, delivered with warmth and transparency. Our experienced team and state-of-the-art facility ensure your companions receive the best possible care.</p>
        <div class="key-points">
          <div class="chip"><span class="dot blue"></span><div><strong>Licensed Professionals</strong><br><small>Registered veterinarians and nurses</small></div></div>
          <div class="chip"><span class="dot green"></span><div><strong>We Love Animals</strong><br><small>Gentle, fear-reduced handling</small></div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============== MISSION & VALUES ============== -->
  <section class="section soft">
    <div class="container">
      <h2 class="reveal">Our Mission & Values</h2>
      <p class="lead reveal">We’re committed to enhancing the health and happiness of pets through exceptional veterinary care.</p>

      <div class="grid-3">
        <div class="card reveal">
          <div class="icon-wrap">
            <!-- heart pulse -->
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none">
              <path d="M3 13c1.5-4.5 5.5-6 9-1 3.5-5 7.5-3.5 9 1" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <h3>Our Mission</h3>
          <p>To provide exceptional care through advanced medicine, compassionate service, and a focus on every pet’s well-being.</p>
        </div>

        <div class="card reveal">
          <div class="icon-wrap">
            <!-- target -->
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none">
              <circle cx="12" cy="12" r="9" stroke="#fff" stroke-width="2"/><circle cx="12" cy="12" r="3" fill="#fff"/>
            </svg>
          </div>
          <h3>Our Vision</h3>
          <p>To be our community’s most trusted veterinary practice—known for innovation, empathy, and long-term relationships.</p>
        </div>

        <div class="card reveal">
          <div class="icon-wrap">
            <!-- star -->
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none">
              <path d="M12 3l2.8 6h6.2l-5 3.9 1.9 6.1L12 16l-5.9 3.9 1.9-6.1-5-3.9h6.2L12 3z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>Our Values</h3>
          <p>Compassion, integrity, excellence, and clear communication guide everything we do—for pets and their families.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============== TEAM ============== -->
  <section class="section">
    <div class="container">
      <h2 class="reveal">Meet Our Expert Team</h2>
      <p class="lead reveal">Experienced veterinarians and support staff dedicated to the highest quality care.</p>

      <div class="team">
        <?php
        $team = [
          ['Dr. Sarah Johnson','Chief Veterinarian','DVM • Emergency Care','https://images.unsplash.com/photo-1524504388940-b1c1722653e1?q=80&w=1200&auto=format&fit=crop'],
          ['Dr. Michael Chen','Senior Veterinarian','Surgery • Orthopedics','https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=1200&auto=format&fit=crop'],
          ['Dr. Emily Rodriguez','Veterinary Specialist','Exotic Pets • Dermatology','https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?q=80&w=1200&auto=format&fit=crop'],
        ];
        foreach($team as $t): ?>
          <div class="person reveal">
            <div class="photo"><img src="<?php echo $t[3] ?>" alt="<?php echo htmlspecialchars($t[0]) ?>"></div>
            <div class="body">
              <div class="name"><?php echo htmlspecialchars($t[0]) ?></div>
              <div class="badge-mini"><?php echo htmlspecialchars($t[1]) ?></div>
              <p style="color:#6b7280;margin-top:6px;"><?php echo htmlspecialchars($t[2]) ?></p>
              <div class="skills">
                <?php foreach(explode('•', $t[2]) as $s): ?>
                  <span class="pill"><?php echo htmlspecialchars(trim($s)) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============== FACILITIES ============== -->
  <section class="section soft">
    <div class="container">
      <h2 class="reveal">State-of-the-Art Facilities</h2>
      <p class="lead reveal">Modern equipment and advanced technology ensure the best possible care for your pets.</p>

      <div class="facilities">
        <?php
        $fac = [
          ['Modern Surgery Suite','Fully equipped operating rooms with advanced monitoring systems'],
          ['Digital Radiology','High-resolution imaging for accurate diagnostics'],
          ['In-House Laboratory','Quick test results for faster diagnosis and treatment'],
          ['Recovery Rooms','Comfortable spaces for post-surgery monitoring and care'],
        ];
        foreach($fac as $f): ?>
          <div class="facility reveal">
            <div class="bubble">
              <!-- simple hospital/plus icon -->
              <svg viewBox="0 0 24 24" width="28" height="28" fill="none">
                <rect x="3" y="3" width="18" height="18" rx="3" stroke="#fff" stroke-width="2"/>
                <path d="M12 7v10M7 12h10" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>
            <h3 style="margin:6px 0 4px;"><?php echo htmlspecialchars($f[0]) ?></h3>
            <p style="color:#64748b;"><?php echo htmlspecialchars($f[1]) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============== STATS ============== -->
  <section class="stats">
    <div class="stats-inner">
      <div class="stat reveal"><b>5000</b><small>Happy Pets Treated</small></div>
      <div class="stat reveal"><b>15</b><small>Years of Service</small></div>
      <div class="stat reveal"><b>10</b><small>Expert Veterinarians</small></div>
      <div class="stat reveal"><b>24</b><small>Hours Emergency Care</small></div>
    </div>
  </section>

  <!-- ============== CTA ============== -->
  <section class="cta">
    <h2 class="reveal" style="margin:0 0 8px;">Ready to Experience Our Care?</h2>
    <p class="lead reveal">Book an appointment today or reach us for any question.</p>
    <div class="reveal" style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
      <a class="btn btn-primary" href="<?php echo $base_url ?>?page=home#appointment">📅 Book Appointment</a>
      <a class="btn btn-outline" href="<?php echo $base_url ?>?page=home#contact">✉️ Contact Us</a>
    </div>
  </section>
</div>

<script>
/* Simple reveal-on-view (no external libs) */
(function(){
  const els = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver(entries=>{
    entries.forEach(ent=>{
      if(ent.isIntersecting){ ent.target.classList.add('show'); io.unobserve(ent.target); }
    });
  }, {threshold:.14});
  els.forEach(el=>io.observe(el));
})();
</script>
