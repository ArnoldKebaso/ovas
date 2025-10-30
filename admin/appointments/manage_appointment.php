<?php
// admin/appointments/manage_appointment.php  (styled UI)

$ROOT = realpath(__DIR__ . '/../../');            // -> C:\xampp\htdocs\ovas
require_once $ROOT . '/initialize.php';

/** @var PDO $pdo */
$pdo = db();

// -------- helpers ----------
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function genCode(){ return 'APPT-' . date('ymdHis'); }

// -------- POST actions -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'create') {
    $code   = genCode();
    $user   = (int)($_POST['user_id'] ?? 0);
    $pet    = (int)($_POST['pet_id'] ?? 0);
    $svc    = (int)($_POST['service_id'] ?? 0);
    $slot   = (int)($_POST['time_slot_id'] ?? 0);
    $date   = $_POST['schedule_date'] ?? date('Y-m-d');
    $notes  = $_POST['notes'] ?? null;

    $stmt = $pdo->prepare("
      INSERT INTO appointments (code,user_id,pet_id,service_id,time_slot_id,schedule_date,notes,status)
      VALUES (?,?,?,?,?,?,?, 'Pending')
    ");
    $stmt->execute([$code,$user,$pet,$svc,$slot,$date,$notes]);

    header('Location: /ovas/admin/index.php?page=appointments&created=1'); exit;
  }

  if ($action === 'status') {
    $id  = (int)($_POST['id'] ?? 0);
    $val = $_POST['value'] ?? 'Pending';
    if ($id && in_array($val, ['Pending','Confirmed','Completed','Cancelled'], true)) {
      $pdo->prepare("UPDATE appointments SET status=? WHERE id=?")->execute([$val,$id]);
    }
    header('Location: /ovas/admin/index.php?page=appointments&updated=1'); exit;
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) $pdo->prepare("DELETE FROM appointments WHERE id=?")->execute([$id]);
    header('Location: /ovas/admin/index.php?page=appointments&deleted=1'); exit;
  }
}

