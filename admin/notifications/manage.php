<?php
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';
?>
<style>
.cardx{padding:1rem;border:1px solid #e2e8f0;border-radius:14px;background:#fff;margin-bottom:1rem;box-shadow:0 1px 2px rgba(15,23,42,.06)}
.row{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
.row > label{display:flex;flex-direction:column;gap:6px;min-width:240px;flex:1}
input.input, textarea.input{border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;font:inherit}
textarea.input{min-height:96px}
.muted{color:#64748b}
.btn{display:inline-flex;align-items:center;gap:8px;border:1px solid transparent;border-radius:999px;padding:10px 16px;font-weight:700;cursor:pointer}
.btn-primary{background:#1e5eff;color:#fff}
.btn-outline{background:#fff;border-color:#e2e8f0;color:#0f172a}
</style>

<div class="cardx">
  <h4 style="margin:.25rem 0 .75rem">Send Test Email</h4>
  <form id="emailForm" class="row" onsubmit="return false">
    <label>Email<input type="email" id="e_to" required class="input" placeholder="clinic@example.com"></label>
    <label>Name<input type="text" id="e_name" class="input" placeholder="Recipient"></label>
    <label>Subject<input type="text" id="e_sub" value="Test from OVAS" class="input"></label>
    <label style="flex:1 1 100%">HTML<textarea id="e_html" rows="4" class="input"><p>Hello from OVAS</p></textarea></label>
    <div>
      <button class="btn btn-primary" id="btnEmail">Send Email</button>
    </div>
    <div id="emailMsg" class="muted"></div>
  </form>
</div>

<div class="cardx">
  <h4 style="margin:.25rem 0 .75rem">Send Test SMS</h4>
  <form id="smsForm" class="row" onsubmit="return false">
    <label>Phone<input type="text" id="s_phone" placeholder="2547..." class="input"></label>
    <label style="flex:1 1 100%">Message<textarea id="s_msg" rows="3" class="input">Test SMS from OVAS</textarea></label>
    <div>
      <button class="btn btn-outline" id="btnSMS">Send SMS</button>
    </div>
    <div id="smsMsg" class="muted"></div>
  </form>
</div>

<script>
async function post(url, data){ const fd=new FormData(); for(const k in data) fd.append(k,data[k]); const r=await fetch(url,{method:'POST',body:fd}); return r.json(); }
document.getElementById('btnEmail').addEventListener('click', async ()=>{
  const res = await post('/ovas/api/notifications.php?action=send_email', {
    to: document.getElementById('e_to').value,
    name: document.getElementById('e_name').value,
    subject: document.getElementById('e_sub').value,
    html: document.getElementById('e_html').value,
  });
  document.getElementById('emailMsg').textContent = res.success? 'Sent' : 'Failed';
});
document.getElementById('btnSMS').addEventListener('click', async ()=>{
  const res = await post('/ovas/api/notifications.php?action=send_sms', {
    phone: document.getElementById('s_phone').value,
    message: document.getElementById('s_msg').value,
  });
  document.getElementById('smsMsg').textContent = res.success? 'Queued' : 'Failed';
});
</script>
