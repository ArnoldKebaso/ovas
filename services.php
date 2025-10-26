<?php
// SERVICES PAGE — Clean Drop-in with Dynamic Database Data
require_once 'initialize.php';
require_once 'classes/ServicesModel.php';

$short_name = isset($_settings) ? ($_settings->info('short_name') ?: 'VAP') : 'VAP';
$phone      = isset($_settings) ? ($_settings->info('contact')     ?: '07123456789') : '07123456789';
$base_url   = './';

/* ---------- Get Services from Database ---------- */
$servicesModel = new ServicesModel();
$services = $servicesModel->active();

// Get categories for filtering
$categories = $servicesModel->getCategories();

// Format services for frontend (ensure consistent structure)
$formattedServices = [];
foreach ($services as $service) {
    $formattedServices[] = [
        'id' => $service['id'],
        'name' => $service['name'],
        'description' => $service['description'],
        'price' => (float)$service['fee'],
        'duration' => $service['duration'] . ' min',
        'tags' => strtolower($service['category']),
        'category' => $service['category'],
        'image' => $service['image_path'] ?: 'https://images.unsplash.com/photo-1600959907703-125ba1374a12?q=80&w=1600&auto=format&fit=crop'
    ];
}
$services = $formattedServices;
    'price'=>1000.00,
    'duration'=>'—',
    'tags'=>'birds,cats,dogs',
    'image'=>'https://images.unsplash.com/photo-1581594693700-89e55f19ef8f?q=80&w=1600&auto=format&fit=crop'
  ],
  [
    'id'=>106,
    'name'=>'Pet Boarding',
    'description'=>'Safe, comfortable, supervised stays while you’re away.',
    'price'=>900.00,
    'duration'=>'overnight',
    'tags'=>'dogs,cats',
    'image'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=1600&auto=format&fit=crop'
  ],
];