// -------- selects ----------
$users    = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$pets     = $pdo->query("SELECT id, name FROM pets ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT id, name FROM services WHERE is_active=1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$slots    = $pdo->query("SELECT id, start_time, end_time FROM time_slots WHERE is_active=1 ORDER BY start_time")->fetchAll(PDO::FETCH_ASSOC);

// -------- data table -------
$sql = "
SELECT a.id,a.code,a.schedule_date,a.status,
       u.name   AS client,
       p.name   AS pet,
       s.name   AS service,
       CONCAT(t.start_time,' - ',t.end_time) AS time_slot
FROM appointments a
JOIN users      u ON u.id=a.user_id
JOIN pets       p ON p.id=a.pet_id
JOIN services   s ON s.id=a.service_id
JOIN time_slots t ON t.id=a.time_slot_id
ORDER BY a.created_at DESC
";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$count = count($rows);
?>
<style>
/* scoped, minimal, fits your current theme */
.ap-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
.ap-left{display:flex;gap:12px;align-items:center}
.ap-right{display:flex;gap:8px;align-items:center}
.badge{display:inline-block;padding:.25rem .5rem;border-radius:999px;font-size:.75rem;font-weight:600}
.badge--pending{background:#fff7ed;color:#9a3412;border:1px solid #fed7aa}
.badge--confirmed{background:#eef2ff;color:#3730a3;border:1px solid #c7d2fe}
.badge--completed{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
.badge--cancelled{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.table th{white-space:nowrap}
.table td,.table th{vertical-align:middle}
#apForm .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
@media (max-width: 900px){#apForm .grid{grid-template-columns:1fr}}
.input,select,textarea{width:100%}
.card + .card{margin-top:12px}
.btn-icon{padding:6px 10px}
</style>

<div class="card shadow-sm">
  <div class="card-body ap-toolbar">
    <div class="ap-left">
      <h3 style="margin:0">Appointments</h3>
      <span class="badge badge--confirmed" title="Total records"><?= (int)$count ?> total</span>
      <?php if(isset($_GET['created'])): ?><span class="badge badge--completed">Created</span><?php endif; ?>
      <?php if(isset($_GET['updated'])): ?><span class="badge badge--confirmed">Updated</span><?php endif; ?>
      <?php if(isset($_GET['deleted'])): ?><span class="badge badge--cancelled">Deleted</span><?php endif; ?>
    </div>
    <div class="ap-right">
      <input id="apSearch" class="input" placeholder="Search client, pet or service…" style="max-width:260px">
      <select id="apStatus" class="input" style="max-width:170px">
        <option value="">All statuses</option>
        <option>Pending</option>
        <option>Confirmed</option>
        <option>Completed</option>
        <option>Cancelled</option>
      </select>
      <button class="btn btn-primary" onclick="toggleApForm(true)">+ New Appointment</button>
    </div>
  </div>
</div>

<!-- New appointment (collapsible) -->
<div id="apForm" class="card shadow-sm" style="display:none">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <h4 style="margin:0">New Appointment</h4>
      <button class="btn btn-outline btn-icon" type="button" onclick="toggleApForm(false)">Close</button>
    </div>

    <form method="post" action="/ovas/admin/index.php?page=appointments">
      <input type="hidden" name="action" value="create">
      <div class="grid">
        <label>Client
          <select name="user_id" class="input" required>
            <option value="">Select client</option>
            <?php foreach($users as $u): ?>
              <option value="<?= (int)$u['id'] ?>"><?= h($u['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Pet
          <select name="pet_id" class="input" required>
            <option value="">Select pet</option>
            <?php foreach($pets as $p): ?>
              <option value="<?= (int)$p['id'] ?>"><?= h($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Service
          <select name="service_id" class="input" required>
            <option value="">Select service</option>
            <?php foreach($services as $s): ?>
              <option value="<?= (int)$s['id'] ?>"><?= h($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Date
          <input type="date" class="input" name="schedule_date" value="<?= h(date('Y-m-d')) ?>" required>
        </label>

        <label>Time Slot
          <select name="time_slot_id" class="input" required>
            <option value="">Select time</option>
            <?php foreach($slots as $t): ?>
              <option value="<?= (int)$t['id'] ?>"><?= h($t['start_time'].' – '.$t['end_time']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Notes
          <input type="text" class="input" name="notes" placeholder="Optional notes">
        </label>
      </div>

      <div style="margin-top:12px;display:flex;gap:8px">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn btn-outline" type="button" onclick="toggleApForm(false)">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- List -->
<div class="card shadow-sm">
  <div class="card-body">
    <?php if(!$rows): ?>
      <div class="alert alert-info mb-0">No appointments found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle" id="apTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Client</th>
              <th>Pet</th>
              <th>Service</th>
              <th>Date</th>
              <th>Slot</th>
              <th>Status</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): 
              $status = $r['status'] ?? 'Pending';
              $cls = 'badge--pending';
              if($status==='Confirmed') $cls='badge--confirmed';
              if($status==='Completed') $cls='badge--completed';
              if($status==='Cancelled') $cls='badge--cancelled';
            ?>
              <tr data-status="<?= h($status) ?>">
                <td><?= h($r['code']) ?></td>
                <td><?= h($r['client']) ?></td>
                <td><?= h($r['pet']) ?></td>
                <td><?= h($r['service']) ?></td>
                <td><?= h($r['schedule_date']) ?></td>
                <td><?= h($r['time_slot']) ?></td>
                <td><span class="badge <?= $cls ?>"><?= h($status) ?></span></td>
                <td>
                  <form method="post" action="/ovas/admin/index.php?page=appointments" style="display:inline">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="hidden" name="value" value="Confirmed">
                    <button class="btn btn-outline btn-icon" type="submit">Confirm</button>
                  </form>
                  <form method="post" action="/ovas/admin/index.php?page=appointments" style="display:inline">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="hidden" name="value" value="Completed">
                    <button class="btn btn-outline btn-icon" type="submit">Complete</button>
                  </form>
                  <form method="post" action="/ovas/admin/index.php?page=appointments" style="display:inline" onsubmit="return confirm('Delete this appointment?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-outline btn-icon" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleApForm(open){ document.getElementById('apForm').style.display = open ? 'block' : 'none'; }

// client-side search + filter (quick UX)
const q = document.getElementById('apSearch');
const s = document.getElementById('apStatus');
const rows = [...document.querySelectorAll('#apTable tbody tr')];

function applyFilter(){
  const term = (q.value || '').toLowerCase();
  const status = s.value;
  rows.forEach(tr=>{
    const text = tr.innerText.toLowerCase();
    const okText = !term || text.includes(term);
    const okStatus = !status || tr.dataset.status === status;
    tr.style.display = (okText && okStatus) ? '' : 'none';
  });
}
if(q) q.addEventListener('input', applyFilter);
if(s) s.addEventListener('change', applyFilter);
</script>
