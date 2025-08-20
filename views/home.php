<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameTopUp Premium - Mobile Legends & More</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .loading {
            display: none;
        }
        .loading.show {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-gamepad text-2xl"></i>
                    <h1 class="text-2xl font-bold">GameTopUp Premium</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="userInfo" class="hidden">
                        <span id="userEmail" class="mr-4"></span>
                        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </div>
                    <div id="authButtons">
                        <button onclick="showLoginModal()" class="bg-white text-purple-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition-colors mr-2">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </button>
                        <button onclick="showRegisterModal()" class="bg-transparent border border-white hover:bg-white hover:text-purple-600 px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Top Up Your Favorite Games</h2>
            <p class="text-xl text-gray-600 mb-8">Fast, secure, and reliable game top-up services</p>
            <div class="flex justify-center space-x-4">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <i class="fas fa-bolt text-2xl text-yellow-500 mb-2"></i>
                    <p class="font-semibold">Instant Delivery</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <i class="fas fa-shield-alt text-2xl text-green-500 mb-2"></i>
                    <p class="font-semibold">Secure Payment</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <i class="fas fa-headset text-2xl text-blue-500 mb-2"></i>
                    <p class="font-semibold">24/7 Support</p>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Available Games</h3>
            <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Products will be loaded here -->
            </div>
            <div id="loading" class="loading text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                <p class="text-gray-600 mt-2">Loading products...</p>
            </div>
        </div>

        <!-- Featured Products -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Featured Products</h3>
            <div id="featuredProducts" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Featured products will be loaded here -->
            </div>
        </div>
    </main>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Login</h3>
                    <button onclick="hideLoginModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="loginForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="loginEmail" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="loginPassword" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        Login
                    </button>
                </form>
                <p class="text-center mt-4 text-sm text-gray-600">
                    Don't have an account? 
                    <button onclick="hideLoginModal(); showRegisterModal()" class="text-purple-600 hover:underline">Register</button>
                </p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Register</h3>
                    <button onclick="hideRegisterModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="registerForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="registerEmail" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="registerPassword" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        Register
                    </button>
                </form>
                <p class="text-center mt-4 text-sm text-gray-600">
                    Already have an account? 
                    <button onclick="hideRegisterModal(); showLoginModal()" class="text-purple-600 hover:underline">Login</button>
                </p>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div id="otpModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Verify Email</h3>
                    <button onclick="hideOtpModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-gray-600 mb-4">We've sent a verification code to your email. Please enter it below.</p>
                <form id="otpForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
                        <input type="text" id="otpCode" required pattern="[0-9]{6}" maxlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-center text-lg tracking-widest">
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        Verify
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-lg font-bold mb-4">GameTopUp Premium</h4>
                    <p class="text-gray-300">Your trusted partner for game top-ups and digital services.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white">Home</a></li>
                        <li><a href="#" class="hover:text-white">Products</a></li>
                        <li><a href="#" class="hover:text-white">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Contact</h4>
                    <p class="text-gray-300">Email: support@gametopup.com</p>
                    <p class="text-gray-300">24/7 Customer Support</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; 2024 GameTopUp Premium. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Global variables
        let currentUser = null;
        let products = [];

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            checkAuthStatus();
            loadProducts();
        });

        // Authentication functions
        async function checkAuthStatus() {
            try {
                const response = await fetch('/api/auth/me');
                const data = await response.json();
                
                if (data.authenticated) {
                    currentUser = data.user;
                    showUserInfo();
                } else {
                    showAuthButtons();
                }
            } catch (error) {
                console.error('Auth check failed:', error);
                showAuthButtons();
            }
        }

        function showUserInfo() {
            document.getElementById('userInfo').classList.remove('hidden');
            document.getElementById('authButtons').classList.add('hidden');
            document.getElementById('userEmail').textContent = currentUser.email;
        }

        function showAuthButtons() {
            document.getElementById('userInfo').classList.add('hidden');
            document.getElementById('authButtons').classList.remove('hidden');
        }

        async function logout() {
            try {
                await fetch('/api/auth/logout', { method: 'POST' });
                currentUser = null;
                showAuthButtons();
            } catch (error) {
                console.error('Logout failed:', error);
            }
        }

        // Modal functions
        function showLoginModal() {
            document.getElementById('loginModal').classList.remove('hidden');
        }

        function hideLoginModal() {
            document.getElementById('loginModal').classList.add('hidden');
            document.getElementById('loginForm').reset();
        }

        function showRegisterModal() {
            document.getElementById('registerModal').classList.remove('hidden');
        }

        function hideRegisterModal() {
            document.getElementById('registerModal').classList.add('hidden');
            document.getElementById('registerForm').reset();
        }

        function showOtpModal() {
            document.getElementById('otpModal').classList.remove('hidden');
        }

        function hideOtpModal() {
            document.getElementById('otpModal').classList.add('hidden');
            document.getElementById('otpForm').reset();
        }

        // Form handlers
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();
                
                if (response.ok) {
                    hideLoginModal();
                    checkAuthStatus();
                    alert('Login successful!');
                } else {
                    alert(data.error || 'Login failed');
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('Login failed. Please try again.');
            }
        });

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();
                
                if (response.ok) {
                    hideRegisterModal();
                    showOtpModal();
                    alert('Registration successful! Please check your email for verification code.');
                } else {
                    alert(data.error || 'Registration failed');
                }
            } catch (error) {
                console.error('Registration error:', error);
                alert('Registration failed. Please try again.');
            }
        });

        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('registerEmail').value;
            const code = document.getElementById('otpCode').value;

            try {
                const response = await fetch('/api/auth/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, code })
                });

                const data = await response.json();
                
                if (response.ok) {
                    hideOtpModal();
                    checkAuthStatus();
                    alert('Email verified successfully!');
                } else {
                    alert(data.error || 'Verification failed');
                }
            } catch (error) {
                console.error('OTP verification error:', error);
                alert('Verification failed. Please try again.');
            }
        });

        // Product loading
        async function loadProducts() {
            const loading = document.getElementById('loading');
            const productsGrid = document.getElementById('productsGrid');
            
            loading.classList.add('show');
            productsGrid.innerHTML = '';

            try {
                const response = await fetch('/api/catalog');
                const data = await response.json();
                
                if (response.ok && data.products) {
                    products = data.products;
                    renderProducts(products);
                } else {
                    // Show sample products if API fails
                    showSampleProducts();
                }
            } catch (error) {
                console.error('Failed to load products:', error);
                showSampleProducts();
            } finally {
                loading.classList.remove('show');
            }
        }

        function renderProducts(products) {
            const productsGrid = document.getElementById('productsGrid');
            const featuredProducts = document.getElementById('featuredProducts');
            
            productsGrid.innerHTML = '';
            featuredProducts.innerHTML = '';

            products.forEach((product, index) => {
                const productCard = createProductCard(product);
                productsGrid.appendChild(productCard);

                // Add first 4 products to featured section
                if (index < 4) {
                    const featuredCard = createProductCard(product);
                    featuredProducts.appendChild(featuredCard);
                }
            });
        }

        function createProductCard(product) {
            const card = document.createElement('div');
            card.className = 'product-card bg-white rounded-lg shadow-md overflow-hidden';
            
            card.innerHTML = `
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">${product.name || 'Game Top-Up'}</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">Active</span>
                    </div>
                    <p class="text-gray-600 mb-4">${product.description || 'Premium game top-up service'}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-bold text-purple-600">$${product.price || '9.99'}</span>
                        <button onclick="buyProduct('${product.id || '1'}')" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Buy Now
                        </button>
                    </div>
                </div>
            `;
            
            return card;
        }

        function showSampleProducts() {
            const sampleProducts = [
                { id: '1', name: 'Mobile Legends', description: 'Diamonds & Starlight Pass', price: '9.99' },
                { id: '2', name: 'PUBG Mobile', description: 'UC (Unknown Cash)', price: '4.99' },
                { id: '3', name: 'Free Fire', description: 'Diamonds', price: '7.99' },
                { id: '4', name: 'Genshin Impact', description: 'Genesis Crystals', price: '14.99' },
                { id: '5', name: 'Call of Duty Mobile', description: 'CP (Call of Duty Points)', price: '6.99' },
                { id: '6', name: 'Arena of Valor', description: 'Vouchers', price: '5.99' }
            ];
            
            renderProducts(sampleProducts);
        }

        function buyProduct(productId) {
            if (!currentUser) {
                alert('Please login to purchase products');
                showLoginModal();
                return;
            }
            
            alert(`Purchase initiated for product ${productId}. This would redirect to checkout.`);
        }
    </script>
</body>
</html>
