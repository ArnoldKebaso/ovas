<?php
if (!isset($_settings)) require_once __DIR__ . '/sess_auth.php';

// Use helpers/consts from header.php
$appName  = $_settings->info('short_name') ?: ($_settings->info('name') ?: 'OVAS');
$userName = trim((string)($_settings->userdata('firstname') ?? '') . ' ' . (string)($_settings->userdata('lastname') ?? ''));
if (!$userName) $userName = $_settings->userdata('username') ?? 'Admin';
$avatar   = safe_img($_settings->userdata('avatar') ?? '', OVAS_PH_AVATAR);
$userType = (int)($_settings->userdata('type') ?? 1);
$current  = $_GET['page'] ?? 'home';

function is_active($needle, $current){
  if ($needle === 'home') return ($current === 'home' || $current === '');
  return (strpos($current ?? '', $needle) !== false);
}

// Optional: small badge
$pendingAppointments = 0;
try {
  if (class_exists('AppointmentsModel')) {
    $am = new AppointmentsModel();
    if (method_exists($am,'countPending'))       $pendingAppointments = (int)$am->countPending();
    elseif (method_exists($am,'countByStatus'))  $pendingAppointments = (int)$am->countByStatus('pending');
  }
} catch(Throwable $e){ $pendingAppointments=0; }
?>
<aside class="main-sidebar elevation-4 sidebar-dark-primary">
  <!-- Brand (text only to avoid double logo) -->
  <a href="<?php echo base_url ?>" class="brand-link">
    <span class="brand-text font-weight-light"><strong><?php echo htmlspecialchars($appName) ?></strong></span>
  </a>

  <div class="sidebar">
    <!-- User panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
      <div class="image">
        <img src="<?php echo $avatar ?>" class="img-circle elevation-2" alt="User" style="width:35px;height:35px;" onerror="this.src='<?php echo OVAS_PH_AVATAR ?>'">
      </div>
      <div class="info">
        <a href="<?php echo base_url ?>?page=profile" class="d-block"><?php echo htmlspecialchars($userName) ?></a>
      </div>
    </div>

    <!-- Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
        <li class="nav-item">
          <a href="<?php echo base_url ?>" class="nav-link <?php echo is_active('home',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=appointments" class="nav-link <?php echo is_active('appointments',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-calendar-check"></i>
            <p>Appointments <?php if($pendingAppointments>0): ?><span class="right badge badge-warning"><?php echo $pendingAppointments ?></span><?php endif; ?></p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=calendar" class="nav-link <?php echo is_active('calendar',$current)?'active':'' ?>">
            <i class="nav-icon far fa-calendar-alt"></i><p>Calendar</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=pets" class="nav-link <?php echo is_active('pets',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-paw"></i><p>Pets</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=services" class="nav-link <?php echo is_active('services',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-stethoscope"></i><p>Services</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=time_slots" class="nav-link <?php echo is_active('time_slots',$current)?'active':'' ?>">
            <i class="nav-icon far fa-clock"></i><p>Time Slots</p>
          </a>
        </li>

        <?php if ($userType === 1): ?>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=payments" class="nav-link <?php echo is_active('payments',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-receipt"></i><p>Payments</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=users" class="nav-link <?php echo is_active('users',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-users"></i><p>Users</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=system_settings" class="nav-link <?php echo is_active('system_settings',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-cogs"></i><p>System Settings</p>
          </a>
        </li>
        <?php endif; ?>

        <li class="nav-header">ACCOUNT</li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>?page=profile" class="nav-link <?php echo is_active('profile',$current)?'active':'' ?>">
            <i class="nav-icon fas fa-user"></i><p>My Profile</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo base_url ?>classes/Login.php?f=logout" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i><p>Sign out</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
