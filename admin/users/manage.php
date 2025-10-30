<?php
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';

/** @var PDO $pdo */
$pdo = db();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$rows = $pdo->query("SELECT id,name,email,phone,address,is_admin,status,created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$total = count($rows);

$editing = null;
if (isset($_GET['edit'])) { $st=$pdo->prepare("SELECT * FROM users WHERE id=?"); $st->execute([(int)$_GET['edit']]); $editing=$st->fetch(PDO::FETCH_ASSOC); }
?>
<style>
.u-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
.u-left{display:flex;gap:12px;align-items:center}
.badge{display:inline-block;padding:.25rem .5rem;border-radius:999px;font-size:.75rem;font-weight:600}
.badge--ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
.badge--muted{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.badge--warn{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.table th{white-space:nowrap}
.table td,.table th{vertical-align:middle}
#userForm .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
@media (max-width: 900px){#userForm .grid{grid-template-columns:1fr}}
.input,textarea{width:100%}
</style>

<div class="card shadow-sm">
  <div class="card-body u-toolbar">
    <div class="u-left">
      <h3 style="margin:0">Users</h3>
      <span class="badge badge--muted" title="Total records"><?= (int)$total ?> total</span>
      <?php if(isset($_GET['created'])): ?><span class="badge badge--ok">Created</span><?php endif; ?>
      <?php if(isset($_GET['updated'])): ?><span class="badge badge--muted">Updated</span><?php endif; ?>
      <?php if(isset($_GET['deleted'])): ?><span class="badge badge--warn">Deleted</span><?php endif; ?>
    </div>
    <div>
      <button class="btn btn-primary" onclick="toggleUserForm(true)">+ New User</button>
    </div>
  </div>
</div>

<div id="userForm" class="card shadow-sm" style="display:<?= $editing? 'block':'none' ?>">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <h4 style="margin:0"><?= $editing? 'Edit User' : 'New User' ?></h4>
      <button class="btn btn-outline btn-icon" type="button" onclick="toggleUserForm(false)">Close</button>
    </div>
    <form method="post" action="/ovas/admin/index.php?page=users">
      <input type="hidden" name="action" value="<?= $editing? 'update':'create' ?>">
      <?php if($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
      <div class="grid">
        <label>Name
          <input type="text" class="input" name="name" value="<?= h($editing['name'] ?? '') ?>" required>
        </label>
        <label>Email
          <input type="email" class="input" name="email" value="<?= h($editing['email'] ?? '') ?>" required>
        </label>
        <label>Phone
          <input type="text" class="input" name="phone" value="<?= h($editing['phone'] ?? '') ?>">
        </label>
        <label>Address
          <input type="text" class="input" name="address" value="<?= h($editing['address'] ?? '') ?>">
        </label>
        <label>Password <?= $editing? '(leave blank to keep)':'' ?>
          <input type="password" class="input" name="password" <?= $editing? '':'required' ?>>
        </label>
        <label>
          <input type="checkbox" name="is_admin" value="1" <?= ($editing ? (int)$editing['is_admin'] : 0) ? 'checked' : '' ?>> Admin
        </label>
        <label>
          <input type="checkbox" name="status" value="1" <?= ($editing ? (int)$editing['status'] : 1) ? 'checked' : '' ?>> Active
        </label>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn btn-outline" type="button" onclick="toggleUserForm(false)">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if(!$rows): ?>
      <div class="alert alert-info mb-0">No users found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle" id="usersTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Status</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?= h($r['name']) ?></td>
                <td><?= h($r['email']) ?></td>
                <td><?= h($r['phone'] ?? '') ?></td>
                <td><?= ((int)$r['is_admin']===1)? 'Admin':'User' ?></td>
                <td><?= ((int)$r['status']===1)? 'Active':'Disabled' ?></td>
                <td>
                  <a class="btn btn-outline btn-icon" href="/ovas/admin/index.php?page=users&edit=<?= (int)$r['id'] ?>">Edit</a>
                  <form method="post" action="/ovas/admin/index.php?page=users" style="display:inline" onsubmit="return confirm('Delete this user?')">
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
function toggleUserForm(open){ document.getElementById('userForm').style.display = open ? 'block' : 'none'; }
</script>

