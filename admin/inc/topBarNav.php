<?php
if (!isset($_settings)) require_once __DIR__ . '/sess_auth.php';

// Use helpers/consts from header.php (do NOT redeclare functions)
$logo    = safe_img($_settings->info('logo'), OVAS_PH_LOGO);
$appName = $_settings->info('short_name') ?: ($_settings->info('name') ?: 'OVAS');

$userName = trim((string)($_settings->userdata('firstname') ?? '') . ' ' . (string)($_settings->userdata('lastname') ?? ''));
if (!$userName) $userName = $_settings->userdata('username') ?? 'Admin';
$userEmail = $_settings->userdata('email') ?? '';
$userType  = (int)($_settings->userdata('type') ?? 1);
$avatar    = safe_img($_settings->userdata('avatar') ?? '', OVAS_PH_AVATAR);

// Pending appointments (model first, fallback DB)
$pendingAppointments = 0;
try {
  if (class_exists('AppointmentsModel')) {
    $am = new AppointmentsModel();
    if (method_exists($am,'countPending'))       $pendingAppointments = (int)$am->countPending();
    elseif (method_exists($am,'countByStatus'))  $pendingAppointments = (int)$am->countByStatus('pending');
  } elseif (class_exists('DBConnection')) {
    $db = new DBConnection(); $conn = $db->conn ?? null;
    if ($conn) {
      $tbl=null;
      $s=$conn->query("SHOW TABLES LIKE 'appointments'"); if($s && $s->rowCount()>0) $tbl='appointments';
      if(!$tbl){ $s=$conn->query("SHOW TABLES LIKE 'appointment_list'"); if($s && $s->rowCount()>0) $tbl='appointment_list'; }
      if($tbl){
        $q=$conn->prepare("SELECT COUNT(*) cnt FROM {$tbl} WHERE (status='pending' OR status=0 OR status IS NULL)");
        $q->execute(); $pendingAppointments=(int)($q->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);
      }
    }
  }
} catch(Throwable $e){ $pendingAppointments=0; }

$logoutUrl = base_url . "classes/Login.php?f=logout";
?>
<nav class="main-header navbar navbar-expand navbar-dark">
  <!-- Left -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="<?php echo base_url ?>" class="nav-link d-flex align-items-center">
        <!-- SINGLE logo (top bar only) -->
        <img src="<?php echo $logo ?>" class="brand-image elevation-2 mr-2" alt="Logo" onerror="this.src='<?php echo OVAS_PH_LOGO ?>'">
        <strong class="text-white"><?php echo htmlspecialchars($appName) ?></strong>
      </a>
    </li>
  </ul>

  <!-- Right -->
  <ul class="navbar-nav ml-auto">
    <!-- Notifications -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" aria-label="Notifications">
        <i class="far fa-bell"></i>
        <?php if ($pendingAppointments>0): ?>
          <span class="badge badge-warning navbar-badge"><?php echo $pendingAppointments ?></span>
        <?php endif; ?>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">
          <?php echo $pendingAppointments ?> Pending Appointment<?php echo $pendingAppointments==1?'':'s' ?>
        </span>
        <div class="dropdown-divider"></div>
        <a href="<?php echo base_url ?>?page=appointments" class="dropdown-item">
          <i class="fas fa-calendar-check mr-2"></i> View Appointments
        </a>
      </div>
    </li>

    <!-- Fullscreen -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button" aria-label="Fullscreen">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <!-- User -->
    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
        <img src="<?php echo $avatar ?>" class="user-image img-circle elevation-2 mr-2" alt="User" onerror="this.src='<?php echo OVAS_PH_AVATAR ?>'">
        <span class="d-none d-md-inline text-white"><?php echo htmlspecialchars($userName) ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <!-- No logo here -> avoid duplicates -->
        <li class="user-header" style="background:linear-gradient(90deg,#2563eb,#1d4ed8)">
          <img src="<?php echo $avatar ?>" class="img-circle elevation-2 mb-2" alt="User" onerror="this.src='<?php echo OVAS_PH_AVATAR ?>'">
          <p class="mb-0"><?php echo htmlspecialchars($userName) ?><br><small><?php echo htmlspecialchars($userEmail) ?></small></p>
        </li>
        <li class="user-body">
          <div class="row text-center">
            <div class="col-6"><a href="<?php echo base_url ?>?page=profile" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-user mr-1"></i> Profile</a></div>
            <div class="col-6"><a href="<?php echo base_url ?>?page=change_password" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-key mr-1"></i> Password</a></div>
          </div>
        </li>
        <li class="user-footer">
          <a href="<?php echo base_url ?>" class="btn btn-default btn-flat">Home</a>
          <a href="<?php echo $logoutUrl ?>" class="btn btn-danger btn-flat float-right"><i class="fas fa-sign-out-alt mr-1"></i> Sign out</a>
        </li>
      </ul>
    </li>
  </ul>
</nav>
