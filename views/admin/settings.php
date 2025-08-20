<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Settings</h1>
            <div class="space-x-2">
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin">Dashboard</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/users">Users</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/products">Products</a>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white border rounded-lg p-4">
                <h2 class="font-semibold mb-3">General</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Site Title</label>
                        <input id="site_title" class="border px-3 py-2 rounded w-full" placeholder="GameTopUp Premium">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Maintenance Mode</label>
                        <select id="maintenance_mode" class="border px-3 py-2 rounded w-full">
                            <option value="off">Off</option>
                            <option value="on">On</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Logo</label>
                        <input id="logoFile" type="file" class="border px-3 py-2 rounded w-full">
                        <button id="uploadLogo" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
                        <div class="mt-2"><img id="logoPreview" class="h-10" src="" alt="Logo"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4">
                <h2 class="font-semibold mb-3">Mail</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-sm mb-1">Host</label><input id="mail_host" class="border px-3 py-2 rounded w-full"></div>
                    <div><label class="block text-sm mb-1">Port</label><input id="mail_port" class="border px-3 py-2 rounded w-full"></div>
                    <div><label class="block text-sm mb-1">Username</label><input id="mail_username" class="border px-3 py-2 rounded w-full"></div>
                    <div><label class="block text-sm mb-1">Password</label><input id="mail_password" type="password" class="border px-3 py-2 rounded w-full"></div>
                    <div><label class="block text-sm mb-1">From Address</label><input id="mail_from_address" class="border px-3 py-2 rounded w-full"></div>
                    <div><label class="block text-sm mb-1">From Name</label><input id="mail_from_name" class="border px-3 py-2 rounded w-full"></div>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-4 md:col-span-2">
                <h2 class="font-semibold mb-3">Currencies</h2>
                <div class="flex gap-2 mb-3">
                    <input id="curCode" class="border px-3 py-2 rounded w-24" placeholder="USD" maxlength="3">
                    <input id="curSymbol" class="border px-3 py-2 rounded w-24" placeholder="$">
                    <input id="curRate" type="number" step="0.00000001" class="border px-3 py-2 rounded w-40" placeholder="1.0">
                    <label class="inline-flex items-center gap-2 px-2 border rounded"><input id="curDefault" type="checkbox"> Default</label>
                    <label class="inline-flex items-center gap-2 px-2 border rounded"><input id="curEnabled" type="checkbox" checked> Enabled</label>
                    <button id="saveCurrency" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b"><th class="py-2">Code</th><th>Symbol</th><th>Rate</th><th>Default</th><th>Enabled</th></tr>
                    </thead>
                    <tbody id="currenciesBody"></tbody>
                </table>
            </div>

            <div class="bg-white border rounded-lg p-4 md:col-span-2">
                <h2 class="font-semibold mb-3">Payment Methods</h2>
                <div class="flex gap-2 mb-3">
                    <input id="payCode" class="border px-3 py-2 rounded w-40" placeholder="code">
                    <input id="payName" class="border px-3 py-2 rounded w-80" placeholder="name">
                    <label class="inline-flex items-center gap-2 px-2 border rounded"><input id="payEnabled" type="checkbox" checked> Enabled</label>
                    <button id="savePayment" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b"><th class="py-2">Code</th><th>Name</th><th>Enabled</th></tr>
                    </thead>
                    <tbody id="paymentsBody"></tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <button id="saveAll" class="px-5 py-2 bg-green-600 text-white rounded">Save All Settings</button>
        </div>
    </div>
    <script>
    async function loadSettings(){
        const s = await (await fetch('/api/admin/settings')).json();
        const ids = ['site_title','maintenance_mode','mail_host','mail_port','mail_username','mail_password','mail_from_address','mail_from_name'];
        ids.forEach(id=>{ const el = document.getElementById(id); if(el) el.value = s[id]||''; });
        if (s.site_logo_url) document.getElementById('logoPreview').src = s.site_logo_url;

        const cur = await (await fetch('/api/admin/currencies')).json();
        const tbody = document.getElementById('currenciesBody');
        tbody.innerHTML='';
        (cur.currencies||[]).forEach(c=>{
            const tr = document.createElement('tr'); tr.className='border-b';
            tr.innerHTML = `<td class=\"py-2\">${c.code}</td><td>${c.symbol}</td><td>${c.rate}</td><td>${c.is_default?'Yes':'No'}</td><td>${c.enabled?'Yes':'No'}</td>`;
            tbody.appendChild(tr);
        });

        const pay = await (await fetch('/api/admin/payments')).json();
        const pbody = document.getElementById('paymentsBody');
        pbody.innerHTML='';
        (pay.methods||[]).forEach(m=>{
            const tr = document.createElement('tr'); tr.className='border-b';
            tr.innerHTML = `<td class=\"py-2\">${m.code}</td><td>${m.name}</td><td>${m.enabled?'Yes':'No'}</td>`;
            pbody.appendChild(tr);
        });
    }
    document.getElementById('saveAll').addEventListener('click', async ()=>{
        const payload = {
            site_title: document.getElementById('site_title').value.trim(),
            maintenance_mode: document.getElementById('maintenance_mode').value,
            mail_host: document.getElementById('mail_host').value.trim(),
            mail_port: document.getElementById('mail_port').value.trim(),
            mail_username: document.getElementById('mail_username').value.trim(),
            mail_password: document.getElementById('mail_password').value.trim(),
            mail_from_address: document.getElementById('mail_from_address').value.trim(),
            mail_from_name: document.getElementById('mail_from_name').value.trim()
        };
        const res = await fetch('/api/admin/settings',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        alert(res.ok ? 'Saved' : 'Save failed');
        if (res.ok) loadSettings();
    });
    document.getElementById('uploadLogo').addEventListener('click', async ()=>{
        const file = document.getElementById('logoFile').files[0];
        if(!file){ alert('Choose file'); return; }
        const fd = new FormData(); fd.append('file', file);
        const res = await fetch('/api/admin/upload-logo',{method:'POST', body: fd});
        const data = await res.json();
        if(res.ok){ document.getElementById('logoPreview').src = data.url; } else { alert('Upload failed'); }
    });
    document.getElementById('saveCurrency').addEventListener('click', async ()=>{
        const payload = {
            code: document.getElementById('curCode').value.trim().toUpperCase(),
            symbol: document.getElementById('curSymbol').value.trim(),
            rate: parseFloat(document.getElementById('curRate').value||'1'),
            is_default: document.getElementById('curDefault').checked ? 1 : 0,
            enabled: document.getElementById('curEnabled').checked ? 1 : 0
        };
        const res = await fetch('/api/admin/currencies',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        alert(res.ok ? 'Saved' : 'Save failed');
        if (res.ok) loadSettings();
    });
    document.getElementById('savePayment').addEventListener('click', async ()=>{
        const payload = {
            code: document.getElementById('payCode').value.trim(),
            name: document.getElementById('payName').value.trim(),
            enabled: document.getElementById('payEnabled').checked ? 1 : 0,
            config: {}
        };
        const res = await fetch('/api/admin/payments',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        alert(res.ok ? 'Saved' : 'Save failed');
        if (res.ok) loadSettings();
    });
    loadSettings();
    </script>
</body>
</html>
