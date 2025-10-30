<?php
// admin/timeslots/manage.php - server-rendered CRUD (consistent with Services)
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';

/** @var PDO $pdo */
$pdo = db();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Load slots
$rows = $pdo->query("SELECT id,start_time,end_time,duration_min,max_appointments,is_active,created_at,updated_at FROM time_slots ORDER BY start_time")->fetchAll(PDO::FETCH_ASSOC);
$total = count($rows);
?>
<style>
.ts-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
.ts-left{display:flex;gap:12px;align-items:center}
.badge{display:inline-block;padding:.25rem .5rem;border-radius:999px;font-size:.75rem;font-weight:600}
.badge--ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
.badge--muted{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.badge--warn{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.table th{white-space:nowrap}
.table td,.table th{vertical-align:middle}
#tsForm .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
@media (max-width: 900px){#tsForm .grid{grid-template-columns:1fr}}
.input{width:100%}
.btn-icon{padding:6px 10px}
</style>

<div class="card shadow-sm">
  <div class="card-body ts-toolbar">
    <div class="ts-left">
      <h3 style="margin:0">Time Slots</h3>
      <span class="badge badge--muted" title="Total records"><?= (int)$total ?> total</span>
      <?php if(isset($_GET['created'])): ?><span class="badge badge--ok">Created</span><?php endif; ?>
      <?php if(isset($_GET['updated'])): ?><span class="badge badge--muted">Updated</span><?php endif; ?>
      <?php if(isset($_GET['deleted'])): ?><span class="badge badge--warn">Deleted</span><?php endif; ?>
    </div>
    <div>
      <button class="btn btn-primary" onclick="toggleTsForm(true)">+ New Slot</button>
    </div>
  </div>
</div>

<?php $editing=null; if(isset($_GET['edit'])){ $st=$pdo->prepare("SELECT * FROM time_slots WHERE id=?"); $st->execute([(int)$_GET['edit']]); $editing=$st->fetch(PDO::FETCH_ASSOC);} ?>
<div id="tsForm" class="card shadow-sm" style="display:<?= $editing? 'block':'none' ?>">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <h4 style="margin:0"><?= $editing? 'Edit Time Slot' : 'New Time Slot' ?></h4>
      <button class="btn btn-outline btn-icon" type="button" onclick="toggleTsForm(false)">Close</button>
    </div>

    <form method="post" action="/ovas/admin/index.php?page=timeslots">
      <input type="hidden" name="action" value="<?= $editing? 'update':'create' ?>">
      <?php if($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
      <div class="grid">
        <label>Start Time
          <input type="time" class="input" name="start_time" value="<?= h($editing['start_time'] ?? '') ?>" required>
        </label>
        <label>End Time
          <input type="time" class="input" name="end_time" value="<?= h($editing['end_time'] ?? '') ?>" required>
        </label>
        <label>Duration (min)
          <input type="number" class="input" name="duration_min" value="<?= h($editing['duration_min'] ?? '30') ?>" min="1" required>
        </label>
        <label>Capacity
          <input type="number" class="input" name="max_appointments" value="<?= h($editing['max_appointments'] ?? '1') ?>" min="1" required>
        </label>
        <label>
          <input type="checkbox" name="is_active" value="1" <?= ($editing ? (int)$editing['is_active'] : 1) ? 'checked' : '' ?>> Active
        </label>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn btn-outline" type="button" onclick="toggleTsForm(false)">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if(!$rows): ?>
      <div class="alert alert-info mb-0">No time slots found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle" id="tsTable">
          <thead>
            <tr>
              <th>Start</th>
              <th>End</th>
              <th>Duration</th>
              <th>Capacity</th>
              <th>Status</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?= h($r['start_time']) ?></td>
                <td><?= h($r['end_time']) ?></td>
                <td><?= (int)$r['duration_min'] ?> min</td>
                <td><?= (int)$r['max_appointments'] ?></td>
                <td><?= $r['is_active']? '<span class="badge badge--ok">Active</span>':'<span class="badge badge--muted">Inactive</span>' ?></td>
                <td>
                  <a class="btn btn-outline btn-icon" href="/ovas/admin/index.php?page=timeslots&edit=<?= (int)$r['id'] ?>">Edit</a>
                  <form method="post" action="/ovas/admin/index.php?page=timeslots" style="display:inline">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-outline btn-icon" type="submit"><?= $r['is_active']? 'Deactivate':'Activate' ?></button>
                  </form>
                  <form method="post" action="/ovas/admin/index.php?page=timeslots" style="display:inline" onsubmit="return confirm('Delete this slot?')">
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
function toggleTsForm(open){ document.getElementById('tsForm').style.display = open ? 'block' : 'none'; }
</script>

