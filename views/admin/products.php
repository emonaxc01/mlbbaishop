<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Products</h1>
            <div class="space-x-2">
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin">Dashboard</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/orders">Orders</a>
            </div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="flex gap-2 mb-4">
                <input id="pCode" class="border px-3 py-2 rounded w-40" placeholder="code">
                <input id="pName" class="border px-3 py-2 rounded flex-1" placeholder="name">
                <select id="pStatus" class="border px-3 py-2 rounded w-32">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <button id="saveProduct" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody id="productsBody"></tbody>
            </table>
        </div>
    </div>
    <script>
    async function loadProducts(){
        const res = await fetch('/api/admin/products');
        const data = await res.json();
        const tbody = document.getElementById('productsBody');
        tbody.innerHTML='';
        (data.products||[]).forEach(p => {
            const tr = document.createElement('tr');
            tr.className='border-b';
            tr.innerHTML = `<td class="py-2">${p.id}</td><td>${p.code}</td><td>${p.name}</td><td>${p.status?'Active':'Inactive'}</td><td>${p.created_at}</td>`;
            tbody.appendChild(tr);
        });
    }
    document.getElementById('saveProduct').addEventListener('click', async () =>{
        const code = document.getElementById('pCode').value.trim();
        const name = document.getElementById('pName').value.trim();
        const status = parseInt(document.getElementById('pStatus').value,10);
        if(!code||!name){alert('Code and name required');return;}
        const res = await fetch('/api/admin/products',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({code,name,status})});
        if(res.ok){
            document.getElementById('pCode').value='';
            document.getElementById('pName').value='';
            await loadProducts();
        } else {
            alert('Save failed');
        }
    });
    loadProducts();
    </script>
</body>
</html>
