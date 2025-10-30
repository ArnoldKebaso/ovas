<?php
// admin/payments/manage.php - server-rendered CRUD to match payments table in SQL
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';

/** @var PDO $pdo */
$pdo = db();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Load appointments for selector
$appts = $pdo->query("SELECT a.id,a.code,a.schedule_date, u.name AS user_name, s.name AS service_name
                      FROM appointments a
                      LEFT JOIN users u ON u.id=a.user_id
                      LEFT JOIN services s ON s.id=a.service_id
                      ORDER BY a.created_at DESC")
               ->fetchAll(PDO::FETCH_ASSOC);

// List payments
$rows = $pdo->query("SELECT p.id,p.appointment_id,p.provider,p.amount,p.currency,p.reference,p.status,p.created_at,p.updated_at,
                            a.code AS appt_code, u.name AS user_name
                     FROM payments p
                     LEFT JOIN appointments a ON a.id=p.appointment_id
                     LEFT JOIN users u ON u.id=a.user_id
                     ORDER BY p.created_at DESC")
               ->fetchAll(PDO::FETCH_ASSOC);
$total = count($rows);
?>
<style>
.pay-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
.pay-left{display:flex;gap:12px;align-items:center}
.badge{display:inline-block;padding:.25rem .5rem;border-radius:999px;font-size:.75rem;font-weight:600}
.badge--ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
.badge--muted{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.badge--warn{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.table th{white-space:nowrap}
.table td,.table th{vertical-align:middle}
#payForm .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
@media (max-width: 900px){#payForm .grid{grid-template-columns:1fr}}
input.input, select.input, textarea.input{width:100%;border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;font:inherit}
.btn{display:inline-flex;align-items:center;gap:8px;border:1px solid transparent;border-radius:999px;padding:10px 16px;font-weight:700;cursor:pointer}
.btn-primary{background:#1e5eff;color:#fff}
.btn-outline{background:#fff;border-color:#e2e8f0;color:#0f172a}
.btn-icon{padding:6px 10px}
</style>

<div class="card shadow-sm">
  <div class="card-body pay-toolbar">
    <div class="pay-left">
      <h3 style="margin:0">Payments</h3>
      <span class="badge badge--muted" title="Total records"><?= (int)$total ?> total</span>
      <?php if(isset($_GET['created'])): ?><span class="badge badge--ok">Created</span><?php endif; ?>
      <?php if(isset($_GET['updated'])): ?><span class="badge badge--muted">Updated</span><?php endif; ?>
      <?php if(isset($_GET['deleted'])): ?><span class="badge badge--warn">Deleted</span><?php endif; ?>
    </div>
    <div>
      <button class="btn btn-primary" onclick="togglePayForm(true)">+ New Payment</button>
    </div>
  </div>
</div>

<?php $editing=null; if(isset($_GET['edit'])){ $st=$pdo->prepare("SELECT * FROM payments WHERE id=?"); $st->execute([(int)$_GET['edit']]); $editing=$st->fetch(PDO::FETCH_ASSOC);} ?>
<div id="payForm" class="card shadow-sm" style="display:<?= $editing? 'block':'none' ?>">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <h4 style="margin:0"><?= $editing? 'Edit Payment' : 'New Payment' ?></h4>
      <button class="btn btn-outline btn-icon" type="button" onclick="togglePayForm(false)">Close</button>
    </div>

    <form id="payForm" method="post" action="/ovas/admin/index.php?page=payments">
      <input type="hidden" name="action" value="<?= $editing? 'update':'create' ?>">
      <?php if($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
      <div class="grid">
        <label>Appointment
          <select name="appointment_id" class="input" required>
            <option value="">Select appointment</option>
            <?php foreach($appts as $a): $sel = ($editing && (int)$editing['appointment_id']===(int)$a['id'])? 'selected':''; ?>
              <option value="<?= (int)$a['id'] ?>" <?= $sel ?>>#<?= h($a['code']) ?> - <?= h($a['user_name']) ?> (<?= h($a['schedule_date']) ?>, <?= h($a['service_name']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Amount (KES)
          <input type="number" step="0.01" class="input" name="amount" value="<?= h($editing['amount'] ?? '0.00') ?>" required>
        </label>
        <label>Currency
          <input type="text" class="input" name="currency" value="<?= h($editing['currency'] ?? 'KES') ?>" maxlength="3">
        </label>
        <label>Provider
          <select name="provider" class="input">
            <?php $prov = $editing['provider'] ?? 'mpesa'; foreach(['mpesa'] as $p): ?>
              <option <?= $prov===$p? 'selected':'' ?>><?= $p ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Reference
          <input type="text" class="input" name="reference" value="<?= h($editing['reference'] ?? '') ?>" placeholder="receipt / txn ref">
        </label>
        <label>Status
          <select name="status" class="input" required>
            <?php $st = $editing['status'] ?? 'initiated'; foreach(['initiated','success','failed'] as $s): ?>
              <option <?= $st===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn btn-outline" type="button" onclick="togglePayForm(false)">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if(!$rows): ?>
      <div class="alert alert-info mb-0">No payments found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle" id="payTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Appointment</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Reference</th>
              <th>Created</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= h($r['appt_code'] ?? '') ?> / <?= h($r['user_name'] ?? '') ?></td>
                <td>KSh <?= number_format((float)$r['amount'], 2) ?></td>
                <td><?= h($r['status']) ?></td>
                <td><?= h($r['reference'] ?? '') ?></td>
                <td><?= h($r['created_at']) ?></td>
                <td>
                  <a class="btn btn-outline btn-icon" href="/ovas/admin/index.php?page=payments&edit=<?= (int)$r['id'] ?>">Edit</a>
                  <form method="post" action="/ovas/admin/index.php?page=payments" style="display:inline" onsubmit="return confirm('Delete this payment?')">
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
function togglePayForm(open){ document.getElementById('payForm').style.display = open ? 'block' : 'none'; }
</script>
