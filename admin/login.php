
<?php
require_once('../config.php');

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Use the Login class for authentication
        require_once('../classes/Login.php');
        $auth = new Login();
        
        $result = json_decode($auth->login(), true);
        
        if ($result['status'] === 'success') {
            // Redirect to admin dashboard
            header('Location: index.php?page=home');
            exit;
        } else {
            // Set error message in session
            session_start();
            $_SESSION['login_error'] = $result['msg'] ?? 'Login failed. Please try again.';
        }
    }
}

// Start session to check if already logged in
session_start();

// Check if user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header('Location: index.php?page=home');
    exit;
}

// Safe fallbacks from settings, used only for text
$short_name = isset($_settings) ? ($_settings->info('short_name') ?: 'VAP') : 'VAP';
$home_url   = "../?page=home";
?>
<style>
/* ===== Admin Login (scoped, matches site theme) ===== */
.admin-login :root {} /* noop – we rely on same palette */
.admin-login .hero-wrap{
  background: linear-gradient(135deg, #2563eb, #22c1c3);
  color:#fff;
}
.admin-login .hero{
  max-width:1180px; margin:0 auto;
  padding: clamp(36px, 7vw, 96px) 16px 56px;
  display:flex; gap:24px; align-items:flex-end; flex-wrap:wrap;
}
.admin-login .hero h1{
  margin:0; font-size: clamp(30px, 5vw, 48px); line-height:1.07; font-weight:900;
}
.admin-login .hero p{ margin:8px 0 0; opacity:.92; }

.admin-login .section{ padding: clamp(28px, 6vw, 72px) 16px; background:#fff; }
.admin-login .container{ max-width:1180px; margin:0 auto; }

.admin-login .wrap{
  display:grid; grid-template-columns: 1.2fr .95fr; gap:22px; align-items:start;
}
@media (max-width: 980px){ .admin-login .wrap{ grid-template-columns:1fr; } }

.admin-login .intro{
  color:#0f172a; padding:8px 6px;
}
.admin-login .intro .badge{
  display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px;
  background:#eef2ff; color:#1d4ed8; font-weight:800; border:1px solid #dbe6ff;
}
.admin-login .intro h2{ margin:10px 0 6px; font-size: clamp(26px, 3vw, 36px); font-weight:900; color:#0f172a; }
.admin-login .intro p{ margin:0; color:#64748b; max-width:640px; }

.admin-login .card{
  background:#fff; border:1px solid #e6edf7; border-radius:22px; box-shadow:0 8px 20px rgba(2,6,23,.06);
  padding:18px;
}
.admin-login .head{
  display:flex; align-items:center; gap:12px; margin-bottom:6px;
}
.admin-login .logo{
  width:52px; height:52px; border-radius:14px; display:grid; place-items:center; color:#fff;
  background:linear-gradient(135deg, #2563eb, #22c1c3); box-shadow:0 10px 28px rgba(37,99,235,.15);
}
.admin-login h3{ margin:0; font-size:22px; font-weight:800; color:#0f172a; }
.admin-login .muted{ color:#64748b; }

.admin-login .field{ margin-top:12px; }
.admin-login label{ display:block; color:#334155; margin-bottom:6px; font-size:14px;}
.admin-login .ctrl{
  display:flex; align-items:center; gap:10px; background:#fff; border:1px solid #e6edf7; border-radius:12px;
  padding: 10px 12px; transition:border-color .18s ease, box-shadow .18s ease;
}
.admin-login .ctrl:focus-within{ border-color:#cfe0ff; box-shadow:0 0 0 4px #ecf3ff; }
.admin-login .ctrl .icon{ width:22px; color:#2563eb; opacity:.9; }
.admin-login .ctrl input{
  border:0; outline:none; width:100%; font-size:15px; background:transparent; color:#0f172a;
}
.admin-login .ctrl .action{
  cursor:pointer; border:0; background:#eef2ff; color:#1d4ed8; font-weight:800; padding:8px 10px; border-radius:10px;
}

.admin-login .row-between{
  display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:10px;
}
.admin-login .row-between label{ display:flex; align-items:center; gap:8px; color:#334155; font-size:14px; }
.admin-login .link{ color:#2563eb; text-decoration:none; font-weight:700; }
.admin-login .link:hover{ text-decoration:underline; }

.admin-login .actions{ display:flex; gap:10px; align-items:center; margin-top:12px; flex-wrap:wrap; }
.admin-login .btn{ display:inline-flex; align-items:center; gap:8px; padding:12px 18px; border-radius:999px; border:0; cursor:pointer; font-weight:800; }
.admin-login .btn-primary{ background:#2563eb; color:#fff; box-shadow:0 10px 28px rgba(37,99,235,.15); }
.admin-login .btn-ghost{ background:#eef2ff; color:#1d4ed8; }

.admin-login .footnote{ margin-top:12px; color:#64748b; font-size:13.5px; }

.admin-login .alert{ display:none; margin-top:10px; padding:10px 12px; border-radius:12px; font-weight:700; }
.admin-login .alert.err{ display:block; background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

/* tiny reveal */
.admin-login .reveal{ opacity:0; transform: translateY(10px); transition:opacity .5s ease, transform .5s ease; }
.admin-login .reveal.show{ opacity:1; transform:none; }
</style>

<div class="admin-login">

  <!-- Hero, same gradient style as other pages -->
  <section class="hero-wrap">
    <div class="hero">
      <div>
        <h1><?php echo htmlspecialchars($short_name) ?> Admin</h1>
        <p>Sign in to manage services, appointments, and content with ease.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container wrap">
      <!-- Left: short intro, matches site typography -->
      <div class="intro reveal">
        <span class="badge">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="#1d4ed8" stroke-width="2"></circle>
            <path d="M12 7v5l3 3" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round"></path>
          </svg>
          Admin Area
        </span>
        <h2>Welcome back</h2>
        <p>Use your administrator credentials to access the dashboard. You can jump back to the website anytime from the top navbar.</p>
      </div>

      <!-- Right: login card -->
      <div class="card reveal">
        <div class="head">
          <div class="logo">🐾</div>
          <div>
            <h3>Login</h3>
            <div class="muted">Administrator access</div>
          </div>
        </div>

        <?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']): ?>
          <div class="alert err"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
        <?php endif; ?>

        <!-- Keep your backend contract: post to login.php (adjust if your handler differs) -->
        <form id="login-frm" action="login.php" method="post" autocomplete="on">
          <div class="field">
            <label for="username">Email Address</label>
            <div class="ctrl">
              <span class="icon">�</span>
              <input type="email" id="username" name="username" placeholder="admin@ovas.test" required autofocus>
            </div>
          </div>

          <div class="field">
            <label for="password">Password</label>
            <div class="ctrl">
              <span class="icon">🔒</span>
              <input type="password" id="password" name="password" placeholder="••••••••" required>
              <button class="action" type="button" id="togglePass" aria-label="Show password">Show</button>
            </div>
          </div>

          <div class="row-between">
            <label><input type="checkbox" name="remember" value="1"> Remember me</label>
            <a class="link" href="<?php echo $home_url ?>">Go to Website</a>
          </div>

          <div class="actions">
            <button class="btn btn-primary" type="submit">Sign In</button>
            <a class="btn btn-ghost" href="<?php echo $home_url ?>">Back Home</a>
          </div>

          <div class="footnote">
            Trouble signing in? Contact your system administrator to reset your access.
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<script>
// simple reveal-on-view (scoped)
(function(){
  const els = document.querySelectorAll('.admin-login .reveal');
  const io = new IntersectionObserver(entries=>{
    entries.forEach(ent=>{ if(ent.isIntersecting){ ent.target.classList.add('show'); io.unobserve(ent.target);} });
  }, {threshold:.14});
  els.forEach(el=>io.observe(el));
})();

// show/hide password, and small UX polish to disable submit if empty
(function(){
  const btn = document.getElementById('togglePass');
  const pass = document.getElementById('password');
  const user = document.getElementById('username');
  const form = document.getElementById('login-frm');
  const submit = form.querySelector('button[type="submit"]');

  if(btn && pass){
    btn.addEventListener('click', ()=>{
      const show = pass.type === 'password';
      pass.type = show ? 'text' : 'password';
      btn.textContent = show ? 'Hide' : 'Show';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      pass.focus();
    });
  }
  const check = ()=> submit.disabled = !(user.value.trim() && pass.value.trim());
  user.addEventListener('input', check); pass.addEventListener('input', check); check();
})();
</script>

