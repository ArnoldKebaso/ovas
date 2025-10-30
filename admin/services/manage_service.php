<?php
// admin/services/manage_service.php - server-rendered CRUD (styled like appointments)
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';

/** @var PDO $pdo */
$pdo = db();

// Helpers
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'create' || $action === 'update') {
    $id    = (int)($_POST['id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $fee   = (float)($_POST['fee'] ?? 0);
    $dur   = (int)($_POST['duration_min'] ?? 30);
    $act   = isset($_POST['is_active']) ? 1 : 0;

    if ($name !== '' && $dur > 0 && $fee >= 0) {
      if ($action === 'create') {
        $stmt = $pdo->prepare("INSERT INTO services (name,description,fee,duration_min,is_active) VALUES (?,?,?,?,?)");
        $stmt->execute([$name,$desc,$fee,$dur,$act]);
        header('Location: /ovas/admin/index.php?page=services&created=1'); exit;
      } else {
        $stmt = $pdo->prepare("UPDATE services SET name=?, description=?, fee=?, duration_min=?, is_active=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
        $stmt->execute([$name,$desc,$fee,$dur,$act,$id]);
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
    if ($id) {
      try {
        $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$id]);
        header('Location: /ovas/admin/index.php?page=services&deleted=1'); exit;
      } catch (Throwable $e) {
        header('Location: /ovas/admin/index.php?page=services&error=ref'); exit;
      }
    }
  }
}

// If editing, load current row
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = null;
if ($editId) {
  $st = $pdo->prepare("SELECT * FROM services WHERE id=?");
  $st->execute([$editId]);
  $editing = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Load services
$rows = $pdo->query("SELECT id,name,description,fee,duration_min,is_active,created_at,updated_at FROM services ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$total = count($rows);
?>
<style>
.sv-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
.sv-left{display:flex;gap:12px;align-items:center}
.badge{display:inline-block;padding:.25rem .5rem;border-radius:999px;font-size:.75rem;font-weight:600}
.badge--ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
.badge--muted{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.badge--warn{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.table th{white-space:nowrap}
.table td,.table th{vertical-align:middle}
#svcForm .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
@media (max-width: 900px){#svcForm .grid{grid-template-columns:1fr}}
.input,textarea{width:100%}
.btn-icon{padding:6px 10px}
</style>

<div class="card shadow-sm">
  <div class="card-body sv-toolbar">
    <div class="sv-left">
      <h3 style="margin:0">Services</h3>
      <span class="badge badge--muted" title="Total records"><?= (int)$total ?> total</span>
      <?php if(isset($_GET['created'])): ?><span class="badge badge--ok">Created</span><?php endif; ?>
      <?php if(isset($_GET['updated'])): ?><span class="badge badge--muted">Updated</span><?php endif; ?>
      <?php if(isset($_GET['deleted'])): ?><span class="badge badge--warn">Deleted</span><?php endif; ?>
    </div>
    <div>
      <button class="btn btn-primary" onclick="toggleSvcForm(true)">+ New Service</button>
    </div>
  </div>
</div>

<!-- Create/Update form -->
<div id="svcForm" class="card shadow-sm" style="display:<?= $editing? 'block':'none' ?>">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <h4 style="margin:0"><?= $editing? 'Edit Service' : 'New Service' ?></h4>
      <button class="btn btn-outline btn-icon" type="button" onclick="toggleSvcForm(false)">Close</button>
    </div>

    <form method="post" action="/ovas/admin/index.php?page=services">
      <input type="hidden" name="action" value="<?= $editing? 'update':'create' ?>">
      <?php if($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
      <div class="grid">
        <label>Name
          <input type="text" class="input" name="name" value="<?= h($editing['name'] ?? '') ?>" required>
        </label>
        <label>Fee (KSh)
          <input type="number" class="input" step="0.01" name="fee" value="<?= h($editing['fee'] ?? '0.00') ?>" required>
        </label>
        <label>Duration (minutes)
          <input type="number" class="input" name="duration_min" value="<?= h($editing['duration_min'] ?? '30') ?>" min="1" required>
        </label>
        <label>Status
          <input type="checkbox" name="is_active" value="1" <?= ($editing ? (int)$editing['is_active'] : 1) ? 'checked' : '' ?>> Active
        </label>
        <label style="grid-column:1/-1">Description
          <textarea class="input" name="description" rows="3" placeholder="Optional details"><?= h($editing['description'] ?? '') ?></textarea>
        </label>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn btn-outline" type="button" onclick="toggleSvcForm(false)">Cancel</button>
      </div>
    </form>
  </div>
  </div>

<!-- Services list -->
<div class="card shadow-sm">
  <div class="card-body">
    <?php if(!$rows): ?>
      <div class="alert alert-info mb-0">No services found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle" id="svcTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Duration</th>
              <th>Fee</th>
              <th>Status</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?= h($r['name']) ?></td>
                <td><?= h($r['description']) ?></td>
                <td><?= (int)$r['duration_min'] ?> mins</td>
                <td>KSh <?= number_format((float)$r['fee'],2) ?></td>
                <td><?= $r['is_active']? '<span class="badge badge--ok">Active</span>':'<span class="badge badge--muted">Inactive</span>' ?></td>
                <td>
                  <a class="btn btn-outline btn-icon" href="/ovas/admin/index.php?page=services&edit=<?= (int)$r['id'] ?>">Edit</a>
                  <form method="post" action="/ovas/admin/index.php?page=services" style="display:inline">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-outline btn-icon" type="submit"><?= $r['is_active']? 'Deactivate':'Activate' ?></button>
                  </form>
                  <form method="post" action="/ovas/admin/index.php?page=services" style="display:inline" onsubmit="return confirm('Delete this service?')">
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
function toggleSvcForm(open){ document.getElementById('svcForm').style.display = open ? 'block' : 'none'; }
</script>
