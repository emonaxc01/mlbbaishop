<?php

namespace App\Models;

use App\Support\Database;

class Currency
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllActive()
    {
        $sql = "SELECT * FROM currencies WHERE is_active = 1 ORDER BY code ASC";
        return $this->db->fetchAll($sql);
    }

    public function getByCode($code)
    {
        $sql = "SELECT * FROM currencies WHERE code = :code AND is_active = 1";
        return $this->db->fetch($sql, ['code' => $code]);
    }

    public function getDefault()
    {
        $sql = "SELECT * FROM currencies WHERE code = 'BDT' AND is_active = 1";
        return $this->db->fetch($sql);
    }

    public function convertPrice($price, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $price;
        }

        $fromRate = $this->getByCode($fromCurrency);
        $toRate = $this->getByCode($toCurrency);

        if (!$fromRate || !$toRate) {
            return $price; // Return original price if conversion fails
        }

        // Convert from base currency (BDT) to target currency
        if ($fromCurrency === 'BDT') {
            return $price * $toRate['exchange_rate'];
        }

        // Convert from source currency to base currency (BDT), then to target currency
        $basePrice = $price / $fromRate['exchange_rate'];
        return $basePrice * $toRate['exchange_rate'];
    }

    public function formatPrice($price, $currencyCode)
    {
        $currency = $this->getByCode($currencyCode);
        if (!$currency) {
            return number_format($price, 2);
        }

        $symbol = $currency['symbol'];
        $formattedPrice = number_format($price, 2);

        // Different currency formatting based on locale
        switch ($currencyCode) {
            case 'USD':
            case 'EUR':
                return $symbol . $formattedPrice;
            case 'BDT':
            case 'INR':
            case 'PKR':
                return $formattedPrice . ' ' . $symbol;
            default:
                return $symbol . ' ' . $formattedPrice;
        }
    }

    public function getExchangeRates()
    {
        $currencies = $this->getAllActive();
        $rates = [];
        
        foreach ($currencies as $currency) {
            $rates[$currency['code']] = [
                'symbol' => $currency['symbol'],
                'rate' => (float) $currency['exchange_rate'],
                'name' => $currency['name']
            ];
        }
        
        return $rates;
    }

    public function updateExchangeRate($code, $newRate)
    {
        return $this->db->update('currencies', 
            ['exchange_rate' => $newRate], 
            'code = :code', 
            ['code' => $code]
        );
    }

    public function create($data)
    {
        return $this->db->insert('currencies', $data);
    }

    public function update($code, $data)
    {
        return $this->db->update('currencies', $data, 'code = :code', ['code' => $code]);
    }

    public function delete($code)
    {
        // Don't allow deletion of BDT (base currency)
        if ($code === 'BDT') {
            throw new \Exception('Cannot delete base currency BDT');
        }
        
        return $this->db->update('currencies', ['is_active' => 0], 'code = :code', ['code' => $code]);
    }

    public function getCurrencyStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_currencies,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_currencies,
                    MIN(exchange_rate) as min_rate,
                    MAX(exchange_rate) as max_rate
                FROM currencies";
        return $this->db->fetch($sql);
    }

    public function validateCurrencyCode($code)
    {
        // Check if currency code is valid (3 letters)
        if (!preg_match('/^[A-Z]{3}$/', $code)) {
            return false;
        }
        
        // Check if currency already exists
        $existing = $this->getByCode($code);
        return empty($existing);
    }

    public function getSupportedCurrencies()
    {
        return [
            'BDT' => 'Bangladeshi Taka',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'INR' => 'Indian Rupee',
            'PKR' => 'Pakistani Rupee',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'AUD' => 'Australian Dollar',
            'CAD' => 'Canadian Dollar',
            'CHF' => 'Swiss Franc'
        ];
    }
}