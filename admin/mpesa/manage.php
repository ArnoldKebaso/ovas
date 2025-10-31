<?php
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';
?>
<style>
.cardx{padding:1rem;border:1px solid #e2e8f0;border-radius:14px;background:#fff;margin-bottom:1rem;box-shadow:0 1px 2px rgba(15,23,42,.06)}
.row{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
.row > label{display:flex;flex-direction:column;gap:6px;min-width:220px;flex:1}
input.input{border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;font:inherit}
.muted{color:#64748b}
.btn{display:inline-flex;align-items:center;gap:8px;border:1px solid transparent;border-radius:999px;padding:10px 16px;font-weight:700;cursor:pointer}
.btn-primary{background:#1e5eff;color:#fff}
.btn-outline{background:#fff;border-color:#e2e8f0;color:#0f172a}
</style>

<div class="cardx">
  <h4 style="margin:.25rem 0 .75rem">M-Pesa STK Push (Test)</h4>
  <form id="stkForm" class="row" onsubmit="return false">
    <label>Phone<input type="text" id="m_phone" placeholder="2547..." class="input" required></label>
    <label>Amount<input type="number" id="m_amount" value="500" class="input" required></label>
    <label>Account Ref<input type="text" id="m_ref" value="OVAS" class="input"></label>
    <label>Description<input type="text" id="m_desc" value="Booking Fee" class="input"></label>
    <div>
      <button class="btn btn-primary" id="btnStk">Send STK Push</button>
    </div>
    <div id="stkMsg" class="muted"></div>
  </form>
</div>

<script>
async function post(url, data){ const fd=new FormData(); for(const k in data) fd.append(k,data[k]); const r=await fetch(url,{method:'POST',body:fd}); return r.json(); }
document.getElementById('btnStk').addEventListener('click', async ()=>{
  const res = await post('/ovas/api/mpesa.php?action=stk_push', {
    phone: document.getElementById('m_phone').value,
    amount: document.getElementById('m_amount').value,
    account_ref: document.getElementById('m_ref').value,
    desc: document.getElementById('m_desc').value,
  });
  document.getElementById('stkMsg').textContent = res.success? (res.data?.customer_message || 'Prompt sent') : (res.message || 'Failed');
});
</script>
