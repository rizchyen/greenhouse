<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Greenhouse Monitor</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; background: #f0f4f0; padding: 24px; }
    h1   { color: #1a5c1a; margin-bottom: 4px; }
    .subtitle { color: #888; font-size: 13px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center; }
    .pulse { width:10px; height:10px; border-radius:50%; background:#2c7a2c;
             display:inline-block; margin-right:6px;
             animation: pulse 1.5s ease-in-out infinite; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.8)} }
    .cards { display:flex; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
    .card  { background:white; border-radius:10px; padding:16px 24px;
             border-left:5px solid #2c7a2c; min-width:140px; transition: all .3s; }
    .card .label { font-size:12px; color:#888; margin-bottom:4px; }
    .card .value { font-size:26px; font-weight:bold; color:#1a5c1a; }
    .card.fan-on  { border-left-color:#e65c00; }
    .card.fan-on  .value { color:#e65c00; }
    .card.fan-off .value { color:#888; }
    table { width:100%; border-collapse:collapse; background:white;
            border-radius:10px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.07); }
    thead tr { background:#2c7a2c; color:white; }
    th, td { padding:11px 18px; text-align:left; font-size:14px; }
    tbody tr { transition: background .2s; }
    tbody tr:nth-child(even) { background:#f9fdf9; }
    tbody tr:hover { background:#edf7ed; }
    tbody tr.new-row { background:#fffbe6; }
    .badge { display:inline-block; padding:2px 10px; border-radius:20px;
             font-size:12px; font-weight:bold; }
    .badge.on  { background:#fff0e6; color:#d45000; }
    .badge.off { background:#f2f2f2; color:#888; }
    #status { font-size:12px; color:#2c7a2c; }
    #error  { font-size:12px; color:#c00; }
  </style>
</head>
<body>

<h1>Greenhouse Live Monitor</h1>
<div class="subtitle">
  <span><span class="pulse"></span> Live — updates every 5 seconds</span>
  <span id="status">Connecting...</span>
</div>

<div class="cards">
  <div class="card" id="card-temp">
    <div class="label">Temperature</div>
    <div class="value" id="val-temp">—</div>
  </div>
  <div class="card" id="card-hum">
    <div class="label">Humidity</div>
    <div class="value" id="val-hum">—</div>
  </div>
  <div class="card" id="card-fan">
    <div class="label">Fan status</div>
    <div class="value" id="val-fan">—</div>
  </div>
  <div class="card" id="card-count">
    <div class="label">Total records</div>
    <div class="value" id="val-count">—</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Temperature (°C)</th>
      <th>Humidity (%)</th>
      <th>Fan status</th>
      <th>Time</th>
    </tr>
  </thead>
  <tbody id="table-body">
    <tr><td colspan="5" style="text-align:center;color:#aaa;padding:24px;">Loading data...</td></tr>
  </tbody>
</table>

<script>
let lastTopId = 0;   // track the most recent ID we've seen

function fetchData() {
  fetch('get_data.php')
    .then(r => r.json())
    .then(data => {

      // Update status line
      document.getElementById('status').textContent =
        'Last updated: ' + new Date().toLocaleTimeString();

      // Update summary cards
      if (data.latest) {
        const l = data.latest;
        document.getElementById('val-temp').textContent  = l.temperature + ' °C';
        document.getElementById('val-hum').textContent   = l.humidity    + ' %';
        document.getElementById('val-fan').textContent   = l.fan_status;
        document.getElementById('val-count').textContent = data.count;

        // Change fan card colour live
        const fanCard = document.getElementById('card-fan');
        fanCard.className = 'card ' + (l.fan_status === 'ON' ? 'fan-on' : 'fan-off');
      }

      // Rebuild table rows
      const tbody = document.getElementById('table-body');
      tbody.innerHTML = '';

      const newTopId = data.rows.length > 0 ? parseInt(data.rows[0].id) : 0;
      const hasNewRows = newTopId > lastTopId;

      data.rows.forEach((row, index) => {
        const tr = document.createElement('tr');
        // Highlight new rows with a yellow flash
        if (hasNewRows && index === 0 && parseInt(row.id) > lastTopId) {
          tr.className = 'new-row';
          setTimeout(() => tr.classList.remove('new-row'), 2000);
        }
        const badgeClass = row.fan_status === 'ON' ? 'on' : 'off';
        tr.innerHTML = `
          <td>${row.id}</td>
          <td>${row.temperature}</td>
          <td>${row.humidity}</td>
          <td><span class="badge ${badgeClass}">${row.fan_status}</span></td>
          <td>${row.time_stamp}</td>`;
        tbody.appendChild(tr);
      });

      lastTopId = newTopId;
    })
    .catch(() => {
      document.getElementById('status').innerHTML =
        '<span id="error">Connection error — retrying...</span>';
    });
}

// Fetch immediately, then every 5 seconds
fetchData();
setInterval(fetchData, 5000);
</script>
</body>
</html>