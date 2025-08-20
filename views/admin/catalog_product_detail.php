<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Product Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <a href="/admin/catalog" class="text-blue-600">← Back to Catalog</a>
        <h1 class="text-2xl font-bold mt-3">Manage Variations & Meta</h1>
        <div class="grid md:grid-cols-2 gap-6 mt-4">
            <div class="bg-white border rounded-lg p-4">
                <h2 class="font-semibold mb-3">Variations</h2>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <input id="vName" class="border px-3 py-2 rounded" placeholder="name">
                    <input id="vSku" class="border px-3 py-2 rounded" placeholder="sku">
                    <input id="vPrice" type="number" step="0.01" class="border px-3 py-2 rounded" placeholder="price">
                    <input id="vStock" type="number" class="border px-3 py-2 rounded" placeholder="stock">
                    <select id="vStatus" class="border px-3 py-2 rounded"><option value="1">Active</option><option value="0">Draft</option></select>
                </div>
                <button id="saveVariation" class="px-4 py-2 bg-blue-600 text-white rounded">Add / Update</button>
                <table class="w-full text-sm mt-4"><thead><tr class="text-left border-b"><th class="py-2">ID</th><th>Name</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead><tbody id="varsBody"></tbody></table>
            </div>
            <div class="bg-white border rounded-lg p-4">
                <h2 class="font-semibold mb-3">Product Meta</h2>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <input id="mKey" class="border px-3 py-2 rounded" placeholder="key">
                    <input id="mValue" class="border px-3 py-2 rounded" placeholder="value">
                </div>
                <button id="saveMeta" class="px-4 py-2 bg-blue-600 text-white rounded">Add / Update</button>
                <table class="w-full text-sm mt-4"><thead><tr class="text-left border-b"><th class="py-2">ID</th><th>Key</th><th>Value</th><th>Actions</th></tr></thead><tbody id="metaBody"></tbody></table>
            </div>
        </div>
    </div>
    <script>
    const url = new URL(window.location.href);
    const productId = parseInt(url.searchParams.get('id')||'0',10);
    let editingVarId = 0; let editingMetaId = 0;
    async function loadVariations(){
        const res = await fetch('/api/admin/catalog/variations?productId='+productId);
        const data = await res.json();
        const tbody = document.getElementById('varsBody'); tbody.innerHTML='';
        (data.items||[]).forEach(v=>{
            const tr = document.createElement('tr'); tr.className='border-b';
            tr.innerHTML = `<td class=\"py-2\">${v.id}</td><td>${v.name}</td><td>${v.sku||''}</td><td>${v.price}</td><td>${v.stock}</td><td>${v.status?'Active':'Draft'}</td><td><a href=\"#\" data-edit-var=\"${v.id}\" class=\"text-blue-600\">Edit</a> <a href=\"#\" data-del-var=\"${v.id}\" class=\"text-red-600\">Delete</a></td>`;
            tbody.appendChild(tr);
        });
    }
    async function loadMeta(){
        const res = await fetch('/api/admin/catalog/product/meta?productId='+productId);
        const data = await res.json();
        const tbody = document.getElementById('metaBody'); tbody.innerHTML='';
        (data.items||[]).forEach(m=>{
            const tr = document.createElement('tr'); tr.className='border-b';
            tr.innerHTML = `<td class=\"py-2\">${m.id}</td><td>${m.meta_key}</td><td>${m.meta_value||''}</td><td><a href=\"#\" data-edit-meta=\"${m.id}\" data-k=\"${m.meta_key}\" data-v=\"${(m.meta_value||'').replace(/"/g,'&quot;')}\" class=\"text-blue-600\">Edit</a> <a href=\"#\" data-del-meta=\"${m.id}\" class=\"text-red-600\">Delete</a></td>`;
            tbody.appendChild(tr);
        });
    }
    document.getElementById('saveVariation').addEventListener('click', async ()=>{
        const payload = { id: editingVarId, product_id: productId, name: vName.value.trim(), sku: vSku.value.trim(), price: parseFloat(vPrice.value||'0'), stock: parseInt(vStock.value||'0',10), status: parseInt(vStatus.value,10) };
        const res = await fetch('/api/admin/catalog/variation',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        if(res.ok){ editingVarId = 0; ['vName','vSku','vPrice','vStock'].forEach(id=>document.getElementById(id).value=''); vStatus.value='1'; loadVariations(); } else { alert('Save failed'); }
    });
    document.getElementById('saveMeta').addEventListener('click', async ()=>{
        const payload = { id: editingMetaId, product_id: productId, meta_key: mKey.value.trim(), meta_value: mValue.value };
        const res = await fetch('/api/admin/catalog/product/meta',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        if(res.ok){ editingMetaId = 0; mKey.value=''; mValue.value=''; loadMeta(); } else { alert('Save failed'); }
    });
    document.addEventListener('click', async (e)=>{
        if(e.target && e.target.dataset){
            if(e.target.dataset.editVar){ editingVarId = parseInt(e.target.dataset.editVar,10); }
            if(e.target.dataset.delVar){ const id = e.target.dataset.delVar; if(confirm('Delete variation '+id+'?')){ const r = await fetch('/api/admin/catalog/variation/delete?id='+id); if(r.ok) loadVariations(); }}
            if(e.target.dataset.editMeta){ editingMetaId = parseInt(e.target.dataset.editMeta,10); mKey.value = e.target.dataset.k; mValue.value = e.target.dataset.v || ''; }
            if(e.target.dataset.delMeta){ const id = e.target.dataset.delMeta; if(confirm('Delete meta '+id+'?')){ const r = await fetch('/api/admin/catalog/product/meta/delete?id='+id); if(r.ok) loadMeta(); }}
        }
    });
    loadVariations(); loadMeta();
    </script>
</body>
</html>
