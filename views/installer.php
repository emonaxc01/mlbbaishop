<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Web Installer</h1>
        <p class="text-sm text-gray-600 mb-4">Currently, installation steps are handled via CLI (composer install, php bin/migrate.php). A full web installer can be expanded here to write .env, run migrations and create the first admin account.</p>
        <div class="bg-white border rounded-lg p-4">
            <ol class="list-decimal pl-6 space-y-2 text-sm">
                <li>Set DB and Mail credentials in <code>.env</code></li>
                <li>Run <code>composer install</code></li>
                <li>Run <code>php bin/migrate.php</code></li>
                <li>Create an admin user, then set <code>is_admin=1</code></li>
            </ol>
        </div>
    </div>
</body>
</html>
