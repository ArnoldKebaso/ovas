<?php
// admin/pets/manage.php - server-rendered CRUD (matches Services/Appts look)
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';

/** @var PDO $pdo */
$pdo = db();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Handle POST via admin/index.php pre-handler (no logic here)

// Load owners (users table)
$owners = $pdo->query("SELECT id, name, email FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Query pets using base schema from database/ovas_db.sql
$rows = $pdo->query(
  "SELECT p.id, p.user_id, u.name AS owner_name, u.email AS owner_email,
          p.name, p.species, p.breed, p.age, p.weight, p.notes,
          p.created_at, p.updated_at
   FROM pets p
   LEFT JOIN users u ON u.id = p.user_id
   ORDER BY p.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);
$total = count($rows);
?>
<style>
.pt-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
.pt-left{display:flex;gap:12px;align-items:center}
.badge{display:inline-block;padding:.25rem .5rem;border-radius:999px;font-size:.75rem;font-weight:600}
.badge--muted{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.badge--warn{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.table th{white-space:nowrap}
.table td,.table th{vertical-align:middle}
#petForm .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
@media (max-width: 900px){#petForm .grid{grid-template-columns:1fr}}
.input,textarea,select{width:100%}
.btn-icon{padding:6px 10px}
</style>

<div class="card shadow-sm">
  <div class="card-body pt-toolbar">
    <div class="pt-left">
      <h3 style="margin:0">Pets</h3>
      <span class="badge badge--muted" title="Total records"><?= (int)$total ?> total</span>
      <?php if(isset($_GET['created'])): ?><span class="badge" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0">Created</span><?php endif; ?>
      <?php if(isset($_GET['updated'])): ?><span class="badge badge--muted">Updated</span><?php endif; ?>
      <?php if(isset($_GET['deleted'])): ?><span class="badge badge--warn">Deleted</span><?php endif; ?>
    </div>
    <div>
      <button class="btn btn-primary" onclick="togglePetForm(true)">+ New Pet</button>
    </div>
  </div>
</div>

<!-- Create/Update form -->
<?php $editing = null; if(isset($_GET['edit'])){ $st=$pdo->prepare("SELECT * FROM pets WHERE id=?"); $st->execute([(int)$_GET['edit']]); $editing=$st->fetch(PDO::FETCH_ASSOC);} ?>
<div id="petForm" class="card shadow-sm" style="display:<?= $editing? 'block':'none' ?>">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <h4 style="margin:0"><?= $editing? 'Edit Pet' : 'New Pet' ?></h4>
      <button class="btn btn-outline btn-icon" type="button" onclick="togglePetForm(false)">Close</button>
    </div>

    <form method="post" action="/ovas/admin/index.php?page=pets">
      <input type="hidden" name="action" value="<?= $editing? 'update':'create' ?>">
      <?php if($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
      <div class="grid">
        <label>Owner
          <select name="user_id" class="input" required>
            <option value="">Select owner</option>
            <?php foreach($owners as $u): $sel = ($editing && (int)$editing['user_id']===(int)$u['id'])? 'selected':''; ?>
              <option value="<?= (int)$u['id'] ?>" <?= $sel ?>><?= h($u['name']) ?> (<?= h($u['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Pet Name
          <input type="text" class="input" name="name" value="<?= h($editing['name'] ?? '') ?>" required>
        </label>
        <label>Species
          <select name="species" class="input" required>
            <?php $sp = $editing['species'] ?? 'dog'; foreach(['dog','cat','rabbit','bird','other'] as $s): ?>
              <option <?= $sp===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Breed
          <input type="text" class="input" name="breed" value="<?= h($editing['breed'] ?? '') ?>">
        </label>
        <label>Age
          <input type="text" class="input" name="age" placeholder="e.g., 3y 2m" value="<?= h($editing['age'] ?? '') ?>">
        </label>
        <label>Weight
          <input type="text" class="input" name="weight" placeholder="e.g., 12.5 kg" value="<?= h($editing['weight'] ?? '') ?>">
        </label>
        <label style="grid-column:1/-1">Notes
          <textarea class="input" name="notes" rows="3" placeholder="Medical notes or remarks"><?= h($editing['notes'] ?? '') ?></textarea>
        </label>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn btn-outline" type="button" onclick="togglePetForm(false)">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Pets list -->
<div class="card shadow-sm">
  <div class="card-body">
    <?php if(!$rows): ?>
      <div class="alert alert-info mb-0">No pets found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle" id="petTable">
          <thead>
            <tr>
              <th>Pet</th>
              <th>Owner</th>
              <th>Species</th>
              <th>Breed</th>
              <th>Age</th>
              <th>Weight</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?= h($r['name']) ?></td>
                <td><?= h($r['owner_name']) ?></td>
                <td><?= h($r['species']) ?></td>
                <td><?= h($r['breed']) ?></td>
                <td><?= h($r['age'] ?? '') ?></td>
                <td><?= h($r['weight'] ?? '') ?></td>
                <td>
                  <a class="btn btn-outline btn-icon" href="/ovas/admin/index.php?page=pets&edit=<?= (int)$r['id'] ?>">Edit</a>
                  <form method="post" action="/ovas/admin/index.php?page=pets" style="display:inline" onsubmit="return confirm('Delete this pet?')">
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
function togglePetForm(open){ document.getElementById('petForm').style.display = open ? 'block' : 'none'; }
</script>

