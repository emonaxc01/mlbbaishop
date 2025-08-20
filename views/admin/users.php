<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Users</h1>
            <div class="space-x-2">
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin">Dashboard</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/orders">Orders</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/products">Products</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/settings">Settings</a>
            </div>
        </div>
        <div class="bg-white border rounded-lg p-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">ID</th>
                        <th>Email</th>
                        <th>Verified</th>
                        <th>Admin</th>
                        <th>Wallet</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersBody"></tbody>
            </table>
        </div>
    </div>
    <script>
    async function loadUsers(){
        const res = await fetch('/api/admin/users');
        const data = await res.json();
        const tbody = document.getElementById('usersBody');
        tbody.innerHTML='';
        (data.users||[]).forEach(u=>{
            const tr = document.createElement('tr');
            tr.className='border-b';
            tr.innerHTML = `<td class=\"py-2\">${u.id}</td><td>${u.email}</td><td><input type=\"checkbox\" data-id=\"${u.id}\" data-k=\"is_verified\" ${u.is_verified?'checked':''}></td><td><input type=\"checkbox\" data-id=\"${u.id}\" data-k=\"is_admin\" ${u.is_admin?'checked':''}></td><td><input type=\"number\" step=\"0.01\" class=\"border px-2 py-1 rounded w-24\" data-id=\"${u.id}\" data-k=\"wallet_balance\" value=\"${u.wallet_balance}\"></td><td>${u.created_at}</td><td><button class=\"px-3 py-1 bg-blue-600 text-white rounded\" data-id=\"${u.id}\" data-action=\"save\">Save</button></td>`;
            tbody.appendChild(tr);
        });
    }
    document.addEventListener('click', async (e)=>{
        if(e.target && e.target.dataset && e.target.dataset.action==='save'){
            const id = parseInt(e.target.dataset.id,10);
            const row = e.target.closest('tr');
            const is_verified = row.querySelector('input[data-k="is_verified"]').checked ? 1 : 0;
            const is_admin = row.querySelector('input[data-k="is_admin"]').checked ? 1 : 0;
            const wallet_balance = parseFloat(row.querySelector('input[data-k="wallet_balance"]').value||'0');
            const res = await fetch('/api/admin/users',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id,is_verified,is_admin,wallet_balance})});
            if(res.ok){ alert('Saved'); } else { alert('Save failed'); }
        }
    });
    loadUsers();
    </script>
</body>
</html>
