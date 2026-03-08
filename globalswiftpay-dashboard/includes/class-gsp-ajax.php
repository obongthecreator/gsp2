<?php
/**
 * AJAX handler for GlobalSwiftPay Dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSP_Ajax {
    
    public function __construct() {
        // User AJAX actions
        add_action('wp_ajax_gsp_submit_deposit', array($this, 'submit_deposit'));
        add_action('wp_ajax_gsp_submit_add_balance', array($this, 'submit_add_balance'));
        add_action('wp_ajax_gsp_submit_withdrawal', array($this, 'submit_withdrawal'));
        add_action('wp_ajax_gsp_submit_transfer', array($this, 'submit_transfer'));
        add_action('wp_ajax_gsp_submit_conversion', array($this, 'submit_conversion'));
        add_action('wp_ajax_gsp_get_transactions', array($this, 'get_transactions'));
        add_action('wp_ajax_gsp_get_balance', array($this, 'get_balance'));
        add_action('wp_ajax_gsp_update_profile', array($this, 'update_profile'));
        
        // Admin AJAX actions
        add_action('wp_ajax_gsp_admin_update_deposit', array($this, 'admin_update_deposit'));
        add_action('wp_ajax_gsp_admin_update_withdrawal', array($this, 'admin_update_withdrawal'));
        add_action('wp_ajax_gsp_admin_update_transfer', array($this, 'admin_update_transfer'));
        add_action('wp_ajax_gsp_admin_update_conversion', array($this, 'admin_update_conversion'));
        add_action('wp_ajax_gsp_admin_update_settings', array($this, 'admin_update_settings'));
        add_action('wp_ajax_gsp_admin_update_user_balance', array($this, 'admin_update_user_balance'));
        add_action('wp_ajax_gsp_admin_adjust_user_balance', array($this, 'admin_adjust_user_balance'));
        add_action('wp_ajax_gsp_admin_get_user_balance', array($this, 'admin_get_user_balance'));
        add_action('wp_ajax_gsp_admin_add_user', array($this, 'admin_add_user'));
        add_action('wp_ajax_gsp_admin_edit_user', array($this, 'admin_edit_user'));
        add_action('wp_ajax_gsp_admin_delete_user', array($this, 'admin_delete_user'));
        add_action('wp_ajax_gsp_admin_update_user_status', array($this, 'admin_update_user_status'));
        add_action('wp_ajax_gsp_admin_detect_wallet_sources', array($this, 'admin_detect_wallet_sources'));
        add_action('wp_ajax_gsp_admin_migrate_wallet_balances', array($this, 'admin_migrate_wallet_balances'));
        add_action('wp_ajax_gsp_admin_migrate_all_wallet_sources', array($this, 'admin_migrate_all_wallet_sources'));
        add_action('wp_ajax_gsp_admin_import_csv_balances', array($this, 'admin_import_csv_balances'));
        add_action('wp_ajax_gsp_admin_import_rm_users', array($this, 'admin_import_rm_users'));
        add_action('wp_ajax_gsp_admin_migrate_user_details', array($this, 'admin_migrate_user_details'));
    }
    
    /**
     * Verify nonce
     */
    private function verify_nonce($nonce_field = 'nonce') {
        if (!isset($_POST[$nonce_field]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), 'gsp_nonce')) {
            wp_send_json_error(array('message' => 'Security verification failed.'));
        }
    }
    
    /**
     * Verify admin nonce
     */
    private function verify_admin_nonce() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'gsp_admin_nonce')) {
            wp_send_json_error(array('message' => 'Security verification failed.'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized access.'));
        }
    }
    
    /**
     * Submit deposit request
     */
    public function submit_deposit() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }
        
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
        
        if (empty($name) || empty($email) || $amount <= 0) {
            wp_send_json_error(array('message' => 'All fields are required.'));
        }
        
        $deposit_id = GSP_Transactions::create_deposit($user_id, array(
            'name' => $name,
            'email' => $email,
            'amount' => $amount
        ));
        
        if ($deposit_id) {
            // Notify admin
            GSP_Email::notify_admin('deposit', array(
                'user' => $name,
                'email' => $email,
                'amount' => '$' . number_format($amount, 2)
            ));
            
            wp_send_json_success(array('message' => 'Deposit request submitted successfully. Awaiting approval.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to submit deposit request. Please try again.'));
        }
    }
    
    /**
     * Submit add balance (with receipt upload)
     */
    public function submit_add_balance() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }
        
        $sender_name = isset($_POST['sender_name']) ? sanitize_text_field(wp_unslash($_POST['sender_name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
        
        if (empty($sender_name) || empty($email) || $amount <= 0) {
            wp_send_json_error(array('message' => 'All fields are required.'));
        }
        
        // Handle file upload with enhanced security
        $receipt_path = '';
        if (!empty($_FILES['receipt']) && isset($_FILES['receipt']['tmp_name']) && $_FILES['receipt']['tmp_name'] !== '') {
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            
            // Maximum file size: 5MB
            $max_file_size = 5 * 1024 * 1024;
            if (isset($_FILES['receipt']['size']) && $_FILES['receipt']['size'] > $max_file_size) {
                wp_send_json_error(array('message' => 'File size exceeds maximum limit of 5MB.'));
            }
            
            // Validate file extension using wp_check_filetype
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'pdf');
            $allowed_mimes = array(
                'image/jpeg',
                'image/png',
                'application/pdf'
            );
            $file_name = isset($_FILES['receipt']['name']) ? sanitize_file_name($_FILES['receipt']['name']) : '';
            $file_info = wp_check_filetype($file_name, array(
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'pdf' => 'application/pdf'
            ));
            
            if (!$file_info['ext'] || !in_array(strtolower($file_info['ext']), $allowed_extensions, true)) {
                wp_send_json_error(array('message' => 'Invalid file type. Only PDF, PNG, and JPEG are allowed.'));
            }
            
            // Additional MIME type validation using finfo
            $tmp_file = isset($_FILES['receipt']['tmp_name']) ? $_FILES['receipt']['tmp_name'] : '';
            if (!empty($tmp_file) && file_exists($tmp_file)) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $detected_mime = $finfo->file($tmp_file);
                if (!in_array($detected_mime, $allowed_mimes, true)) {
                    wp_send_json_error(array('message' => 'File content does not match allowed types. Only PDF, PNG, and JPEG are allowed.'));
                }
            }
            
            $upload = wp_handle_upload($_FILES['receipt'], array(
                'test_form' => false,
                'mimes' => array(
                    'jpg|jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'pdf' => 'application/pdf'
                )
            ));
            
            if (isset($upload['error'])) {
                wp_send_json_error(array('message' => $upload['error']));
            }
            
            $receipt_path = $upload['url'];
        }
        
        $deposit_id = GSP_Transactions::create_deposit($user_id, array(
            'name' => $sender_name,
            'email' => $email,
            'amount' => $amount,
            'receipt_path' => $receipt_path,
            'sender_name' => $sender_name
        ));
        
        if ($deposit_id) {
            // Notify admin
            GSP_Email::notify_admin('deposit', array(
                'sender' => $sender_name,
                'email' => $email,
                'amount' => '$' . number_format($amount, 2),
                'receipt' => $receipt_path ? 'Uploaded' : 'Not provided'
            ));
            
            wp_send_json_success(array('message' => 'Balance request submitted successfully. Awaiting admin approval.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to submit request. Please try again.'));
        }
    }
    
    /**
     * Submit withdrawal request
     */
    public function submit_withdrawal() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }
        
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
        $method = isset($_POST['method']) ? sanitize_text_field(wp_unslash($_POST['method'])) : '';
        $details = isset($_POST['details']) ? array_map('sanitize_text_field', wp_unslash($_POST['details'])) : array();
        
        if ($amount <= 0 || empty($method)) {
            wp_send_json_error(array('message' => 'All fields are required.'));
        }
        
        // Check if user has enough balance
        if (!GSP_User::can_withdraw($user_id, $amount)) {
            wp_send_json_error(array('message' => 'Insufficient balance for this withdrawal.'));
        }
        
        $withdrawal_id = GSP_Transactions::create_withdrawal($user_id, $amount, $method, $details);
        
        if ($withdrawal_id) {
            $user = get_userdata($user_id);
            
            // Notify admin
            GSP_Email::notify_admin('withdrawal', array(
                'user' => $user->display_name,
                'email' => $user->user_email,
                'amount' => '$' . number_format($amount, 2),
                'method' => $method
            ));
            
            wp_send_json_success(array('message' => 'Withdrawal request submitted successfully. Awaiting admin approval.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to submit withdrawal request. Please try again.'));
        }
    }
    
    /**
     * Submit transfer request
     */
    public function submit_transfer() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }
        
        $recipient_email = isset($_POST['recipient_email']) ? sanitize_email(wp_unslash($_POST['recipient_email'])) : '';
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
        $token_code = isset($_POST['token_code']) ? sanitize_text_field(wp_unslash($_POST['token_code'])) : '';
        
        if (empty($recipient_email) || $amount <= 0 || empty($token_code)) {
            wp_send_json_error(array('message' => 'All fields are required.'));
        }
        
        // Find recipient user
        $recipient = get_user_by('email', $recipient_email);
        if (!$recipient) {
            wp_send_json_error(array('message' => 'Recipient user not found.'));
        }
        
        if ($recipient->ID === $user_id) {
            wp_send_json_error(array('message' => 'You cannot transfer to yourself.'));
        }
        
        // Check if user has enough balance
        if (!GSP_User::can_transfer($user_id, $amount)) {
            wp_send_json_error(array('message' => 'Insufficient balance for this transfer.'));
        }
        
        $transfer_id = GSP_Transactions::create_transfer($user_id, $recipient->ID, $amount, $token_code);
        
        if ($transfer_id) {
            $user = get_userdata($user_id);
            
            // Notify admin
            GSP_Email::notify_admin('transfer', array(
                'from' => $user->display_name . ' (' . $user->user_email . ')',
                'to' => $recipient->display_name . ' (' . $recipient_email . ')',
                'amount' => '$' . number_format($amount, 2),
                'token_code' => $token_code
            ));
            
            wp_send_json_success(array('message' => 'Your transfer is being reviewed by the system, you\'ll get an email shortly.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to submit transfer request. Please try again.'));
        }
    }
    
    /**
     * Submit conversion request
     */
    public function submit_conversion() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }
        
        $conversion_type = isset($_POST['conversion_type']) ? sanitize_text_field(wp_unslash($_POST['conversion_type'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
        $security_phrase = isset($_POST['security_phrase']) ? sanitize_text_field(wp_unslash($_POST['security_phrase'])) : '';
        
        if (empty($conversion_type) || empty($email) || $amount <= 0 || empty($security_phrase)) {
            wp_send_json_error(array('message' => 'All fields are required.'));
        }
        
        // Check if user has enough balance
        if (!GSP_User::can_withdraw($user_id, $amount)) {
            wp_send_json_error(array('message' => 'Insufficient balance for this conversion.'));
        }
        
        $data = array(
            'conversion_type' => $conversion_type,
            'email' => $email,
            'amount' => $amount,
            'security_phrase' => $security_phrase
        );
        
        // Handle different conversion types
        if ($conversion_type === 'btc') {
            $btc_address = isset($_POST['btc_address']) ? sanitize_text_field(wp_unslash($_POST['btc_address'])) : '';
            if (empty($btc_address)) {
                wp_send_json_error(array('message' => 'Bitcoin address is required.'));
            }
            $data['destination_address'] = $btc_address;
        } elseif ($conversion_type === 'usdt') {
            $usdt_address = isset($_POST['usdt_address']) ? sanitize_text_field(wp_unslash($_POST['usdt_address'])) : '';
            if (empty($usdt_address)) {
                wp_send_json_error(array('message' => 'USDT address is required.'));
            }
            $data['destination_address'] = $usdt_address;
        } elseif ($conversion_type === 'bank') {
            $bank_details = array(
                'bank_name' => isset($_POST['bank_name']) ? sanitize_text_field(wp_unslash($_POST['bank_name'])) : '',
                'account_name' => isset($_POST['account_name']) ? sanitize_text_field(wp_unslash($_POST['account_name'])) : '',
                'account_number' => isset($_POST['account_number']) ? sanitize_text_field(wp_unslash($_POST['account_number'])) : '',
                'swift_code' => isset($_POST['swift_code']) ? sanitize_text_field(wp_unslash($_POST['swift_code'])) : '',
                'bank_address' => isset($_POST['bank_address']) ? sanitize_text_field(wp_unslash($_POST['bank_address'])) : '',
                'country' => isset($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : ''
            );
            
            foreach ($bank_details as $key => $value) {
                if (empty($value)) {
                    wp_send_json_error(array('message' => 'All bank details are required.'));
                }
            }
            
            $data['bank_details'] = $bank_details;
        }
        
        $conversion_id = GSP_Transactions::create_conversion($user_id, $data);
        
        if ($conversion_id) {
            $user = get_userdata($user_id);
            
            // Notify admin
            GSP_Email::notify_admin('conversion', array(
                'user' => $user->display_name,
                'email' => $email,
                'amount' => '$' . number_format($amount, 2),
                'type' => strtoupper($conversion_type)
            ));
            
            // Send confirmation email to user
            GSP_Email::send_conversion_request_received($email, $amount, $conversion_type);
            
            wp_send_json_success(array('message' => 'Conversion request submitted successfully. Awaiting admin approval.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to submit conversion request. Please try again.'));
        }
    }
    
    /**
     * Get user transactions
     */
    public function get_transactions() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }

        nocache_headers();
        
        $transactions = GSP_Transactions::get_user_transactions($user_id);
        
        wp_send_json_success(array('transactions' => $transactions));
    }
    
    /**
     * Get user balance
     */
    public function get_balance() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }

        nocache_headers();
        
        $balance = GSP_User::get_balance($user_id);
        
        wp_send_json_success(array(
            'wallet_balance' => number_format($balance->wallet_balance, 2),
            'savings_balance' => number_format($balance->savings_balance, 2)
        ));
    }
    
    /**
     * Update user profile
     */
    public function update_profile() {
        $this->verify_nonce();
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Please log in to continue.'));
        }
        
        $display_name = isset($_POST['display_name']) ? sanitize_text_field(wp_unslash($_POST['display_name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $first_name = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '';
        $current_password = isset($_POST['current_password']) ? wp_unslash($_POST['current_password']) : '';
        $new_password = isset($_POST['new_password']) ? wp_unslash($_POST['new_password']) : '';
        $confirm_password = isset($_POST['confirm_password']) ? wp_unslash($_POST['confirm_password']) : '';
        
        if (empty($display_name) || empty($email)) {
            wp_send_json_error(array('message' => 'Display name and email are required.'));
        }
        
        // Validate email format
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        }
        
        // Check if email is already used by another user
        $existing_user = get_user_by('email', $email);
        if ($existing_user && $existing_user->ID !== $user_id) {
            wp_send_json_error(array('message' => 'This email address is already in use.'));
        }
        
        // Handle password change
        if (!empty($new_password)) {
            if (empty($current_password)) {
                wp_send_json_error(array('message' => 'Please enter your current password to change it.'));
            }
            
            // Verify current password
            $user = get_user_by('id', $user_id);
            if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
                wp_send_json_error(array('message' => 'Current password is incorrect.'));
            }
            
            if ($new_password !== $confirm_password) {
                wp_send_json_error(array('message' => 'New passwords do not match.'));
            }
            
            if (strlen($new_password) < 6) {
                wp_send_json_error(array('message' => 'Password must be at least 6 characters long.'));
            }
        }
        
        // Update user data
        $user_data = array(
            'ID' => $user_id,
            'display_name' => $display_name,
            'user_email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name
        );
        
        if (!empty($new_password)) {
            $user_data['user_pass'] = $new_password;
        }
        
        $result = wp_update_user($user_data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        // Handle profile image upload
        $avatar_url = '';
        if (!empty($_FILES['profile_avatar']) && !empty($_FILES['profile_avatar']['tmp_name'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            // Check file size (2MB max)
            if ($_FILES['profile_avatar']['size'] > 2 * 1024 * 1024) {
                wp_send_json_error(array('message' => 'Profile image must be less than 2MB.'));
            }
            
            // Check file type
            $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $_FILES['profile_avatar']['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($file_type, $allowed_types)) {
                wp_send_json_error(array('message' => 'Only JPG, PNG, and GIF images are allowed.'));
            }
            
            // Upload the file
            $attachment_id = media_handle_upload('profile_avatar', 0);
            
            if (is_wp_error($attachment_id)) {
                wp_send_json_error(array('message' => 'Failed to upload profile image: ' . $attachment_id->get_error_message()));
            }
            
            // Update user meta with custom avatar
            update_user_meta($user_id, 'gsp_custom_avatar', $attachment_id);
            $avatar_url = wp_get_attachment_url($attachment_id);
        } else {
            // Get current avatar
            $custom_avatar_id = get_user_meta($user_id, 'gsp_custom_avatar', true);
            if ($custom_avatar_id) {
                $avatar_url = wp_get_attachment_url($custom_avatar_id);
            } else {
                $avatar_url = get_avatar_url($user_id, array('size' => 100));
            }
        }
        
        wp_send_json_success(array(
            'message' => 'Profile updated successfully.',
            'display_name' => $display_name,
            'avatar_url' => $avatar_url
        ));
    }
    
    /**
     * Admin: Update deposit status
     */
    public function admin_update_deposit() {
        $this->verify_admin_nonce();
        
        $deposit_id = isset($_POST['deposit_id']) ? intval($_POST['deposit_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field(wp_unslash($_POST['notes'])) : '';
        
        if (!$deposit_id || !in_array($status, array('approved', 'declined'), true)) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }
        
        $result = GSP_Transactions::update_deposit_status($deposit_id, $status, $notes);
        
        if ($result) {
            wp_send_json_success(array('message' => 'Deposit status updated successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update deposit status.'));
        }
    }
    
    /**
     * Admin: Update withdrawal status
     */
    public function admin_update_withdrawal() {
        $this->verify_admin_nonce();
        
        $withdrawal_id = isset($_POST['withdrawal_id']) ? intval($_POST['withdrawal_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field(wp_unslash($_POST['notes'])) : '';
        
        if (!$withdrawal_id || !in_array($status, array('approved', 'declined'), true)) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }
        
        $result = GSP_Transactions::update_withdrawal_status($withdrawal_id, $status, $notes);
        
        if ($result) {
            wp_send_json_success(array('message' => 'Withdrawal status updated successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update withdrawal status.'));
        }
    }
    
    /**
     * Admin: Update transfer status
     */
    public function admin_update_transfer() {
        $this->verify_admin_nonce();
        
        $transfer_id = isset($_POST['transfer_id']) ? intval($_POST['transfer_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field(wp_unslash($_POST['notes'])) : '';
        
        if (!$transfer_id || !in_array($status, array('approved', 'declined'), true)) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }
        
        $result = GSP_Transactions::update_transfer_status($transfer_id, $status, $notes);
        
        if ($result) {
            wp_send_json_success(array('message' => 'Transfer status updated successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update transfer status.'));
        }
    }
    
    /**
     * Admin: Update conversion status
     */
    public function admin_update_conversion() {
        $this->verify_admin_nonce();
        
        $conversion_id = isset($_POST['conversion_id']) ? intval($_POST['conversion_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field(wp_unslash($_POST['notes'])) : '';
        
        if (!$conversion_id || !in_array($status, array('approved', 'declined'), true)) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }
        
        $result = GSP_Transactions::update_conversion_status($conversion_id, $status, $notes);
        
        if ($result) {
            wp_send_json_success(array('message' => 'Conversion status updated successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update conversion status.'));
        }
    }
    
    /**
     * Admin: Update settings
     */
    public function admin_update_settings() {
        $this->verify_admin_nonce();
        
        $settings = isset($_POST['settings']) ? array_map('sanitize_text_field', wp_unslash($_POST['settings'])) : array();
        $failed_keys = array();
        
        foreach ($settings as $key => $value) {
            $saved = GSP_Database::set_setting($key, $value);
            if (!$saved) {
                $failed_keys[] = $key;
            }
        }

        if (!empty($failed_keys)) {
            wp_send_json_error(array('message' => 'Failed to save settings. Please try again.'));
        }

        wp_send_json_success(array('message' => 'Settings updated successfully.'));
    }
    
    /**
     * Admin: Update user balance
     */
    public function admin_update_user_balance() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to update balances.'));
        }
        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $balance = isset($_POST['balance']) ? floatval($_POST['balance']) : 0;
        
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Invalid user.'));
        }
        
        // Verify user exists
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => 'User not found.'));
        }
        
        $savings_balance = isset($_POST['savings_balance']) ? floatval($_POST['savings_balance']) : null;
        
        $wallet_result = GSP_User::set_wallet_balance($user_id, $balance);
        
        if (!$wallet_result) {
            wp_send_json_error(array('message' => 'Failed to update wallet balance. Database error.'));
        }
        
        if ($savings_balance !== null) {
            $savings_result = GSP_User::set_savings_balance($user_id, $savings_balance);
            if (!$savings_result) {
                wp_send_json_error(array('message' => 'Wallet balance updated but savings balance failed.'));
            }
        }
        
        wp_send_json_success(array(
            'message' => 'User balance updated successfully.',
            'wallet_balance' => $balance,
            'savings_balance' => $savings_balance
        ));
    }

    /**
     * Admin: Get user balance
     */
    public function admin_get_user_balance() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to view balances.'));
        }
        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Invalid user.'));
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => 'User not found.'));
        }
        
        $balance = GSP_User::get_balance($user_id);
        
        wp_send_json_success(array(
            'wallet_balance' => $balance->wallet_balance,
            'savings_balance' => $balance->savings_balance
        ));
    }

    /**
     * Admin: Add or deduct user balance
     */
    public function admin_adjust_user_balance() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to adjust balances.'));
        }
        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $operation = isset($_POST['operation']) ? sanitize_text_field(wp_unslash($_POST['operation'])) : '';
        $wallet_amount = isset($_POST['wallet_amount']) ? floatval($_POST['wallet_amount']) : 0;
        $savings_amount = isset($_POST['savings_amount']) ? floatval($_POST['savings_amount']) : 0;
        
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Invalid user.'));
        }
        
        if (!in_array($operation, array('add', 'subtract'), true)) {
            wp_send_json_error(array('message' => 'Invalid operation.'));
        }
        
        if ($wallet_amount <= 0 && $savings_amount <= 0) {
            wp_send_json_error(array('message' => 'Please enter an amount greater than zero.'));
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => 'User not found.'));
        }
        
        // For deductions, check that user has sufficient balance
        if ($operation === 'subtract') {
            $current_balance = GSP_User::get_balance($user_id);
            if ($wallet_amount > 0 && $wallet_amount > $current_balance->wallet_balance) {
                wp_send_json_error(array('message' => 'Insufficient wallet balance. Current: $' . number_format($current_balance->wallet_balance, 2)));
            }
            if ($savings_amount > 0 && $savings_amount > $current_balance->savings_balance) {
                wp_send_json_error(array('message' => 'Insufficient savings balance. Current: $' . number_format($current_balance->savings_balance, 2)));
            }
        }
        
        // Apply wallet adjustment
        if ($wallet_amount > 0) {
            $result = GSP_User::update_wallet_balance($user_id, $wallet_amount, $operation);
            if (!$result) {
                wp_send_json_error(array('message' => 'Failed to update wallet balance.'));
            }
        }
        
        // Apply savings adjustment
        if ($savings_amount > 0) {
            $result = GSP_User::update_savings_balance($user_id, $savings_amount, $operation);
            if (!$result) {
                wp_send_json_error(array('message' => 'Failed to update savings balance.'));
            }
        }
        
        // Get updated balance
        $new_balance = GSP_User::get_balance($user_id);
        $action_label = ($operation === 'add') ? 'added to' : 'deducted from';
        
        wp_send_json_success(array(
            'message' => 'Balance successfully ' . $action_label . ' user account.',
            'wallet_balance' => $new_balance->wallet_balance,
            'savings_balance' => $new_balance->savings_balance
        ));
    }

    /**
     * Admin: Add new user
     */
    public function admin_add_user() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('create_users')) {
            wp_send_json_error(array('message' => 'You do not have permission to create users.'));
        }
        
        $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $balance = isset($_POST['balance']) ? floatval($_POST['balance']) : 0;
        
        if (empty($username) || empty($email) || empty($password)) {
            wp_send_json_error(array('message' => 'Username, email, and password are required.'));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        }
        
        if (username_exists($username)) {
            wp_send_json_error(array('message' => 'This username is already taken.'));
        }
        
        if (email_exists($email)) {
            wp_send_json_error(array('message' => 'This email is already registered.'));
        }
        
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        }
        
        if (!empty($display_name)) {
            wp_update_user(array(
                'ID' => $user_id,
                'display_name' => $display_name
            ));
        }
        
        if ($balance > 0) {
            GSP_User::set_wallet_balance($user_id, $balance);
        }
        
        // Set user status as approved for admin-created users
        update_user_meta($user_id, 'gsp_user_status', 'approved');
        
        wp_send_json_success(array(
            'message' => 'User created successfully.',
            'user_id' => $user_id
        ));
    }

    /**
     * Admin: Edit user
     */
    public function admin_edit_user() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('edit_users')) {
            wp_send_json_error(array('message' => 'You do not have permission to edit users.'));
        }
        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
        
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Invalid user.'));
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => 'User not found.'));
        }
        
        if (empty($email)) {
            wp_send_json_error(array('message' => 'Email is required.'));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        }
        
        // Check if email is taken by another user
        $existing_user = get_user_by('email', $email);
        if ($existing_user && $existing_user->ID !== $user_id) {
            wp_send_json_error(array('message' => 'This email is already used by another user.'));
        }
        
        $user_data = array(
            'ID' => $user_id,
            'user_email' => $email,
            'display_name' => $display_name
        );
        
        if (!empty($password)) {
            $user_data['user_pass'] = $password;
        }
        
        $result = wp_update_user($user_data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        // Update phone and country user meta
        update_user_meta($user_id, 'gsp_mobile', $phone);
        update_user_meta($user_id, 'gsp_country', $country);
        
        wp_send_json_success(array(
            'message' => 'User updated successfully.',
            'phone' => $phone,
            'country' => $country
        ));
    }

    /**
     * Admin: Delete user
     */
    public function admin_delete_user() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('delete_users')) {
            wp_send_json_error(array('message' => 'You do not have permission to delete users.'));
        }
        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Invalid user.'));
        }
        
        // Prevent deleting the current user
        if ($user_id === get_current_user_id()) {
            wp_send_json_error(array('message' => 'You cannot delete your own account.'));
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => 'User not found.'));
        }
        
        // Delete user's balance record
        global $wpdb;
        $table_name = $wpdb->prefix . 'gsp_user_balances';
        $wpdb->delete($table_name, array('user_id' => $user_id), array('%d'));
        
        // Delete the WordPress user
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        $result = wp_delete_user($user_id);
        
        if (!$result) {
            wp_send_json_error(array('message' => 'Failed to delete user.'));
        }
        
        wp_send_json_success(array('message' => 'User deleted successfully.'));
    }

    /**
     * Admin: Update user status (approve/decline)
     */
    public function admin_update_user_status() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('edit_users')) {
            wp_send_json_error(array('message' => 'You do not have permission to update user status.'));
        }
        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';
        
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Invalid user.'));
        }
        
        if (!in_array($status, array('approved', 'declined', 'pending'), true)) {
            wp_send_json_error(array('message' => 'Invalid status.'));
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => 'User not found.'));
        }
        
        // Update user status meta
        update_user_meta($user_id, 'gsp_user_status', $status);
        
        wp_send_json_success(array(
            'message' => 'User status updated to ' . ucfirst($status) . '.',
            'status' => $status
        ));
    }

    /**
     * Admin: Detect wallet sources from other plugins
     */
    public function admin_detect_wallet_sources() {
        $this->verify_admin_nonce();

        $sources = GSP_User::detect_wallet_sources();

        wp_send_json_success(array('sources' => $sources));
    }

    /**
     * Admin: Migrate wallet balances from selected source
     */
    public function admin_migrate_wallet_balances() {
        $this->verify_admin_nonce();

        $source_id = isset($_POST['source_id']) ? sanitize_text_field(wp_unslash($_POST['source_id'])) : '';
        if (empty($source_id)) {
            wp_send_json_error(array('message' => 'Select a source to migrate.'));
        }

        $result = GSP_User::migrate_wallet_balances($source_id);

        wp_send_json_success(array(
            'message' => sprintf(
                'Wallet migration completed. Updated %1$d of %2$d records.',
                (int) $result['updated'],
                (int) $result['total']
            )
        ));
    }

    /**
     * Admin: Migrate balances from all detected sources
     */
    public function admin_migrate_all_wallet_sources() {
        $this->verify_admin_nonce();

        $sources = GSP_User::detect_wallet_sources();
        if (empty($sources)) {
            wp_send_json_error(array('message' => 'No wallet sources detected.'));
        }

        $total_updated = 0;
        $total_records = 0;
        $failed_sources = array();
        foreach ($sources as $source) {
            try {
                $result = GSP_User::migrate_wallet_balances($source['id']);
                $total_updated += (int) $result['updated'];
                $total_records += (int) $result['total'];
            } catch (Exception $e) {
                error_log('Wallet migration failed for a source: ' . $e->getMessage());
                $failed_sources[] = isset($source['label']) ? $source['label'] : $source['id'];
            }
        }

        if (!empty($failed_sources)) {
            wp_send_json_error(array(
                'message' => sprintf(
                    'Migration completed with errors. Updated %1$d of %2$d records. Failed sources: %3$s',
                    $total_updated,
                    $total_records,
                    implode(', ', $failed_sources)
                )
            ));
        }

        wp_send_json_success(array(
            'message' => sprintf(
                'Wallet migration completed. Updated %1$d of %2$d records.',
                $total_updated,
                $total_records
            )
        ));
    }

    /**
     * Handle CSV balance import
     */
    public function admin_import_csv_balances() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to perform this action.'));
        }
        
        if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(array('message' => 'No file uploaded or upload error.'));
        }
        
        $file = $_FILES['csv_file'];
        
        // Validate file type
        $allowed_extensions = array('csv');
        $file_name = isset($file['name']) ? sanitize_file_name($file['name']) : '';
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_extensions, true)) {
            wp_send_json_error(array('message' => 'Invalid file type. Only CSV files are allowed.'));
        }
        
        // Validate MIME type
        $tmp_file = isset($file['tmp_name']) ? $file['tmp_name'] : '';
        if (!empty($tmp_file) && file_exists($tmp_file)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detected_mime = $finfo->file($tmp_file);
            $allowed_mimes = array('text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel');
            if (!in_array($detected_mime, $allowed_mimes, true)) {
                wp_send_json_error(array('message' => 'Invalid file content type.'));
            }
        }
        
        // Read and parse CSV
        $handle = fopen($tmp_file, 'r');
        if (!$handle) {
            wp_send_json_error(array('message' => 'Could not read CSV file.'));
        }
        
        $imported = 0;
        $skipped = 0;
        $errors = array();
        $row_num = 0;
        $header = null;
        $email_col = -1;
        $balance_col = -1;
        
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $row_num++;
            
            // Try to detect header row
            if ($row_num === 1) {
                // Check if this looks like a header
                $row_lower = array_map('strtolower', array_map('trim', $row));
                
                // Look for email column
                foreach ($row_lower as $idx => $col) {
                    if (in_array($col, array('email', 'user_email', 'e-mail', 'mail'), true)) {
                        $email_col = $idx;
                    }
                    if (in_array($col, array('balance', 'wallet_balance', 'amount', 'wallet'), true)) {
                        $balance_col = $idx;
                    }
                }
                
                // If we found both columns, this is a header row - skip it
                if ($email_col >= 0 && $balance_col >= 0) {
                    continue;
                }
                
                // No header detected, assume first column is email, second is balance
                $email_col = 0;
                $balance_col = 1;
            }
            
            // Validate row has enough columns
            if (count($row) < 2) {
                $skipped++;
                continue;
            }
            
            $email = isset($row[$email_col]) ? sanitize_email(trim($row[$email_col])) : '';
            $balance = isset($row[$balance_col]) ? trim($row[$balance_col]) : '';
            
            // Clean up balance (remove $ and commas)
            $balance = preg_replace('/[^0-9.-]/', '', $balance);
            $balance = floatval($balance);
            
            if (empty($email) || !is_email($email)) {
                $skipped++;
                continue;
            }
            
            // Find WordPress user by email
            $user = get_user_by('email', $email);
            if (!$user) {
                $errors[] = sprintf('Row %d: User not found for email %s', $row_num, $email);
                $skipped++;
                continue;
            }
            
            // Update or insert balance
            if (GSP_User::set_wallet_balance($user->ID, $balance)) {
                $imported++;
            } else {
                $errors[] = sprintf('Row %d: Failed to update balance for %s', $row_num, $email);
                $skipped++;
            }
        }
        
        fclose($handle);
        
        $message = sprintf('Import completed. %d balances imported, %d skipped.', $imported, $skipped);
        
        if (!empty($errors) && count($errors) <= 10) {
            $message .= "\n\nErrors:\n" . implode("\n", $errors);
        } elseif (!empty($errors)) {
            $message .= sprintf("\n\n%d errors occurred (showing first 10):\n", count($errors));
            $message .= implode("\n", array_slice($errors, 0, 10));
        }
        
        if ($imported > 0) {
            wp_send_json_success(array('message' => $message));
        } else {
            wp_send_json_error(array('message' => $message));
        }
    }

    /**
     * Admin: Import Registration Magic user data from XML
     */
    public function admin_import_rm_users() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to import users.'));
        }
        
        // Look for the RM XML file in the plugin directory or uploads
        $xml_paths = array(
            ABSPATH . 'RMagic (1).xml',
            ABSPATH . 'RMagic.xml',
            WP_CONTENT_DIR . '/uploads/RMagic (1).xml',
            WP_CONTENT_DIR . '/uploads/RMagic.xml',
        );
        
        // Also check the repository root if running in dev
        $plugin_dir = dirname(dirname(__FILE__));
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
            wp_send_json_error(array('message' => 'Registration Magic XML file not found. Please upload the file to your WordPress root directory as "RMagic.xml".'));
        }
        
        // Parse the XML (disable external entities for security)
        $prev_entity_loader = libxml_disable_entity_loader(true);
        $xml = simplexml_load_file($xml_file, 'SimpleXMLElement', LIBXML_NONET);
        libxml_disable_entity_loader($prev_entity_loader);
        if (!$xml) {
            wp_send_json_error(array('message' => 'Failed to parse the XML file. Please ensure it is a valid Registration Magic export.'));
        }
        
        $forms = $xml->FORMS;
        if (!$forms) {
            wp_send_json_error(array('message' => 'Invalid XML structure: FORMS element not found.'));
        }
        
        // Build field_id -> label mapping
        $field_map = array();
        foreach ($forms->FIELDS as $field) {
            $fid = (string)$field->field_id;
            $label = (string)$field->field_label;
            $type = (string)$field->field_type;
            $field_map[$fid] = array('label' => $label, 'type' => $type);
        }
        
        // Build submission_id -> email mapping
        $sub_emails = array();
        foreach ($forms->SUBMISSIONS as $sub) {
            $sid = (string)$sub->submission_id;
            $email = (string)$sub->user_email;
            $sub_emails[$sid] = $email;
        }
        
        // Identify field IDs for target fields
        $username_field_ids = array();
        $email_field_ids = array();
        $fname_field_ids = array();
        $lname_field_ids = array();
        $country_field_ids = array();
        $mobile_field_ids = array();
        $phone_field_ids = array();
        
        foreach ($field_map as $fid => $info) {
            $type_lower = strtolower($info['type']);
            $label_lower = strtolower($info['label']);
            
            if ($type_lower === 'username' || $label_lower === 'gsp account number') {
                $username_field_ids[] = $fid;
            } elseif ($type_lower === 'email') {
                $email_field_ids[] = $fid;
            } elseif ($type_lower === 'fname' || $label_lower === 'first name') {
                $fname_field_ids[] = $fid;
            } elseif ($type_lower === 'lname' || $label_lower === 'last name') {
                $lname_field_ids[] = $fid;
            } elseif ($type_lower === 'country') {
                $country_field_ids[] = $fid;
            } elseif ($type_lower === 'mobile' || $label_lower === 'mobile number') {
                $mobile_field_ids[] = $fid;
            }
        }
        
        // Also check for phone numbers in fields not listed in FIELDS definition
        // (field 23 appears in submissions but not in field definitions for this export)
        $all_submission_fids = array();
        foreach ($forms->SUBMISSION_FIELDS as $sf) {
            $fid = (string)$sf->field_id;
            $all_submission_fids[$fid] = true;
        }
        foreach ($all_submission_fids as $fid => $_) {
            if (!isset($field_map[$fid])) {
                $phone_field_ids[] = $fid;
            }
        }
        
        // Build user data from SUBMISSION_FIELDS
        $submissions = array();
        foreach ($forms->SUBMISSION_FIELDS as $sf) {
            $sid = (string)$sf->submission_id;
            $fid = (string)$sf->field_id;
            $val = (string)$sf->value;
            
            if (!isset($submissions[$sid])) {
                $submissions[$sid] = array();
            }
            $submissions[$sid][$fid] = $val;
        }
        
        // Build user records
        $users_data = array();
        foreach ($submissions as $sid => $fields) {
            // Get email from submission fields first, then from submissions table
            $email = '';
            foreach ($email_field_ids as $fid) {
                if (!empty($fields[$fid])) {
                    $email = $fields[$fid];
                    break;
                }
            }
            if (empty($email) && isset($sub_emails[$sid])) {
                $email = $sub_emails[$sid];
            }
            
            if (empty($email)) {
                continue;
            }
            
            // Get GSP account number
            $gsp_account = '';
            foreach ($username_field_ids as $fid) {
                if (!empty($fields[$fid])) {
                    $gsp_account = $fields[$fid];
                    break;
                }
            }
            
            // Get first name
            $first_name = '';
            foreach ($fname_field_ids as $fid) {
                if (!empty($fields[$fid])) {
                    $first_name = $fields[$fid];
                    break;
                }
            }
            
            // Get last name
            $last_name = '';
            foreach ($lname_field_ids as $fid) {
                if (!empty($fields[$fid])) {
                    $last_name = $fields[$fid];
                    break;
                }
            }
            
            // Get country (clean format like "Turkey[TR]" -> "Turkey")
            $country = '';
            foreach ($country_field_ids as $fid) {
                if (!empty($fields[$fid])) {
                    $country = preg_replace('/\[[A-Z]{2,3}\]$/', '', $fields[$fid]);
                    $country = trim($country);
                    break;
                }
            }
            
            // Get mobile number (prefer mobile field, fall back to phone fields)
            $mobile = '';
            foreach ($mobile_field_ids as $fid) {
                if (!empty($fields[$fid])) {
                    $mobile = $fields[$fid];
                    break;
                }
            }
            if (empty($mobile)) {
                foreach ($phone_field_ids as $fid) {
                    if (!empty($fields[$fid]) && preg_match('/^[\+\d\s\-\(\)]+$/', $fields[$fid])) {
                        $mobile = $fields[$fid];
                        break;
                    }
                }
            }
            
            $users_data[strtolower($email)] = array(
                'email' => $email,
                'gsp_account' => $gsp_account,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'country' => $country,
                'mobile' => $mobile,
            );
        }
        
        // Now match with WordPress users and update meta
        $updated = 0;
        $not_found = 0;
        $skipped = 0;
        $total = count($users_data);
        
        foreach ($users_data as $email_key => $data) {
            $wp_user = get_user_by('email', $data['email']);
            
            // Try case-insensitive email match
            if (!$wp_user) {
                $wp_user = get_user_by('email', strtolower($data['email']));
            }
            
            if (!$wp_user) {
                // Try matching by GSP account number (username)
                if (!empty($data['gsp_account'])) {
                    $wp_user = get_user_by('login', $data['gsp_account']);
                }
            }
            
            if (!$wp_user) {
                $not_found++;
                continue;
            }
            
            $user_updated = false;
            
            // Update GSP account number
            if (!empty($data['gsp_account'])) {
                $existing = get_user_meta($wp_user->ID, 'gsp_account_number', true);
                if (empty($existing)) {
                    update_user_meta($wp_user->ID, 'gsp_account_number', sanitize_text_field($data['gsp_account']));
                    $user_updated = true;
                }
            }
            
            // Update mobile
            if (!empty($data['mobile'])) {
                $existing = get_user_meta($wp_user->ID, 'gsp_mobile', true);
                if (empty($existing)) {
                    update_user_meta($wp_user->ID, 'gsp_mobile', sanitize_text_field($data['mobile']));
                    $user_updated = true;
                }
            }
            
            // Update country
            if (!empty($data['country'])) {
                $existing = get_user_meta($wp_user->ID, 'gsp_country', true);
                if (empty($existing)) {
                    update_user_meta($wp_user->ID, 'gsp_country', sanitize_text_field($data['country']));
                    $user_updated = true;
                }
            }
            
            // Update first/last name if not already set
            if (!empty($data['first_name'])) {
                $existing = get_user_meta($wp_user->ID, 'first_name', true);
                if (empty($existing)) {
                    update_user_meta($wp_user->ID, 'first_name', sanitize_text_field($data['first_name']));
                    $user_updated = true;
                }
            }
            
            if (!empty($data['last_name'])) {
                $existing = get_user_meta($wp_user->ID, 'last_name', true);
                if (empty($existing)) {
                    update_user_meta($wp_user->ID, 'last_name', sanitize_text_field($data['last_name']));
                    $user_updated = true;
                }
            }
            
            if ($user_updated) {
                $updated++;
            } else {
                $skipped++;
            }
        }
        
        $message = sprintf(
            'Import complete. %d users found in XML. %d users updated, %d already had data (skipped), %d not found in WordPress.',
            $total, $updated, $skipped, $not_found
        );
        
        wp_send_json_success(array(
            'message' => $message,
            'total' => $total,
            'updated' => $updated,
            'skipped' => $skipped,
            'not_found' => $not_found
        ));
    }

    /**
     * Admin: Auto-migrate phone and country from other plugins
     * Scans common user meta keys from Registration Magic, WooCommerce, BuddyPress, etc.
     */
    public function admin_migrate_user_details() {
        $this->verify_admin_nonce();
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to perform this action.'));
        }
        
        // Known meta keys for phone numbers across popular plugins
        $phone_meta_keys = array(
            'billing_phone',           // WooCommerce
            'shipping_phone',          // WooCommerce
            'phone',                   // Generic
            'phone_number',            // Generic
            'mobile',                  // Generic
            'mobile_number',           // Generic
            'user_phone',              // Generic
            '_phone',                  // Various
            'bp_phone',               // BuddyPress
            'mepr_phone',             // MemberPress
            'um_phone_number',        // Ultimate Member
            'rm_phone',               // Registration Magic
            'rmagic_phone',           // Registration Magic
        );
        
        // Known meta keys for country across popular plugins
        $country_meta_keys = array(
            'billing_country',         // WooCommerce
            'shipping_country',        // WooCommerce
            'country',                 // Generic
            'user_country',            // Generic
            '_country',                // Various
            'bp_country',             // BuddyPress
            'mepr_country',           // MemberPress
            'um_country',             // Ultimate Member
            'rm_country',             // Registration Magic
            'rmagic_country',         // Registration Magic
        );
        
        // Country code to name mapping for WooCommerce 2-letter codes
        $country_codes = array(
            'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada',
            'AU' => 'Australia', 'DE' => 'Germany', 'FR' => 'France', 'NG' => 'Nigeria',
            'GH' => 'Ghana', 'KE' => 'Kenya', 'ZA' => 'South Africa', 'IN' => 'India',
            'BR' => 'Brazil', 'MX' => 'Mexico', 'JP' => 'Japan', 'CN' => 'China',
            'KR' => 'South Korea', 'IT' => 'Italy', 'ES' => 'Spain', 'NL' => 'Netherlands',
            'SE' => 'Sweden', 'NO' => 'Norway', 'DK' => 'Denmark', 'FI' => 'Finland',
            'PL' => 'Poland', 'RU' => 'Russia', 'TR' => 'Turkey', 'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia', 'EG' => 'Egypt', 'PH' => 'Philippines', 'TH' => 'Thailand',
            'MY' => 'Malaysia', 'SG' => 'Singapore', 'ID' => 'Indonesia', 'PK' => 'Pakistan',
            'BD' => 'Bangladesh', 'LK' => 'Sri Lanka', 'NZ' => 'New Zealand', 'IE' => 'Ireland',
            'CH' => 'Switzerland', 'AT' => 'Austria', 'BE' => 'Belgium', 'PT' => 'Portugal',
            'GR' => 'Greece', 'CZ' => 'Czech Republic', 'RO' => 'Romania', 'HU' => 'Hungary',
            'IL' => 'Israel', 'CL' => 'Chile', 'CO' => 'Colombia', 'AR' => 'Argentina',
            'PE' => 'Peru', 'VE' => 'Venezuela', 'EC' => 'Ecuador', 'UY' => 'Uruguay',
            'CR' => 'Costa Rica', 'PA' => 'Panama', 'JM' => 'Jamaica', 'TT' => 'Trinidad and Tobago',
            'CM' => 'Cameroon', 'CI' => 'Ivory Coast', 'SN' => 'Senegal', 'TZ' => 'Tanzania',
            'UG' => 'Uganda', 'ZW' => 'Zimbabwe', 'ET' => 'Ethiopia', 'RW' => 'Rwanda',
        );
        
        $users = get_users(array('fields' => array('ID')));
        $updated_phone = 0;
        $updated_country = 0;
        $already_set = 0;
        $sources_found = array();
        
        foreach ($users as $user) {
            $uid = $user->ID;
            $existing_phone = get_user_meta($uid, 'gsp_mobile', true);
            $existing_country = get_user_meta($uid, 'gsp_country', true);
            
            // Migrate phone number
            if (empty($existing_phone)) {
                foreach ($phone_meta_keys as $key) {
                    $value = get_user_meta($uid, $key, true);
                    if (!empty($value)) {
                        update_user_meta($uid, 'gsp_mobile', sanitize_text_field($value));
                        $updated_phone++;
                        if (!isset($sources_found[$key])) $sources_found[$key] = 0;
                        $sources_found[$key]++;
                        break;
                    }
                }
            } else {
                $already_set++;
            }
            
            // Migrate country
            if (empty($existing_country)) {
                foreach ($country_meta_keys as $key) {
                    $value = get_user_meta($uid, $key, true);
                    if (!empty($value)) {
                        // Convert 2-letter country codes to names
                        $country_name = $value;
                        if (strlen($value) === 2 && isset($country_codes[strtoupper($value)])) {
                            $country_name = $country_codes[strtoupper($value)];
                        }
                        // Remove bracket suffixes like "Turkey[TR]"
                        $country_name = preg_replace('/\[[A-Z]{2,3}\]$/', '', $country_name);
                        $country_name = trim($country_name);
                        
                        update_user_meta($uid, 'gsp_country', sanitize_text_field($country_name));
                        $updated_country++;
                        if (!isset($sources_found[$key])) $sources_found[$key] = 0;
                        $sources_found[$key]++;
                        break;
                    }
                }
            }
        }
        
        $total_users = count($users);
        $source_summary = array();
        foreach ($sources_found as $key => $count) {
            $source_summary[] = $key . ': ' . $count . ' users';
        }
        
        $message = sprintf(
            "Migration complete.\nTotal users scanned: %d\nPhone numbers migrated: %d\nCountries migrated: %d\nAlready had phone set: %d",
            $total_users, $updated_phone, $updated_country, $already_set
        );
        
        if (!empty($source_summary)) {
            $message .= "\n\nSources found:\n" . implode("\n", $source_summary);
        } else {
            $message .= "\n\nNo additional phone/country data found in other plugins.";
        }
        
        wp_send_json_success(array(
            'message' => $message,
            'updated_phone' => $updated_phone,
            'updated_country' => $updated_country,
            'total_users' => $total_users,
            'sources' => $sources_found
        ));
    }
}
