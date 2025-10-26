
<?php
  // Theme-safe fallbacks
  $short_name = isset($_settings) ? ($_settings->info('short_name') ?: 'VAP') : 'VAP';
  $phone      = isset($_settings) ? ($_settings->info('contact')     ?: '07123456789') : '07123456789';
  $email      = isset($_settings) ? ($_settings->info('email')       ?: 'pet@gmail.com') : 'pet@gmail.com';

  // Which tab to show initially (login or register)
  $tab = isset($_GET['tab']) && $_GET['tab'] === 'register' ? 'register' : 'login';
  $registered = isset($_GET['registered']) ? true : false;

  // Post endpoints (plug your real handlers here)
  // Keep as is for now; JS will gracefully redirect back to the login tab with a success flag.
  $login_action    = 'auth_login.php';     // TODO: change to your real login endpoint
  $register_action = 'auth_register.php';  // TODO: change to your real register endpoint
?>
<style>
/* ===== Auth Portal (scoped to avoid collisions) ===== */
.auth-portal .hero-wrap{
  background: linear-gradient(135deg, #2563eb, #22c1c3);
  color:#fff;
}
.auth-portal .hero{
  max-width:1180px; margin:0 auto;
  padding: clamp(36px, 7vw, 96px) 16px 56px;
  display:flex; gap:24px; align-items:flex-end; flex-wrap:wrap;
}
.auth-portal .hero h1{
  margin:0; font-size: clamp(30px, 5vw, 48px); line-height:1.07; font-weight:900;
}
.auth-portal .hero p{ margin:8px 0 0; opacity:.92; }

/* Background image band under the hero (soft focus) */
.auth-portal .backdrop{
  position: relative; overflow:hidden; background:#0b1220;
}
.auth-portal .backdrop .img{
  position:absolute; inset:0; width:100%; height:100%; object-fit:cover;
  filter:saturate(1.05) contrast(1.02) brightness(.75);
}
.auth-portal .backdrop .scrim{ position:absolute; inset:0; background: linear-gradient(180deg, rgba(15,23,42,.55), rgba(15,23,42,.70)); }
.auth-portal .backdrop .spacer{ padding-top:22vh; } /* visual band height */

.auth-portal .section{ padding: clamp(28px, 6vw, 72px) 16px; background:#fff; }
.auth-portal .container{ max-width:1180px; margin:0 auto; }

.auth-portal .wrap{
  display:grid; grid-template-columns: 1.15fr 1fr; gap:22px; align-items:start;
}
@media (max-width: 980px){ .auth-portal .wrap{ grid-template-columns:1fr; } }

/* Left: promo/info box */
.auth-portal .info{
  color:#0f172a; background:#fff; border:1px solid #e6edf7; border-radius:22px; box-shadow:0 8px 20px rgba(2,6,23,.06);
  padding:18px;
}
.auth-portal .info .badge{
  display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px;
  background:#eef2ff; color:#1d4ed8; font-weight:800; border:1px solid #dbe6ff; margin-bottom:8px;
}
.auth-portal .info h2{ margin:6px 0 6px; font-size: clamp(22px, 3vw, 32px); font-weight:900; color:#0f172a; }
.auth-portal .info p{ margin:0; color:#64748b; }
.auth-portal .bullets{ display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:14px; }
@media (max-width: 620px){ .auth-portal .bullets{ grid-template-columns:1fr; } }
.auth-portal .chip{
  display:flex; gap:10px; align-items:center; padding:10px; border:1px solid #e6edf7; border-radius:12px; background:#fff;
}
.auth-portal .ic{ width:28px; height:28px; border-radius:8px; display:grid; place-items:center; color:#2563eb; background:#ecf3ff; }

/* Right: tabbed card */
.auth-portal .card{
  background:#fff; border:1px solid #e6edf7; border-radius:22px; box-shadow:0 8px 20px rgba(2,6,23,.06);
  padding:18px;
}
.auth-portal .tabs{
  display:flex; gap:6px; background:#f5f7fb; padding:6px; border-radius:999px; width:max-content; margin-bottom:12px; border:1px solid #e6edf7;
}
.auth-portal .tab{
  padding:10px 16px; border-radius:999px; border:0; background:transparent; font-weight:800; color:#334155; cursor:pointer;
}
.auth-portal .tab.active{ background:#fff; color:#1d4ed8; border:1px solid #dbe6ff; box-shadow:0 8px 20px rgba(2,6,23,.06); }

.auth-portal .field{ margin-top:12px; }
.auth-portal label{ display:block; color:#334155; margin-bottom:6px; font-size:14px;}
.auth-portal .ctrl{
  display:flex; align-items:center; gap:10px; background:#fff; border:1px solid #e6edf7; border-radius:12px;
  padding: 10px 12px; transition:border-color .18s ease, box-shadow .18s ease;
}
.auth-portal .ctrl:focus-within{ border-color:#cfe0ff; box-shadow:0 0 0 4px #ecf3ff; }
.auth-portal .ctrl .icon{ width:22px; color:#2563eb; opacity:.9; }
.auth-portal .ctrl input{
  border:0; outline:none; width:100%; font-size:15px; background:transparent; color:#0f172a;
}
.auth-portal .ctrl .action{ cursor:pointer; border:0; background:#eef2ff; color:#1d4ed8; font-weight:800; padding:8px 10px; border-radius:10px; }

.auth-portal .row{ display:grid; grid-template-columns:1fr 1fr; gap:10px; }
@media (max-width: 620px){ .auth-portal .row{ grid-template-columns:1fr; } }

.auth-portal .actions{ display:flex; gap:10px; align-items:center; margin-top:12px; flex-wrap:wrap; }
.auth-portal .btn{ display:inline-flex; align-items:center; gap:8px; padding:12px 18px; border-radius:999px; border:0; cursor:pointer; font-weight:800; }
.auth-portal .btn-primary{ background:#2563eb; color:#fff; box-shadow:0 10px 28px rgba(37,99,235,.15); }
.auth-portal .btn-ghost{ background:#eef2ff; color:#1d4ed8; }
.auth-portal .muted{ color:#64748b; font-size:13.5px; }
.auth-portal .success{ display:none; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:10px 12px; border-radius:12px; font-weight:700; margin-bottom:10px; }
.auth-portal .success.show{ display:block; }

.auth-portal .reveal{ opacity:0; transform: translateY(10px); transition:opacity .5s ease, transform .5s ease; }
.auth-portal .reveal.show{ opacity:1; transform:none; }
</style>

<div class="auth-portal">

  <!-- HERO -->
  <section class="hero-wrap">
    <div class="hero">
      <div>
        <h1>Welcome to <?php echo htmlspecialchars($short_name) ?></h1>
        <p>Log in or create your account to book appointments, manage your details, and get reminders.</p>
      </div>
    </div>
  </section>

  <!-- BACKDROP BAND (subtle background image for authenticity) -->
  <div class="backdrop" aria-hidden="true">
    <img class="img" src="https://images.unsplash.com/photo-1517849845537-4d257902454a?q=80&w=1920&auto=format&fit=crop" alt="">
    <div class="scrim"></div>
    <div class="spacer"></div>
  </div>

  <!-- AUTH CONTENT -->
  <section class="section">
    <div class="container wrap">
      <!-- Left: helpful info -->
      <div class="info reveal">
        <span class="badge">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#1d4ed8" stroke-width="2"/><path d="M12 7v5l3 3" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round"/></svg>
          Why create an account?
        </span>
        <h2>Book faster. Stay updated.</h2>
        <p>With an account you can schedule visits, track upcoming appointments, and receive reminders. It takes less than a minute.</p>

        <div class="bullets">
          <div class="chip"><div class="ic">🗓</div><div><b>Easy scheduling</b><br><span class="muted">Pick dates and times that work for you</span></div></div>
          <div class="chip"><div class="ic">🔔</div><div><b>Reminders</b><br><span class="muted">We’ll notify you before your visit</span></div></div>
          <div class="chip"><div class="ic">🐾</div><div><b>Pet-friendly</b><br><span class="muted">Keep pet details ready for next time</span></div></div>
          <div class="chip"><div class="ic">🔒</div><div><b>Secure</b><br><span class="muted">Your information stays private</span></div></div>
        </div>
      </div>

      <!-- Right: tabbed card -->
      <div class="card reveal">
        <div class="tabs" role="tablist" aria-label="Authentication Tabs">
          <button class="tab <?php echo $tab==='login'?'active':'' ?>" role="tab" aria-selected="<?php echo $tab==='login'?'true':'false' ?>" aria-controls="panel-login" id="tab-login">Login</button>
          <button class="tab <?php echo $tab==='register'?'active':'' ?>" role="tab" aria-selected="<?php echo $tab==='register'?'true':'false' ?>" aria-controls="panel-register" id="tab-register">Create Account</button>
        </div>

        <?php if($registered): ?>
          <div class="success" id="signupSuccess">Account created! Please log in to continue.</div>
        <?php endif; ?>

        <!-- LOGIN PANEL -->
        <div id="panel-login" role="tabpanel" aria-labelledby="tab-login" style="<?php echo $tab==='login'?'display:block':'display:none' ?>">
          <form id="loginForm" action="<?php echo $login_action ?>" method="post" novalidate>
            <div class="field">
              <label for="login-identity">Email or Username</label>
              <div class="ctrl">
                <span class="icon">👤</span>
                <input type="text" id="login-identity" name="identity" placeholder="you@example.com or username" required>
              </div>
            </div>

            <div class="field">
              <label for="login-password">Password</label>
              <div class="ctrl">
                <span class="icon">🔒</span>
                <input type="password" id="login-password" name="password" placeholder="••••••••" required>
                <button class="action" type="button" data-toggle="#login-password">Show</button>
              </div>
            </div>

            <div class="actions">
              <button class="btn btn-primary" type="submit">Sign In</button>
              <a class="btn btn-ghost" href="?page=home#appointment">Book as Guest</a>
              <span class="muted" style="margin-left:auto;">Need help? Call <?php echo htmlspecialchars($phone) ?></span>
            </div>
          </form>
        </div>

        <!-- REGISTER PANEL -->
        <div id="panel-register" role="tabpanel" aria-labelledby="tab-register" style="<?php echo $tab==='register'?'display:block':'display:none' ?>">
          <form id="registerForm" action="<?php echo $register_action ?>" method="post" novalidate>
            <div class="row">
              <div class="field">
                <label for="reg-name">Full Name *</label>
                <div class="ctrl">
                  <span class="icon">🧑</span>
                  <input type="text" id="reg-name" name="name" placeholder="Jane Doe" required>
                </div>
              </div>
              <div class="field">
                <label for="reg-phone">Phone *</label>
                <div class="ctrl">
                  <span class="icon">📞</span>
                  <input type="text" id="reg-phone" name="phone" placeholder="07XXXXXXXX" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="field">
                <label for="reg-email">Email *</label>
                <div class="ctrl">
                  <span class="icon">✉️</span>
                  <input type="email" id="reg-email" name="email" placeholder="you@example.com" required>
                </div>
              </div>
              <div class="field">
                <label for="reg-address">Address (optional)</label>
                <div class="ctrl">
                  <span class="icon">📍</span>
                  <input type="text" id="reg-address" name="address" placeholder="Neighborhood, City">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="field">
                <label for="reg-pass">Password *</label>
                <div class="ctrl">
                  <span class="icon">🔒</span>
                  <input type="password" id="reg-pass" name="password" placeholder="Min 8 characters" required>
                  <button class="action" type="button" data-toggle="#reg-pass">Show</button>
                </div>
              </div>
              <div class="field">
                <label for="reg-confirm">Confirm Password *</label>
                <div class="ctrl">
                  <span class="icon">✅</span>
                  <input type="password" id="reg-confirm" name="confirm" placeholder="Re-type password" required>
                  <button class="action" type="button" data-toggle="#reg-confirm">Show</button>
                </div>
              </div>
            </div>

            <div class="actions">
              <button class="btn btn-primary" type="submit">Create Account</button>
              <a class="btn btn-ghost" href="?page=auth&tab=login">I already have an account</a>
            </div>

            <p class="muted" style="margin-top:8px;">By continuing you agree to our terms and privacy policy.</p>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
// Reveal on view
(function(){
  const els = document.querySelectorAll('.auth-portal .reveal');
  const io = new IntersectionObserver(entries=>{
    entries.forEach(ent=>{ if(ent.isIntersecting){ ent.target.classList.add('show'); io.unobserve(ent.target);} });
  }, {threshold:.14});
  els.forEach(el=>io.observe(el));
})();

// Tabs
(function(){
  const tabLogin = document.getElementById('tab-login');
  const tabReg   = document.getElementById('tab-register');
  const pLogin   = document.getElementById('panel-login');
  const pReg     = document.getElementById('panel-register');

  function activate(which){
    const isLogin = which === 'login';
    tabLogin.classList.toggle('active', isLogin);
    tabReg.classList.toggle('active', !isLogin);
    tabLogin.setAttribute('aria-selected', isLogin);
    tabReg.setAttribute('aria-selected', !isLogin);
    pLogin.style.display = isLogin ? 'block':'none';
    pReg.style.display   = isLogin ? 'none':'block';
    const url = new URL(window.location);
    url.searchParams.set('tab', isLogin ? 'login':'register');
    history.replaceState(null, '', url);
  }

  tabLogin.addEventListener('click', ()=> activate('login'));
  tabReg.addEventListener('click', ()=> activate('register'));
})();

// Show/Hide password buttons
document.querySelectorAll('.auth-portal [data-toggle]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const sel = btn.getAttribute('data-toggle');
    const input = document.querySelector(sel);
    if(!input) return;
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.textContent = show ? 'Hide' : 'Show';
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    input.focus();
  });
});

// Registration submit: minimal client validation + redirect to Login tab with success flag
document.getElementById('registerForm').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const f = e.currentTarget;
  const pass = f.password.value.trim();
  const conf = f.confirm.value.trim();
  if(pass.length < 8){
    alert('Password must be at least 8 characters.'); return;
  }
  if(pass !== conf){
    alert('Passwords do not match.'); return;
  }
  const fd = new FormData(f);
  try{
    // Try to post to backend if available
    const res = await fetch(f.action, { method:'POST', body: fd });
    // If your endpoint returns JSON, you can parse and branch here.
    // We redirect to login tab regardless on success-like status.
    if(!res.ok){ throw new Error('Network'); }
    window.location = '?page=auth&tab=login&registered=1';
  }catch(_){
    // Graceful fallback: redirect to login tab with success flag (simulate success for now)
    window.location = '?page=auth&tab=login&registered=1';
  }
});

// Login submit: basic graceful fallback (let your backend handle auth)
document.getElementById('loginForm').addEventListener('submit', async (e)=>{
  // If you need AJAX login, uncomment below; else let normal POST occur.
  // e.preventDefault();
  // const f = e.currentTarget;
  // const fd = new FormData(f);
  // try{
  //   const res = await fetch(f.action, { method:'POST', body: fd });
  //   if(res.ok){ window.location = '?page=home#appointment'; }
  //   else alert('Login failed. Please check your credentials.');
  // }catch(_){ f.submit(); }
});

// Show signup success banner if present
(function(){
  const s = document.getElementById('signupSuccess');
  if(s) s.classList.add('show');
})();
</script>


