<?php

namespace App\Models;

use App\Support\Database;

class Order
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        // Generate unique order number
        $data['order_number'] = $this->generateOrderNumber();
        
        return $this->db->insert('orders', $data);
    }

    public function getById($id)
    {
        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds, u.email as user_email, u.name as user_name
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getByOrderNumber($orderNumber)
    {
        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds, u.email as user_email, u.name as user_name
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.order_number = :order_number";
        return $this->db->fetch($sql, ['order_number' => $orderNumber]);
    }

    public function getByUserId($userId, $limit = 50, $offset = 0)
    {
        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                WHERE o.user_id = :user_id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";
        return $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function getAll($limit = 100, $offset = 0, $filters = [])
    {
        $whereClause = "1=1";
        $params = ['limit' => $limit, 'offset' => $offset];

        if (!empty($filters['status'])) {
            $whereClause .= " AND o.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $whereClause .= " AND o.payment_status = :payment_status";
            $params['payment_status'] = $filters['payment_status'];
        }

        if (!empty($filters['game_id'])) {
            $whereClause .= " AND o.game_id = :game_id";
            $params['game_id'] = $filters['game_id'];
        }

        if (!empty($filters['date_from'])) {
            $whereClause .= " AND DATE(o.created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereClause .= " AND DATE(o.created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds, u.email as user_email, u.name as user_name
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE {$whereClause}
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        return $this->db->fetchAll($sql, $params);
    }

    public function update($id, $data)
    {
        return $this->db->update('orders', $data, 'id = :id', ['id' => $id]);
    }

    public function updateStatus($id, $status)
    {
        return $this->db->update('orders', ['status' => $status], 'id = :id', ['id' => $id]);
    }

    public function updatePaymentStatus($id, $paymentStatus)
    {
        return $this->db->update('orders', ['payment_status' => $paymentStatus], 'id = :id', ['id' => $id]);
    }

    public function addNote($orderId, $note, $isInternal = false)
    {
        $data = [
            'order_id' => $orderId,
            'note' => $note,
            'is_internal' => $isInternal ? 1 : 0
        ];
        return $this->db->insert('order_notes', $data);
    }

    public function getNotes($orderId)
    {
        $sql = "SELECT * FROM order_notes WHERE order_id = :order_id ORDER BY created_at ASC";
        return $this->db->fetchAll($sql, ['order_id' => $orderId]);
    }

    public function getOrderStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_orders,
                    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as total_revenue,
                    AVG(amount) as average_order_value
                FROM orders";
        return $this->db->fetch($sql);
    }

    public function getGameOrderStats()
    {
        $sql = "SELECT 
                    g.name as game_name,
                    COUNT(o.id) as total_orders,
                    SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN o.payment_status = 'paid' THEN o.amount ELSE 0 END) as total_revenue
                FROM games g
                LEFT JOIN orders o ON g.id = o.game_id
                WHERE g.is_active = 1
                GROUP BY g.id, g.name
                ORDER BY total_revenue DESC";
        return $this->db->fetchAll($sql);
    }

    public function getRecentOrders($limit = 10)
    {
        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds, u.email as user_email, u.name as user_name
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    public function searchOrders($query, $limit = 50)
    {
        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds, u.email as user_email, u.name as user_name
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.order_number LIKE :query 
                   OR o.player_id LIKE :query 
                   OR o.server_id LIKE :query
                   OR u.email LIKE :query
                   OR u.name LIKE :query
                ORDER BY o.created_at DESC
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['query' => "%{$query}%", 'limit' => $limit]);
    }

    private function generateOrderNumber()
    {
        $prefix = 'ORD';
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        return $prefix . '-' . $timestamp . '-' . $random;
    }

    public function getOrdersByDateRange($startDate, $endDate)
    {
        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds, u.email as user_email, u.name as user_name
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
                ORDER BY o.created_at DESC";
        return $this->db->fetchAll($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    public function getOrdersByGame($gameId, $limit = 100)
    {
        $sql = "SELECT o.*, g.name as game_name, g.slug as game_slug, gp.name as package_name, 
                       gp.diamonds, u.email as user_email, u.name as user_name
                FROM orders o
                JOIN games g ON o.game_id = g.id
                JOIN game_packages gp ON o.package_id = gp.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.game_id = :game_id
                ORDER BY o.created_at DESC
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['game_id' => $gameId, 'limit' => $limit]);
    }
}