$IMG_FALLBACK = 'https://images.unsplash.com/photo-1518020382113-a7e8fc38eac9?q=80&w=1600&auto=format&fit=crop';
?>
<style>
  :root{
    --brand:#2563eb; --brand2:#22c1c3; --text:#0f172a; --muted:#64748b;
    --bg:#f5f7fb; --card:#ffffff; --border:#e6edf7;
    --ring:0 10px 28px rgba(37,99,235,.15); --ring-soft:0 8px 20px rgba(2,6,23,.06);
    --radius:20px;
  }
  .page{background:#fff;}

  /* ===== HERO (no overlap, fixed spacing) ===== */
  .hero-wrap{ position:relative; background:#fff; }
  .hero-cover{
    height: 44vh; min-height:360px; max-height:560px;
    background-image:url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=1600&auto=format&fit=crop');
    background-size:cover; background-position:center;
    filter:saturate(1.04);
  }
  .hero-scrim{ position:absolute; inset:0; background: radial-gradient(1000px 520px at 20% 40%, rgba(0,0,0,.35), rgba(0,0,0,.55)); mix-blend-multiply; }
  .hero-inner{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; padding: clamp(18px, 4vw, 48px); }
  .hero-card{ width:min(1180px, 96%); color:#fff; display:grid; grid-template-columns: 1.2fr .8fr; gap:20px; }
  @media (max-width: 980px){ .hero-card{ grid-template-columns:1fr; } }
  .hero-copy h1{ margin:0 0 8px; font-size: clamp(30px, 4.6vw, 56px); line-height:1.08; font-weight:800; text-shadow: 0 1px 12px rgba(0,0,0,.25); }
  .hero-copy p{ margin:8px 0 16px; color:#eaf2ff; font-size: clamp(15px, 1.6vw, 18px); }
  .hero-badges{ display:flex; gap:12px; flex-wrap:wrap; }
  .badge{ display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background: rgba(255,255,255,.16); backdrop-filter: blur(4px); font-weight:700; }

  /* ===== BELOW HERO: clear separation ===== */
  .controls-wrap{ position:relative; z-index:2; background:#fff; }
  .controls-inner{ max-width:1180px; margin: 0 auto; padding: 0 16px; transform: translateY(-26px); }
  .search-bar{
    background:#fff; border:1px solid var(--border); border-radius: 999px; padding: 10px;
    display:flex; gap:8px; box-shadow: 0 12px 36px rgba(0,0,0,.08); align-items:center;
  }
  .search-bar input{ flex:1; border:0; outline:none; padding:10px 12px; font-size:16px; border-radius:999px; }
  .btn{ display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:10px 16px; border:0; cursor:pointer; font-weight:700; }
  .btn-primary{ background: var(--brand); color:#fff; box-shadow: var(--ring); }
  .chip-row{ display:flex; gap:10px; flex-wrap:wrap; padding: 10px 4px 0; }
  .btn-chip{
    border:1px solid var(--border); background:#fff; color:#1f2937; padding:8px 12px; border-radius:999px; font-weight:600;
    box-shadow: var(--ring-soft); transition: transform .12s ease, box-shadow .2s ease, background .2s ease;
    cursor:pointer;
  }
  .btn-chip:hover{ transform: translateY(-2px); box-shadow: 0 16px 28px rgba(31,41,55,.08); }
  .btn-chip.active{ background:#ecf3ff; border-color:#cfe0ff; }

  /* ===== GRID ===== */
  .section{ padding: 8px 16px 48px; }
  .container{ max-width:1180px; margin:0 auto; }

  .grid{ display:grid; grid-template-columns: repeat(3, 1fr); gap:20px; }
  @media (max-width:1100px){ .grid{ grid-template-columns: repeat(2,1fr);} }
  @media (max-width:720px){ .grid{ grid-template-columns: 1fr;} }

  .card{
    background:var(--card); border:1px solid var(--border); border-radius: var(--radius); overflow:hidden;
    box-shadow: var(--ring-soft); transition: transform .16s ease, box-shadow .22s ease;
    display:flex; flex-direction:column; /* keep consistent height */
  }
  .card:hover{ transform: translateY(-6px); box-shadow: 0 22px 38px rgba(31,41,55,.1); }

  .cover{ position:relative; aspect-ratio: 16/9; background:#eef2f9; }
  .cover img{ width:100%; height:100%; object-fit:cover; display:block; filter:saturate(1.05); }
  .price-badge{
    position:absolute; top:14px; right:14px; background:#fff; color:#2563eb; font-weight:800;
    border-radius:14px; padding:6px 10px; box-shadow: var(--ring-soft);
  }
  .body{ display:flex; flex-direction:column; gap:10px; padding: 14px 16px 16px; flex:1; }
  .title{ margin:0; font-size:18px; font-weight:800; color:var(--text); }
  .desc{ color:var(--muted); font-size:14.5px; min-height: 46px; }
  .meta{ display:flex; gap:10px; flex-wrap:wrap; margin-top:2px; }
  .pill{ display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid var(--border); border-radius:999px; font-size:12.5px; color:#475569; background:#fff; }

  .actions{ margin-top:auto; display:flex; justify-content:space-between; align-items:center; gap:10px; }
  .link{ display:inline-flex; align-items:center; gap:6px; color:#334155; font-weight:700; text-decoration:none; }
  .link:hover{ color:#111827; }
  .cta{ display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:10px 16px; border:0; cursor:pointer; font-weight:700; background: var(--brand); color:#fff; box-shadow: var(--ring); }

  /* Reveal animation */
  .reveal{ opacity:0; transform: translateY(12px); transition: opacity .6s ease, transform .6s ease; }
  .reveal.show{ opacity:1; transform:none; }

  .empty{ text-align:center; color:#64748b; padding:32px 8px; }
</style>

<div class="page">
  <!-- ============== HERO ============== -->
  <section class="hero-wrap">
    <div class="hero-cover"></div>
    <div class="hero-scrim"></div>
    <div class="hero-inner">
      <div class="hero-card">
        <div class="hero-copy reveal">
          <h1>Our Veterinary Services</h1>
          <p>Comprehensive care for your beloved pets. Expert veterinarians, modern equipment, and compassionate service.</p>
          <div class="hero-badges">
            <span class="badge">50+ Services</span>
            <span class="badge">10 Specialists</span>
            <span class="badge">24/7 Emergency</span>
          </div>
        </div>
        <div class="reveal" style="align-self:flex-end;">
          <div class="badge" style="background:rgba(255,255,255,.22)">☎ Emergency: <?php echo htmlspecialchars($phone) ?></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============== CONTROLS (separate layer so no merge) ============== -->
  <div class="controls-wrap">
    <div class="controls-inner">
      <div class="search-bar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="#64748b" stroke-width="2"/><path d="M21 21l-4-4" stroke="#64748b" stroke-width="2" stroke-linecap="round"/></svg>
        <input id="searchInput" type="text" placeholder="Search services (e.g., vaccination, grooming, dental)…" />
        <button class="btn btn-primary" id="btnSearch">Search</button>
      </div>
      <div class="chip-row" id="chipRow">
        <button type="button" class="btn-chip active" data-chip="all">All Services</button>
        <button type="button" class="btn-chip" data-chip="birds">Birds</button>
        <button type="button" class="btn-chip" data-chip="cats">Cats</button>
        <button type="button" class="btn-chip" data-chip="dogs">Dogs</button>
        <button type="button" class="btn-chip" data-chip="rabbits">Rabbits</button>
      </div>
    </div>
  </div>

  <!-- ============== GRID ============== -->
  <section class="section">
    <div class="container">
      <div class="grid" id="cardsGrid">
        <?php foreach($services as $svc):
          $tags = strtolower($svc['tags']);
          $img  = $svc['image'] ?: $IMG_FALLBACK;
          $price = $svc['price'];
        ?>
        <article class="card reveal"
                 data-tags="<?php echo htmlspecialchars($tags) ?>"
                 data-title="<?php echo htmlspecialchars(strtolower($svc['name'].' '.$svc['description'])) ?>">
          <div class="cover">
            <img loading="lazy" src="<?php echo $img ?>"
                 alt="<?php echo htmlspecialchars($svc['name']) ?>"
                 onerror="this.onerror=null;this.src='<?php echo $IMG_FALLBACK ?>';">
            <?php if(is_numeric($price)): ?>
              <div class="price-badge">KSh <?php echo number_format((float)$price, 2) ?></div>
            <?php endif; ?>
          </div>
          <div class="body">
            <h3 class="title"><?php echo htmlspecialchars($svc['name']) ?></h3>
            <p class="desc"><?php echo htmlspecialchars($svc['description']) ?></p>
            <div class="meta">
              <?php if(!empty($svc['duration'])): ?>
                <span class="pill">⏱ <?php echo htmlspecialchars($svc['duration']) ?></span>
              <?php endif; ?>
              <?php foreach(explode(',', $tags) as $tg): $tg=trim($tg); if(!$tg) continue; ?>
                <span class="pill">🐾 <?php echo htmlspecialchars(ucfirst($tg)) ?></span>
              <?php endforeach; ?>
            </div>
            <div class="actions">
              <!-- Use your existing details page name here (fixed to view_details) -->
              <a class="link" href="<?php echo $base_url ?>?page=view_details&id=<?php echo urlencode($svc['id']) ?>">Details</a>
              <a class="cta"  href="<?php echo $base_url ?>appointment.php?service_id=<?php echo urlencode($svc['id']) ?>">Book Now</a>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <div id="emptyState" class="empty" style="display:none;">No services match your search.</div>
    </div>
  </section>
</div>

<script>
/* Reveal */
(function(){
  const els = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver(entries=>{
    entries.forEach(ent=>{ if(ent.isIntersecting){ ent.target.classList.add('show'); io.unobserve(ent.target); } });
  }, {threshold:.14});
  els.forEach(el=>io.observe(el));
})();

/* Search + Filter chips (clickable) */
(function(){
  const input = document.getElementById('searchInput');
  const btn   = document.getElementById('btnSearch');
  const chips = Array.from(document.querySelectorAll('[data-chip]'));
  const cards = Array.from(document.querySelectorAll('#cardsGrid .card'));
  const empty = document.getElementById('emptyState');

  function apply(){
    const q = (input.value || '').trim().toLowerCase();
    const activeChip = document.querySelector('.btn-chip.active')?.getAttribute('data-chip') || 'all';
    let shown = 0;
    cards.forEach(card=>{
      const title = card.getAttribute('data-title') || '';
      const tags  = (card.getAttribute('data-tags') || '').split(',').map(s=>s.trim());
      const matchText = q ? title.includes(q) : true;
      const matchChip = activeChip==='all' ? true : tags.includes(activeChip);
      const vis = matchText && matchChip;
      card.style.display = vis ? '' : 'none';
      if(vis) shown++;
    });
    empty.style.display = shown ? 'none' : '';
  }

  btn.addEventListener('click', apply);
  input.addEventListener('keydown', e=>{ if(e.key==='Enter') apply(); });
  chips.forEach(c=>{
    c.addEventListener('click', ()=>{
      chips.forEach(x=>x.classList.remove('active'));
      c.classList.add('active');
      apply();
    });
  });
})();
</script>
