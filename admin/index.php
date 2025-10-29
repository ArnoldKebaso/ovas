<?php
// admin/index.php
// Set current section for active state BEFORE requiring header.
// Auth gate & no-cache
require_once('inc/auth_check.php');
// (Optional) app config; safe if absent
$configPath = dirname(__DIR__) . '/config.php';
if (is_file($configPath)) require_once $configPath;

// Minimal allowlist router
$allowedPages = [
    'home'        => __DIR__ . '/pages/home.php',        // your dashboard content (merged old home)
    'appointments'=> __DIR__ . '/pages/appointments.php',
    'calendar'    => __DIR__ . '/pages/calendar.php',
    'services'    => __DIR__ . '/pages/services.php',
    'timeslots'   => __DIR__ . '/pages/timeslots.php',
    'payments'    => __DIR__ . '/pages/payments.php',
    // add more here…
];

$page = isset($_GET['page']) ? strtolower(trim($_GET['page'])) : 'home';
if (!array_key_exists($page, $allowedPages)) $page = 'home';


require_once __DIR__ . '/inc/header.php';


?>
    <main class="content">
      <?php
      // Simple router for center content
      switch ($current) {
        case 'appointments':
          echo '<h1 class="page-title">Appointments</h1>';
          echo '<div class="card">Hook your appointments table here.</div>';
          break;

        case 'calendar':
          echo '<h1 class="page-title">Calendar</h1>';
          echo '<div class="card">Embed calendar widget / Google Calendar feed.</div>';
          break;

        case 'services':
          echo '<h1 class="page-title">Services</h1>';
          echo '<div class="card">Manage service list (name, fee, duration).</div>';
          break;

        case 'timeslots':
          echo '<h1 class="page-title">Time Slots</h1>';
          echo '<div class="card">CRUD for time slots and capacity.</div>';
          break;

        case 'payments':
          echo '<h1 class="page-title">Payments</h1>';
          echo '<div class="card">M-Pesa transactions & status.</div>';
          break;

        default: // dashboard
          echo '<h1 class="page-title">Dashboard</h1>';
          ?>
          <section class="cards">
            <div class="card card--3">
              <div class="muted">Pending Appointments</div>
              <div style="font-weight:800;font-size:2rem;margin-top:.25rem">0</div>
            </div>
            <div class="card card--3">
              <div class="muted">Services</div>
              <div style="font-weight:800;font-size:2rem;margin-top:.25rem">6</div>
            </div>
            <div class="card card--3">
              <div class="muted">Today’s Slots</div>
              <div style="font-weight:800;font-size:2rem;margin-top:.25rem">20</div>
            </div>
            <div class="card card--3">
              <div class="muted">Payments (KES)</div>
              <div style="font-weight:800;font-size:2rem;margin-top:.25rem">0.00</div>
            </div>

            <div class="card card--6 shadow-sm">
              <h3 style="margin:0 0 .5rem">Recent Appointments</h3>
              <table>
                <thead><tr><th>Code</th><th>Client</th><th>Pet</th><th>Service</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                  <tr><td>APPT-0001</td><td>—</td><td>—</td><td>Check-up</td><td>—</td><td><span class="chip" style="background:#fff7ed;color:#c2410c">Pending</span></td></tr>
                </tbody>
              </table>
            </div>

            <div class="card card--6 shadow-sm">
              <h3 style="margin:0 0 .5rem">Quick Actions</h3>
              <div class="spacer"></div>
              <a class="btn btn-primary" href="/ovas/admin/index.php?page=appointments">Manage Appointments</a>
              <a class="btn btn-outline" style="margin-left:.5rem" href="/ovas/admin/index.php?page=services">Manage Services</a>
            </div>
          </section>
          <?php
      }
      ?>
    </main>
  </div><!-- /.layout -->

  <!-- No footer include to avoid double footers; add if needed -->
  <script>
    // Optional: persist active link on nav click if server doesn’t set $current
    document.querySelectorAll('#adminSidebar a[data-key]').forEach(a=>{
      a.addEventListener('click',()=> {
        localStorage.setItem('admin.active', a.dataset.key);
      });
    });
    const saved = localStorage.getItem('admin.active');
    if(saved){
      document.querySelectorAll('#adminSidebar a').forEach(x=>x.classList.remove('active'));
      const act = document.querySelector(`#adminSidebar a[data-key="${saved}"]`);
      if(act) act.classList.add('active');
    }
  </script>
</body>
</html>
