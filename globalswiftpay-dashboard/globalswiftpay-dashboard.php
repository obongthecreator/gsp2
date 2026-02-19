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
}

// Initialize the plugin
GlobalSwiftPay_Dashboard::get_instance();
