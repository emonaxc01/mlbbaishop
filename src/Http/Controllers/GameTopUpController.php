<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Currency;
use App\Models\Order;
use App\Support\Database;

class GameTopUpController
{
    private $gameModel;
    private $currencyModel;
    private $orderModel;

    public function __construct()
    {
        $this->gameModel = new Game();
        $this->currencyModel = new Currency();
        $this->orderModel = new Order();
    }

    // Get all active games
    public function getGames()
    {
        try {
            $games = $this->gameModel->getAllActive();
            
            return [
                'success' => true,
                'data' => $games,
                'message' => 'Games retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve games: ' . $e->getMessage()
            ];
        }
    }

    // Get game by slug
    public function getGame($slug)
    {
        try {
            $game = $this->gameModel->getBySlug($slug);
            
            if (!$game) {
                return [
                    'success' => false,
                    'message' => 'Game not found'
                ];
            }
            
            return [
                'success' => true,
                'data' => $game,
                'message' => 'Game retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve game: ' . $e->getMessage()
            ];
        }
    }

    // Get packages for a specific game
    public function getGamePackages($gameSlug)
    {
        try {
            $game = $this->gameModel->getBySlug($gameSlug);
            if (!$game) {
                return [
                    'success' => false,
                    'message' => 'Game not found'
                ];
            }

            $packages = $this->gameModel->getPackages($game['id']);
            
            return [
                'success' => true,
                'data' => [
                    'game' => $game,
                    'packages' => $packages
                ],
                'message' => 'Game packages retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve game packages: ' . $e->getMessage()
            ];
        }
    }

    // Get all currencies
    public function getCurrencies()
    {
        try {
            $currencies = $this->currencyModel->getAllActive();
            
            return [
                'success' => true,
                'data' => $currencies,
                'message' => 'Currencies retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve currencies: ' . $e->getMessage()
            ];
        }
    }

    // Get exchange rates
    public function getExchangeRates()
    {
        try {
            $rates = $this->currencyModel->getExchangeRates();
            
            return [
                'success' => true,
                'data' => $rates,
                'message' => 'Exchange rates retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve exchange rates: ' . $e->getMessage()
            ];
        }
    }

