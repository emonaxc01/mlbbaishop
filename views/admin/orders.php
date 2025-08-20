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
    </script>
</body>
</html>
