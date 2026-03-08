<?php
/**
 * GSP2 Settings Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSP2_Settings {
    
    private static $settings_keys = array(
        'btc_address',
        'support_email',
        'upgrade_link',
        'convert_link',
        'save_link',
        'dashboard_link',
        'hero_image',
        'laptop_image',
        'about_image',
        'generate_image',
        'security_image',
        'privacy_image',
        'dark_mode_default',
        'footer_text',
        'copyright_text'
    );
    
    public static function get($key, $default = '') {
        return get_option('gsp2_' . $key, $default);
    }
    
    public static function set($key, $value) {
        return update_option('gsp2_' . $key, $value);
    }
    
    public static function get_all() {
        $settings = array();
        foreach (self::$settings_keys as $key) {
            $settings[$key] = self::get($key);
        }
        return $settings;
    }
    
    public static function save_all($data) {
        foreach (self::$settings_keys as $key) {
            if (isset($data[$key])) {
                self::set($key, sanitize_text_field($data[$key]));
            }
        }
    }
}
