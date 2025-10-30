<?php
// admin/index.php - Admin dashboard router
require_once __DIR__ . '/../config.php';

// Enforce admin session
require_once __DIR__ . '/inc/auth_check.php';

// Determine current section
$current = isset($_GET['page']) ? trim($_GET['page']) : 'dashboard';

// Handle POST actions before rendering layout to avoid blank pages
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Services CRUD (server-rendered)
  if ($current === 'services') {
    $pdo = db();
    $action = $_POST['action'] ?? '';
    try {
      if ($action === 'create' || $action === 'update') {
        $id  = (int)($_POST['id'] ?? 0);
        $nm  = trim($_POST['name'] ?? '');
        $ds  = trim($_POST['description'] ?? '');
        $fee = (float)($_POST['fee'] ?? 0);
        $dur = (int)($_POST['duration_min'] ?? 30);
        $act = isset($_POST['is_active']) ? 1 : 0;
        if ($nm !== '' && $dur > 0 && $fee >= 0) {
          if ($action === 'create') {
            $st = $pdo->prepare("INSERT INTO services (name,description,fee,duration_min,is_active) VALUES (?,?,?,?,?)");
            $st->execute([$nm,$ds,$fee,$dur,$act]);
            header('Location: /ovas/admin/index.php?page=services&created=1'); exit;
          } else {
            $st = $pdo->prepare("UPDATE services SET name=?, description=?, fee=?, duration_min=?, is_active=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
            $st->execute([$nm,$ds,$fee,$dur,$act,$id]);
            header('Location: /ovas/admin/index.php?page=services&updated=1'); exit;
          }
        }
      }
      if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("UPDATE services SET is_active = NOT is_active, updated_at=CURRENT_TIMESTAMP WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=services&updated=1'); exit;
      }
      if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=services&deleted=1'); exit;
      }
    } catch (Throwable $e) {
      header('Location: /ovas/admin/index.php?page=services&error=1'); exit;
    }
  }
  // Payments CRUD (server-rendered to match schema)
  if ($current === 'payments') {
    $pdo = db();
    $action = $_POST['action'] ?? '';
    try {
      if ($action === 'create' || $action === 'update') {
        $id   = (int)($_POST['id'] ?? 0);
        $appt = (int)($_POST['appointment_id'] ?? 0);
        $amt  = (float)($_POST['amount'] ?? 0);
        $cur  = trim($_POST['currency'] ?? 'KES');
        $prov = trim($_POST['provider'] ?? 'mpesa');
        $ref  = trim($_POST['reference'] ?? '');
        $st   = trim($_POST['status'] ?? 'initiated');
        if ($appt && $amt >= 0) {
          if ($action === 'create') {
            $q = $pdo->prepare("INSERT INTO payments (appointment_id,provider,amount,currency,reference,status,created_at,updated_at) VALUES (?,?,?,?,?,?,NOW(),NOW())");
            $q->execute([$appt,$prov,$amt,$cur,$ref,$st]);
            header('Location: /ovas/admin/index.php?page=payments&created=1'); exit;
          } else {
            $q = $pdo->prepare("UPDATE payments SET appointment_id=?, provider=?, amount=?, currency=?, reference=?, status=?, updated_at=NOW() WHERE id=?");
            $q->execute([$appt,$prov,$amt,$cur,$ref,$st,$id]);
            header('Location: /ovas/admin/index.php?page=payments&updated=1'); exit;
          }
        }
      }
      if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("DELETE FROM payments WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=payments&deleted=1'); exit;
      }
    } catch (Throwable $e) {
      header('Location: /ovas/admin/index.php?page=payments&error=1'); exit;
    }
  }
  // Pets CRUD (server-rendered)
  if ($current === 'pets') {
    $pdo = db();
    $action = $_POST['action'] ?? '';
    try {
      if ($action === 'create' || $action === 'update') {
        $id   = (int)($_POST['id'] ?? 0);
        $uid  = (int)($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $sp   = trim($_POST['species'] ?? 'dog');
        $br   = trim($_POST['breed'] ?? '');
        $age  = trim($_POST['age'] ?? '');
        $wt   = trim($_POST['weight'] ?? '');
        $nt   = trim($_POST['notes'] ?? '');
        if ($uid && $name !== '') {
          if ($action === 'create') {
            $st = $pdo->prepare("INSERT INTO pets (user_id,name,species,breed,age,weight,notes) VALUES (?,?,?,?,?,?,?)");
            $st->execute([$uid,$name,$sp,$br,$age,$wt,$nt]);
            header('Location: /ovas/admin/index.php?page=pets&created=1'); exit;
          } else {
            $st = $pdo->prepare("UPDATE pets SET user_id=?, name=?, species=?, breed=?, age=?, weight=?, notes=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
            $st->execute([$uid,$name,$sp,$br,$age,$wt,$nt,$id]);
            header('Location: /ovas/admin/index.php?page=pets&updated=1'); exit;
          }
        }
      }
      if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("DELETE FROM pets WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=pets&deleted=1'); exit;
      }
    } catch (Throwable $e) {
      header('Location: /ovas/admin/index.php?page=pets&error=1'); exit;
    }
  }
  // Time slots CRUD (server-rendered)
  if ($current === 'timeslots' || $current === 'time_slots') {
    $pdo = db();
    $action = $_POST['action'] ?? '';
    try {
      if ($action === 'create' || $action === 'update') {
        $id   = (int)($_POST['id'] ?? 0);
        $st   = $_POST['start_time'] ?? '08:00:00';
        $et   = $_POST['end_time'] ?? '08:30:00';
        $dur  = (int)($_POST['duration_min'] ?? 30);
        $cap  = (int)($_POST['max_appointments'] ?? 1);
        $act  = isset($_POST['is_active']) ? 1 : 0;
        if ($action === 'create') {
          $q = $pdo->prepare("INSERT INTO time_slots (start_time,end_time,duration_min,max_appointments,is_active) VALUES (?,?,?,?,?)");
          $q->execute([$st,$et,$dur,$cap,$act]);
          header('Location: /ovas/admin/index.php?page=timeslots&created=1'); exit;
        } else {
          $q = $pdo->prepare("UPDATE time_slots SET start_time=?,end_time=?,duration_min=?,max_appointments=?,is_active=?,updated_at=CURRENT_TIMESTAMP WHERE id=?");
          $q->execute([$st,$et,$dur,$cap,$act,$id]);
          header('Location: /ovas/admin/index.php?page=timeslots&updated=1'); exit;
        }
      }
      if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("UPDATE time_slots SET is_active = NOT is_active, updated_at=CURRENT_TIMESTAMP WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=timeslots&updated=1'); exit;
      }
      if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("DELETE FROM time_slots WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=timeslots&deleted=1'); exit;
      }
    } catch (Throwable $e) {
      header('Location: /ovas/admin/index.php?page=timeslots&error=1'); exit;
    }
  }
  // Users CRUD (server-rendered)
  if ($current === 'users') {
    $pdo = db();
    $action = $_POST['action'] ?? '';
    try {
      if ($action === 'create' || $action === 'update') {
        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email= trim($_POST['email'] ?? '');
        $phone= trim($_POST['phone'] ?? '');
        $addr = trim($_POST['address'] ?? '');
        $isad = isset($_POST['is_admin']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        $pass = $_POST['password'] ?? '';
        if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
          if ($action === 'create') {
            $hash = password_hash($pass ?: 'password', PASSWORD_BCRYPT);
            $q = $pdo->prepare("INSERT INTO users (name,email,phone,address,password_hash,is_admin,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,NOW(),NOW())");
            $q->execute([$name,$email,$phone,$addr,$hash,$isad,$status]);
            header('Location: /ovas/admin/index.php?page=users&created=1'); exit;
          } else {
            if ($pass !== '') {
              $hash = password_hash($pass, PASSWORD_BCRYPT);
              $q = $pdo->prepare("UPDATE users SET name=?,email=?,phone=?,address=?,password_hash=?,is_admin=?,status=?,updated_at=NOW() WHERE id=?");
              $q->execute([$name,$email,$phone,$addr,$hash,$isad,$status,$id]);
            } else {
              $q = $pdo->prepare("UPDATE users SET name=?,email=?,phone=?,address=?,is_admin=?,status=?,updated_at=NOW() WHERE id=?");
              $q->execute([$name,$email,$phone,$addr,$isad,$status,$id]);
            }
            header('Location: /ovas/admin/index.php?page=users&updated=1'); exit;
          }
        }
      }
      if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=users&deleted=1'); exit;
      }
    } catch (Throwable $e) {
      header('Location: /ovas/admin/index.php?page=users&error=1'); exit;
    }
  }
}

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
    $CAL_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'calendar';
    $cal     = $CAL_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($cal)) {
      require $cal;
    } else {
      echo '<div class="card"><div class="card-body">Calendar module missing.</div></div>';
    }
    break;

  /* =========================
     SERVICES
     ========================= */
  case 'services':
    echo '<h1 class="page-title">Services</h1>';
    $SERV_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'services';
    $svc     = $SERV_DIR . DIRECTORY_SEPARATOR . 'manage_service.php';
    if (is_file($svc)) {
      require $svc;
    } else {
      echo '<div class="card"><div class="card-body">Services module missing.</div></div>';
    }
    break;

  /* =========================
     PETS
     ========================= */
  case 'pets':
    echo '<h1 class="page-title">Pets</h1>';
    $PETS_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'pets';
    $pets     = $PETS_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($pets)) {
      require $pets;
    } else {
      echo '<div class="card"><div class="card-body">Pets module missing.</div></div>';
    }
    break;

  /* =========================
     USERS
     ========================= */
  case 'users':
    echo '<h1 class="page-title">Users</h1>';
    $USR_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'users';
    $usr     = $USR_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($usr)) {
      require $usr;
    } else {
      echo '<div class="card"><div class="card-body">Users module missing.</div></div>';
    }
    break;

  /* =========================
     NOTIFICATIONS
     ========================= */
  case 'notifications':
    echo '<h1 class="page-title">Notifications</h1>';
    $N_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'notifications';
    $n     = $N_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($n)) { require $n; } else { echo '<div class="card"><div class="card-body">Notifications module missing.</div></div>'; }
    break;

  /* =========================
     M-PESA
     ========================= */
  case 'mpesa':
    echo '<h1 class="page-title">M-Pesa</h1>';
    $M_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'mpesa';
    $m     = $M_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($m)) { require $m; } else { echo '<div class="card"><div class="card-body">M-Pesa module missing.</div></div>'; }
    break;

  /* =========================
     PETS
     ========================= */
  case 'pets':
    echo '<h1 class="page-title">Pets</h1>';
    $PETS_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'pets';
    $pets     = $PETS_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($pets)) {
      require $pets;
    } else {
      echo '<div class="card"><div class="card-body">Pets module missing.</div></div>';
    }
    break;

  /* =========================
     TIME SLOTS
     ========================= */
  case 'timeslots':
  case 'time_slots':
    echo '<h1 class="page-title">Time Slots</h1>';
    $TS_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'timeslots';
    $ts     = $TS_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($ts)) {
      require $ts;
    } else {
      echo '<div class="card"><div class="card-body">Time Slots module missing.</div></div>';
    }
    break;

  /* =========================
     PAYMENTS
     ========================= */
  case 'payments':
    echo '<h1 class="page-title">Payments</h1>';
    $PAY_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'payments';
    $pay     = $PAY_DIR . DIRECTORY_SEPARATOR . 'manage.php';
    if (is_file($pay)) {
      require $pay;
    } else {
      echo '<div class="card"><div class="card-body">Payments module missing.</div></div>';
    }
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
  // Inject missing menu links when header wasn’t updated yet
  const nav = document.getElementById('adminSidebar');
  const ensureLink = (key, text, href) => {
    if (!nav) return;
    if (!nav.querySelector(`a[data-key="${key}"]`)) {
      const a = document.createElement('a');
      a.href = href; a.dataset.key = key; a.textContent = text;
      nav.appendChild(a);
    }
  };
  ensureLink('users', 'Users', '/ovas/admin/index.php?page=users');
  ensureLink('notifications', 'Notifications', '/ovas/admin/index.php?page=notifications');
  ensureLink('mpesa', 'M-Pesa', '/ovas/admin/index.php?page=mpesa');
</script>
</body>
</html>

