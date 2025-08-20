<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Web Installer</h1>
        <div id="checks" class="bg-white border rounded-lg p-4 mb-6"></div>

        <div class="bg-white border rounded-lg p-4 mb-6">
            <h2 class="font-semibold mb-3">Environment</h2>
            <div class="grid md:grid-cols-2 gap-3">
                <input id="app_url" class="border px-3 py-2 rounded" placeholder="https://yourdomain.com">
                <input id="db_host" class="border px-3 py-2 rounded" placeholder="127.0.0.1" value="127.0.0.1">
                <input id="db_port" class="border px-3 py-2 rounded" placeholder="3306" value="3306">
                <input id="db_name" class="border px-3 py-2 rounded" placeholder="gametopup">
                <input id="db_user" class="border px-3 py-2 rounded" placeholder="root">
                <input id="db_pass" class="border px-3 py-2 rounded" placeholder="password">
                <input id="mail_host" class="border px-3 py-2 rounded" placeholder="smtp.example.com">
                <input id="mail_port" class="border px-3 py-2 rounded" placeholder="587" value="587">
                <input id="mail_user" class="border px-3 py-2 rounded" placeholder="smtp user">
                <input id="mail_pass" class="border px-3 py-2 rounded" placeholder="smtp pass">
                <input id="mail_from" class="border px-3 py-2 rounded" placeholder="no-reply@yourdomain.com">
                <input id="mail_name" class="border px-3 py-2 rounded" placeholder="GameTopUp Premium">
            </div>
            <button id="saveEnv" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded">Save .env</button>
        </div>

        <div class="bg-white border rounded-lg p-4 mb-6">
            <h2 class="font-semibold mb-3">Migrations</h2>
            <button id="runMigrations" class="px-4 py-2 bg-green-600 text-white rounded">Run Migrations</button>
        </div>

        <div class="bg-white border rounded-lg p-4">
            <h2 class="font-semibold mb-3">Create Admin</h2>
            <div class="grid md:grid-cols-2 gap-3">
                <input id="admin_email" class="border px-3 py-2 rounded" placeholder="you@example.com">
                <input id="admin_password" class="border px-3 py-2 rounded" placeholder="password (8+)">
            </div>
            <button id="createAdmin" class="mt-3 px-4 py-2 bg-purple-600 text-white rounded">Create Admin</button>
        </div>

        <div class="mt-6 text-sm text-gray-600">Also available at /insall (alias).</div>
    </div>
    <script>
    async function check(){
        const res = await fetch('/install/check');
        const data = await res.json();
        const div = document.getElementById('checks');
        const ok = Object.values(data.extensions).every(Boolean) && data.writable.base;
        div.innerHTML = `<div class="${ok?'text-green-700':'text-red-700'}">PHP ${data.php_version}</div>`+
            `<ul class="list-disc pl-6">`+
            Object.entries(data.extensions).map(([k,v])=>`<li>${k}: ${v?'OK':'Missing'}</li>`).join('')+
            `</ul>`+
            `<div>.env writable: ${data.writable['.env']?'Yes':'No'}</div>`;
    }
    document.getElementById('saveEnv').addEventListener('click', async ()=>{
        const payload = {
            app_url: app_url.value.trim(), db_host: db_host.value.trim(), db_port: db_port.value.trim(), db_name: db_name.value.trim(), db_user: db_user.value.trim(), db_pass: db_pass.value,
            mail_host: mail_host.value.trim(), mail_port: mail_port.value.trim(), mail_user: mail_user.value.trim(), mail_pass: mail_pass.value, mail_from: mail_from.value.trim(), mail_name: mail_name.value.trim()
        };
        const res = await fetch('/install/save-env',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        alert(res.ok?'Saved .env':'Failed');
    });
    document.getElementById('runMigrations').addEventListener('click', async ()=>{
        const res = await fetch('/install/migrate');
        alert(res.ok?'Migrations ran':'Failed');
    });
    document.getElementById('createAdmin').addEventListener('click', async ()=>{
        const payload = { email: admin_email.value.trim(), password: admin_password.value };
        const res = await fetch('/install/create-admin',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        alert(res.ok?'Admin created':'Failed');
    });
    check();
    </script>
</body>
</html>