    // Convert price between currencies
    public function convertPrice()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['price']) || !isset($input['from_currency']) || !isset($input['to_currency'])) {
                return [
                    'success' => false,
                    'message' => 'Missing required parameters: price, from_currency, to_currency'
                ];
            }

            $price = (float) $input['price'];
            $fromCurrency = $input['from_currency'];
            $toCurrency = $input['to_currency'];

            $convertedPrice = $this->currencyModel->convertPrice($price, $fromCurrency, $toCurrency);
            $formattedPrice = $this->currencyModel->formatPrice($convertedPrice, $toCurrency);
            
            return [
                'success' => true,
                'data' => [
                    'original_price' => $price,
                    'original_currency' => $fromCurrency,
                    'converted_price' => $convertedPrice,
                    'target_currency' => $toCurrency,
                    'formatted_price' => $formattedPrice
                ],
                'message' => 'Price converted successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to convert price: ' . $e->getMessage()
            ];
        }
    }

    // Create a new order
    public function createOrder()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = ['game_id', 'package_id', 'player_id', 'amount', 'currency', 'payment_method'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    return [
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ];
                }
            }

            // Validate game and package
            $game = $this->gameModel->getById($input['game_id']);
            if (!$game) {
                return [
                    'success' => false,
                    'message' => 'Invalid game ID'
                ];
            }

            $package = $this->gameModel->getPackageById($input['package_id']);
            if (!$package) {
                return [
                    'success' => false,
                    'message' => 'Invalid package ID'
                ];
            }

            // Validate currency
            $currency = $this->currencyModel->getByCode($input['currency']);
            if (!$currency) {
                return [
                    'success' => false,
                    'message' => 'Invalid currency'
                ];
            }

            // Prepare order data
            $orderData = [
                'user_id' => $input['user_id'] ?? null,
                'game_id' => $input['game_id'],
                'package_id' => $input['package_id'],
                'player_id' => $input['player_id'],
                'server_id' => $input['server_id'] ?? null,
                'amount' => $input['amount'],
                'currency' => $input['currency'],
                'payment_method' => $input['payment_method'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $input['notes'] ?? null
            ];

            // Create order
            $orderId = $this->orderModel->create($orderData);
            
            if ($orderId) {
                // Get the created order
                $order = $this->orderModel->getById($orderId);
                
                return [
                    'success' => true,
                    'data' => [
                        'order_id' => $orderId,
                        'order_number' => $order['order_number'],
                        'order' => $order
                    ],
                    'message' => 'Order created successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create order'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ];
        }
    }

    // Get order by ID or order number
    public function getOrder($identifier)
    {
        try {
            // Try to get by order number first
            $order = $this->orderModel->getByOrderNumber($identifier);
            
            if (!$order) {
                // Try to get by ID
                $order = $this->orderModel->getById($identifier);
            }
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Order not found'
                ];
            }
            
            // Get order notes
            $notes = $this->orderModel->getNotes($order['id']);
            $order['notes'] = $notes;
            
            return [
                'success' => true,
                'data' => $order,
                'message' => 'Order retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve order: ' . $e->getMessage()
            ];
        }
    }

    // Get user orders
    public function getUserOrders($userId)
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $limit = $input['limit'] ?? 50;
            $offset = $input['offset'] ?? 0;
            
            $orders = $this->orderModel->getByUserId($userId, $limit, $offset);
            
            return [
                'success' => true,
                'data' => $orders,
                'message' => 'User orders retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve user orders: ' . $e->getMessage()
            ];
        }
    }

    // Update order status
    public function updateOrderStatus($orderId)
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['status'])) {
                return [
                    'success' => false,
                    'message' => 'Missing status parameter'
                ];
            }

            $validStatuses = ['pending', 'processing', 'completed', 'cancelled', 'failed'];
            if (!in_array($input['status'], $validStatuses)) {
                return [
                    'success' => false,
                    'message' => 'Invalid status value'
                ];
            }

            $success = $this->orderModel->updateStatus($orderId, $input['status']);
            
            if ($success) {
                // Add note about status change
                $this->orderModel->addNote($orderId, "Order status changed to: {$input['status']}", true);
                
                return [
                    'success' => true,
                    'message' => 'Order status updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update order status'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ];
        }
    }

    // Add note to order
    public function addOrderNote($orderId)
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['note']) || empty($input['note'])) {
                return [
                    'success' => false,
                    'message' => 'Missing note content'
                ];
            }

            $isInternal = $input['is_internal'] ?? false;
            $noteId = $this->orderModel->addNote($orderId, $input['note'], $isInternal);
            
            if ($noteId) {
                return [
                    'success' => true,
                    'data' => ['note_id' => $noteId],
                    'message' => 'Note added successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to add note'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to add note: ' . $e->getMessage()
            ];
        }
    }

    // Get order statistics
    public function getOrderStats()
    {
        try {
            $stats = $this->orderModel->getOrderStats();
            $gameStats = $this->orderModel->getGameOrderStats();
            
            return [
                'success' => true,
                'data' => [
                    'overall_stats' => $stats,
                    'game_stats' => $gameStats
                ],
                'message' => 'Order statistics retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve order statistics: ' . $e->getMessage()
            ];
        }
    }

    // Search orders
    public function searchOrders()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['query']) || empty($input['query'])) {
                return [
                    'success' => false,
                    'message' => 'Missing search query'
                ];
            }

            $query = $input['query'];
            $limit = $input['limit'] ?? 50;
            
            $orders = $this->orderModel->searchOrders($query, $limit);
            
            return [
                'success' => true,
                'data' => $orders,
                'message' => 'Orders search completed successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to search orders: ' . $e->getMessage()
            ];
        }
    }

    // Get recent orders
    public function getRecentOrders()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $limit = $input['limit'] ?? 10;
            
            $orders = $this->orderModel->getRecentOrders($limit);
            
            return [
                'success' => true,
                'data' => $orders,
                'message' => 'Recent orders retrieved successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve recent orders: ' . $e->getMessage()
            ];
        }
    }
}