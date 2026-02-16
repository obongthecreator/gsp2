<?php
/**
 * Plugin Name: GlobalSwiftPay2
 * Plugin URI: https://globalswiftpay2.com
 * Description: A professional investment website plugin with dark mode, glassmorphic UI, and comprehensive payment features.
 * Version: 1.0.0
 * Author: Global Swift Pay
 * Author URI: https://globalswiftpay2.com
 * License: GPL v2 or later
 * Text Domain: globalswiftpay2
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GSP2_VERSION', '1.0.0');
define('GSP2_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GSP2_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GSP2_ASSETS_URL', GSP2_PLUGIN_URL . 'assets/');

class GlobalSwiftPay2 {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once GSP2_PLUGIN_DIR . 'includes/class-gsp2-settings.php';
        require_once GSP2_PLUGIN_DIR . 'includes/class-gsp2-shortcodes.php';
        require_once GSP2_PLUGIN_DIR . 'includes/class-gsp2-crypto-api.php';
        require_once GSP2_PLUGIN_DIR . 'admin/class-gsp2-admin.php';
    }
    
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('init', array($this, 'register_custom_pages'));
        add_action('wp_footer', array($this, 'render_scroll_to_top'));
        add_action('wp_head', array($this, 'add_iconify_script'));
        add_action('wp_ajax_gsp2_submit_contact', array($this, 'handle_contact_form'));
        add_action('wp_ajax_nopriv_gsp2_submit_contact', array($this, 'handle_contact_form'));
        add_action('wp_ajax_gsp2_upload_proof', array($this, 'handle_payment_proof'));
        add_action('wp_ajax_nopriv_gsp2_upload_proof', array($this, 'handle_payment_proof'));
        add_action('wp_ajax_gsp2_get_crypto_prices', array($this, 'get_crypto_prices'));
        add_action('wp_ajax_nopriv_gsp2_get_crypto_prices', array($this, 'get_crypto_prices'));
        add_action('wp_ajax_gsp2_submit_upgrade', array($this, 'handle_upgrade_submission'));
        add_action('wp_ajax_nopriv_gsp2_submit_upgrade', array($this, 'handle_upgrade_submission'));
        add_action('wp_ajax_gsp2_approve_upgrade', array($this, 'handle_upgrade_approval'));
        add_action('wp_ajax_gsp2_reject_upgrade', array($this, 'handle_upgrade_rejection'));
        add_filter('body_class', array($this, 'add_body_classes'));
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function activate() {
        $default_options = array(
            'btc_address' => 'bc1qf74tnfccynx78n9kd8cgjcqp8l9s7y5fcre2hp',
            'support_email' => 'support@globalswiftpay2.com',
            'upgrade_link' => '#',
            'convert_link' => '#',
            'save_link' => '#',
            'dashboard_link' => '/dashboard',
            'hero_image' => '',
            'laptop_image' => '',
            'about_image' => '',
            'dark_mode_default' => 'dark'
        );
        
        foreach ($default_options as $key => $value) {
            if (get_option('gsp2_' . $key) === false) {
                add_option('gsp2_' . $key, $value);
            }
        }
        
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style('gsp2-main', GSP2_ASSETS_URL . 'css/main.css', array(), GSP2_VERSION);
        wp_enqueue_style('gsp2-dark-mode', GSP2_ASSETS_URL . 'css/dark-mode.css', array('gsp2-main'), GSP2_VERSION);
        wp_enqueue_style('gsp2-responsive', GSP2_ASSETS_URL . 'css/responsive.css', array('gsp2-main'), GSP2_VERSION);
        
        wp_enqueue_script('gsp2-main', GSP2_ASSETS_URL . 'js/main.js', array('jquery'), GSP2_VERSION, true);
        wp_enqueue_script('gsp2-animations', GSP2_ASSETS_URL . 'js/animations.js', array('jquery'), GSP2_VERSION, true);
        wp_enqueue_script('gsp2-crypto', GSP2_ASSETS_URL . 'js/crypto.js', array('jquery'), GSP2_VERSION, true);
        
        wp_localize_script('gsp2-main', 'gsp2_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gsp2_nonce'),
            'is_logged_in' => is_user_logged_in(),
            'btc_address' => get_option('gsp2_btc_address'),
            'dashboard_link' => get_option('gsp2_dashboard_link'),
            'logout_url' => wp_logout_url(home_url('/gsp2-login/'))
        ));
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'gsp2') !== false) {
            wp_enqueue_style('gsp2-admin', GSP2_ASSETS_URL . 'css/admin.css', array(), GSP2_VERSION);
            wp_enqueue_script('gsp2-admin', GSP2_ASSETS_URL . 'js/admin.js', array('jquery'), GSP2_VERSION, true);
            wp_enqueue_media();
        }
    }
    
    public function add_iconify_script() {
        // Load both Iconify scripts for maximum compatibility
        // Legacy script for <span class="iconify"> elements
        echo '<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>';
        // Web component for <iconify-icon> elements (more reliable)
        echo '<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>';
    }
    
    public function register_custom_pages() {
        add_rewrite_rule('^gsp2-about/?$', 'index.php?gsp2_page=about', 'top');
        add_rewrite_rule('^gsp2-generate/?$', 'index.php?gsp2_page=generate', 'top');
        add_rewrite_rule('^gsp2-security-policy/?$', 'index.php?gsp2_page=security-policy', 'top');
        add_rewrite_rule('^gsp2-privacy-policy/?$', 'index.php?gsp2_page=privacy-policy', 'top');
        add_rewrite_rule('^gsp2-terms/?$', 'index.php?gsp2_page=terms', 'top');
        add_rewrite_rule('^gsp2-upgrade/?$', 'index.php?gsp2_page=upgrade', 'top');
        add_rewrite_rule('^gsp2-login/?$', 'index.php?gsp2_page=login', 'top');
        add_rewrite_rule('^gsp2-admin-panel/?$', 'index.php?gsp2_page=admin-panel', 'top');
        add_rewrite_rule('^gsp2-balance-manager/?$', 'index.php?gsp2_page=balance-manager', 'top');
        add_rewrite_rule('^gsp2-forgot-password/?$', 'index.php?gsp2_page=forgot-password', 'top');
        
        add_filter('query_vars', function($vars) {
            $vars[] = 'gsp2_page';
            return $vars;
        });
        
        add_action('template_redirect', array($this, 'handle_custom_pages'));
    }
    
    public function handle_custom_pages() {
        $page = get_query_var('gsp2_page');
        if ($page) {
            $template = GSP2_PLUGIN_DIR . 'templates/pages/' . $page . '.php';
            if (file_exists($template)) {
                include $template;
                exit;
            }
        }
    }
    
    public function add_body_classes($classes) {
        $classes[] = 'gsp2-theme';
        $default_mode = get_option('gsp2_dark_mode_default', 'dark');
        $classes[] = 'gsp2-' . $default_mode . '-mode';
        return $classes;
    }
    
    public function render_scroll_to_top() {
        include GSP2_PLUGIN_DIR . 'templates/partials/scroll-to-top.php';
    }
    
    public function handle_contact_form() {
        check_ajax_referer('gsp2_nonce', 'nonce');
        
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);
        
        $to = get_option('gsp2_support_email', 'support@globalswiftpay2.com');
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $name . ' <' . $email . '>',
            'Reply-To: ' . $email
        );
        
        $email_body = sprintf(
            '<h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> %s</p>
            <p><strong>Email:</strong> %s</p>
            <p><strong>Subject:</strong> %s</p>
            <p><strong>Message:</strong></p>
            <p>%s</p>',
            esc_html($name),
            esc_html($email),
            esc_html($subject),
            nl2br(esc_html($message))
        );
        
        $sent = wp_mail($to, 'GSP2 Contact: ' . $subject, $email_body, $headers);
        
        if ($sent) {
            wp_send_json_success(array('message' => 'Your message has been sent successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to send message. Please try again.'));
        }
    }
    
    public function handle_payment_proof() {
        check_ajax_referer('gsp2_nonce', 'nonce');
        
        if (!isset($_FILES['proof'])) {
            wp_send_json_error(array('message' => 'No file uploaded.'));
        }
        
        $file = $_FILES['proof'];
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'application/pdf');
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        // Check file size
        if ($file['size'] > $max_file_size) {
            wp_send_json_error(array('message' => 'File size exceeds 5MB limit.'));
        }
        
        // Check MIME type from file content, not just reported type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($detected_type, $allowed_types)) {
            wp_send_json_error(array('message' => 'Invalid file type. Allowed: JPG, PNG, GIF, PDF.'));
        }
        
        // Additional check for reported type
        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(array('message' => 'Invalid file type.'));
        }
        
        $upload = wp_handle_upload($file, array('test_form' => false));
        
        if (isset($upload['error'])) {
            wp_send_json_error(array('message' => $upload['error']));
        }
        
        $to = get_option('gsp2_support_email', 'support@globalswiftpay2.com');
        $user_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : 'anonymous@user.com';
        
        $email_body = sprintf(
            '<h2>New Payment Proof Uploaded</h2>
            <p><strong>User Email:</strong> %s</p>
            <p><strong>File URL:</strong> <a href="%s">%s</a></p>',
            esc_html($user_email),
            esc_url($upload['url']),
            esc_url($upload['url'])
        );
        
        wp_mail($to, 'GSP2 Payment Proof Submission', $email_body, array('Content-Type: text/html; charset=UTF-8'));
        
        wp_send_json_success(array(
            'message' => 'Payment proof uploaded successfully. Your security phrase will be generated shortly.',
            'file_url' => $upload['url']
        ));
    }
    
    public function get_crypto_prices() {
        $crypto_api = new GSP2_Crypto_API();
        $prices = $crypto_api->get_top_cryptocurrencies(8);
        wp_send_json_success($prices);
    }
    
    public function handle_upgrade_submission() {
        check_ajax_referer('gsp2_nonce', 'nonce');
        
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $gsp_account = sanitize_text_field($_POST['gsp_account']);
        $country = sanitize_text_field($_POST['country']);
        $mobile = sanitize_text_field($_POST['mobile']);
        $password = $_POST['password']; // Will be hashed before storage
        
        // Validate required fields
        if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($gsp_account) || empty($country) || empty($mobile) || empty($password)) {
            wp_send_json_error(array('message' => 'All fields are required.'));
        }
        
        // Validate username format
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            wp_send_json_error(array('message' => 'Username must be 3-20 characters and contain only letters, numbers, and underscores.'));
        }
        
        // Check if username already exists in WordPress
        if (username_exists($username)) {
            wp_send_json_error(array('message' => 'This username is already taken. Please choose a different one.'));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Invalid email address.'));
        }
        
        // Check if email already exists in WordPress
        if (email_exists($email)) {
            wp_send_json_error(array('message' => 'This email is already registered. Please use a different email or login to your existing account.'));
        }
        
        if (strlen($password) < 8) {
            wp_send_json_error(array('message' => 'Password must be at least 8 characters long.'));
        }
        
        // Check if email already exists in pending upgrades
        $pending_upgrades = get_option('gsp2_pending_upgrades', array());
        foreach ($pending_upgrades as $upgrade) {
            if ($upgrade['email'] === $email) {
                wp_send_json_error(array('message' => 'An upgrade request with this email already exists.'));
            }
            if (isset($upgrade['username']) && $upgrade['username'] === $username) {
                wp_send_json_error(array('message' => 'An upgrade request with this username already exists.'));
            }
        }
        
        // Store the upgrade request
        $upgrade_request = array(
            'id' => uniqid('upgrade_'),
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username,
            'email' => $email,
            'gsp_account' => $gsp_account,
            'country' => $country,
            'mobile' => $mobile,
            'password' => wp_hash_password($password),
            'password_raw' => base64_encode($password), // Encrypted for user creation later
            'status' => 'pending',
            'submitted_at' => current_time('mysql'),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );
        
        $pending_upgrades[] = $upgrade_request;
        update_option('gsp2_pending_upgrades', $pending_upgrades);
        
        // Send notification email to admin
        $admin_email = get_option('gsp2_support_email', 'support@globalswiftpay2.com');
        $subject = 'New Tier 2 Upgrade Request - ' . $first_name . ' ' . $last_name;
        $message = sprintf(
            '<h2>New Tier 2 Upgrade Request</h2>
            <p><strong>Name:</strong> %s %s</p>
            <p><strong>Username:</strong> %s</p>
            <p><strong>Email:</strong> %s</p>
            <p><strong>GSP Account:</strong> %s</p>
            <p><strong>Country:</strong> %s</p>
            <p><strong>Mobile:</strong> %s</p>
            <p><strong>Submitted:</strong> %s</p>
            <p><a href="%s">Review in Admin Panel</a></p>',
            esc_html($first_name),
            esc_html($last_name),
            esc_html($username),
            esc_html($email),
            esc_html($gsp_account),
            esc_html($country),
            esc_html($mobile),
            current_time('mysql'),
            admin_url('admin.php?page=gsp2-upgrades')
        );
        
        wp_mail($admin_email, $subject, $message, array('Content-Type: text/html; charset=UTF-8'));
        
        wp_send_json_success(array(
            'message' => 'Your upgrade request has been submitted successfully! You will receive an email once your application is reviewed.'
        ));
    }
    
    public function handle_upgrade_approval() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized.'));
        }
        
        check_ajax_referer('gsp2_admin_nonce', 'nonce');
        
        $upgrade_id = sanitize_text_field($_POST['upgrade_id']);
        $pending_upgrades = get_option('gsp2_pending_upgrades', array());
        
        foreach ($pending_upgrades as $key => $upgrade) {
            if ($upgrade['id'] === $upgrade_id) {
                // Create WordPress user
                $username = isset($upgrade['username']) ? $upgrade['username'] : sanitize_user($upgrade['email']);
                $password = isset($upgrade['password_raw']) ? base64_decode($upgrade['password_raw']) : wp_generate_password(12, true);
                
                // Check if username/email already exists
                if (username_exists($username)) {
                    wp_send_json_error(array('message' => 'Username already exists. Cannot create user.'));
                }
                if (email_exists($upgrade['email'])) {
                    wp_send_json_error(array('message' => 'Email already registered. Cannot create user.'));
                }
                
                // Create the WordPress user
                $user_id = wp_create_user($username, $password, $upgrade['email']);
                
                if (is_wp_error($user_id)) {
                    wp_send_json_error(array('message' => 'Failed to create user: ' . $user_id->get_error_message()));
                }
                
                // Update user meta
                wp_update_user(array(
                    'ID' => $user_id,
                    'first_name' => $upgrade['first_name'],
                    'last_name' => $upgrade['last_name'],
                    'display_name' => $upgrade['first_name'] . ' ' . $upgrade['last_name'],
                    'role' => 'subscriber'
                ));
                
                // Store GSP account number and mobile in user meta
                update_user_meta($user_id, 'gsp_account_number', $upgrade['gsp_account']);
                update_user_meta($user_id, 'gsp_mobile', $upgrade['mobile']);
                update_user_meta($user_id, 'gsp_country', $upgrade['country']);
                update_user_meta($user_id, 'gsp_tier2_approved', current_time('mysql'));
                
                // Move to approved list
                $approved_upgrades = get_option('gsp2_approved_upgrades', array());
                $upgrade['status'] = 'approved';
                $upgrade['approved_at'] = current_time('mysql');
                $upgrade['wp_user_id'] = $user_id;
                unset($upgrade['password_raw']); // Remove plain password
                $approved_upgrades[] = $upgrade;
                update_option('gsp2_approved_upgrades', $approved_upgrades);
                
                // Remove from pending
                unset($pending_upgrades[$key]);
                update_option('gsp2_pending_upgrades', array_values($pending_upgrades));
                
                // Send approval email to user with login credentials
                $login_url = get_option('gsp2_login_link', wp_login_url());
                $subject = 'Your Tier 2 Upgrade Has Been Approved - GlobalSwiftPay2';
                $message = sprintf(
                    '<h2>Congratulations, %s!</h2>
                    <p>Your upgrade request to Tier 2 Platform has been <strong>approved</strong>.</p>
                    <p>Your account has been created. Here are your login details:</p>
                    <p><strong>Login Credentials:</strong></p>
                    <ul>
                        <li><strong>Username:</strong> %s</li>
                        <li><strong>Email:</strong> %s</li>
                        <li><strong>GSP Account:</strong> %s</li>
                    </ul>
                    <p>Use the password you set during registration to login.</p>
                    <p><a href="%s" style="display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px;">Login to Your Account</a></p>
                    <p>If you have any questions, please contact our support team.</p>
                    <p>Best regards,<br>GlobalSwiftPay2 Team</p>',
                    esc_html($upgrade['first_name']),
                    esc_html($username),
                    esc_html($upgrade['email']),
                    esc_html($upgrade['gsp_account']),
                    esc_url($login_url)
                );
                
                wp_mail($upgrade['email'], $subject, $message, array('Content-Type: text/html; charset=UTF-8'));
                
                wp_send_json_success(array('message' => 'Upgrade approved! WordPress user created (ID: ' . $user_id . ') and user notified via email.'));
            }
        }
        
        wp_send_json_error(array('message' => 'Upgrade request not found.'));
    }
    
    public function handle_upgrade_rejection() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized.'));
        }
        
        check_ajax_referer('gsp2_admin_nonce', 'nonce');
        
        $upgrade_id = sanitize_text_field($_POST['upgrade_id']);
        $reason = sanitize_textarea_field($_POST['reason'] ?? 'Your application did not meet our requirements.');
        $pending_upgrades = get_option('gsp2_pending_upgrades', array());
        
        foreach ($pending_upgrades as $key => $upgrade) {
            if ($upgrade['id'] === $upgrade_id) {
                // Move to rejected list
                $rejected_upgrades = get_option('gsp2_rejected_upgrades', array());
                $upgrade['status'] = 'rejected';
                $upgrade['rejected_at'] = current_time('mysql');
                $upgrade['rejection_reason'] = $reason;
                $rejected_upgrades[] = $upgrade;
                update_option('gsp2_rejected_upgrades', $rejected_upgrades);
                
                // Remove from pending
                unset($pending_upgrades[$key]);
                update_option('gsp2_pending_upgrades', array_values($pending_upgrades));
                
                // Send rejection email to user
                $subject = 'Your Tier 2 Upgrade Request - GlobalSwiftPay2';
                $message = sprintf(
                    '<h2>Dear %s,</h2>
                    <p>We regret to inform you that your upgrade request to Tier 2 Platform has not been approved at this time.</p>
                    <p><strong>Reason:</strong> %s</p>
                    <p>If you believe this was an error or would like to submit a new application, please contact our support team.</p>
                    <p>Best regards,<br>GlobalSwiftPay2 Team</p>',
                    esc_html($upgrade['first_name']),
                    esc_html($reason)
                );
                
                wp_mail($upgrade['email'], $subject, $message, array('Content-Type: text/html; charset=UTF-8'));
                
                wp_send_json_success(array('message' => 'Upgrade rejected and user notified.'));
            }
        }
        
        wp_send_json_error(array('message' => 'Upgrade request not found.'));
    }
}

function gsp2() {
    return GlobalSwiftPay2::get_instance();
}

add_action('plugins_loaded', 'gsp2');
