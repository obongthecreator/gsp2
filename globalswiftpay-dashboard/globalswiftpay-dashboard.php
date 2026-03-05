<?php
/**
 * Plugin Name: GlobalSwiftPay Dashboard
 * Plugin URI: https://globalswiftpay2.com
 * Description: A professional investment dashboard plugin with glass morphism design for GlobalSwiftPay
 * Version: 1.0.8
 * Author: GlobalSwiftPay
 * Author URI: https://globalswiftpay2.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: globalswiftpay-dashboard
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GSP_VERSION', '1.0.8');
define('GSP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GSP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GSP_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once GSP_PLUGIN_DIR . 'includes/class-gsp-database.php';
require_once GSP_PLUGIN_DIR . 'includes/class-gsp-user.php';
require_once GSP_PLUGIN_DIR . 'includes/class-gsp-transactions.php';
require_once GSP_PLUGIN_DIR . 'includes/class-gsp-email.php';
require_once GSP_PLUGIN_DIR . 'includes/class-gsp-ajax.php';
require_once GSP_PLUGIN_DIR . 'admin/class-gsp-admin.php';
require_once GSP_PLUGIN_DIR . 'public/class-gsp-public.php';

/**
 * Main Plugin Class
 */
class GlobalSwiftPay_Dashboard {
    
    private static $instance = null;
    private static $asset_versions = array();
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize plugin
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Register shortcode
        add_shortcode('gsp_dashboard', array($this, 'render_dashboard_shortcode'));
        
        // Register admin frontend shortcode
        add_shortcode('gsp_admin_dashboard', array($this, 'render_admin_frontend_shortcode'));
        
        // Initialize admin
        if (is_admin()) {
            new GSP_Admin();
            // Auto-import RM user data once
            add_action('admin_init', array($this, 'maybe_import_rm_user_data'));
        }
        
        // Initialize AJAX handlers
        new GSP_Ajax();
        
