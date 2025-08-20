<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Game Top-Up Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ff3e3e',
                        secondary: '#2d3748',
                        accent: '#ff9900',
                        premium: '#8a2be2',
                        dark: '#1a202c',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .toast {
            animation: fadeIn 0.3s, fadeIn 0.3s reverse forwards 2s;
        }
        
        .package {
            transition: all 0.3s ease;
        }
        
        .package:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .game-card {
            transition: all 0.3s ease;
        }
        
        .game-card:hover {
            transform: scale(1.05);
        }
        
        .premium-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(45deg, #8a2be2, #4b0082);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .currency-selector {
            border: 2px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(to right, #ff3e3e, #8a2be2) border-box;
            border-radius: 30px;
        }
        
        .login-tab {
            transition: all 0.3s ease;
        }
        
        .login-tab.active {
            background: linear-gradient(to right, #ff3e3e, #8a2be2);
            color: white;
        }
        
        .slider-dot {
            transition: all 0.3s ease;
        }
        
        .slider-dot.active {
            width: 16px;
            border-radius: 8px;
            opacity: 1;
            background: white;
        }
        
        .loading-bar {
            height: 3px;
            width: 100%;
            background: linear-gradient(to right, #ff3e3e, #8a2be2);
            position: absolute;
            bottom: 0;
            left: 0;
            animation: loading 2s ease-in-out;
        }
        
        @keyframes loading {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 200px;
            z-index: 100;
            overflow: hidden;
        }
        
        .dropdown-menu.show {
            display: block;
            animation: slideIn 0.3s ease-out;
        }
        
        .wallet-balance {
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        /* Mobile bottom navigation */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-around;
            align-items: center;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 8px 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #6b7280;
            font-size: 0.75rem;
            padding: 5px 0;
            flex: 1;
        }
        
        .mobile-bottom-nav-item.active {
            color: #ff3e3e;
        }
        
        .mobile-bottom-nav-icon {
            font-size: 1.25rem;
            margin-bottom: 4px;
        }
        
        @media (min-width: 768px) {
            .mobile-bottom-nav {
                display: none;
            }
        }
        
        /* Mobile menu button */
        .mobile-menu-button {
            display: block;
        }
        
        @media (min-width: 768px) {
            .mobile-menu-button {
                display: none;
            }
        }
        
        /* Mobile dropdown */
        .mobile-dropdown {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1001;
        }
        
        .mobile-dropdown.show {
            display: block;
        }
        
        .mobile-dropdown-content {
            position: absolute;
            top: 60px;
            right: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 200px;
            overflow: hidden;
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-gray-100 text-gray-900 font-sans min-h-screen">
    <!-- Loading bar for API simulation -->
    <div class="fixed top-0 left-0 w-full z-50 hidden" id="apiLoading">
        <div class="loading-bar"></div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-r from-primary to-premium rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-white text-xl"></i>
                </div>
                <span class="font-bold text-xl bg-gradient-to-r from-primary to-premium bg-clip-text text-transparent">GameTopUp Premium</span>
            </div>
            <nav class="hidden md:flex gap-6 text-sm font-medium items-center">
                <a href="#games" class="hover:text-primary transition">Games</a>
                <a href="#" class="hover:text-primary transition">Promotions</a>
                <a href="#" class="hover:text-primary transition">Support</a>
                
                <!-- User Menu -->
                <div class="relative" id="userMenuContainer">
                    <button class="bg-gradient-to-r from-primary to-premium text-white px-4 py-2 rounded-lg hover:opacity-90 transition flex items-center gap-2" id="userMenuButton">
                        <i class="fas fa-user-circle"></i>
                        <span>Login</span>
                    </button>
                    
                    <div class="dropdown-menu" id="userDropdown">
                        <div class="p-4 wallet-balance">
                            <div class="text-xs opacity-80">Wallet Balance</div>
                            <div class="text-xl font-bold">$24.50</div>
                        </div>
                        <div class="py-2">
                            <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-user text-sm w-5"></i> Profile</a>
                            <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-history text-sm w-5"></i> Orders</a>
                            <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-headset text-sm w-5"></i> Support</a>
                            <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-star text-sm w-5"></i> Review Us</a>
                            <hr class="my-2">
                            <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-red-600" id="logoutButton"><i class="fas fa-sign-out-alt text-sm w-5"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </nav>
            <button class="mobile-menu-button text-gray-600 md:hidden" id="mobileMenuButton">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </header>

    <!-- Mobile Dropdown Menu -->
    <div class="mobile-dropdown" id="mobileDropdown">
        <div class="mobile-dropdown-content">
            <div class="p-4 wallet-balance">
                <div class="text-xs opacity-80">Wallet Balance</div>
                <div class="text-xl font-bold">$24.50</div>
            </div>
            <div class="py-2">
                <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-home text-sm w-5"></i> Home</a>
                <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-wallet text-sm w-5"></i> Wallet</a>
                <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-history text-sm w-5"></i> Orders</a>
                <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-user text-sm w-5"></i> Profile</a>
                <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-headset text-sm w-5"></i> Support</a>
                <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-gray-700"><i class="fas fa-star text-sm w-5"></i> Review Us</a>
                <hr class="my-2">
                <a href="#" class="px-4 py-2 flex items-center gap-2 hover:bg-gray-100 text-red-600" id="mobileLogoutButton"><i class="fas fa-sign-out-alt text-sm w-5"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Banner Slider -->
    <section class="relative bg-gradient-to-r from-purple-900 to-indigo-800">
        <div class="max-w-6xl mx-auto">
            <div class="relative overflow-hidden h-60 md:h-80">
                <div class="absolute inset-0 flex transition-transform duration-500" id="slider">
                    <div class="w-full flex-shrink-0">
                        <div class="flex h-full items-center px-6">
                            <div class="text-white max-w-md slide-in">
                                <span class="bg-gradient-to-r from-accent to-yellow-500 text-xs px-3 py-1 rounded-full font-bold">NEW</span>
                                <h2 class="text-2xl md:text-3xl font-bold mb-3 mt-2">Double Diamonds Weekend!</h2>
                                <p class="mb-4 opacity-90">Top up now and get double diamonds for a limited time only!</p>
                                <button class="bg-gradient-to-r from-accent to-yellow-600 text-white px-5 py-2 rounded-lg font-medium hover:opacity-90 transition pulse">Get Offer</button>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex-shrink-0">
                        <div class="flex h-full items-center px-6 bg-gradient-to-r from-blue-900 to-teal-800">
                            <div class="text-white max-w-md slide-in">
                                <span class="bg-gradient-to-r from-green-400 to-emerald-600 text-xs px-3 py-1 rounded-full font-bold">HOT</span>
                                <h2 class="text-2xl md:text-3xl font-bold mb-3 mt-2">New Season Rewards</h2>
                                <p class="mb-4 opacity-90">Unlock exclusive skins and weapons with our special packages</p>
                                <button class="bg-gradient-to-r from-primary to-red-600 text-white px-5 py-2 rounded-lg font-medium hover:opacity-90 transition pulse">View Rewards</button>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex-shrink-0">
                        <div class="flex h-full items-center px-6 bg-gradient-to-r from-red-900 to-orange-800">
                            <div class="text-white max-w-md slide-in">
                                <span class="bg-gradient-to-r from-premium to-purple-600 text-xs px-3 py-1 rounded-full font-bold">VIP</span>
                                <h2 class="text-2xl md:text-3xl font-bold mb-3 mt-2">VIP Member Benefits</h2>
                                <p class="mb-4 opacity-90">Get 10% bonus diamonds on every top-up as a VIP member</p>
                                <button class="bg-gradient-to-r from-premium to-purple-600 text-white px-5 py-2 rounded-lg font-medium hover:opacity-90 transition pulse">Become VIP</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
                <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50 active" data-index="0"></button>
                <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50" data-index="1"></button>
                <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50" data-index="2"></button>
            </div>
            <button id="prevBtn" class="absolute left-2 top-1/2 transform -translate-y-1/2 text-white bg-black/30 rounded-full w-10 h-10 flex items-center justify-center hover:bg-black/50 transition">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button id="nextBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-white bg-black/30 rounded-full w-10 h-10 flex items-center justify-center hover:bg-black/50 transition">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <!-- Game Selection -->
    <section id="games" class="max-w-6xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Select Your Game</h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
            <!-- Free Fire -->
            <button data-game="freefire" data-product-id="1001" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-yellow-400 to-red-600 flex items-center justify-center mb-2">
                    <i class="fas fa-fire text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">Free Fire</div>
                <div class="premium-badge">HOT</div>
            </button>
            
            <!-- Mobile Legends -->
            <button data-game="mlbb" data-product-id="1002" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mb-2">
                    <i class="fas fa-crown text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">Mobile Legends</div>
            </button>
            
            <!-- PUBG Mobile -->
            <button data-game="pubg" data-product-id="1003" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center mb-2">
                    <i class="fas fa-crosshairs text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">PUBG Mobile</div>
            </button>
            
            <!-- Call of Duty -->
            <button data-game="cod" data-product-id="1004" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-gray-700 to-black flex items-center justify-center mb-2">
                    <i class="fas fa-skull text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">Call of Duty</div>
            </button>
            
            <!-- Genshin Impact -->
            <button data-game="genshin" data-product-id="1005" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-300 to-blue-500 flex items-center justify-center mb-2">
                    <i class="fas fa-wind text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">Genshin Impact</div>
            </button>
            
            <!-- Valorant -->
            <button data-game="valorant" data-product-id="1006" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center mb-2">
                    <i class="fas fa-bolt text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">Valorant</div>
            </button>
            
            <!-- League of Legends -->
            <button data-game="lol" data-product-id="1007" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-yellow-600 to-orange-700 flex items-center justify-center mb-2">
                    <i class="fas fa-fist-raised text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">League of Legends</div>
            </button>
            
            <!-- Fortnite -->
            <button data-game="fortnite" data-product-id="1008" class="game-card group flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white transition shadow-sm hover:shadow-md card-hover">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center mb-2">
                    <i class="fas fa-umbrella-beach text-white text-xl"></i>
                </div>
                <div class="text-center text-sm font-medium group-hover:text-primary">Fortnite</div>
            </button>
        </div>
    </section>

    <!-- Game Top-Up Panel -->
    <section id="panel" class="max-w-4xl mx-auto px-4 pb-16">
        <div class="rounded-2xl overflow-hidden border bg-white shadow-xl card-hover">
            <!-- Panel Header -->
            <div class="p-5 bg-gradient-to-r from-primary to-premium">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-xl font-bold text-white"><span id="panelGameName">Free Fire</span> Top-Up</h3>
                    <div class="flex items-center gap-3">
                        <span class="text-xs px-3 py-1 bg-white/20 text-white rounded-full">100% Secure Payment</span>
                        <div class="currency-selector">
                            <select id="currencySelect" class="bg-white px-3 py-1 rounded-full text-sm font-medium focus:outline-none">
                                <option value="BDT">BDT</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="INR">INR</option>
                                <option value="PKR">PKR</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Login -->
            <div class="p-5 border-b">
                <div class="flex items-center gap-3 text-lg font-bold mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white">1</div>
                    <div>Account Information</div>
                </div>
                
                <!-- Game-specific login forms -->
                <div id="freefireLogin" class="game-login">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto]">
                        <div>
                            <label for="playerId" class="block text-sm font-medium mb-1">Free Fire UID</label>
                            <div class="relative">
                                <input 
                                    id="playerId" 
                                    type="text" 
                                    class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent pl-12" 
                                    placeholder="Enter your Free Fire UID"
                                    aria-label="Free Fire UID"
                                >
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Numeric characters only</p>
                        </div>
                        <div class="flex items-end">
                            <button 
                                id="loginBtn" 
                                class="w-full bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 px-5 py-3 rounded-xl font-medium disabled:opacity-70 transition hover:opacity-90"
                                disabled
                                aria-label="Login"
                            >
                                Verify
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="mlbbLogin" class="game-login hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="mlbbUserId" class="block text-sm font-medium mb-1">MLBB User ID</label>
                            <div class="relative">
                                <input 
                                    id="mlbbUserId" 
                                    type="text" 
                                    class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent pl-12" 
                                    placeholder="Enter your User ID"
                                >
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="mlbbServerId" class="block text-sm font-medium mb-1">MLBB Server ID</label>
                            <div class="relative">
                                <input 
                                    id="mlbbServerId" 
                                    type="text" 
                                    class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent pl-12" 
                                    placeholder="Enter your Server ID"
                                >
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                                    <i class="fas fa-server"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button 
                            id="mlbbLoginBtn" 
                            class="w-full md:w-auto bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 px-5 py-3 rounded-xl font-medium disabled:opacity-70 transition hover:opacity-90"
                            disabled
                        >
                            Verify Account
                        </button>
                    </div>
                </div>
                
                <div id="codeItemLogin" class="game-login hidden">
                    <div>
                        <label for="userEmail" class="block text-sm font-medium mb-1">Email Address</label>
                        <div class="relative">
                            <input 
                                id="userEmail" 
                                type="email" 
                                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent pl-12" 
                                placeholder="Enter your email address"
                            >
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">We'll send the code to this email</p>
                    </div>
                </div>
                
                <div id="ingameItemLogin" class="game-login hidden">
                    <div class="flex border-b mb-4">
                        <button class="login-tab active px-4 py-2 rounded-t-lg" data-tab="email">Email</button>
                        <button class="login-tab px-4 py-2 rounded-t-lg" data-tab="phone">Phone</button>
                    </div>
                    
                    <div id="emailLoginForm">
                        <div class="mb-4">
                            <label for="loginEmail" class="block text-sm font-medium mb-1">Email Address</label>
                            <input 
                                id="loginEmail" 
                                type="email" 
                                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                placeholder="Enter your email"
                            >
                        </div>
                        <div class="mb-4">
                            <label for="loginPassword" class="block text-sm font-medium mb-1">Password</label>
                            <input 
                                id="loginPassword" 
                                type="password" 
                                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                placeholder="Enter your password"
                            >
                        </div>
                        <div class="mb-4">
                            <label for="backupCodes" class="block text-sm font-medium mb-1>Backup Codes (Optional)</label>
                            <input 
                                id="backupCodes" 
                                type="text" 
                                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                placeholder="Enter backup codes if any"
                            >
                        </div>
                    </div>
                    
                    <div id="phoneLoginForm" class="hidden">
                        <div class="mb-4">
                            <label for="loginPhone" class="block text-sm font-medium mb-1">Phone Number</label>
                            <input 
                                id="loginPhone" 
                                type="tel" 
                                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                placeholder="Enter your phone number"
                            >
                        </div>
                        <div class="mb-4">
                            <label for="phonePassword" class="block text-sm font-medium mb-1">Password</label>
                            <input 
                                id="phonePassword" 
                                type="password" 
                                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                placeholder="Enter your password"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Top-up Amount -->
            <div class="p-5 border-b">
                <div class="flex items-center gap-3 text-lg font-bold mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white">2</div>
                    <div>Select Diamond Amount</div>
                </div>
                
                <div id="packages" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <!-- Packages will be injected by JS -->
                </div>
            </div>

            <!-- Step 3: Payment Method -->
            <div class="p-5 border-b">
                <div class="flex items-center gap-3 text-lg font-bold mb-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white">3</div>
                    <div>Select Payment Method</div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <button class="payment-method border-2 border-transparent rounded-xl p-4 flex flex-col items-center hover:border-primary bg-gray-50 transition card-hover" data-payment="credit_card">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                            <i class="fab fa-cc-visa text-blue-600 text-xl"></i>
                        </div>
                        <div class="text-xs font-medium">Credit Card</div>
                    </button>
                    
                    <button class="payment-method border-2 border-transparent rounded-xl p-4 flex flex-col items-center hover:border-primary bg-gray-50 transition card-hover" data-payment="mobile_banking">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mb-2">
                            <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                        </div>
                        <div class="text-xs font-medium">Mobile Banking</div>
                    </button>
                    
                    <button class="payment-method border-2 border-transparent rounded-xl p-4 flex flex-col items-center hover:border-primary bg-gray-50 transition card-hover" data-payment="e_wallet">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mb-2">
                            <i class="fas fa-wallet text-purple-600 text-xl"></i>
                        </div>
                        <div class="text-xs font-medium">E-Wallet</div>
                    </button>
                    
                    <button class="payment-method border-2 border-transparent rounded-xl p-4 flex flex-col items-center hover:border-primary bg-gray-50 transition card-hover" data-payment="crypto">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mb-2">
                            <i class="fas fa-coins text-yellow-600 text-xl"></i>
                        </div>
                        <div class="text-xs font-medium">Cryptocurrency</div>
                    </button>
                </div>
            </div>

            <!-- Step 4: Checkout -->
            <div class="p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center sm:text-left">
                    <p class="text-sm text-gray-600">Selected:</p>
                    <p id="selectedPackage" class="font-medium">None</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm">Total:</span>
                    <span id="totalAmount" class="text-xl font-bold text-primary">0.00</span>
                    <span id="currencySymbol" class="text-sm">BDT</span>
                </div>
                <button 
                    id="checkoutBtn" 
                    class="w-full sm:w-auto bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 px-6 py-3 rounded-xl font-medium disabled:opacity-70 transition flex items-center justify-center gap-2 hover:opacity-90"
                    disabled
                    aria-label="Proceed to Pay"
                >
                    <i class="fas fa-lock"></i>
                    Proceed to Pay
                </button>
            </div>
        </div>
    </section>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="#" class="mobile-bottom-nav-item active">
            <i class="fas fa-home mobile-bottom-nav-icon"></i>
            <span>Home</span>
        </a>
        <a href="#" class="mobile-bottom-nav-item">
            <i class="fas fa-wallet mobile-bottom-nav-icon"></i>
            <span>Wallet</span>
        </a>
        <a href="#" class="mobile-bottom-nav-item">
            <i class="fas fa-history mobile-bottom-nav-icon"></i>
            <span>Orders</span>
        </a>
        <a href="#" class="mobile-bottom-nav-item">
            <i class="fas fa-user mobile-bottom-nav-icon"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-secondary to-gray-900 text-white py-10 mb-16 md:mb-0">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-crown text-premium"></i>
                        GameTopUp Premium
                    </h3>
                    <p class="text-gray-400 text-sm">Fast and secure game top-up center for all your favorite games with premium features.</p>
                    <div class="flex gap-3 mt-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-discord"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium mb-4 text-white">Quick Links</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> Home</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> Games</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> Promotions</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4 text-white">Top Games</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-fire text-red-500"></i> Free Fire</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-crown text-blue-500"></i> Mobile Legends</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-crosshairs text-green-500"></i> PUBG Mobile</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-skull text-gray-500"></i> Call of Duty</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4 text-white">Newsletter</h4>
                    <p class="text-gray-400 text-sm mb-3">Subscribe to get special offers and updates</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="bg-gray-800 text-white px-4 py-2 rounded-l-lg w-full focus:outline-none text-sm">
                        <button class="bg-gradient-to-r from-primary to-premium text-white px-4 py-2 rounded-r-lg text-sm hover:opacity-90 transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-gray-400">
                        <i class="fas fa-headset text-xl"></i>
                        <div>
                            <div class="text-sm">24/7 Support</div>
                            <div class="font-medium">support@premium-topup.com</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-gray-500 text-sm">
                <p>© 2023 GameTopUp Premium. All rights reserved. This is a demo site for educational purposes.</p>
            </div>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-16 md:bottom-4 left-1/2 transform -translate-x-1/2 hidden bg-gradient-to-r from-primary to-premium text-white text-sm px-5 py-3 rounded-xl shadow-lg font-medium"></div>

    <!-- Order Confirmation Modal -->
    <div id="orderModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden animate-fade">
            <div class="bg-gradient-to-r from-primary to-premium p-5 text-white">
                <h3 class="text-xl font-bold">Confirm Your Order</h3>
            </div>
            <div class="p-6">
                <div class="mb-5">
                    <h4 class="font-bold mb-3 text-lg text-center">Order Summary</h4>
                    <div class="bg-gray-50 p-5 rounded-xl border">
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div class="text-gray-600">Game:</div>
                            <div class="font-medium" id="confirmGame">Free Fire</div>
                            
                            <div class="text-gray-600">Player ID:</div>
                            <div class="font-medium" id="confirmPlayerId">1234567890</div>
                            
                            <div class="text-gray-600">Package:</div>
                            <div class="font-medium" id="confirmPackage">115 Diamonds</div>
                            
                            <div class="text-gray-600">Amount:</div>
                            <div class="font-bold text-primary text-lg" id="confirmAmount">220.00 BDT</div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t">
                            <div class="text-gray-600 mb-2">Payment Method:</div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fab fa-cc-visa text-blue-600"></i>
                                </div>
                                <div class="font-medium" id="confirmPaymentMethod">Credit Card</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-4">
                    <button id="cancelOrder" class="px-5 py-2.5 border rounded-xl hover:bg-gray-100 transition font-medium">Cancel</button>
                    <button id="confirmOrder" class="px-5 py-2.5 bg-gradient-to-r from-primary to-premium text-white rounded-xl hover:opacity-90 transition font-medium">Confirm Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden text-center animate-fade">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-5 text-white">
                <h3 class="text-xl font-bold">Payment Successful!</h3>
            </div>
            <div class="p-8">
                <div class="text-green-500 text-6xl mb-5">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 class="text-xl font-bold mb-2">Thank You for Your Purchase!</h4>
                <p class="text-gray-600 mb-6">Your diamonds will be delivered to your account within a few minutes.</p>
                <div class="bg-gray-50 p-5 rounded-xl border mb-7">
                    <div class="flex justify-center mb-3">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-r from-primary to-premium flex items-center justify-center">
                            <i class="fas fa-crown text-white text-2xl"></i>
                        </div>
                    </div>
                    <p class="font-medium">Order ID: <span id="orderId" class="text-premium">ORD-12345</span></p>
                    <p class="mt-2">Player ID: <span id="successPlayerId">1234567890</span></p>
                    <p class="mt-2">Amount: <span class="font-bold text-green-600" id="successAmount">220.00 BDT</span></p>
                </div>
                <button id="closeSuccess" class="px-6 py-2.5 bg-gradient-to-r from-primary to-premium text-white rounded-xl hover:opacity-90 transition font-medium">Continue</button>
            </div>
        </div>
    </div>

    <script>
        // Exchange rates (base currency: BDT)
        const exchangeRates = {
            BDT: { symbol: '৳', rate: 1 },
            USD: { symbol: '$', rate: 0.0091 },
            EUR: { symbol: '€', rate: 0.0084 },
            INR: { symbol: '₹', rate: 0.76 },
            PKR: { symbol: '₨', rate: 2.52 }
        };

        // Game packages data (in base currency BDT)
        const packages = {
            freefire: [
                { code: 'd25', label: '25 Diamonds', diamonds: 25, price: 50 },
                { code: 'd50', label: '50 Diamonds', diamonds: 50, price: 100 },
                { code: 'd115', label: '115 Diamonds', diamonds: 115, price: 220 },
                { code: 'd230', label: '230 Diamonds', diamonds: 230, price: 420 },
                { code: 'd610', label: '610 Diamonds', diamonds: 610, price: 1000 }
            ],
            mlbb: [
                { code: 'd50', label: '50 Diamonds', diamonds: 50, price: 120 },
                { code: 'd100', label: '100 Diamonds', diamonds: 100, price: 240 },
                { code: 'd200', label: '200 Diamonds', diamonds: 200, price: 460 },
                { code: 'd500', label: '500 Diamonds', diamonds: 500, price: 1100 }
            ],
            pubg: [
                { code: 'd100', label: '100 UC', diamonds: 100, price: 110 },
                { code: 'd250', label: '250 UC', diamonds: 250, price: 270 },
                { code: 'd500', label: '500 UC', diamonds: 500, price: 530 },
                { code: 'd1000', label: '1000 UC', diamonds: 1000, price: 1050 }
            ],
            cod: [
                { code: 'd100', label: '100 CP', diamonds: 100, price: 120 },
                { code: 'd200', label: '200 CP', diamonds: 200, price: 230 },
                { code: 'd500', label: '500 CP', diamonds: 500, price: 550 },
                { code: 'd1000', label: '1000 CP', diamonds: 1000, price: 1050 }
            ],
            genshin: [
                { code: 'd60', label: '60 Crystals', diamonds: 60, price: 100 },
                { code: 'd300', label: '300 Crystals', diamonds: 300, price: 450 },
                { code: 'd980', label: '980 Crystals', diamonds: 980, price: 1400 },
                { code: 'd1980', label: '1980 Crystals', diamonds: 1980, price: 2700 }
            ],
            valorant: [
                { code: 'd125', label: '125 Points', diamonds: 125, price: 150 },
                { code: 'd400', label: '400 Points', diamonds: 400, price: 450 },
                { code: 'd1000', label: '1000 Points', diamonds: 1000, price: 1100 },
                { code: 'd2050', label: '2050 Points', diamonds: 2050, price: 2200 }
            ],
            lol: [
                { code: 'd125', label: '125 RP', diamonds: 125, price: 130 },
                { code: 'd420', label: '420 RP', diamonds: 420, price: 430 },
                { code: 'd940', label: '940 RP', diamonds: 940, price: 950 },
                { code: 'd1650', label: '1650 RP', diamonds: 1650, price: 1700 }
            ],
            fortnite: [
                { code: 'd100', label: '100 V-Bucks', diamonds: 100, price: 110 },
                { code: 'd500', label: '500 V-Bucks', diamonds: 500, price: 520 },
                { code: 'd1000', label: '1000 V-Bucks', diamonds: 1000, price: 1020 },
                { code: 'd13500', label: '13500 V-Bucks', diamonds: 13500, price: 13500 }
            ]
        };

        // State management
        let state = {
            selectedGame: 'freefire',
            selectedPackage: null,
            playerId: '',
            loggedIn: false,
            currentCurrency: 'BDT',
            paymentMethod: null,
            productId: '1001',
            variationId: null,
            paymentId: null,
            userLoggedIn: false
        };

        // DOM Elements
        const panel = document.getElementById('panel');
        const gameCards = document.querySelectorAll('.game-card');
        const playerInput = document.getElementById('playerId');
        const loginBtn = document.getElementById('loginBtn');
        const mlbbLoginBtn = document.getElementById('mlbbLoginBtn');
        const packagesContainer = document.getElementById('packages');
        const selectedPackageEl = document.getElementById('selectedPackage');
        const totalAmountEl = document.getElementById('totalAmount');
        const currencySymbolEl = document.getElementById('currencySymbol');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const toast = document.getElementById('toast');
        const orderModal = document.getElementById('orderModal');
        const successModal = document.getElementById('successModal');
        const confirmGame = document.getElementById('confirmGame');
        const confirmPlayerId = document.getElementById('confirmPlayerId');
        const confirmPackage = document.getElementById('confirmPackage');
        const confirmAmount = document.getElementById('confirmAmount');
        const confirmPaymentMethod = document.getElementById('confirmPaymentMethod');
        const orderId = document.getElementById('orderId');
        const successPlayerId = document.getElementById('successPlayerId');
        const successAmount = document.getElementById('successAmount');
        const currencySelect = document.getElementById('currencySelect');
        const panelGameName = document.getElementById('panelGameName');
        const apiLoading = document.getElementById('apiLoading');
        const userMenuButton = document.getElementById('userMenuButton');
        const userDropdown = document.getElementById('userDropdown');
        const logoutButton = document.getElementById('logoutButton');
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileDropdown = document.getElementById('mobileDropdown');
        const mobileLogoutButton = document.getElementById('mobileLogoutButton');
        const bottomNavItems = document.querySelectorAll('.mobile-bottom-nav-item');

        // Banner slider
        const slider = document.getElementById('slider');
        const dots = document.querySelectorAll('.slider-dot');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        let slideIndex = 0;
        const slideCount = 3;
        let sliderInterval;

        // Initialize the slider
        function initSlider() {
            updateSlider();
            
            // Auto slide
            sliderInterval = setInterval(() => {
                slideIndex = (slideIndex + 1) % slideCount;
                updateSlider();
            }, 5000);
            
            // Previous button
            prevBtn.addEventListener('click', () => {
                clearInterval(sliderInterval);
                slideIndex = (slideIndex - 1 + slideCount) % slideCount;
                updateSlider();
                sliderInterval = setInterval(() => {
                    slideIndex = (slideIndex + 1) % slideCount;
                    updateSlider();
                }, 5000);
            });
            
            // Next button
            nextBtn.addEventListener('click', () => {
                clearInterval(sliderInterval);
                slideIndex = (slideIndex + 1) % slideCount;
                updateSlider();
                sliderInterval = setInterval(() => {
                    slideIndex = (slideIndex + 1) % slideCount;
                    updateSlider();
                }, 5000);
            });
            
            // Dots
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    clearInterval(sliderInterval);
                    slideIndex = index;
                    updateSlider();
                    sliderInterval = setInterval(() => {
                        slideIndex = (slideIndex + 1) % slideCount;
                        updateSlider();
                    }, 5000);
                });
            });
        }
        
        // Update slider position
        function updateSlider() {
            slider.style.transform = `translateX(-${slideIndex * 100}%)`;
            
            // Update dots
            dots.forEach((dot, index) => {
                if (index === slideIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            toast.textContent = message;
            toast.classList.remove('hidden');
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Convert price to current currency
        function convertPrice(price) {
            const rate = exchangeRates[state.currentCurrency].rate;
            return price * rate;
        }

        // Format currency
        function formatCurrency(amount) {
            return amount.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Update browser URL without reloading
        function updateBrowserURL() {
            const params = new URLSearchParams();
            
            if (state.productId) params.set('app', state.productId);
            if (state.variationId) params.set('item', state.variationId);
            if (state.paymentId) params.set('channel', state.paymentId);
            
            const newURL = `${window.location.origin}${window.location.pathname}?${params.toString()}`;
            window.history.pushState({}, '', newURL);
        }

        // Save state to localStorage
        function saveStateToLocalStorage() {
            const stateToSave = {
                selectedGame: state.selectedGame,
                productId: state.productId,
                variationId: state.variationId,
                paymentId: state.paymentId,
                playerId: state.playerId,
                currentCurrency: state.currentCurrency,
                userLoggedIn: state.userLoggedIn
            };
            
            localStorage.setItem('gameTopUpState', JSON.stringify(stateToSave));
        }

        // Load state from localStorage
        function loadStateFromLocalStorage() {
            const savedState = localStorage.getItem('gameTopUpState');
            if (savedState) {
                const parsedState = JSON.parse(savedState);
                
                // Update state
                state.selectedGame = parsedState.selectedGame || 'freefire';
                state.productId = parsedState.productId || '1001';
                state.variationId = parsedState.variationId || null;
                state.paymentId = parsedState.paymentId || null;
                state.playerId = parsedState.playerId || '';
                state.currentCurrency = parsedState.currentCurrency || 'BDT';
                state.userLoggedIn = parsedState.userLoggedIn || false;
                
                // Update UI based on saved state
                currencySelect.value = state.currentCurrency;
                currencySymbolEl.textContent = state.currentCurrency;
                
                // Update user menu
                updateUserMenu();
                
                // Highlight the saved game
                document.querySelectorAll('.game-card').forEach(card => {
                    if (card.dataset.game === state.selectedGame) {
                        card.classList.remove('border', 'border-gray-200', 'shadow-sm');
                        card.classList.add('border-2', 'border-primary', 'shadow-lg');
                        panelGameName.textContent = card.querySelector('div').textContent;
                    } else {
                        card.classList.remove('border-2', 'border-primary', 'shadow-lg');
                        card.classList.add('border', 'border-gray-200', 'shadow-sm');
                    }
                });
                
                // Show the appropriate login form
                showGameLoginForm(state.selectedGame);
                
                // Render packages
                renderPackages();
                
                // Pre-fill player ID if available
                if (state.playerId) {
                    playerInput.value = state.playerId;
                    playerInput.classList.add('border-green-500');
                    loginBtn.disabled = false;
                    loginBtn.classList.remove('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                    loginBtn.classList.add('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
                }
                
                // Update URL
                updateBrowserURL();
                
                showToast('Previous session restored');
            }
        }

        // Update user menu based on login state
        function updateUserMenu() {
            if (state.userLoggedIn) {
                userMenuButton.innerHTML = '<i class="fas fa-user-circle"></i><span>My Account</span>';
                userDropdown.classList.add('show');
            } else {
                userMenuButton.innerHTML = '<i class="fas fa-user-circle"></i><span>Login</span>';
                userDropdown.classList.remove('show');
            }
        }

        // Render packages for selected game
        function renderPackages() {
            if (!state.selectedGame) return;
            
            packagesContainer.innerHTML = '';
            
            const gamePackages = packages[state.selectedGame] || [];
            
            gamePackages.forEach(pkg => {
                const convertedPrice = convertPrice(pkg.price);
                const formattedPrice = formatCurrency(convertedPrice);
                
                const packageEl = document.createElement('div');
                packageEl.className = 'package bg-white rounded-xl p-4 cursor-pointer border border-gray-200 transition-all duration-300 shadow-sm relative card-hover';
                packageEl.setAttribute('data-variation-id', pkg.code);
                packageEl.innerHTML = `
                    <div class="text-center font-bold text-gray-800">${pkg.label}</div>
                    <div class="text-center text-premium font-bold text-lg mt-2">${formattedPrice} ${exchangeRates[state.currentCurrency].symbol}</div>
                    <div class="text-center text-xs text-gray-500 mt-1">${pkg.diamonds} Diamonds</div>
                    ${pkg.price >= 500 ? `<div class="premium-badge">BEST</div>` : ''}
                `;
                
                packageEl.addEventListener('click', () => {
                    // Remove active class from all packages
                    document.querySelectorAll('.package').forEach(p => {
                        p.classList.remove('ring-2', 'ring-premium', 'border-premium', 'bg-premium/5');
                    });
                    
                    // Add active class to selected package
                    packageEl.classList.add('ring-2', 'ring-premium', 'border-premium', 'bg-premium/5');
                    
                    // Update state
                    state.selectedPackage = pkg;
                    state.variationId = pkg.code;
                    
                    // Update UI
                    selectedPackageEl.textContent = pkg.label;
                    totalAmountEl.textContent = formattedPrice;
                    
                    // Update URL
                    updateBrowserURL();
                    
                    // Save to localStorage
                    saveStateToLocalStorage();
                    
                    // Enable checkout if all conditions are met
                    updateCheckoutButton();
                });
                
                // If this package was previously selected, highlight it
                if (state.variationId === pkg.code) {
                    packageEl.classList.add('ring-2', 'ring-premium', 'border-premium', 'bg-premium/5');
                    selectedPackageEl.textContent = pkg.label;
                    totalAmountEl.textContent = formattedPrice;
                }
                
                packagesContainer.appendChild(packageEl);
            });
        }

        // Update payment method selection
        function updatePaymentMethod() {
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', () => {
                    // Remove active class from all methods
                    document.querySelectorAll('.payment-method').forEach(m => {
                        m.classList.remove('border-primary', 'ring-2', 'ring-primary');
                    });
                    
                    // Add active class to selected method
                    method.classList.add('border-primary', 'ring-2', 'ring-primary');
                    
                    // Update state
                    state.paymentMethod = method.getAttribute('data-payment');
                    state.paymentId = state.paymentMethod;
                    
                    // Update confirmation modal
                    confirmPaymentMethod.textContent = method.querySelector('div').textContent;
                    
                    // Update URL
                    updateBrowserURL();
                    
                    // Save to localStorage
                    saveStateToLocalStorage();
                    
                    // Enable checkout if all conditions are met
                    updateCheckoutButton();
                });
            });
            
            // If a payment method was previously selected, highlight it
            if (state.paymentId) {
                document.querySelectorAll('.payment-method').forEach(method => {
                    if (method.getAttribute('data-payment') === state.paymentId) {
                        method.classList.add('border-primary', 'ring-2', 'ring-primary');
                        confirmPaymentMethod.textContent = method.querySelector('div').textContent;
                    }
                });
            }
        }

        // Update checkout button state
        function updateCheckoutButton() {
            if (state.loggedIn && state.selectedPackage && state.paymentMethod) {
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                checkoutBtn.classList.add('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
            } else {
                checkoutBtn.disabled = true;
                checkoutBtn.classList.add('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                checkoutBtn.classList.remove('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
            }
        }

        // Validate player ID
        function validatePlayerId(id) {
            return /^\d{3,16}$/.test(id);
        }

        // Show game-specific login form
        function showGameLoginForm(game) {
            // Hide all login forms first
            document.querySelectorAll('.game-login').forEach(form => {
                form.classList.add('hidden');
            });
            
            // Show the appropriate form
            if (game === 'freefire') {
                document.getElementById('freefireLogin').classList.remove('hidden');
            } else if (game === 'mlbb') {
                document.getElementById('mlbbLogin').classList.remove('hidden');
            } else if (['cod', 'pubg', 'valorant', 'lol', 'fortnite'].includes(game)) {
                document.getElementById('codeItemLogin').classList.remove('hidden');
            } else if (game === 'genshin') {
                document.getElementById('ingameItemLogin').classList.remove('hidden');
            }
        }

        // Simulate API call
        function simulateApiCall() {
            apiLoading.classList.remove('hidden');
            
            setTimeout(() => {
                // Simulate API call to Garena
                fetch('https://dd.garena.com/js/', {
                    method: 'GET',
                    mode: 'no-cors'
                })
                .then(() => {
                    console.log('API call to Garena successful');
                    showToast('Security verification completed');
                })
                .catch(error => {
                    console.log('API simulation completed');
                })
                .finally(() => {
                    setTimeout(() => {
                        apiLoading.classList.add('hidden');
                    }, 500);
                });
            }, 2500);
        }

        // Initialize event listeners
        function initEventListeners() {
            // User menu toggle
            userMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });
            
            // Mobile menu button
            mobileMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                mobileDropdown.classList.toggle('show');
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
                
                if (!mobileMenuButton.contains(e.target) && !mobileDropdown.contains(e.target)) {
                    mobileDropdown.classList.remove('show');
                }
            });
            
            // Logout button
            logoutButton.addEventListener('click', () => {
                state.userLoggedIn = false;
                updateUserMenu();
                showToast('Logged out successfully');
                saveStateToLocalStorage();
            });
            
            // Mobile logout button
            mobileLogoutButton.addEventListener('click', () => {
                state.userLoggedIn = false;
                updateUserMenu();
                mobileDropdown.classList.remove('show');
                showToast('Logged out successfully');
                saveStateToLocalStorage();
            });
            
            // Bottom navigation items
            bottomNavItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    bottomNavItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    
                    // Close mobile dropdown if open
                    mobileDropdown.classList.remove('show');
                    
                    // Scroll to top if Home is clicked
                    if (item.querySelector('span').textContent === 'Home') {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                    
                    showToast(`${item.querySelector('span').textContent} page`);
                });
            });
            
            // Currency selection
            currencySelect.addEventListener('change', () => {
                state.currentCurrency = currencySelect.value;
                currencySymbolEl.textContent = state.currentCurrency;
                
                // Update displayed prices
                if (state.selectedPackage) {
                    const convertedPrice = convertPrice(state.selectedPackage.price);
                    totalAmountEl.textContent = formatCurrency(convertedPrice);
                }
                
                // Re-render packages with new currency
                renderPackages();
                
                // Save to localStorage
                saveStateToLocalStorage();
                
                showToast(`Currency changed to ${state.currentCurrency}`);
            });

            // Game selection
            gameCards.forEach(card => {
                card.addEventListener('click', () => {
                    const game = card.dataset.game;
                    const productId = card.dataset.productId;
                    
                    // Remove active class from all game cards
                    document.querySelectorAll('.game-card').forEach(c => {
                        c.classList.remove('border-2', 'border-primary', 'shadow-lg');
                        c.classList.add('border', 'border-gray-200', 'shadow-sm');
                    });
                    
                    // Add active class to selected game card
                    card.classList.remove('border', 'border-gray-200', 'shadow-sm');
                    card.classList.add('border-2', 'border-primary', 'shadow-lg');
                    
                    // Update state
                    state.selectedGame = game;
                    state.productId = productId;
                    state.selectedPackage = null;
                    state.loggedIn = false;
                    state.paymentMethod = null;
                    state.variationId = null;
                    state.paymentId = null;
                    
                    // Update panel game name
                    panelGameName.textContent = card.querySelector('div').textContent;
                    
                    // Reset UI
                    selectedPackageEl.textContent = 'None';
                    const convertedPrice = convertPrice(0);
                    totalAmountEl.textContent = formatCurrency(convertedPrice);
                    
                    // Remove active classes
                    document.querySelectorAll('.package').forEach(p => {
                        p.classList.remove('ring-2', 'ring-premium', 'border-premium', 'bg-premium/5');
                    });
                    
                    document.querySelectorAll('.payment-method').forEach(m => {
                        m.classList.remove('border-primary', 'ring-2', 'ring-primary');
                    });
                    
                    // Show game-specific login form
                    showGameLoginForm(game);
                    
                    // Show panel and scroll to it
                    panel.scrollIntoView({ behavior: 'smooth' });
                    
                    // Render packages
                    renderPackages();
                    
                    // Reset login button
                    loginBtn.disabled = true;
                    loginBtn.textContent = 'Verify';
                    loginBtn.classList.remove('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
                    loginBtn.classList.add('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                    
                    // Enable player input
                    playerInput.disabled = false;
                    playerInput.value = '';
                    playerInput.classList.remove('border-green-500', 'border-red-500');
                    
                    // Update URL
                    updateBrowserURL();
                    
                    // Save to localStorage
                    saveStateToLocalStorage();
                    
                    showToast(`Selected: ${card.querySelector('div').textContent}`);
                });
            });
            
            // Player ID input (for Free Fire)
            playerInput.addEventListener('input', () => {
                const id = playerInput.value.trim();
                state.playerId = id;
                
                // Validate ID
                if (validatePlayerId(id)) {
                    playerInput.classList.remove('border-red-500');
                    playerInput.classList.add('border-green-500');
                    loginBtn.disabled = false;
                    loginBtn.classList.remove('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                    loginBtn.classList.add('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
                } else {
                    playerInput.classList.remove('border-green-500');
                    playerInput.classList.add('border-red-500');
                    loginBtn.disabled = true;
                    loginBtn.classList.add('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                    loginBtn.classList.remove('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
                }
                
                // Save to localStorage
                saveStateToLocalStorage();
            });
            
            // MLBB User ID input
            document.getElementById('mlbbUserId').addEventListener('input', function() {
                validateMlbbForm();
            });
            
            // MLBB Server ID input
            document.getElementById('mlbbServerId').addEventListener('input', function() {
                validateMlbbForm();
            });
            
            // Login button (Free Fire)
            loginBtn.addEventListener('click', () => {
                if (!validatePlayerId(state.playerId)) return;
                
                state.loggedIn = true;
                state.userLoggedIn = true;
                showToast(`Verified Free Fire UID: ${state.playerId}`);
                
                // Update UI
                loginBtn.textContent = 'Verified';
                loginBtn.classList.remove('bg-gradient-to-r', 'from-primary', 'to-premium');
                loginBtn.classList.add('bg-gradient-to-r', 'from-green-500', 'to-emerald-600');
                loginBtn.disabled = true;
                playerInput.disabled = true;
                
                // Update user menu
                updateUserMenu();
                
                // Save to localStorage
                saveStateToLocalStorage();
                
                // Enable checkout if package is selected
                updateCheckoutButton();
            });
            
            // MLBB Login button
            mlbbLoginBtn.addEventListener('click', function() {
                const userId = document.getElementById('mlbbUserId').value;
                const serverId = document.getElementById('mlbbServerId').value;
                
                state.loggedIn = true;
                state.userLoggedIn = true;
                state.playerId = `${userId}-${serverId}`;
                showToast(`Verified MLBB Account: ${userId} (Server: ${serverId})`);
                
                // Update UI
                mlbbLoginBtn.textContent = 'Verified';
                mlbbLoginBtn.classList.remove('bg-gradient-to-r', 'from-primary', 'to-premium');
                mlbbLoginBtn.classList.add('bg-gradient-to-r', 'from-green-500', 'to-emerald-600');
                mlbbLoginBtn.disabled = true;
                document.getElementById('mlbbUserId').disabled = true;
                document.getElementById('mlbbServerId').disabled = true;
                
                // Update user menu
                updateUserMenu();
                
                // Save to localStorage
                saveStateToLocalStorage();
                
                // Enable checkout if package is selected
                updateCheckoutButton();
            });
            
            // Login tabs
            document.querySelectorAll('.login-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Update active tab
                    document.querySelectorAll('.login-tab').forEach(t => {
                        t.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // Show corresponding form
                    if (tabName === 'email') {
                        document.getElementById('emailLoginForm').classList.remove('hidden');
                        document.getElementById('phoneLoginForm').classlessList.add('hidden');
                    } else {
                        document.getElementById('emailLoginForm').classList.add('hidden');
                        document.getElementById('phoneLoginForm').classList.remove('hidden');
                    }
                });
            });
            
            // Checkout button
            checkoutBtn.addEventListener('click', () => {
                if (!state.loggedIn || !state.selectedPackage || !state.paymentMethod) return;
                
                // Update confirmation modal
                confirmGame.textContent = state.selectedGame.charAt(0).toUpperCase() + state.selectedGame.slice(1);
                confirmPlayerId.textContent = state.playerId;
                confirmPackage.textContent = state.selectedPackage.label;
                
                const convertedPrice = convertPrice(state.selectedPackage.price);
                confirmAmount.textContent = `${formatCurrency(convertedPrice)} ${state.currentCurrency}`;
                confirmPaymentMethod.textContent = document.querySelector(`[data-payment="${state.paymentMethod}"]`).querySelector('div').textContent;
                
                // Show modal
                orderModal.classList.remove('hidden');
            });
            
            // Order modal buttons
            document.getElementById('cancelOrder').addEventListener('click', () => {
                orderModal.classList.add('hidden');
            });
            
            document.getElementById('confirmOrder').addEventListener('click', () => {
                // Simulate API call to create order
                showToast('Processing payment...');
                
                // Simulate payment processing
                setTimeout(() => {
                    orderModal.classList.add('hidden');
                    
                    // Generate random order ID
                    const randomId = 'ORD-' + Math.floor(10000 + Math.random() * 90000);
                    orderId.textContent = randomId;
                    successPlayerId.textContent = state.playerId;
                    
                    // Update success amount
                    const convertedPrice = convertPrice(state.selectedPackage.price);
                    successAmount.textContent = `${formatCurrency(convertedPrice)} ${state.currentCurrency}`;
                    
                    // Show success modal
                    successModal.classList.remove('hidden');
                }, 2000);
            });
            
            // Success modal button
            document.getElementById('closeSuccess').addEventListener('click', () => {
                successModal.classList.add('hidden');
                
                // Reset state
                state.selectedPackage = null;
                state.loggedIn = false;
                state.paymentMethod = null;
                state.variationId = null;
                state.paymentId = null;
                
                // Reset UI
                selectedPackageEl.textContent = 'None';
                const convertedPrice = convertPrice(0);
                totalAmountEl.textContent = formatCurrency(convertedPrice);
                
                // Remove active classes
                document.querySelectorAll('.package').forEach(p => {
                    p.classList.remove('ring-2', 'ring-premium', 'border-premium', 'bg-premium/5');
                });
                
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('border-primary', 'ring-2', 'ring-primary');
                });
                
                // Update URL
                updateBrowserURL();
                
                // Save to localStorage
                saveStateToLocalStorage();
                
                updateCheckoutButton();
            });
        }

        // Validate MLBB form
        function validateMlbbForm() {
            const userId = document.getElementById('mlbbUserId').value;
            const serverId = document.getElementById('mlbbServerId').value;
            
            if (userId && serverId) {
                mlbbLoginBtn.disabled = false;
                mlbbLoginBtn.classList.remove('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                mlbbLoginBtn.classList.add('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
            } else {
                mlbbLoginBtn.disabled = true;
                mlbbLoginBtn.classList.add('bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-700');
                mlbbLoginBtn.classList.remove('bg-gradient-to-r', 'from-primary', 'to-premium', 'text-white', 'hover:opacity-90');
            }
        }

        // Initialize the application
        function init() {
            initSlider();
            initEventListeners();
            updatePaymentMethod();
            
            // Load saved state from localStorage
            loadStateFromLocalStorage();
            
            // Render packages for default selected game
            renderPackages();
            
            // Simulate API call after 2 seconds
            setTimeout(() => {
                simulateApiCall();
            }, 2000);
            
            showToast('Welcome to GameTopUp Premium!');
        }

        // Start the app
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
