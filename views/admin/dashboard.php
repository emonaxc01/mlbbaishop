<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <div class="space-x-2">
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/products">Products</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/admin/orders">Orders</a>
                <a class="px-4 py-2 bg-white border rounded-lg" href="/">Back to Site</a>
            </div>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg border">
                <div class="text-sm text-gray-500">Total Users</div>
                <div class="text-3xl font-bold" id="statUsers">—</div>
            </div>
            <div class="bg-white p-4 rounded-lg border">
                <div class="text-sm text-gray-500">Total Orders</div>
                <div class="text-3xl font-bold" id="statOrders">—</div>
            </div>
            <div class="bg-white p-4 rounded-lg border">
                <div class="text-sm text-gray-500">Revenue (BDT)</div>
                <div class="text-3xl font-bold" id="statRevenue">—</div>
            </div>
        </div>
    </div>
    <script>
    (async function(){
        try {
            const res = await fetch('/api/admin/orders');
            const data = await res.json();
            const orders = data.orders || [];
            document.getElementById('statOrders').textContent = orders.length.toLocaleString();
            let revenue = 0;
            orders.forEach(o => { if (o.currency === 'BDT') revenue += parseFloat(o.amount); });
            document.getElementById('statRevenue').textContent = revenue.toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2});
        } catch (e) {}
        try {
            const res = await fetch('/api/admin/products');
            const data = await res.json();
            document.getElementById('statUsers').textContent = '—';
        } catch (e) {}
    })();
    </script>
 </body>
</html>
