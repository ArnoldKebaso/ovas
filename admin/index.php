<?php
// admin/index.php - Admin dashboard router
require_once __DIR__ . '/../config.php';

// Enforce admin session
require_once __DIR__ . '/inc/auth_check.php';

// Determine current section
$current = isset($_GET['page']) ? trim($_GET['page']) : 'dashboard';

// Handle AJAX sub-requests without emitting layout chrome
if (isset($_GET['ajax'])) {
  switch ($current) {
    case 'appointments':
      require __DIR__ . '/appointments/manage_appointment.php';
      break;
    default:
      header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'error' => 'Unknown ajax route']);
  }
  exit;
}

// Layout shell (topbar + sidebar)
require_once __DIR__ . '/inc/header.php';
?>
<main class="content">
<?php
switch ($current) {

  /* =========================
     APPOINTMENTS (CRUD + DATA)
     ========================= */
  case 'appointments':
    echo '<h1 class="page-title">Appointments</h1>';

    // Always point to /admin/appointments/*
    $APPT_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'appointments';
    $crud     = $APPT_DIR . DIRECTORY_SEPARATOR . 'manage_appointment.php';

    if (is_file($crud)) {
      require $crud;      // full CRUD UI
    } else {
      echo '<div class="card"><div class="card-body">Appointments module missing.</div></div>';
    }
    break;

  /* =========================
     CALENDAR
     ========================= */
  case 'calendar':
    echo '<h1 class="page-title">Calendar</h1>';
    echo '<div class="card">Embed calendar widget / Google Calendar feed.</div>';
    break;

  /* =========================
     SERVICES
     ========================= */
  case 'services':
    echo '<h1 class="page-title">Services</h1>';
    echo '<div class="card">Manage service list (name, fee, duration).</div>';
    break;

  /* =========================
     TIME SLOTS
     ========================= */
  case 'timeslots':
  case 'time_slots':
    echo '<h1 class="page-title">Time Slots</h1>';
    echo '<div class="card">CRUD for time slots and capacity.</div>';
    break;

  /* =========================
     PAYMENTS
     ========================= */
  case 'payments':
    echo '<h1 class="page-title">Payments</h1>';
    echo '<div class="card">M-Pesa transactions & status.</div>';
    break;

  /* =========================
     DASHBOARD (default)
     ========================= */
  default:
    echo '<h1 class="page-title">Dashboard</h1>';
    ?>
    <section class="cards">
      <div class="card card--3"><div class="muted">Pending Appointments</div><div style="font-weight:800;font-size:2rem;margin-top:.25rem">0</div></div>
      <div class="card card--3"><div class="muted">Services</div><div style="font-weight:800;font-size:2rem;margin-top:.25rem">6</div></div>
      <div class="card card--3"><div class="muted">Today’s Slots</div><div style="font-weight:800;font-size:2rem;margin-top:.25rem">20</div></div>
      <div class="card card--3"><div class="muted">Payments (KES)</div><div style="font-weight:800;font-size:2rem;margin-top:.25rem">0.00</div></div>

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
    break;
}
?>
</main>
</div><!-- /.layout -->

<!-- Bootstrap 5 JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
  // Keep left menu active state even with pure-query routing
  document.querySelectorAll('#adminSidebar a[data-key]').forEach(a=>{
    a.addEventListener('click',()=> localStorage.setItem('admin.active', a.dataset.key));
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
