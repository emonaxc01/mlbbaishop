<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Catalog</h1>
            <div class="space-x-2">
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin">Dashboard</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/settings">Settings</a>
            </div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="grid grid-cols-2 gap-3 mb-3">
                <input id="pName" class="border px-3 py-2 rounded" placeholder="name">
                <input id="pSlug" class="border px-3 py-2 rounded" placeholder="slug">
                <input id="pSku" class="border px-3 py-2 rounded" placeholder="sku">
                <input id="pPrice" type="number" step="0.01" class="border px-3 py-2 rounded" placeholder="price">
                <input id="pStock" type="number" class="border px-3 py-2 rounded" placeholder="stock">
                <select id="pStatus" class="border px-3 py-2 rounded"><option value="1">Active</option><option value="0">Draft</option></select>
                <input id="pImage" class="border px-3 py-2 rounded col-span-2" placeholder="image url">
            </div>
            <div class="mb-3"><textarea id="pDesc" rows="3" class="border px-3 py-2 rounded w-full" placeholder="description"></textarea></div>
            <button id="saveProduct" class="px-4 py-2 bg-blue-600 text-white rounded">Add / Update</button>
        </div>
        <div class="bg-white border rounded-lg p-4 mt-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left border-b"><th class="py-2">ID</th><th>Name</th><th>Slug</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="body"></tbody>
            </table>
        </div>
    </div>
    <script>
    let editingId = 0;
    async function loadList(){
        const res = await fetch('/api/admin/catalog/products');
        const data = await res.json();
        const tbody = document.getElementById('body');
        tbody.innerHTML='';
        (data.items||[]).forEach(p=>{
            const tr = document.createElement('tr'); tr.className='border-b';
            tr.innerHTML = `<td class=\"py-2\">${p.id}</td><td>${p.name}</td><td>${p.slug}</td><td>${p.sku||''}</td><td>${p.price}</td><td>${p.stock}</td><td>${p.status?'Active':'Draft'}</td><td><a class=\"px-2 py-1 text-blue-600\" href=\"/admin/catalog/product?id=${p.id}\">Edit</a> <a class=\"px-2 py-1 text-red-600\" data-del=\"${p.id}\" href=\"#\">Delete</a></td>`;
            tbody.appendChild(tr);
        });
    }
    document.getElementById('saveProduct').addEventListener('click', async ()=>{
        const payload = { id: editingId, name: pName.value.trim(), slug: pSlug.value.trim(), sku: pSku.value.trim(), price: parseFloat(pPrice.value||'0'), stock: parseInt(pStock.value||'0',10), status: parseInt(pStatus.value,10), image_url: pImage.value.trim(), description: pDesc.value };
        const res = await fetch('/api/admin/catalog/product',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        if(res.ok){ editingId = 0; ['pName','pSlug','pSku','pPrice','pStock','pImage','pDesc'].forEach(id=>document.getElementById(id).value=''); pStatus.value='1'; loadList(); } else { alert('Save failed'); }
    });
    document.addEventListener('click', async (e)=>{
        if(e.target && e.target.dataset && e.target.dataset.del){
            const id = e.target.dataset.del;
            if(confirm('Delete product '+id+'?')){
                const res = await fetch('/api/admin/catalog/product/delete?id='+id);
                if(res.ok) loadList(); else alert('Delete failed');
            }
        }
    });
    loadList();
    </script>
</body>
</html>
