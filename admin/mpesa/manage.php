<?php
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';
?>
<style>
.cardx{padding:1rem;border:1px solid #e2e8f0;border-radius:12px;background:#fff;margin-bottom:1rem}
.row{display:flex;gap:.75rem;flex-wrap:wrap}
.row > *{flex:1 1 240px}
.muted{color:#64748b}
</style>

<div class="cardx">
  <h4 style="margin:.25rem 0 .75rem">M-Pesa STK Push (Test)</h4>
  <form id="stkForm" class="row" onsubmit="return false">
    <label>Phone<input type="text" id="m_phone" placeholder="2547..." class="input" required></label>
    <label>Amount<input type="number" id="m_amount" value="500" class="input" required></label>
    <label>Account Ref<input type="text" id="m_ref" value="OVAS" class="input"></label>
    <label>Description<input type="text" id="m_desc" value="Booking Fee" class="input"></label>
    <button class="btn btn-primary" id="btnStk">Send STK Push</button>
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

