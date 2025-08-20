<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Orders</h1>
            <div class="space-x-2">
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin">Dashboard</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/products">Products</a>
            </div>
        </div>
        <div class="bg-white border rounded-lg p-4 overflow-x-auto">
            <div class="flex items-center gap-2 mb-3">
                <a href="/api/admin/export/orders" class="px-3 py-2 bg-gray-800 text-white rounded">Export CSV</a>
                <label class="px-3 py-2 border rounded cursor-pointer">Import CSV<input id="importOrders" type="file" class="hidden"></label>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">ID</th>
                        <th>User</th>
                        <th>Game</th>
                        <th>Product</th>
                        <th>Variation</th>
                        <th>Player ID</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody id="ordersBody"></tbody>
            </table>
            <div class="mt-6">
                <h3 class="font-semibold mb-2">Add Note</h3>
                <div class="flex items-center gap-2">
                    <input id="noteOrderId" class="border px-3 py-2 rounded w-32" placeholder="Order ID">
                    <input id="noteText" class="border px-3 py-2 rounded flex-1" placeholder="Your note to user">
                    <label class="inline-flex items-center gap-2"><input id="noteEmail" type="checkbox"> Email user</label>
                    <button id="addNote" class="px-3 py-2 bg-blue-600 text-white rounded">Add</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    async function loadOrders(){
        const res = await fetch('/api/admin/orders');
        const data = await res.json();
        const tbody = document.getElementById('ordersBody');
        tbody.innerHTML='';
        (data.orders||[]).forEach(o => {
            const tr = document.createElement('tr');
            tr.className='border-b';
            tr.innerHTML = `<td class="py-2">${o.id}</td><td>${o.email}</td><td>${o.game}</td><td>${o.product_id}</td><td>${o.variation_code}</td><td>${o.player_id}</td><td>${o.amount}</td><td>${o.currency}</td><td>${o.payment_method}</td><td>${o.status}</td><td>${o.created_at}</td>`;
            tbody.appendChild(tr);
        });
    }
    loadOrders();
    document.getElementById('addNote').addEventListener('click', async ()=>{
        const order_id = parseInt(document.getElementById('noteOrderId').value||'0',10);
        const note = document.getElementById('noteText').value.trim();
        const email_user = document.getElementById('noteEmail').checked ? 1 : 0;
        if(order_id<=0 || !note){ alert('Provide order id and note'); return; }
        const res = await fetch('/api/admin/orders/note',{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({order_id, note, email_user})});
        alert(res.ok ? 'Note added' : 'Failed');
        if(res.ok){ document.getElementById('noteText').value=''; document.getElementById('noteEmail').checked=false; }
    });
    document.getElementById('importOrders').addEventListener('change', async (e)=>{
        if(!e.target.files[0]) return;
        const fd = new FormData(); fd.append('file', e.target.files[0]);
        const res = await fetch('/api/admin/import/orders',{method:'POST', body: fd});
        alert(res.ok ? 'Imported' : 'Failed');
        if(res.ok) loadOrders();
        e.target.value='';
    });
    </script>
</body>
</html>
