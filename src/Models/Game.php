<?php

namespace App\Models;

use App\Support\Database;

class Game
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllActive()
    {
        $sql = "SELECT * FROM games WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM games WHERE slug = :slug AND is_active = 1";
        return $this->db->fetch($sql, ['slug' => $slug]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM games WHERE id = :id AND is_active = 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getPackages($gameId)
    {
        $sql = "SELECT * FROM game_packages WHERE game_id = :game_id AND is_active = 1 ORDER BY sort_order ASC, diamonds ASC";
        return $this->db->fetchAll($sql, ['game_id' => $gameId]);
    }

    public function getPackageByCode($gameId, $code)
    {
        $sql = "SELECT * FROM game_packages WHERE game_id = :game_id AND code = :code AND is_active = 1";
        return $this->db->fetch($sql, ['game_id' => $gameId, 'code' => $code]);
    }

    public function getFeaturedPackages($gameId)
    {
        $sql = "SELECT * FROM game_packages WHERE game_id = :game_id AND is_featured = 1 AND is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, ['game_id' => $gameId]);
    }

    public function searchGames($query)
    {
        $sql = "SELECT * FROM games WHERE is_active = 1 AND (name LIKE :query OR description LIKE :query) ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, ['query' => "%{$query}%"]);
    }

    public function getGamesWithPackageCount()
    {
        $sql = "SELECT g.*, COUNT(gp.id) as package_count 
                FROM games g 
                LEFT JOIN game_packages gp ON g.id = gp.game_id AND gp.is_active = 1 
                WHERE g.is_active = 1 
                GROUP BY g.id 
                ORDER BY g.sort_order ASC, g.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function create($data)
    {
        return $this->db->insert('games', $data);
    }

    public function update($id, $data)
    {
        return $this->db->update('games', $data, 'id = :id', ['id' => $id]);
    }

    public function delete($id)
    {
        // Soft delete - just mark as inactive
        return $this->db->update('games', ['is_active' => 0], 'id = :id', ['id' => $id]);
    }

    public function createPackage($data)
    {
        return $this->db->insert('game_packages', $data);
    }

    public function updatePackage($id, $data)
    {
        return $this->db->update('game_packages', $data, 'id = :id', ['id' => $id]);
    }

    public function deletePackage($id)
    {
        // Soft delete - just mark as inactive
        return $this->db->update('game_packages', ['is_active' => 0], 'id = :id', ['id' => $id]);
    }

    public function getPackageById($id)
    {
        $sql = "SELECT gp.*, g.name as game_name, g.slug as game_slug 
                FROM game_packages gp 
                JOIN games g ON gp.game_id = g.id 
                WHERE gp.id = :id AND gp.is_active = 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getAllPackages()
    {
        $sql = "SELECT gp.*, g.name as game_name, g.slug as game_slug 
                FROM game_packages gp 
                JOIN games g ON gp.game_id = g.id 
                WHERE gp.is_active = 1 
                ORDER BY g.sort_order ASC, gp.sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    public function getPackagesByGameSlug($gameSlug)
    {
        $sql = "SELECT gp.*, g.name as game_name, g.slug as game_slug 
                FROM game_packages gp 
                JOIN games g ON gp.game_id = g.id 
                WHERE g.slug = :game_slug AND gp.is_active = 1 AND g.is_active = 1 
                ORDER BY gp.sort_order ASC, gp.diamonds ASC";
        return $this->db->fetchAll($sql, ['game_slug' => $gameSlug]);
    }

    public function getGameStats()
    {
        $sql = "SELECT 
                    COUNT(DISTINCT g.id) as total_games,
                    COUNT(DISTINCT gp.id) as total_packages,
                    SUM(CASE WHEN g.is_active = 1 THEN 1 ELSE 0 END) as active_games,
                    SUM(CASE WHEN gp.is_active = 1 THEN 1 ELSE 0 END) as active_packages
                FROM games g 
                LEFT JOIN game_packages gp ON g.id = gp.game_id";
        return $this->db->fetch($sql);
    }
}