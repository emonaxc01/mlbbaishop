<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameTopUp Premium - Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">GameTopUp Premium</h1>
            <p class="text-gray-600">E-commerce Platform Installer</p>
        </div>

        <!-- Requirements Check -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">System Requirements Check</h2>
            <div id="checks" class="space-y-4">
                <div class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="ml-2">Checking system requirements...</span>
                </div>
            </div>
        </div>

        <!-- Environment Configuration -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Environment Configuration</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">App URL</label>
                    <input id="app_url" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://yourdomain.com" value="https://apiv2.mlbbai.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                    <input id="db_host" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="127.0.0.1" value="127.0.0.1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Port</label>
                    <input id="db_port" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="3306" value="3306">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Name *</label>
                    <input id="db_name" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="gametopup" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Username *</label>
                    <input id="db_user" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="root" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Password</label>
                    <input id="db_pass" type="password" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="password">
                </div>
            </div>
            
            <div class="mt-4">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Email Configuration (Optional)</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                        <input id="mail_host" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="smtp.example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                        <input id="mail_port" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="587" value="587">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Username</label>
                        <input id="mail_user" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="smtp user">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
                        <input id="mail_pass" type="password" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="smtp pass">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Email</label>
                        <input id="mail_from" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="no-reply@yourdomain.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input id="mail_name" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="GameTopUp Premium" value="GameTopUp Premium">
                    </div>
                </div>
            </div>
            
            <button id="saveEnv" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Save Environment Configuration
            </button>
            <div id="saveEnvResult" class="mt-2"></div>
        </div>

        <!-- Database Migrations -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Database Setup</h2>
            <p class="text-gray-600 mb-4">This will create all necessary database tables and insert sample data.</p>
            <button id="runMigrations" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Run Database Migrations
            </button>
            <div id="migrationsResult" class="mt-2"></div>
        </div>

        <!-- Admin User Creation -->
        <div class="bg-white border rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Create Admin User</h2>
            <p class="text-gray-600 mb-4">Create the initial administrator account for the platform.</p>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email *</label>
                    <input id="admin_email" type="email" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="admin@example.com" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Password *</label>
                    <input id="admin_password" type="password" class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="password (8+ characters)" required>
                </div>
            </div>
            <button id="createAdmin" class="mt-4 px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Create Admin User
            </button>
            <div id="createAdminResult" class="mt-2"></div>
        </div>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Also available at <a href="/insall" class="text-blue-600 hover:underline">/insall</a> (alias).</p>
            <p class="mt-2">Need help? Check the <a href="/debug.php" class="text-blue-600 hover:underline">debug page</a> for detailed system information.</p>
        </div>
    </div>

    <script>
    // Utility functions
    function showMessage(elementId, message, type = 'info') {
        const element = document.getElementById(elementId);
        const colors = {
            success: 'text-green-700 bg-green-100 border-green-300',
            error: 'text-red-700 bg-red-100 border-red-300',
            warning: 'text-yellow-700 bg-yellow-100 border-yellow-300',
            info: 'text-blue-700 bg-blue-100 border-blue-300'
        };
        element.innerHTML = `<div class="p-3 border rounded-md ${colors[type]}">${message}</div>`;
    }

    function setButtonState(buttonId, disabled, text) {
        const button = document.getElementById(buttonId);
        button.disabled = disabled;
        button.textContent = text;
    }

    // Check system requirements
    async function checkRequirements() {
        try {
            const response = await fetch('/install/check');
            const data = await response.json();
            
            const checksDiv = document.getElementById('checks');
            let html = '';
            
            // PHP Version
            html += `<div class="flex items-center justify-between p-3 border rounded-md ${data.php_version >= '8.0' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}">`;
            html += `<span>PHP Version</span><span class="font-mono">${data.php_version}</span>`;
            html += `</div>`;
            
            // Extensions
            html += `<div class="mt-4"><h3 class="font-medium mb-2">PHP Extensions</h3>`;
            Object.entries(data.extensions).forEach(([ext, loaded]) => {
                html += `<div class="flex items-center justify-between p-2 border rounded-md ${loaded ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}">`;
                html += `<span>${ext}</span><span>${loaded ? '✅ Loaded' : '❌ Missing'}</span>`;
                html += `</div>`;
            });
            html += `</div>`;
            
            // Permissions
            html += `<div class="mt-4"><h3 class="font-medium mb-2">File Permissions</h3>`;
            Object.entries(data.writable).forEach(([path, writable]) => {
                html += `<div class="flex items-center justify-between p-2 border rounded-md ${writable ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}">`;
                html += `<span>${path}</span><span>${writable ? '✅ Writable' : '❌ Not Writable'}</span>`;
                html += `</div>`;
            });
            html += `</div>`;
            
            // Errors
            if (data.errors && data.errors.length > 0) {
                html += `<div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">`;
                html += `<h3 class="font-medium text-red-800 mb-2">Critical Issues:</h3>`;
                html += `<ul class="list-disc list-inside text-red-700 space-y-1">`;
                data.errors.forEach(error => {
                    html += `<li>${error}</li>`;
                });
                html += `</ul></div>`;
            }
            
            // Warnings
            if (data.warnings && data.warnings.length > 0) {
                html += `<div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">`;
                html += `<h3 class="font-medium text-yellow-800 mb-2">Warnings:</h3>`;
                html += `<ul class="list-disc list-inside text-yellow-700 space-y-1">`;
                data.warnings.forEach(warning => {
                    html += `<li>${warning}</li>`;
                });
                html += `</ul></div>`;
            }
            
            // Info
            if (data.info && data.info.length > 0) {
                html += `<div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">`;
                html += `<h3 class="font-medium text-blue-800 mb-2">Information:</h3>`;
                html += `<ul class="list-disc list-inside text-blue-700 space-y-1">`;
                data.info.forEach(info => {
                    html += `<li>${info}</li>`;
                });
                html += `</ul></div>`;
            }
            
            checksDiv.innerHTML = html;
            
        } catch (error) {
            document.getElementById('checks').innerHTML = `
                <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-red-700">Failed to check requirements: ${error.message}</p>
                </div>
            `;
        }
    }

    // Save environment configuration
    document.getElementById('saveEnv').addEventListener('click', async () => {
        const button = document.getElementById('saveEnv');
        setButtonState('saveEnv', true, 'Saving...');
        
        try {
            const payload = {
                app_url: document.getElementById('app_url').value.trim(),
                db_host: document.getElementById('db_host').value.trim(),
                db_port: document.getElementById('db_port').value.trim(),
                db_name: document.getElementById('db_name').value.trim(),
                db_user: document.getElementById('db_user').value.trim(),
                db_pass: document.getElementById('db_pass').value,
                mail_host: document.getElementById('mail_host').value.trim(),
                mail_port: document.getElementById('mail_port').value.trim(),
                mail_user: document.getElementById('mail_user').value.trim(),
                mail_pass: document.getElementById('mail_pass').value,
                mail_from: document.getElementById('mail_from').value.trim(),
                mail_name: document.getElementById('mail_name').value.trim()
            };
            
            const response = await fetch('/install/save-env', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                showMessage('saveEnvResult', result.message, 'success');
                setButtonState('saveEnv', false, 'Environment Saved');
            } else {
                showMessage('saveEnvResult', result.error || 'Failed to save environment', 'error');
                setButtonState('saveEnv', false, 'Save Environment Configuration');
            }
        } catch (error) {
            showMessage('saveEnvResult', `Network error: ${error.message}`, 'error');
            setButtonState('saveEnv', false, 'Save Environment Configuration');
        }
    });

    // Run migrations
    document.getElementById('runMigrations').addEventListener('click', async () => {
        const button = document.getElementById('runMigrations');
        setButtonState('runMigrations', true, 'Running Migrations...');
        
        try {
            const response = await fetch('/install/migrate');
            const result = await response.json();
            
            if (response.ok) {
                showMessage('migrationsResult', `${result.message}<br>Executed: ${result.executed.join(', ')}`, 'success');
                setButtonState('runMigrations', false, 'Migrations Complete');
            } else {
                showMessage('migrationsResult', result.error || 'Failed to run migrations', 'error');
                setButtonState('runMigrations', false, 'Run Database Migrations');
            }
        } catch (error) {
            showMessage('migrationsResult', `Network error: ${error.message}`, 'error');
            setButtonState('runMigrations', false, 'Run Database Migrations');
        }
    });

    // Create admin user
    document.getElementById('createAdmin').addEventListener('click', async () => {
        const button = document.getElementById('createAdmin');
        setButtonState('createAdmin', true, 'Creating Admin...');
        
        try {
            const payload = {
                email: document.getElementById('admin_email').value.trim(),
                password: document.getElementById('admin_password').value
            };
            
            const response = await fetch('/install/create-admin', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                showMessage('createAdminResult', `${result.message}<br><a href="/admin" class="text-blue-600 hover:underline">Go to Admin Panel</a>`, 'success');
                setButtonState('createAdmin', false, 'Admin Created');
            } else {
                showMessage('createAdminResult', result.error || 'Failed to create admin', 'error');
                setButtonState('createAdmin', false, 'Create Admin User');
            }
        } catch (error) {
            showMessage('createAdminResult', `Network error: ${error.message}`, 'error');
            setButtonState('createAdmin', false, 'Create Admin User');
        }
    });

    // Initialize
    checkRequirements();
    </script>
</body>
</html>
