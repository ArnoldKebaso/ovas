<?php
$ROOT = realpath(__DIR__ . '/../../');
require_once $ROOT . '/initialize.php';
?>
<style>
.cal-card{padding:1rem;border:1px solid #e2e8f0;border-radius:12px;background:#fff}
.row{display:flex;gap:.75rem;flex-wrap:wrap}
.row > *{flex:1 1 200px}
table{width:100%;border-collapse:collapse}
th,td{padding:.5rem;border-bottom:1px solid #e2e8f0;text-align:left}
.muted{color:#64748b}
.badge{display:inline-block;padding:.25rem .5rem;border-radius:999px;background:#eef2ff;color:#1d4ed8;font-weight:600}
</style>

<div class="cal-card">
  <div class="row">
    <div>
      <div class="muted">Google Calendar</div>
      <div id="calStatus" class="badge">Checking…</div>
    </div>
    <div style="flex:2">
      <form id="rangeForm" class="row" onsubmit="return false">
        <label>From<input type="date" id="from" value="<?= date('Y-m-d') ?>" class="input"></label>
        <label>To<input type="date" id="to" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="input"></label>
        <button class="btn btn-primary" id="btnList">List Events</button>
      </form>
    </div>
  </div>
  <div class="row" style="margin-top:.75rem">
    <form id="manualForm" class="row" onsubmit="return false">
      <label>Summary<input type="text" id="m_summary" placeholder="Title" required></label>
      <label>Start<input type="datetime-local" id="m_start" required></label>
      <label>End<input type="datetime-local" id="m_end" required></label>
      <button class="btn btn-outline" id="btnCreate">Create Manual Event</button>
    </form>
  </div>
</div>

<div class="cal-card" style="margin-top:1rem">
  <h4 style="margin:.25rem 0 .75rem">Events</h4>
  <div class="table-responsive">
    <table id="calTable">
      <thead><tr><th>Summary</th><th>Start</th><th>End</th><th>Link</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
  <div id="calMsg" class="muted"></div>
</div>

<script>
async function api(action, params={}, method='GET'){
  const url = new URL('/ovas/api/calendar.php', location.origin);
  url.searchParams.set('action', action);
  const opts = { method };
  if(method !== 'GET'){
    const fd = new FormData();
    for(const k in params) fd.append(k, params[k]);
    opts.body = fd;
  } else {
    Object.entries(params).forEach(([k,v])=> url.searchParams.set(k,v));
  }
  const r = await fetch(url, opts); return r.json();
}

async function checkStatus(){
  const res = await api('status');
  document.getElementById('calStatus').textContent = res.configured? 'Connected' : 'Not Configured';
}

async function listEvents(){
  const from = document.getElementById('from').value + 'T00:00:00+03:00';
  const to   = document.getElementById('to').value + 'T23:59:59+03:00';
  const res = await api('list', {from,to});
  const tb = document.querySelector('#calTable tbody');
  tb.innerHTML = (res.events||[]).map(e=>`<tr>
    <td>${e.summary||''}</td>
    <td>${e.start||''}</td>
    <td>${e.end||''}</td>
    <td>${e.htmlLink? `<a target="_blank" href="${e.htmlLink}">Open</a>`:''}</td>
  </tr>`).join('');
  document.getElementById('calMsg').textContent = res.events?.length? '' : 'No events in range';
}

async function createManual(){
  const s = document.getElementById('m_summary').value.trim();
  const st = document.getElementById('m_start').value;
  const en = document.getElementById('m_end').value;
  if(!s||!st||!en) return;
  const toIso = v=> new Date(v).toISOString();
  const res = await api('create_manual',{summary:s, description:'', start:toIso(st), end:toIso(en)}, 'POST');
  alert(res.success? 'Event created' : 'Failed');
  if(res.success) listEvents();
}

document.getElementById('btnList').addEventListener('click', listEvents);
document.getElementById('btnCreate').addEventListener('click', createManual);
checkStatus();
</script>

