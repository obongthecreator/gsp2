<?php
/**
 * Database handler for GlobalSwiftPay Dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSP_Database {
    private static $settings_table_checked = null;
    private static $settings_table_checking = false;
    
    /**
     * Create all necessary database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Transactions table
        $table_transactions = $wpdb->prefix . 'gsp_transactions';
        $sql_transactions = "CREATE TABLE $table_transactions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            type varchar(50) NOT NULL,
            amount decimal(20,8) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            details longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY type (type),
            KEY status (status)
        ) $charset_collate;";
        
        // User balances table
        $table_balances = $wpdb->prefix . 'gsp_user_balances';
        $sql_balances = "CREATE TABLE $table_balances (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL UNIQUE,
            wallet_balance decimal(20,8) DEFAULT 0,
            savings_balance decimal(20,8) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        // Deposit requests table
        $table_deposits = $wpdb->prefix . 'gsp_deposits';
        $sql_deposits = "CREATE TABLE $table_deposits (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            amount decimal(20,8) NOT NULL,
            receipt_path varchar(500),
            sender_name varchar(255),
            status varchar(20) DEFAULT 'pending',
            admin_notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Withdrawal requests table
        $table_withdrawals = $wpdb->prefix . 'gsp_withdrawals';
        $sql_withdrawals = "CREATE TABLE $table_withdrawals (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            amount decimal(20,8) NOT NULL,
            withdrawal_method varchar(50) NOT NULL,
            details longtext,
            status varchar(20) DEFAULT 'pending',
            admin_notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Transfer requests table
        $table_transfers = $wpdb->prefix . 'gsp_transfers';
        $sql_transfers = "CREATE TABLE $table_transfers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            from_user_id bigint(20) NOT NULL,
            to_user_id bigint(20) NOT NULL,
            amount decimal(20,8) NOT NULL,
            token_code varchar(100) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            admin_notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY from_user_id (from_user_id),
            KEY to_user_id (to_user_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Conversion requests table
        $table_conversions = $wpdb->prefix . 'gsp_conversions';
        $sql_conversions = "CREATE TABLE $table_conversions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            email varchar(255) NOT NULL,
            conversion_type varchar(50) NOT NULL,
            amount decimal(20,8) NOT NULL,
            destination_address text,
            bank_details longtext,
            security_phrase varchar(255),
            status varchar(20) DEFAULT 'pending',
            admin_notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY conversion_type (conversion_type),
            KEY status (status)
        ) $charset_collate;";
        
        // Settings table
        $table_settings = $wpdb->prefix . 'gsp_settings';
        $sql_settings = "CREATE TABLE $table_settings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            setting_key varchar(100) NOT NULL UNIQUE,
            setting_value longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY setting_key (setting_key)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_transactions);
        dbDelta($sql_balances);
        dbDelta($sql_deposits);
        dbDelta($sql_withdrawals);
        dbDelta($sql_transfers);
        dbDelta($sql_conversions);
        dbDelta($sql_settings);
        
        // Insert default settings
        self::insert_default_settings();
    }
    
    /**
     * Insert default settings
     * Note: Financial details are left empty for admin to configure securely
     */
    private static function insert_default_settings() {
        $defaults = self::get_default_settings();
        
        foreach ($defaults as $key => $value) {
            // Only insert if setting doesn't already exist
            if (self::get_setting($key) === null) {
                self::set_setting($key, $value);
            }
        }
    }
    
    /**
     * Get a setting value
     */
    public static function get_setting($key) {
        global $wpdb;
        $table = self::get_settings_table_name();
        $value = null;

        if ($table && self::ensure_settings_table()) {
            $wpdb->last_error = '';
            $value = $wpdb->get_var($wpdb->prepare(
                "SELECT setting_value FROM `$table` WHERE setting_key = %s",
                $key
            ));

            if (!empty($wpdb->last_error)) {
                $value = null;
            }
        }

        if ($value === null) {
            $value = self::get_setting_option($key, null);
        }

        if ($value === null) {
            $defaults = self::get_default_settings();
            if (array_key_exists($key, $defaults)) {
                return $defaults[$key];
            }
        }

        return $value;
    }
    
    /**
     * Set a setting value
     */
    public static function set_setting($key, $value) {
        global $wpdb;
        $table = self::get_settings_table_name();
        $db_success = false;

        if ($table && self::ensure_settings_table()) {
            $wpdb->last_error = '';
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM `$table` WHERE setting_key = %s",
                $key
            ));

            if (empty($wpdb->last_error)) {
                if ($existing) {
                    $updated = $wpdb->update(
                        $table,
                        array('setting_value' => $value),
                        array('setting_key' => $key)
                    );
                    $db_success = ($updated !== false && empty($wpdb->last_error));
                } else {
                    $inserted = $wpdb->insert(
                        $table,
                        array(
                            'setting_key' => $key,
                            'setting_value' => $value
                        )
                    );
                    $db_success = ($inserted !== false && empty($wpdb->last_error));
                }
            }
        }

        $option_success = self::update_setting_option($key, $value);

        return ($db_success || $option_success);
    }
    
    /**
     * Get all settings
     */
    public static function get_all_settings() {
        global $wpdb;
        $table = self::get_settings_table_name();
        $settings = array();

        if ($table && self::ensure_settings_table()) {
            $wpdb->last_error = '';
            $results = $wpdb->get_results("SELECT setting_key, setting_value FROM `$table`", ARRAY_A);
            if (empty($wpdb->last_error) && !empty($results)) {
                foreach ($results as $row) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            }
        }

        $option_settings = self::get_option_settings();
        foreach ($option_settings as $key => $value) {
            if (!array_key_exists($key, $settings) || $settings[$key] === null) {
                $settings[$key] = $value;
            }
        }

        $defaults = self::get_default_settings();
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $settings) || $settings[$key] === null) {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    private static function ensure_settings_table() {
        global $wpdb;

        if (self::$settings_table_checked !== null) {
            return self::$settings_table_checked;
        }

        $table_name = self::get_settings_table_name();
        if ($table_name === '') {
            self::$settings_table_checked = false;
            return false;
        }

        if (self::$settings_table_checking) {
            return false;
        }

        self::$settings_table_checking = true;
        $table_exists = $wpdb->get_var($wpdb->prepare(
            'SHOW TABLES LIKE %s',
            $wpdb->esc_like($table_name)
        ));

        if ($table_exists !== $table_name) {
            self::create_tables();
            $table_exists = $wpdb->get_var($wpdb->prepare(
                'SHOW TABLES LIKE %s',
                $wpdb->esc_like($table_name)
            ));
            if ($table_exists !== $table_name) {
                self::$settings_table_checked = false;
                self::$settings_table_checking = false;
                return false;
            }
        }

        $columns = $wpdb->get_col(sprintf('SHOW COLUMNS FROM `%s`', $table_name));
        if (empty($columns)) {
            self::create_tables();
            $columns = $wpdb->get_col(sprintf('SHOW COLUMNS FROM `%s`', $table_name));
            if (empty($columns)) {
                self::$settings_table_checked = false;
                self::$settings_table_checking = false;
                return false;
            }
        }

        $columns_lower = array_map('strtolower', $columns);
        $required_columns = array('setting_key', 'setting_value', 'created_at', 'updated_at');
        if (array_diff($required_columns, $columns_lower)) {
            self::create_tables();
            $columns = $wpdb->get_col(sprintf('SHOW COLUMNS FROM `%s`', $table_name));
            $columns_lower = array_map('strtolower', $columns);
            if (array_diff($required_columns, $columns_lower)) {
                self::$settings_table_checked = false;
                self::$settings_table_checking = false;
                return false;
            }
        }

        self::$settings_table_checked = true;
        self::$settings_table_checking = false;
        return true;
    }

    private static function get_default_settings() {
        return array(
            'account_number' => '',
            'account_name' => '',
            'bank_name' => '',
            'btc_address' => '',
            'usdt_address' => '',
            'company_email' => '',
            'logout_redirect_url' => 'https://globalswiftpay2.com/gsp2-login/'
        );
    }

    private static function get_settings_table_name() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gsp_settings';
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table_name)) {
            return '';
        }
        return $table_name;
    }

    private static function get_setting_option_key($key) {
        return 'gsp_setting_' . sanitize_key($key);
    }

    private static function get_setting_option($key, $default = null) {
        return get_option(self::get_setting_option_key($key), $default);
    }

    private static function update_setting_option($key, $value) {
        $option_key = self::get_setting_option_key($key);
        $existing = get_option($option_key, null);
        if ($existing === $value) {
            return true;
        }
        $updated = update_option($option_key, $value);
        if ($updated) {
            return true;
        }
        return ($existing === $value);
    }

    private static function get_option_settings() {
        $options = wp_load_alloptions();
        if (!is_array($options)) {
            return array();
        }

        $settings = array();
        $prefix = 'gsp_setting_';
        foreach ($options as $option_key => $option_value) {
            if (strpos($option_key, $prefix) === 0) {
                $setting_key = substr($option_key, strlen($prefix));
                $settings[$setting_key] = $option_value;
            }
        }

        return $settings;
    }
}