        // Handle logout redirect
        add_action('wp_logout', array($this, 'redirect_after_logout'));
    }
    
    public function activate() {
        // Create database tables
        GSP_Database::create_tables();
        
        // Create dashboard page
        $this->create_dashboard_page();
        
        // Add capabilities
        $this->add_capabilities();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function init() {
        // Load text domain
        load_plugin_textdomain('globalswiftpay-dashboard', false, dirname(GSP_PLUGIN_BASENAME) . '/languages');
    }

    private function get_asset_version($relative_path) {
        if (isset(self::$asset_versions[$relative_path])) {
            return self::$asset_versions[$relative_path];
        }

        $file_path = GSP_PLUGIN_DIR . wp_normalize_path($relative_path);
        $version = file_exists($file_path) ? filemtime($file_path) : false;
        if ($version === false) {
            $version = GSP_VERSION;
        }
        self::$asset_versions[$relative_path] = $version;

        return $version;
    }
    
    public function enqueue_public_assets() {
        $post = get_post();
        $post_content = $post ? $post->post_content : '';

        if (is_page('gsp-dashboard') || has_shortcode($post_content, 'gsp_dashboard')) {
            $style_version = $this->get_asset_version('assets/css/dashboard.css');
            $forms_version = $this->get_asset_version('assets/css/forms.css');
            $script_version = $this->get_asset_version('assets/js/dashboard.js');

            // Add preconnect for Iconify CDN for faster loading
            add_action('wp_head', function() {
                echo '<link rel="preconnect" href="https://code.iconify.design" crossorigin>' . "\n";
                echo '<link rel="dns-prefetch" href="https://code.iconify.design">' . "\n";
                echo '<link rel="preconnect" href="https://api.iconify.design" crossorigin>' . "\n";
            }, 1);

            // Enqueue Iconify for Solar icons
            wp_enqueue_script('iconify', 'https://code.iconify.design/3/3.1.0/iconify.min.js', array(), '3.1.0', false);
            
            // Enqueue styles
            wp_enqueue_style('gsp-dashboard-style', GSP_PLUGIN_URL . 'assets/css/dashboard.css', array(), $style_version);
            wp_enqueue_style('gsp-forms-style', GSP_PLUGIN_URL . 'assets/css/forms.css', array(), $forms_version);
            
            // Enqueue scripts
            wp_enqueue_script('gsp-dashboard-script', GSP_PLUGIN_URL . 'assets/js/dashboard.js', array('jquery', 'iconify'), $script_version, true);
            
            // Localize script
            wp_localize_script('gsp-dashboard-script', 'gsp_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gsp_nonce'),
                'home_url' => home_url('/')
            ));
        }
        
        // Admin Frontend Dashboard
        if (has_shortcode($post_content, 'gsp_admin_dashboard')) {
            $style_version = $this->get_asset_version('assets/css/dashboard.css');
            $forms_version = $this->get_asset_version('assets/css/forms.css');
            $admin_frontend_style_version = $this->get_asset_version('assets/css/admin-frontend.css');
            $admin_frontend_script_version = $this->get_asset_version('assets/js/admin-frontend.js');

            // Add preconnect for Iconify CDN for faster loading
            add_action('wp_head', function() {
                echo '<link rel="preconnect" href="https://code.iconify.design" crossorigin>' . "\n";
                echo '<link rel="dns-prefetch" href="https://code.iconify.design">' . "\n";
                echo '<link rel="preconnect" href="https://api.iconify.design" crossorigin>' . "\n";
            }, 1);
            
            // Enqueue Iconify for Solar icons - load in head for immediate availability
            wp_enqueue_script('iconify', 'https://code.iconify.design/3/3.1.0/iconify.min.js', array(), '3.1.0', false);
            
            // Enqueue styles
            wp_enqueue_style('gsp-dashboard-style', GSP_PLUGIN_URL . 'assets/css/dashboard.css', array(), $style_version);
            wp_enqueue_style('gsp-forms-style', GSP_PLUGIN_URL . 'assets/css/forms.css', array(), $forms_version);
            wp_enqueue_style('gsp-admin-frontend-style', GSP_PLUGIN_URL . 'assets/css/admin-frontend.css', array('gsp-dashboard-style'), $admin_frontend_style_version);
            
            // Enqueue scripts
            wp_enqueue_script('gsp-admin-frontend-script', GSP_PLUGIN_URL . 'assets/js/admin-frontend.js', array('jquery', 'iconify'), $admin_frontend_script_version, true);
            
            // Localize script with admin nonce
            wp_localize_script('gsp-admin-frontend-script', 'gsp_admin_frontend_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gsp_admin_nonce')
            ));
        }
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'globalswiftpay') !== false) {
            $admin_style_version = $this->get_asset_version('assets/css/admin.css');
            $admin_script_version = $this->get_asset_version('assets/js/admin.js');

            wp_enqueue_style('gsp-admin-style', GSP_PLUGIN_URL . 'assets/css/admin.css', array(), $admin_style_version);
            wp_enqueue_script('gsp-admin-script', GSP_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), $admin_script_version, true);
            
            wp_localize_script('gsp-admin-script', 'gsp_admin_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gsp_admin_nonce')
            ));
        }
    }
    
    private function create_dashboard_page() {
        $page_exists = get_page_by_path('gsp-dashboard');
        
        if (!$page_exists) {
            wp_insert_post(array(
                'post_title' => 'Dashboard',
                'post_name' => 'gsp-dashboard',
                'post_content' => '[gsp_dashboard]',
                'post_status' => 'publish',
                'post_type' => 'page'
            ));
        }
    }
    
    private function add_capabilities() {
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('manage_gsp_dashboard');
        }
    }
    
    public function render_dashboard_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<div class="gsp-login-required"><p>' . __('Please log in to access your dashboard.', 'globalswiftpay-dashboard') . '</p><a href="' . esc_url(home_url('/gsp2-login/')) . '" class="gsp-btn gsp-btn-primary">' . __('Login', 'globalswiftpay-dashboard') . '</a></div>';
        }
        
        ob_start();
        include GSP_PLUGIN_DIR . 'templates/dashboard.php';
        return ob_get_clean();
    }
    
    /**
     * Render frontend admin dashboard shortcode - redirects to WP Admin
     */
    public function render_admin_frontend_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<div class="gsp-login-required"><p>' . __('Please log in to access the admin dashboard.', 'globalswiftpay-dashboard') . '</p><a href="' . esc_url(home_url('/gsp2-login/')) . '" class="gsp-btn gsp-btn-primary">' . __('Login', 'globalswiftpay-dashboard') . '</a></div>';
        }
        
        if (!current_user_can('manage_options')) {
            return '<div class="gsp-access-denied"><p>' . __('You do not have permission to access this page.', 'globalswiftpay-dashboard') . '</p></div>';
        }
        
        // Redirect to WordPress admin area
        wp_redirect(admin_url('admin.php?page=globalswiftpay'));
        exit;
    }
    
    public function redirect_after_logout() {
        // Check if a redirect_to was specified in the logout URL
        $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
        
        if (!empty($redirect_to)) {
            // Use the redirect_to parameter that was passed (e.g. from wp_logout_url())
            $logout_url = esc_url_raw($redirect_to);
        } else {
            // Fall back to the configured logout redirect URL
            $logout_url = GSP_Database::get_setting('logout_redirect_url');
        }
        
        if (empty($logout_url) || $logout_url === '/gsp2-login/') {
            $logout_url = home_url('/gsp2-login/');
        }
        
        // Validate the URL
        $logout_url = esc_url_raw($logout_url);
        if (empty($logout_url)) {
            $logout_url = home_url('/');
        }
        
        wp_redirect($logout_url);
        exit;
    }

    /**
     * Auto-import Registration Magic user data (runs once)
     */
    public function maybe_import_rm_user_data() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Check if already imported
        if (get_option('gsp_rm_data_imported')) {
            return;
        }
        
        // Look for the RM XML file
        $xml_paths = array(
            ABSPATH . 'RMagic (1).xml',
            ABSPATH . 'RMagic.xml',
            WP_CONTENT_DIR . '/uploads/RMagic (1).xml',
            WP_CONTENT_DIR . '/uploads/RMagic.xml',
        );
        
        // Check plugin and repo directories
        $plugin_dir = dirname(__FILE__);
        $repo_dir = dirname($plugin_dir);
        $xml_paths[] = $repo_dir . '/RMagic (1).xml';
        $xml_paths[] = $repo_dir . '/RMagic.xml';
        
        $xml_file = null;
        foreach ($xml_paths as $path) {
            if (file_exists($path)) {
                $xml_file = $path;
                break;
            }
        }
        
        if (!$xml_file) {
            return; // No XML file found, skip silently
        }
        
        // Parse the XML
        if (function_exists('libxml_disable_entity_loader')) {
            $prev = libxml_disable_entity_loader(true);
        }
        $xml = simplexml_load_file($xml_file, 'SimpleXMLElement', LIBXML_NONET);
        if (function_exists('libxml_disable_entity_loader') && isset($prev)) {
            libxml_disable_entity_loader($prev);
        }
        
        if (!$xml || !$xml->FORMS) {
            return;
        }
        
        $forms = $xml->FORMS;
        
        // Build field_id -> type mapping
        $field_map = array();
        foreach ($forms->FIELDS as $field) {
            $fid = (string)$field->field_id;
            $type = strtolower((string)$field->field_type);
            $label = strtolower((string)$field->field_label);
            $field_map[$fid] = array('type' => $type, 'label' => $label);
        }
        
        // Identify field IDs
        $username_fids = $email_fids = $fname_fids = $lname_fids = $country_fids = $mobile_fids = $phone_fids = array();
        foreach ($field_map as $fid => $info) {
            if ($info['type'] === 'username' || $info['label'] === 'gsp account number') $username_fids[] = $fid;
            elseif ($info['type'] === 'email') $email_fids[] = $fid;
            elseif ($info['type'] === 'fname' || $info['label'] === 'first name') $fname_fids[] = $fid;
            elseif ($info['type'] === 'lname' || $info['label'] === 'last name') $lname_fids[] = $fid;
            elseif ($info['type'] === 'country') $country_fids[] = $fid;
            elseif ($info['type'] === 'mobile' || $info['label'] === 'mobile number') $mobile_fids[] = $fid;
        }
        
        // Find unknown field IDs that may be phone numbers
        $all_sfids = array();
        foreach ($forms->SUBMISSION_FIELDS as $sf) {
            $all_sfids[(string)$sf->field_id] = true;
        }
        foreach ($all_sfids as $fid => $unused) {
            if (!isset($field_map[$fid])) {
                $phone_fids[] = $fid;
            }
        }
        
        // Build submission_id -> email
        $sub_emails = array();
        foreach ($forms->SUBMISSIONS as $sub) {
            $sub_emails[(string)$sub->submission_id] = (string)$sub->user_email;
        }
        
        // Build submission data
        $submissions = array();
        foreach ($forms->SUBMISSION_FIELDS as $sf) {
            $sid = (string)$sf->submission_id;
            $fid = (string)$sf->field_id;
            if (!isset($submissions[$sid])) $submissions[$sid] = array();
            $submissions[$sid][$fid] = (string)$sf->value;
        }
        
        // Process each submission
        $updated = 0;
        foreach ($submissions as $sid => $fields) {
            $email = '';
            foreach ($email_fids as $fid) {
                if (!empty($fields[$fid])) { $email = $fields[$fid]; break; }
            }
            if (empty($email) && isset($sub_emails[$sid])) $email = $sub_emails[$sid];
            if (empty($email)) continue;
            
            // Find WordPress user
            $wp_user = get_user_by('email', $email);
            if (!$wp_user) $wp_user = get_user_by('email', strtolower($email));
            if (!$wp_user) {
                $gsp_account = '';
                foreach ($username_fids as $fid) {
                    if (!empty($fields[$fid])) { $gsp_account = $fields[$fid]; break; }
                }
                if ($gsp_account) $wp_user = get_user_by('login', $gsp_account);
            }
            if (!$wp_user) continue;
            
            // Get field values
            $gsp_account = '';
            foreach ($username_fids as $fid) {
                if (!empty($fields[$fid])) { $gsp_account = $fields[$fid]; break; }
            }
            
            $first_name = '';
            foreach ($fname_fids as $fid) {
                if (!empty($fields[$fid])) { $first_name = $fields[$fid]; break; }
            }
            
            $last_name = '';
            foreach ($lname_fids as $fid) {
                if (!empty($fields[$fid])) { $last_name = $fields[$fid]; break; }
            }
            
            $country = '';
            foreach ($country_fids as $fid) {
                if (!empty($fields[$fid])) {
                    // Remove country code brackets e.g. "Turkey[TR]" -> "Turkey"
                    $country = preg_replace('/\[[A-Z]{2,3}\]$/', '', $fields[$fid]);
                    $country = trim($country);
                    break;
                }
            }
            
            $mobile = '';
            foreach ($mobile_fids as $fid) {
                if (!empty($fields[$fid])) { $mobile = $fields[$fid]; break; }
            }
            if (empty($mobile)) {
                foreach ($phone_fids as $fid) {
                    // Only use values that look like phone numbers (digits, spaces, hyphens, plus sign)
                    if (!empty($fields[$fid]) && preg_match('/^[\+\d\s\-\(\)]+$/', $fields[$fid])) {
                        $mobile = $fields[$fid]; break;
                    }
                }
            }
            
            // Update meta (only if empty)
            $did_update = false;
            if ($gsp_account && !get_user_meta($wp_user->ID, 'gsp_account_number', true)) {
                update_user_meta($wp_user->ID, 'gsp_account_number', sanitize_text_field($gsp_account));
                $did_update = true;
            }
            if ($mobile && !get_user_meta($wp_user->ID, 'gsp_mobile', true)) {
                update_user_meta($wp_user->ID, 'gsp_mobile', sanitize_text_field($mobile));
                $did_update = true;
            }
            if ($country && !get_user_meta($wp_user->ID, 'gsp_country', true)) {
                update_user_meta($wp_user->ID, 'gsp_country', sanitize_text_field($country));
                $did_update = true;
            }
            if ($first_name && !get_user_meta($wp_user->ID, 'first_name', true)) {
                update_user_meta($wp_user->ID, 'first_name', sanitize_text_field($first_name));
                $did_update = true;
            }
            if ($last_name && !get_user_meta($wp_user->ID, 'last_name', true)) {
                update_user_meta($wp_user->ID, 'last_name', sanitize_text_field($last_name));
                $did_update = true;
            }
            
            if ($did_update) $updated++;
        }
        
        // Mark as imported so it doesn't run again
        update_option('gsp_rm_data_imported', time());
    }
}

// Initialize the plugin
GlobalSwiftPay_Dashboard::get_instance();
