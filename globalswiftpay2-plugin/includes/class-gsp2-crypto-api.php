<?php
/**
 * GSP2 Cryptocurrency API Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSP2_Crypto_API {
    
    private $api_url = 'https://api.coingecko.com/api/v3';
    private $cache_key = 'gsp2_crypto_prices';
    private $cache_duration = 300; // 5 minutes
    
    public function get_top_cryptocurrencies($limit = 8) {
        $cached = get_transient($this->cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $url = add_query_arg(array(
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => $limit,
            'page' => 1,
            'sparkline' => 'false'
        ), $this->api_url . '/coins/markets');
        
        $response = wp_remote_get($url, array(
            'timeout' => 10
        ));
        
        if (is_wp_error($response)) {
            return $this->get_fallback_data();
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (empty($data) || !is_array($data)) {
            return $this->get_fallback_data();
        }
        
        $formatted = array();
        foreach ($data as $coin) {
            $formatted[] = array(
                'name' => $coin['name'],
                'symbol' => strtoupper($coin['symbol']),
                'price' => $this->format_price($coin['current_price']),
                'price_raw' => $coin['current_price'],
                'market_cap' => $this->format_large_number($coin['market_cap']),
                'market_cap_raw' => $coin['market_cap'],
                'volume' => $this->format_large_number($coin['total_volume']),
                'volume_raw' => $coin['total_volume'],
                'supply' => $this->format_large_number($coin['circulating_supply']),
                'supply_raw' => $coin['circulating_supply'],
                'change_24h' => round($coin['price_change_percentage_24h'], 2),
                'image' => $coin['image']
            );
        }
        
        set_transient($this->cache_key, $formatted, $this->cache_duration);
        
        return $formatted;
    }
    
    private function format_price($price) {
        if ($price >= 1000) {
            return '$' . number_format($price, 2);
        } elseif ($price >= 1) {
            return '$' . number_format($price, 2);
        } else {
            return '$' . number_format($price, 6);
        }
    }
    
    private function format_large_number($number) {
        if ($number >= 1000000000000) {
            return '$' . number_format($number / 1000000000000, 2) . 'T';
        } elseif ($number >= 1000000000) {
            return '$' . number_format($number / 1000000000, 2) . 'B';
        } elseif ($number >= 1000000) {
            return '$' . number_format($number / 1000000, 2) . 'M';
        } else {
            return '$' . number_format($number, 0);
        }
    }
    
    private function get_fallback_data() {
        return array(
            array(
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'price' => '$43,250.00',
                'price_raw' => 43250,
                'market_cap' => '$847.2B',
                'market_cap_raw' => 847200000000,
                'volume' => '$18.5B',
                'volume_raw' => 18500000000,
                'supply' => '19.6M',
                'supply_raw' => 19600000,
                'change_24h' => 2.5,
                'image' => ''
            ),
            array(
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'price' => '$2,280.00',
                'price_raw' => 2280,
                'market_cap' => '$274.1B',
                'market_cap_raw' => 274100000000,
                'volume' => '$8.2B',
                'volume_raw' => 8200000000,
                'supply' => '120.2M',
                'supply_raw' => 120200000,
                'change_24h' => 1.8,
                'image' => ''
            ),
            array(
                'name' => 'Tether',
                'symbol' => 'USDT',
                'price' => '$1.00',
                'price_raw' => 1,
                'market_cap' => '$95.8B',
                'market_cap_raw' => 95800000000,
                'volume' => '$42.1B',
                'volume_raw' => 42100000000,
                'supply' => '95.8B',
                'supply_raw' => 95800000000,
                'change_24h' => 0.01,
                'image' => ''
            ),
            array(
                'name' => 'BNB',
                'symbol' => 'BNB',
                'price' => '$312.50',
                'price_raw' => 312.5,
                'market_cap' => '$48.1B',
                'market_cap_raw' => 48100000000,
                'volume' => '$680M',
                'volume_raw' => 680000000,
                'supply' => '153.9M',
                'supply_raw' => 153900000,
                'change_24h' => -0.5,
                'image' => ''
            ),
            array(
                'name' => 'Solana',
                'symbol' => 'SOL',
                'price' => '$98.20',
                'price_raw' => 98.2,
                'market_cap' => '$42.5B',
                'market_cap_raw' => 42500000000,
                'volume' => '$1.8B',
                'volume_raw' => 1800000000,
                'supply' => '433.2M',
                'supply_raw' => 433200000,
                'change_24h' => 3.2,
                'image' => ''
            ),
            array(
                'name' => 'XRP',
                'symbol' => 'XRP',
                'price' => '$0.62',
                'price_raw' => 0.62,
                'market_cap' => '$33.8B',
                'market_cap_raw' => 33800000000,
                'volume' => '$1.2B',
                'volume_raw' => 1200000000,
                'supply' => '54.5B',
                'supply_raw' => 54500000000,
                'change_24h' => 1.1,
                'image' => ''
            ),
            array(
                'name' => 'USD Coin',
                'symbol' => 'USDC',
                'price' => '$1.00',
                'price_raw' => 1,
                'market_cap' => '$24.2B',
                'market_cap_raw' => 24200000000,
                'volume' => '$3.8B',
                'volume_raw' => 3800000000,
                'supply' => '24.2B',
                'supply_raw' => 24200000000,
                'change_24h' => 0.02,
                'image' => ''
            ),
            array(
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'price' => '$0.58',
                'price_raw' => 0.58,
                'market_cap' => '$20.4B',
                'market_cap_raw' => 20400000000,
                'volume' => '$420M',
                'volume_raw' => 420000000,
                'supply' => '35.2B',
                'supply_raw' => 35200000000,
                'change_24h' => 0.8,
                'image' => ''
            )
        );
    }
}
