<?php
// CONTACT US — Drop-in replacement with a reliable Google Maps embed (no API key required)

$short_name = isset($_settings) ? ($_settings->info('short_name') ?: 'VAP') : 'VAP';
$phone      = isset($_settings) ? ($_settings->info('contact')     ?: '07123456789') : '07123456789';
$email      = isset($_settings) ? ($_settings->info('email')       ?: 'pet@gmail.com') : 'pet@gmail.com';
$address    = isset($_settings) ? ($_settings->info('address')     ?: 'Nairobi, Kenya') : 'Nairobi, Kenya';
$base_url   = './';

/* Map query: use address if present; you can replace with precise coordinates like '-1.2921,36.8219' */
$map_query  = urlencode($address ?: 'Veterinary Clinic Nairobi');
?>
<style>
  :root{
    --brand:#2563eb; --brand2:#22c1c3; --ink:#0f172a; --muted:#64748b;
    --bg:#f5f7fb; --card:#ffffff; --border:#e6edf7;
    --ring:0 10px 28px rgba(37,99,235,.15); --ring-soft:0 8px 20px rgba(2,6,23,.06);
    --r:22px;
  }
  .page{background:#fff; color:var(--ink);}

  .hero-wrap{ background: linear-gradient(135deg, #2563eb, #22c1c3); color:#fff; }
  .hero{ max-width:1180px; margin:0 auto; padding: clamp(36px, 7vw, 96px) 16px 56px; text-align:center; }
  .hero h1{ margin:0; font-size: clamp(32px, 5vw, 64px); line-height:1.06; font-weight:800; }
  .hero p{ opacity:.9; margin:10px auto 0; max-width:760px; }

  .section{ padding: clamp(32px, 6vw, 72px) 16px; }
  .container{ max-width:1180px; margin:0 auto; }
  .lead{ text-align:center; color:var(--muted); max-width:780px; margin:6px auto 24px; }
  .reveal{ opacity:0; transform: translateY(12px); transition: opacity .6s ease, transform .6s ease; }
  .reveal.show{ opacity:1; transform:none; }

  .soft{ background:var(--bg); }
  .grid-4{ display:grid; grid-template-columns: repeat(4, 1fr); gap:18px; }
  @media (max-width:980px){ .grid-4{ grid-template-columns: repeat(2, 1fr);} }
  @media (max-width:580px){ .grid-4{ grid-template-columns: 1fr;} }

  .c-card{ background:#fff; border:1px solid var(--border); border-radius: var(--r); padding:18px; box-shadow: var(--ring-soft); text-align:center; }
  .bubble{ width:64px; height:64px; border-radius:50%; display:grid; place-items:center; color:#fff; margin:0 auto 10px;
           background:linear-gradient(135deg, var(--brand), var(--brand2)); box-shadow: var(--ring); }
  .accent{ display:inline-block; padding:8px 12px; border:1.5px solid #d7e3ff; border-radius:12px; color:#1d4ed8; font-weight:800; background:#f5f9ff; }

  .columns{ display:grid; grid-template-columns: .95fr 1.05fr; gap:22px; align-items:start; }
  @media (max-width:980px){ .columns{ grid-template-columns:1fr; } }
  .panel{ background:#fff; border:1px solid var(--border); border-radius: var(--r); box-shadow: var(--ring-soft); padding:18px; }
  .panel h3{ margin:4px 0 12px; }

  .info-list{ display:grid; gap:12px; }
  .row{ display:flex; gap:10px; align-items:flex-start; }
  .row .icon{ width:40px; height:40px; border-radius:12px; display:grid; place-items:center; color:#fff;
              background:linear-gradient(135deg, var(--brand), var(--brand2)); box-shadow: var(--ring-soft); flex-shrink:0; }
  .row p{ margin:0; color:#475569; }

  form .field{ margin-bottom:12px; }
  .input, .select, .textarea{
    width:100%; padding:12px 14px; border-radius:12px; border:1px solid var(--border); background:#fff; outline:none;
    font-size:15px; transition:border-color .18s ease, box-shadow .18s ease;
  }
  .input:focus, .select:focus, .textarea:focus{ border-color:#cfe0ff; box-shadow:0 0 0 4px #ecf3ff; }
  .textarea{ min-height:140px; resize:vertical; }
  .actions{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:8px; }
  .btn{ display:inline-flex; align-items:center; gap:8px; padding:12px 18px; border-radius:999px; border:0; cursor:pointer; font-weight:800; }
  .btn-primary{ background:#2563eb; color:#fff; box-shadow: var(--ring); }
  .btn-ghost{ background:#eef2ff; color:#1d4ed8; }

  .alert{ display:none; margin:0 0 10px; padding:10px 12px; border-radius:12px; font-weight:700; }
  .alert.ok{ display:block; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .alert.err{ display:block; background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

  /* Responsive Map */
  .map{ position:relative; width:100%; border-radius:14px; overflow:hidden; border:1px solid var(--border); box-shadow: var(--ring-soft); }
  .map::before{ content:""; display:block; padding-top:56.25%; } /* 16:9 aspect ratio */
  .map iframe{ position:absolute; inset:0; width:100%; height:100%; border:0; }
  .map a{ position:absolute; right:8px; bottom:8px; background:#fff; border:1px solid var(--border); padding:6px 10px; border-radius:999px; font-size:12.5px; color:#2563eb; text-decoration:none; box-shadow:var(--ring-soft); }

  .emergency{ background:#fee2e2; color:#7f1d1d; text-align:center; padding:32px 16px; }
  .pill{ display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid #fecaca; color:#991b1b; padding:10px 14px; border-radius:999px; font-weight:800; box-shadow: var(--ring-soft); }

  .faq{ max-width:900px; margin:0 auto; }
  .acc{ background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow: var(--ring-soft); margin-bottom:12px; overflow:hidden; }
  .acc-btn{ width:100%; text-align:left; padding:14px 16px; background:#fff; border:0; font-size:17px; font-weight:800; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
  .acc-panel{ padding:0 16px 14px; color:#475569; display:none; }
  .acc.open .acc-panel{ display:block; }
  .acc.open .acc-btn{ background:#f8fbff; }
</style>

<div class="page" id="contact">

  <!-- HERO -->
  <section class="hero-wrap">
    <div class="hero">
      <h1>Contact <?php echo htmlspecialchars($short_name) ?></h1>
      <p>We’d love to hear from you. Send us a message and we’ll respond as soon as possible.</p>
    </div>
  </section>

  <!-- CONTACT CARDS -->
  <section class="section soft">
    <div class="container">
      <div class="grid-4">
        <div class="c-card reveal">
          <div class="bubble">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none"><path d="M3 5c7 0 16 9 16 16l-3 3C6 21 3 14 3 5Z" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
          </div>
          <h3 style="margin:6px 0 6px;">Call Us</h3><p class="muted">Speak directly with our team</p>
          <div class="accent"><?php echo htmlspecialchars($phone) ?></div>
        </div>
        <div class="c-card reveal">
          <div class="bubble">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none"><path d="M4 6l8 6 8-6v12H4V6Z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/></svg>
          </div>
          <h3 style="margin:6px 0 6px;">Email Us</h3><p class="muted">Send us a message anytime</p>
          <div class="accent" style="color:#16a34a;border-color:#b7efc5;background:#f0fff4;"><?php echo htmlspecialchars($email) ?></div>
        </div>
        <div class="c-card reveal">
          <div class="bubble">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none"><path d="M12 2l7 8-7 12L5 10l7-8Z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/></svg>
          </div>
          <h3 style="margin:6px 0 6px;">Visit Us</h3><p class="muted">Come to our clinic</p>
          <div class="accent" style="color:#0f766e;border-color:#99f6e4;background:#ecfeff;"><?php echo htmlspecialchars($address) ?></div>
        </div>
        <div class="c-card reveal">
          <div class="bubble">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none"><circle cx="12" cy="12" r="9" stroke="#fff" stroke-width="2"/><path d="M12 7v6l3 3" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
          </div>
          <h3 style="margin:6px 0 6px;">Business Hours</h3><p class="muted">We’re here to help</p>
          <div class="accent" style="color:#a16207;border-color:#fde68a;background:#fffbeb;">Mon–Sat: 8:00 AM – 6:00 PM</div>
          <small style="display:block;margin-top:6px;color:#6b7280;">Sunday: Emergency Only</small>
        </div>
      </div>
    </div>
  </section>

  <!-- DETAILS + FORM -->
  <section class="section">
    <div class="container columns">
      <!-- Left: info + MAP (fixed) -->
      <div class="panel reveal">
        <h3>How to Reach Us</h3>
        <div class="info-list">
          <div class="row">
            <div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none"><path d="M3 5c7 0 16 9 16 16l-3 3C6 21 3 14 3 5Z" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg></div>
            <div><strong>Phone</strong><p><?php echo htmlspecialchars($phone) ?></p></div>
          </div>
          <div class="row">
            <div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none"><path d="M4 6l8 6 8-6v12H4V6Z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/></svg></div>
            <div><strong>Email</strong><p><?php echo htmlspecialchars($email) ?></p></div>
          </div>
          <div class="row">
            <div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none"><path d="M12 2l7 8-7 12L5 10l7-8Z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/></svg></div>
            <div><strong>Address</strong><p><?php echo htmlspecialchars($address) ?></p></div>
          </div>
        </div>

        <!-- Reliable Google Map embed -->
        <div class="map" style="margin-top:14px;">
          <iframe
            src="https://www.google.com/maps?q=<?php echo $map_query; ?>&output=embed"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade" aria-label="Map">
          </iframe>
          <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $map_query; ?>" target="_blank" rel="noopener">Open in Google Maps</a>
          <noscript>
            <img src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo $map_query; ?>&zoom=13&size=800x400&markers=color:blue|<?php echo $map_query; ?>&key=" alt="Map (enable JavaScript for interactive map)" style="width:100%;display:block;">
          </noscript>
        </div>
      </div>

      <!-- Right: contact form (unchanged) -->
      <div class="panel reveal">
        <h3>Send Us a Message</h3>
        <p style="color:#64748b;margin-top:-6px;">Have a question or need to schedule an appointment? Fill out the form below and we’ll get back to you shortly.</p>

        <div id="formAlert" class="alert"></div>

        <form id="contactForm" method="POST" action="sendemail.php" novalidate>
          <div class="field">
            <label>Full Name *</label>
            <input class="input" type="text" name="name" placeholder="Your full name" required>
          </div>
          <div class="field">
            <label>Email Address *</label>
            <input class="input" type="email" name="email" placeholder="your.email@example.com" required>
          </div>
          <div class="field">
            <label>Phone Number</label>
            <input class="input" type="text" name="phone" placeholder="Your phone number">
          </div>
          <div class="field">
            <label>Subject *</label>
            <select class="select" name="subject" required>
              <option value="">Choose a subject</option>
              <option>General inquiry</option>
              <option>Appointment request</option>
              <option>Billing question</option>
              <option>Feedback</option>
            </select>
          </div>
          <div class="field">
            <label>Your Message *</label>
            <textarea class="textarea" name="message" placeholder="Please describe your inquiry…" required></textarea>
          </div>
          <div class="actions">
            <button class="btn btn-primary" type="submit">Send Message</button>
            <a class="btn btn-ghost" href="<?php echo $base_url ?>?page=home#appointment">Book Appointment</a>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Emergency + FAQ (unchanged) -->
  <section class="emergency reveal">
    <div class="container" style="text-align:center;">
      <h3 style="margin:0 0 6px;">Emergency? Call Now!</h3>
      <p style="color:#7f1d1d;max-width:760px;margin:0 auto 12px;">For urgent veterinary emergencies, don’t wait. Call us immediately for prompt assistance.</p>
      <span class="pill">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"><path d="M3 5c7 0 16 9 16 16l-3 3C6 21 3 14 3 5Z" stroke="#991b1b" stroke-width="2" stroke-linecap="round"/></svg>
        Emergency: <?php echo htmlspecialchars($phone) ?>
      </span>
    </div>
  </section>

  <section class="section soft">
    <div class="container faq">
      <h2 class="reveal" style="text-align:center;color:var(--brand);">Frequently Asked Questions</h2>
      <p class="lead reveal">Quick answers to common questions</p>

      <div class="acc reveal">
        <button class="acc-btn">What are your business hours?
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="acc-panel">We’re open Monday–Saturday from 8:00 AM to 6:00 PM. Sundays are for emergency care only.</div>
      </div>

      <div class="acc reveal">
        <button class="acc-btn">How do I schedule an appointment?
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="acc-panel">Use the “Book Appointment” button, or call us at <?php echo htmlspecialchars($phone) ?>.</div>
      </div>

      <div class="acc reveal">
        <button class="acc-btn">Do you handle emergencies?
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="acc-panel">Yes. We offer 24/7 emergency guidance by phone and in-clinic triage during business hours.</div>
      </div>

      <div class="acc reveal">
        <button class="acc-btn">What should I bring to my pet’s first visit?
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="#0f172a" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="acc-panel">Bring previous medical records, vaccination history, a stool sample (if requested), and any current medications.</div>
      </div>
    </div>
  </section>
</div>

<script>
/* Reveal-on-view */
(function(){
  const els = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver(entries=>{
    entries.forEach(ent=>{ if(ent.isIntersecting){ ent.target.classList.add('show'); io.unobserve(ent.target); } });
  }, {threshold:.14});
  els.forEach(el=>io.observe(el));
})();

/* FAQ accordion */
document.querySelectorAll('.acc-btn').forEach(btn=>{
  btn.addEventListener('click', ()=> btn.parentElement.classList.toggle('open'));
});

/* Form handling (AJAX with graceful fallback) */
const form = document.getElementById('contactForm');
const alertBox = document.getElementById('formAlert');
form.addEventListener('submit', async (e)=>{
  e.preventDefault();
  alertBox.className='alert'; alertBox.textContent='';
  const fd = new FormData(form);
  for (const k of ['name','email','subject','message']){
    if(!(fd.get(k)||'').trim()){
      alertBox.className='alert err'; alertBox.textContent='Please fill out all required fields.'; return;
    }
  }
  try{
    const res = await fetch(form.action, { method:'POST', body: fd });
    if(!res.ok) throw new Error();
    alertBox.className='alert ok'; alertBox.textContent='Thanks! Your message was sent successfully.'; form.reset();
  }catch(_){ form.submit(); }
});
</script>
