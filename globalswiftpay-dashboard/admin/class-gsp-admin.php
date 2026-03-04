<?php
/**
 * Admin handler for GlobalSwiftPay Dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSP_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('GlobalSwiftPay', 'globalswiftpay-dashboard'),
            __('GlobalSwiftPay', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay',
            array($this, 'render_dashboard_page'),
            'dashicons-chart-area',
            30
        );
        
        // Deposits submenu
        add_submenu_page(
            'globalswiftpay',
            __('Deposits', 'globalswiftpay-dashboard'),
            __('Deposits', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-deposits',
            array($this, 'render_deposits_page')
        );
        
        // Withdrawals submenu
        add_submenu_page(
            'globalswiftpay',
            __('Withdrawals', 'globalswiftpay-dashboard'),
            __('Withdrawals', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-withdrawals',
            array($this, 'render_withdrawals_page')
        );
        
        // Transfers submenu
        add_submenu_page(
            'globalswiftpay',
            __('Transfers', 'globalswiftpay-dashboard'),
            __('Transfers', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-transfers',
            array($this, 'render_transfers_page')
        );
        
        // Conversions submenu
        add_submenu_page(
            'globalswiftpay',
            __('Conversions', 'globalswiftpay-dashboard'),
            __('Conversions', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-conversions',
            array($this, 'render_conversions_page')
        );
        
        // Users submenu
        add_submenu_page(
            'globalswiftpay',
            __('Users', 'globalswiftpay-dashboard'),
            __('Users', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-users',
            array($this, 'render_users_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'globalswiftpay',
            __('Settings', 'globalswiftpay-dashboard'),
            __('Settings', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-settings',
            array($this, 'render_settings_page')
        );

        // Migration submenu
        add_submenu_page(
            'globalswiftpay',
            __('Wallet Migration', 'globalswiftpay-dashboard'),
            __('Wallet Migration', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-migration',
            array($this, 'render_migration_page')
        );

        // User detail page (hidden from menu)
        add_submenu_page(
            null,
            __('User Details', 'globalswiftpay-dashboard'),
            __('User Details', 'globalswiftpay-dashboard'),
            'manage_options',
            'globalswiftpay-user-detail',
            array($this, 'render_user_detail_page')
        );
    }
    
    /**
     * Render admin dashboard page
     */
    public function render_dashboard_page() {
        $pending_deposits = count(GSP_Transactions::get_all_deposits('pending'));
        $pending_withdrawals = count(GSP_Transactions::get_all_withdrawals('pending'));
        $pending_transfers = count(GSP_Transactions::get_all_transfers('pending'));
        $pending_conversions = count(GSP_Transactions::get_all_conversions('pending'));
        
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('GlobalSwiftPay Dashboard', 'globalswiftpay-dashboard'); ?></h1>
            
            <div class="gsp-admin-dashboard">
                <div class="gsp-admin-cards">
                    <div class="gsp-admin-card">
                        <div class="gsp-admin-card-icon deposits">
                            <span class="dashicons dashicons-download"></span>
                        </div>
                        <div class="gsp-admin-card-content">
                            <h3><?php echo esc_html($pending_deposits); ?></h3>
                            <p><?php esc_html_e('Pending Deposits', 'globalswiftpay-dashboard'); ?></p>
                        </div>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay-deposits')); ?>" class="gsp-admin-card-link"><?php esc_html_e('View All', 'globalswiftpay-dashboard'); ?></a>
                    </div>
                    
                    <div class="gsp-admin-card">
                        <div class="gsp-admin-card-icon withdrawals">
                            <span class="dashicons dashicons-upload"></span>
                        </div>
                        <div class="gsp-admin-card-content">
                            <h3><?php echo esc_html($pending_withdrawals); ?></h3>
                            <p><?php esc_html_e('Pending Withdrawals', 'globalswiftpay-dashboard'); ?></p>
                        </div>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay-withdrawals')); ?>" class="gsp-admin-card-link"><?php esc_html_e('View All', 'globalswiftpay-dashboard'); ?></a>
                    </div>
                    
                    <div class="gsp-admin-card">
                        <div class="gsp-admin-card-icon transfers">
                            <span class="dashicons dashicons-randomize"></span>
                        </div>
                        <div class="gsp-admin-card-content">
                            <h3><?php echo esc_html($pending_transfers); ?></h3>
                            <p><?php esc_html_e('Pending Transfers', 'globalswiftpay-dashboard'); ?></p>
                        </div>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay-transfers')); ?>" class="gsp-admin-card-link"><?php esc_html_e('View All', 'globalswiftpay-dashboard'); ?></a>
                    </div>
                    
                    <div class="gsp-admin-card">
                        <div class="gsp-admin-card-icon conversions">
                            <span class="dashicons dashicons-update"></span>
                        </div>
                        <div class="gsp-admin-card-content">
                            <h3><?php echo esc_html($pending_conversions); ?></h3>
                            <p><?php esc_html_e('Pending Conversions', 'globalswiftpay-dashboard'); ?></p>
                        </div>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay-conversions')); ?>" class="gsp-admin-card-link"><?php esc_html_e('View All', 'globalswiftpay-dashboard'); ?></a>
                    </div>
                </div>
                
                <div class="gsp-admin-quick-actions">
                    <h2><?php esc_html_e('Quick Actions', 'globalswiftpay-dashboard'); ?></h2>
                    <div class="gsp-admin-actions-grid">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay-settings')); ?>" class="gsp-admin-action-btn">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e('Update Settings', 'globalswiftpay-dashboard'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay-users')); ?>" class="gsp-admin-action-btn">
                            <span class="dashicons dashicons-admin-users"></span>
                            <?php esc_html_e('Manage Users', 'globalswiftpay-dashboard'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render deposits page
     */
    public function render_deposits_page() {
        $deposits = GSP_Transactions::get_all_deposits();
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('Deposit Requests', 'globalswiftpay-dashboard'); ?></h1>
            
            <div class="gsp-admin-table-container">
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('User', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Name', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Receipt', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deposits)): ?>
                            <tr>
                                <td colspan="9" class="gsp-admin-no-data"><?php esc_html_e('No deposit requests found.', 'globalswiftpay-dashboard'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($deposits as $deposit): ?>
                                <tr data-id="<?php echo esc_attr($deposit->id); ?>">
                                    <td><?php echo esc_html($deposit->id); ?></td>
                                    <td><?php echo esc_html($deposit->display_name ?: $deposit->user_login); ?></td>
                                    <td><?php echo esc_html($deposit->name); ?></td>
                                    <td><?php echo esc_html($deposit->email); ?></td>
                                    <td>$<?php echo esc_html(number_format($deposit->amount, 2)); ?></td>
                                    <td>
                                        <?php if ($deposit->receipt_path): ?>
                                            <a href="<?php echo esc_url($deposit->receipt_path); ?>" target="_blank" class="gsp-view-receipt"><?php esc_html_e('View', 'globalswiftpay-dashboard'); ?></a>
                                        <?php else: ?>
                                            <span class="gsp-no-receipt"><?php esc_html_e('None', 'globalswiftpay-dashboard'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($deposit->status); ?>"><?php echo esc_html(ucfirst($deposit->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($deposit->created_at))); ?></td>
                                    <td class="gsp-admin-actions">
                                        <?php if ($deposit->status === 'pending'): ?>
                                            <button class="gsp-admin-btn gsp-btn-approve" data-action="approve" data-type="deposit" data-id="<?php echo esc_attr($deposit->id); ?>"><?php esc_html_e('Approve', 'globalswiftpay-dashboard'); ?></button>
                                            <button class="gsp-admin-btn gsp-btn-decline" data-action="decline" data-type="deposit" data-id="<?php echo esc_attr($deposit->id); ?>"><?php esc_html_e('Decline', 'globalswiftpay-dashboard'); ?></button>
                                        <?php else: ?>
                                            <span class="gsp-action-completed"><?php echo esc_html(ucfirst($deposit->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render withdrawals page
     */
    public function render_withdrawals_page() {
        $withdrawals = GSP_Transactions::get_all_withdrawals();
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('Withdrawal Requests', 'globalswiftpay-dashboard'); ?></h1>
            
            <div class="gsp-admin-table-container">
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('User', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Method', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Details', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($withdrawals)): ?>
                            <tr>
                                <td colspan="9" class="gsp-admin-no-data"><?php esc_html_e('No withdrawal requests found.', 'globalswiftpay-dashboard'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($withdrawals as $withdrawal): ?>
                                <?php $details = maybe_unserialize($withdrawal->details); ?>
                                <tr data-id="<?php echo esc_attr($withdrawal->id); ?>">
                                    <td><?php echo esc_html($withdrawal->id); ?></td>
                                    <td><?php echo esc_html($withdrawal->display_name ?: $withdrawal->user_login); ?></td>
                                    <td><?php echo esc_html($withdrawal->user_email); ?></td>
                                    <td>$<?php echo esc_html(number_format($withdrawal->amount, 2)); ?></td>
                                    <td><?php echo esc_html(ucfirst($withdrawal->withdrawal_method)); ?></td>
                                    <td>
                                        <button class="gsp-view-details-btn" data-details="<?php echo esc_attr(wp_json_encode($details)); ?>"><?php esc_html_e('View', 'globalswiftpay-dashboard'); ?></button>
                                    </td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($withdrawal->status); ?>"><?php echo esc_html(ucfirst($withdrawal->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($withdrawal->created_at))); ?></td>
                                    <td class="gsp-admin-actions">
                                        <?php if ($withdrawal->status === 'pending'): ?>
                                            <button class="gsp-admin-btn gsp-btn-approve" data-action="approve" data-type="withdrawal" data-id="<?php echo esc_attr($withdrawal->id); ?>"><?php esc_html_e('Approve', 'globalswiftpay-dashboard'); ?></button>
                                            <button class="gsp-admin-btn gsp-btn-decline" data-action="decline" data-type="withdrawal" data-id="<?php echo esc_attr($withdrawal->id); ?>"><?php esc_html_e('Decline', 'globalswiftpay-dashboard'); ?></button>
                                        <?php else: ?>
                                            <span class="gsp-action-completed"><?php echo esc_html(ucfirst($withdrawal->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render transfers page
     */
    public function render_transfers_page() {
        $transfers = GSP_Transactions::get_all_transfers();
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('Transfer Requests', 'globalswiftpay-dashboard'); ?></h1>
            
            <div class="gsp-admin-table-container">
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('From', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('To', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Token Code', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transfers)): ?>
                            <tr>
                                <td colspan="8" class="gsp-admin-no-data"><?php esc_html_e('No transfer requests found.', 'globalswiftpay-dashboard'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transfers as $transfer): ?>
                                <tr data-id="<?php echo esc_attr($transfer->id); ?>">
                                    <td><?php echo esc_html($transfer->id); ?></td>
                                    <td><?php echo esc_html($transfer->from_name); ?><br><small><?php echo esc_html($transfer->from_email); ?></small></td>
                                    <td><?php echo esc_html($transfer->to_name); ?><br><small><?php echo esc_html($transfer->to_email); ?></small></td>
                                    <td>$<?php echo esc_html(number_format($transfer->amount, 2)); ?></td>
                                    <td><code><?php echo esc_html($transfer->token_code); ?></code></td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($transfer->status); ?>"><?php echo esc_html(ucfirst($transfer->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($transfer->created_at))); ?></td>
                                    <td class="gsp-admin-actions">
                                        <?php if ($transfer->status === 'pending'): ?>
                                            <button class="gsp-admin-btn gsp-btn-approve" data-action="approve" data-type="transfer" data-id="<?php echo esc_attr($transfer->id); ?>"><?php esc_html_e('Approve', 'globalswiftpay-dashboard'); ?></button>
                                            <button class="gsp-admin-btn gsp-btn-decline" data-action="decline" data-type="transfer" data-id="<?php echo esc_attr($transfer->id); ?>"><?php esc_html_e('Decline', 'globalswiftpay-dashboard'); ?></button>
                                        <?php else: ?>
                                            <span class="gsp-action-completed"><?php echo esc_html(ucfirst($transfer->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render conversions page
     */
    public function render_conversions_page() {
        $conversions = GSP_Transactions::get_all_conversions();
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('Conversion Requests', 'globalswiftpay-dashboard'); ?></h1>
            
            <div class="gsp-admin-table-container">
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('User', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Type', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Details', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($conversions)): ?>
                            <tr>
                                <td colspan="9" class="gsp-admin-no-data"><?php esc_html_e('No conversion requests found.', 'globalswiftpay-dashboard'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($conversions as $conversion): ?>
                                <?php 
                                $details = array(
                                    'destination_address' => $conversion->destination_address,
                                    'bank_details' => maybe_unserialize($conversion->bank_details),
                                    'security_phrase' => $conversion->security_phrase
                                );
                                ?>
                                <tr data-id="<?php echo esc_attr($conversion->id); ?>">
                                    <td><?php echo esc_html($conversion->id); ?></td>
                                    <td><?php echo esc_html($conversion->display_name ?: $conversion->user_login); ?></td>
                                    <td><?php echo esc_html($conversion->email); ?></td>
                                    <td><span class="gsp-conversion-type gsp-type-<?php echo esc_attr($conversion->conversion_type); ?>"><?php echo esc_html(strtoupper($conversion->conversion_type)); ?></span></td>
                                    <td>$<?php echo esc_html(number_format($conversion->amount, 2)); ?></td>
                                    <td>
                                        <button class="gsp-view-details-btn" data-details="<?php echo esc_attr(wp_json_encode($details)); ?>"><?php esc_html_e('View', 'globalswiftpay-dashboard'); ?></button>
                                    </td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($conversion->status); ?>"><?php echo esc_html(ucfirst($conversion->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($conversion->created_at))); ?></td>
                                    <td class="gsp-admin-actions">
                                        <?php if ($conversion->status === 'pending'): ?>
                                            <button class="gsp-admin-btn gsp-btn-approve" data-action="approve" data-type="conversion" data-id="<?php echo esc_attr($conversion->id); ?>"><?php esc_html_e('Approve', 'globalswiftpay-dashboard'); ?></button>
                                            <button class="gsp-admin-btn gsp-btn-decline" data-action="decline" data-type="conversion" data-id="<?php echo esc_attr($conversion->id); ?>"><?php esc_html_e('Decline', 'globalswiftpay-dashboard'); ?></button>
                                        <?php else: ?>
                                            <span class="gsp-action-completed"><?php echo esc_html(ucfirst($conversion->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render users page
     */
    public function render_users_page() {
        global $wpdb;
        
        // Get filter parameters
        $search_email = isset($_GET['search_email']) ? sanitize_email($_GET['search_email']) : '';
        $search_username = isset($_GET['search_username']) ? sanitize_user($_GET['search_username']) : '';
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 50;
        
        // Build user query arguments
        $args = array(
            'orderby' => 'user_login',
            'order' => 'ASC',
            'number' => $per_page,
            'offset' => ($current_page - 1) * $per_page,
        );
        
        // Apply search filters
        if (!empty($search_email) || !empty($search_username)) {
            $meta_query = array();
            
            if (!empty($search_email)) {
                $args['search'] = '*' . $search_email . '*';
                $args['search_columns'] = array('user_email');
            }
            
            if (!empty($search_username)) {
                $args['search'] = '*' . $search_username . '*';
                $args['search_columns'] = array('user_login');
            }
            
            // If both filters, we need a custom approach
            if (!empty($search_email) && !empty($search_username)) {
                $args['search'] = '*' . $search_username . '*';
                $args['search_columns'] = array('user_login', 'user_email');
            }
        }
        
        // Get total users for pagination (without limit)
        $count_args = $args;
        unset($count_args['number']);
        unset($count_args['offset']);
        $count_args['count_total'] = true;
        $count_args['fields'] = 'ID';
        $total_users = count(get_users($count_args));
        $total_pages = ceil($total_users / $per_page);
        
        // Get users for current page
        $users = get_users($args);
        
        // Build current URL for pagination
        $base_url = admin_url('admin.php?page=globalswiftpay-users');
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('User Balances', 'globalswiftpay-dashboard'); ?></h1>
            
            <!-- Filter Form -->
            <div class="gsp-admin-filters" style="margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 5px;">
                <form method="get" action="<?php echo esc_url($base_url); ?>" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                    <input type="hidden" name="page" value="globalswiftpay-users">
                    
                    <div class="gsp-filter-group">
                        <label for="search_username" style="display: block; margin-bottom: 5px; font-weight: 500;"><?php esc_html_e('Username', 'globalswiftpay-dashboard'); ?></label>
                        <input type="text" id="search_username" name="search_username" value="<?php echo esc_attr($search_username); ?>" placeholder="<?php esc_attr_e('Search by username...', 'globalswiftpay-dashboard'); ?>" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-width: 200px;">
                    </div>
                    
                    <div class="gsp-filter-group">
                        <label for="search_email" style="display: block; margin-bottom: 5px; font-weight: 500;"><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></label>
                        <input type="text" id="search_email" name="search_email" value="<?php echo esc_attr($search_email); ?>" placeholder="<?php esc_attr_e('Search by email...', 'globalswiftpay-dashboard'); ?>" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-width: 200px;">
                    </div>
                    
                    <button type="submit" class="gsp-admin-btn gsp-btn-save"><?php esc_html_e('Filter', 'globalswiftpay-dashboard'); ?></button>
                    
                    <?php if (!empty($search_email) || !empty($search_username)): ?>
                    <a href="<?php echo esc_url($base_url); ?>" class="gsp-admin-btn gsp-btn-decline"><?php esc_html_e('Clear Filters', 'globalswiftpay-dashboard'); ?></a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="gsp-admin-actions-bar" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <button class="gsp-admin-btn gsp-btn-approve" id="gsp-add-user-btn">
                    <span class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></span>
                    <?php esc_html_e('Add New User', 'globalswiftpay-dashboard'); ?>
                </button>
                
                <div class="gsp-pagination-info">
                    <?php 
                    $start = (($current_page - 1) * $per_page) + 1;
                    $end = min($current_page * $per_page, $total_users);
                    printf(
                        esc_html__('Showing %1$d-%2$d of %3$d users (sorted A-Z)', 'globalswiftpay-dashboard'),
                        $start,
                        $end,
                        $total_users
                    );
                    ?>
                </div>
            </div>
            
            <div class="gsp-admin-table-container">
                <table class="gsp-admin-table gsp-users-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Username', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Display Name', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Phone', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Country', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('GSP Account', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Wallet Balance', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Savings Balance', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 20px;"><?php esc_html_e('No users found matching your criteria.', 'globalswiftpay-dashboard'); ?></td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <?php 
                            $balance = GSP_User::get_balance($user->ID);
                            $user_phone = get_user_meta($user->ID, 'gsp_mobile', true);
                            $user_country = get_user_meta($user->ID, 'gsp_country', true);
                            $user_account = get_user_meta($user->ID, 'gsp_account_number', true);
                            ?>
                            <tr data-user-id="<?php echo esc_attr($user->ID); ?>">
                                <td><?php echo esc_html($user->ID); ?></td>
                                <td class="gsp-user-login"><a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay-user-detail&user_id=' . $user->ID)); ?>"><?php echo esc_html($user->user_login); ?></a></td>
                                <td class="gsp-user-email"><?php echo esc_html($user->user_email); ?></td>
                                <td class="gsp-user-display-name"><?php echo esc_html($user->display_name); ?></td>
                                <td><?php echo $user_phone ? esc_html($user_phone) : '<span style="color: #a0aec0;">—</span>'; ?></td>
                                <td><?php echo $user_country ? esc_html($user_country) : '<span style="color: #a0aec0;">—</span>'; ?></td>
                                <td><?php echo $user_account ? esc_html($user_account) : '<span style="color: #a0aec0;">—</span>'; ?></td>
                                <td class="gsp-user-wallet-balance">$<?php echo esc_html(number_format($balance->wallet_balance, 2)); ?></td>
                                <td class="gsp-user-savings-balance">$<?php echo esc_html(number_format($balance->savings_balance, 2)); ?></td>
                                <td class="gsp-user-actions-cell">
                                    <div class="gsp-user-actions">
                                        <button class="gsp-admin-btn gsp-btn-approve gsp-btn-add-balance" data-user-id="<?php echo esc_attr($user->ID); ?>" data-username="<?php echo esc_attr($user->user_login); ?>"><?php esc_html_e('Add Balance', 'globalswiftpay-dashboard'); ?></button>
                                        <button class="gsp-admin-btn gsp-btn-decline gsp-btn-deduct-balance" data-user-id="<?php echo esc_attr($user->ID); ?>" data-username="<?php echo esc_attr($user->user_login); ?>" data-balance="<?php echo esc_attr($balance->wallet_balance); ?>" data-savings="<?php echo esc_attr($balance->savings_balance); ?>"><?php esc_html_e('Deduct Balance', 'globalswiftpay-dashboard'); ?></button>
                                        <button class="gsp-admin-btn gsp-btn-edit-balance" data-user-id="<?php echo esc_attr($user->ID); ?>" data-balance="<?php echo esc_attr($balance->wallet_balance); ?>" data-savings="<?php echo esc_attr($balance->savings_balance); ?>"><?php esc_html_e('Edit Balance', 'globalswiftpay-dashboard'); ?></button>
                                        <button class="gsp-admin-btn gsp-btn-save gsp-btn-edit-user" data-user-id="<?php echo esc_attr($user->ID); ?>" data-username="<?php echo esc_attr($user->user_login); ?>" data-email="<?php echo esc_attr($user->user_email); ?>" data-display-name="<?php echo esc_attr($user->display_name); ?>"><?php esc_html_e('Edit User', 'globalswiftpay-dashboard'); ?></button>
                                        <?php if ($user->ID !== get_current_user_id()): ?>
                                        <button class="gsp-admin-btn gsp-btn-decline gsp-btn-delete-user" data-user-id="<?php echo esc_attr($user->ID); ?>" data-username="<?php echo esc_attr($user->user_login); ?>"><?php esc_html_e('Delete', 'globalswiftpay-dashboard'); ?></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="gsp-pagination" style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                <?php
                // Build pagination URL with current filters
                $pagination_url = $base_url;
                if (!empty($search_username)) {
                    $pagination_url = add_query_arg('search_username', $search_username, $pagination_url);
                }
                if (!empty($search_email)) {
                    $pagination_url = add_query_arg('search_email', $search_email, $pagination_url);
                }
                
                // Previous button
                if ($current_page > 1): ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $current_page - 1, $pagination_url)); ?>" class="gsp-admin-btn" style="padding: 8px 12px;">&laquo; <?php esc_html_e('Previous', 'globalswiftpay-dashboard'); ?></a>
                <?php endif; ?>
                
                <?php
                // Page numbers
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if ($start_page > 1): ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', 1, $pagination_url)); ?>" class="gsp-admin-btn" style="padding: 8px 12px;">1</a>
                    <?php if ($start_page > 2): ?>
                        <span style="padding: 8px;">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="gsp-admin-btn gsp-btn-approve" style="padding: 8px 12px;"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo esc_url(add_query_arg('paged', $i, $pagination_url)); ?>" class="gsp-admin-btn" style="padding: 8px 12px;"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span style="padding: 8px;">...</span>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $total_pages, $pagination_url)); ?>" class="gsp-admin-btn" style="padding: 8px 12px;"><?php echo $total_pages; ?></a>
                <?php endif; ?>
                
                <?php // Next button
                if ($current_page < $total_pages): ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $current_page + 1, $pagination_url)); ?>" class="gsp-admin-btn" style="padding: 8px 12px;"><?php esc_html_e('Next', 'globalswiftpay-dashboard'); ?> &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Edit Balance Modal -->
        <div id="gsp-edit-balance-modal" class="gsp-modal" style="display: none;">
            <div class="gsp-modal-content">
                <span class="gsp-modal-close">&times;</span>
                <h2><?php esc_html_e('Edit User Balance', 'globalswiftpay-dashboard'); ?></h2>
                <form id="gsp-edit-balance-form">
                    <input type="hidden" id="edit-balance-user-id" name="user_id" value="">
                    <div class="gsp-form-group">
                        <label for="edit-balance-amount"><?php esc_html_e('Wallet Balance ($)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="number" id="edit-balance-amount" name="balance" step="0.01" min="0" required>
                    </div>
                    <div class="gsp-form-group">
                        <label for="edit-savings-amount"><?php esc_html_e('Savings Balance ($)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="number" id="edit-savings-amount" name="savings_balance" step="0.01" min="0" value="0">
                    </div>
                    <button type="submit" class="gsp-admin-btn gsp-btn-save"><?php esc_html_e('Save Balance', 'globalswiftpay-dashboard'); ?></button>
                </form>
            </div>
        </div>
        
        <!-- Add Balance Modal -->
        <div id="gsp-add-balance-modal" class="gsp-modal" style="display: none;">
            <div class="gsp-modal-content">
                <span class="gsp-modal-close">&times;</span>
                <h2><?php esc_html_e('Add Balance', 'globalswiftpay-dashboard'); ?></h2>
                <p id="gsp-add-balance-username" style="color: #666; margin-bottom: 15px;"></p>
                <form id="gsp-add-balance-form">
                    <input type="hidden" id="add-balance-user-id" name="user_id" value="">
                    <div class="gsp-form-group">
                        <label for="add-balance-wallet-amount"><?php esc_html_e('Amount to Add to Wallet ($)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="number" id="add-balance-wallet-amount" name="wallet_amount" step="0.01" min="0" required>
                    </div>
                    <div class="gsp-form-group">
                        <label for="add-balance-savings-amount"><?php esc_html_e('Amount to Add to Savings ($)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="number" id="add-balance-savings-amount" name="savings_amount" step="0.01" min="0" value="0">
                    </div>
                    <button type="submit" class="gsp-admin-btn gsp-btn-approve"><?php esc_html_e('Add Balance', 'globalswiftpay-dashboard'); ?></button>
                </form>
            </div>
        </div>
        
        <!-- Deduct Balance Modal -->
        <div id="gsp-deduct-balance-modal" class="gsp-modal" style="display: none;">
            <div class="gsp-modal-content">
                <span class="gsp-modal-close">&times;</span>
                <h2><?php esc_html_e('Deduct Balance', 'globalswiftpay-dashboard'); ?></h2>
                <p id="gsp-deduct-balance-username" style="color: #666; margin-bottom: 15px;"></p>
                <form id="gsp-deduct-balance-form">
                    <input type="hidden" id="deduct-balance-user-id" name="user_id" value="">
                    <div class="gsp-form-group">
                        <label for="deduct-balance-wallet-amount"><?php esc_html_e('Amount to Deduct from Wallet ($)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="number" id="deduct-balance-wallet-amount" name="wallet_amount" step="0.01" min="0" required>
                        <p class="gsp-settings-description" id="deduct-wallet-max" style="margin-top: 4px;"></p>
                    </div>
                    <div class="gsp-form-group">
                        <label for="deduct-balance-savings-amount"><?php esc_html_e('Amount to Deduct from Savings ($)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="number" id="deduct-balance-savings-amount" name="savings_amount" step="0.01" min="0" value="0">
                        <p class="gsp-settings-description" id="deduct-savings-max" style="margin-top: 4px;"></p>
                    </div>
                    <button type="submit" class="gsp-admin-btn gsp-btn-decline"><?php esc_html_e('Deduct Balance', 'globalswiftpay-dashboard'); ?></button>
                </form>
            </div>
        </div>
        
        <!-- Edit User Modal -->
        <div id="gsp-edit-user-modal" class="gsp-modal" style="display: none;">
            <div class="gsp-modal-content">
                <span class="gsp-modal-close">&times;</span>
                <h2><?php esc_html_e('Edit User', 'globalswiftpay-dashboard'); ?></h2>
                <form id="gsp-edit-user-form">
                    <input type="hidden" id="edit-user-id" name="user_id" value="">
                    <div class="gsp-form-group">
                        <label for="edit-user-email"><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></label>
                        <input type="email" id="edit-user-email" name="email" required>
                    </div>
                    <div class="gsp-form-group">
                        <label for="edit-user-display-name"><?php esc_html_e('Display Name', 'globalswiftpay-dashboard'); ?></label>
                        <input type="text" id="edit-user-display-name" name="display_name" required>
                    </div>
                    <div class="gsp-form-group">
                        <label for="edit-user-password"><?php esc_html_e('New Password (leave empty to keep current)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="password" id="edit-user-password" name="password">
                    </div>
                    <button type="submit" class="gsp-admin-btn gsp-btn-save"><?php esc_html_e('Update User', 'globalswiftpay-dashboard'); ?></button>
                </form>
            </div>
        </div>
        
        <!-- Add User Modal -->
        <div id="gsp-add-user-modal" class="gsp-modal" style="display: none;">
            <div class="gsp-modal-content">
                <span class="gsp-modal-close">&times;</span>
                <h2><?php esc_html_e('Add New User', 'globalswiftpay-dashboard'); ?></h2>
                <form id="gsp-add-user-form">
                    <div class="gsp-form-group">
                        <label for="add-user-username"><?php esc_html_e('Username', 'globalswiftpay-dashboard'); ?></label>
                        <input type="text" id="add-user-username" name="username" required>
                    </div>
                    <div class="gsp-form-group">
                        <label for="add-user-email"><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></label>
                        <input type="email" id="add-user-email" name="email" required>
                    </div>
                    <div class="gsp-form-group">
                        <label for="add-user-display-name"><?php esc_html_e('Display Name', 'globalswiftpay-dashboard'); ?></label>
                        <input type="text" id="add-user-display-name" name="display_name">
                    </div>
                    <div class="gsp-form-group">
                        <label for="add-user-password"><?php esc_html_e('Password', 'globalswiftpay-dashboard'); ?></label>
                        <input type="password" id="add-user-password" name="password" required>
                    </div>
                    <div class="gsp-form-group">
                        <label for="add-user-balance"><?php esc_html_e('Initial Wallet Balance ($)', 'globalswiftpay-dashboard'); ?></label>
                        <input type="number" id="add-user-balance" name="balance" step="0.01" min="0" value="0">
                    </div>
                    <button type="submit" class="gsp-admin-btn gsp-btn-approve"><?php esc_html_e('Create User', 'globalswiftpay-dashboard'); ?></button>
                </form>
            </div>
        </div>
        
        <!-- Delete User Confirmation Modal -->
        <div id="gsp-delete-user-modal" class="gsp-modal" style="display: none;">
            <div class="gsp-modal-content">
                <span class="gsp-modal-close">&times;</span>
                <h2><?php esc_html_e('Confirm Delete User', 'globalswiftpay-dashboard'); ?></h2>
                <p id="gsp-delete-user-message"></p>
                <form id="gsp-delete-user-form">
                    <input type="hidden" id="delete-user-id" name="user_id" value="">
                    <button type="button" class="gsp-admin-btn gsp-btn-save gsp-modal-close"><?php esc_html_e('Cancel', 'globalswiftpay-dashboard'); ?></button>
                    <button type="submit" class="gsp-admin-btn gsp-btn-decline"><?php esc_html_e('Delete User', 'globalswiftpay-dashboard'); ?></button>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        $settings = GSP_Database::get_all_settings();
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('GlobalSwiftPay Settings', 'globalswiftpay-dashboard'); ?></h1>
            
            <div class="gsp-admin-settings">
                <form id="gsp-settings-form" class="gsp-settings-form">
                    <div class="gsp-settings-section">
                        <h2><?php esc_html_e('Bank Account Details', 'globalswiftpay-dashboard'); ?></h2>
                        <p class="gsp-settings-description"><?php esc_html_e('This account information will be shown to users when they want to add balance.', 'globalswiftpay-dashboard'); ?></p>
                        
                        <div class="gsp-form-group">
                            <label for="account_number"><?php esc_html_e('Account Number', 'globalswiftpay-dashboard'); ?></label>
                            <input type="text" id="account_number" name="settings[account_number]" value="<?php echo esc_attr($settings['account_number'] ?? ''); ?>" class="gsp-input">
                        </div>
                        
                        <div class="gsp-form-group">
                            <label for="account_name"><?php esc_html_e('Account Name', 'globalswiftpay-dashboard'); ?></label>
                            <input type="text" id="account_name" name="settings[account_name]" value="<?php echo esc_attr($settings['account_name'] ?? ''); ?>" class="gsp-input">
                        </div>
                        
                        <div class="gsp-form-group">
                            <label for="bank_name"><?php esc_html_e('Bank Name', 'globalswiftpay-dashboard'); ?></label>
                            <input type="text" id="bank_name" name="settings[bank_name]" value="<?php echo esc_attr($settings['bank_name'] ?? ''); ?>" class="gsp-input">
                        </div>
                    </div>
                    
                    <div class="gsp-settings-section">
                        <h2><?php esc_html_e('Cryptocurrency Addresses', 'globalswiftpay-dashboard'); ?></h2>
                        <p class="gsp-settings-description"><?php esc_html_e('These addresses will be displayed for cryptocurrency deposits.', 'globalswiftpay-dashboard'); ?></p>
                        
                        <div class="gsp-form-group">
                            <label for="btc_address"><?php esc_html_e('Bitcoin (BTC) Address', 'globalswiftpay-dashboard'); ?></label>
                            <input type="text" id="btc_address" name="settings[btc_address]" value="<?php echo esc_attr($settings['btc_address'] ?? ''); ?>" class="gsp-input">
                        </div>
                        
                        <div class="gsp-form-group">
                            <label for="usdt_address"><?php esc_html_e('Tether (USDT) Address', 'globalswiftpay-dashboard'); ?></label>
                            <input type="text" id="usdt_address" name="settings[usdt_address]" value="<?php echo esc_attr($settings['usdt_address'] ?? ''); ?>" class="gsp-input">
                        </div>
                    </div>
                    
                    <div class="gsp-settings-section">
                        <h2><?php esc_html_e('Contact Information', 'globalswiftpay-dashboard'); ?></h2>
                        
                        <div class="gsp-form-group">
                            <label for="company_email"><?php esc_html_e('Company Email', 'globalswiftpay-dashboard'); ?></label>
                            <input type="email" id="company_email" name="settings[company_email]" value="<?php echo esc_attr($settings['company_email'] ?? ''); ?>" class="gsp-input">
                        </div>
                    </div>
                    
                    <div class="gsp-settings-section">
                        <h2><?php esc_html_e('Site Settings', 'globalswiftpay-dashboard'); ?></h2>
                        
                        <div class="gsp-form-group">
                            <label for="logout_redirect_url"><?php esc_html_e('Logout Redirect URL', 'globalswiftpay-dashboard'); ?></label>
                            <input type="url" id="logout_redirect_url" name="settings[logout_redirect_url]" value="<?php echo esc_attr($settings['logout_redirect_url'] ?? 'https://globalswiftpay2.com/gsp2-login/'); ?>" class="gsp-input" placeholder="https://globalswiftpay2.com/gsp2-login/">
                            <p class="gsp-settings-description"><?php esc_html_e('URL to redirect users after logout. Leave empty for site home page.', 'globalswiftpay-dashboard'); ?></p>
                        </div>
                    </div>
                    
                    <button type="submit" class="gsp-admin-btn gsp-btn-save"><?php esc_html_e('Save Settings', 'globalswiftpay-dashboard'); ?></button>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render wallet migration page
     */
    public function render_migration_page() {
        ?>
        <div class="wrap gsp-admin-wrap">
            <h1><?php esc_html_e('Wallet Migration', 'globalswiftpay-dashboard'); ?></h1>
            <p class="gsp-settings-description"><?php esc_html_e('Detect wallet balances from Wallet System for WooCommerce and migrate them into GlobalSwiftPay balances.', 'globalswiftpay-dashboard'); ?></p>

            <div class="gsp-admin-settings">
                <form id="gsp-wallet-migration-form" class="gsp-settings-form">
                    <div class="gsp-form-group">
                        <label for="gsp-wallet-source"><?php esc_html_e('Detected Source', 'globalswiftpay-dashboard'); ?></label>
                        <select id="gsp-wallet-source" name="wallet_source" class="gsp-input gsp-select">
                            <option value=""><?php esc_html_e('Select a source', 'globalswiftpay-dashboard'); ?></option>
                        </select>
                        <p class="gsp-settings-description"><?php esc_html_e('Use Detect Sources to scan available balance tables and meta keys.', 'globalswiftpay-dashboard'); ?></p>
                    </div>

                    <div class="gsp-form-group">
                        <button type="button" class="gsp-admin-btn gsp-btn-save" id="gsp-detect-wallet-sources"><?php esc_html_e('Detect Sources', 'globalswiftpay-dashboard'); ?></button>
                        <button type="submit" class="gsp-admin-btn gsp-btn-approve"><?php esc_html_e('Run Migration', 'globalswiftpay-dashboard'); ?></button>
                        <button type="button" class="gsp-admin-btn gsp-btn-approve" id="gsp-migrate-all-sources"><?php esc_html_e('Migrate All Sources', 'globalswiftpay-dashboard'); ?></button>
                    </div>
                </form>
            </div>
            
            <hr style="margin: 30px 0;">
            
            <h2><?php esc_html_e('CSV Balance Import', 'globalswiftpay-dashboard'); ?></h2>
            <p class="gsp-settings-description"><?php esc_html_e('Import user balances from a CSV file. The CSV should have columns: email, balance (or user_email, wallet_balance).', 'globalswiftpay-dashboard'); ?></p>
            
            <div class="gsp-admin-settings">
                <form id="gsp-csv-import-form" class="gsp-settings-form" enctype="multipart/form-data">
                    <div class="gsp-form-group">
                        <label for="gsp-csv-file"><?php esc_html_e('CSV File', 'globalswiftpay-dashboard'); ?></label>
                        <input type="file" id="gsp-csv-file" name="csv_file" accept=".csv" class="gsp-input" required>
                        <p class="gsp-settings-description">
                            <?php esc_html_e('CSV format: email,balance (one user per line). Example:', 'globalswiftpay-dashboard'); ?><br>
                            <code>user@example.com,1000.00</code>
                        </p>
                    </div>
                    
                    <div class="gsp-form-group">
                        <button type="submit" class="gsp-admin-btn gsp-btn-approve"><?php esc_html_e('Import Balances from CSV', 'globalswiftpay-dashboard'); ?></button>
                    </div>
                </form>
                
                <div id="gsp-csv-import-results" style="display: none; margin-top: 20px;">
                    <h3><?php esc_html_e('Import Results', 'globalswiftpay-dashboard'); ?></h3>
                    <div id="gsp-csv-import-message"></div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render user detail page
     */
    public function render_user_detail_page() {
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if (!$user_id) {
            echo '<div class="wrap"><p>' . esc_html__('Invalid user ID.', 'globalswiftpay-dashboard') . '</p></div>';
            return;
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            echo '<div class="wrap"><p>' . esc_html__('User not found.', 'globalswiftpay-dashboard') . '</p></div>';
            return;
        }
        
        $balance = GSP_User::get_balance($user_id);
        
        // Get user registration meta (from upgrade form / Registration Magic)
        $user_phone = get_user_meta($user_id, 'gsp_mobile', true);
        $user_country = get_user_meta($user_id, 'gsp_country', true);
        $user_account = get_user_meta($user_id, 'gsp_account_number', true);
        $user_tier2_approved = get_user_meta($user_id, 'gsp_tier2_approved', true);
        
        // Get user's deposits, withdrawals, transfers
        global $wpdb;
        $deposits_table = $wpdb->prefix . 'gsp_deposits';
        $withdrawals_table = $wpdb->prefix . 'gsp_withdrawals';
        $transfers_table = $wpdb->prefix . 'gsp_transfers';
        
        // Suppress errors if tables don't exist yet
        $wpdb->suppress_errors(true);
        
        $deposits = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$deposits_table} WHERE user_id = %d ORDER BY created_at DESC LIMIT 10",
            $user_id
        ));
        
        $withdrawals = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$withdrawals_table} WHERE user_id = %d ORDER BY created_at DESC LIMIT 10",
            $user_id
        ));
        
        $transfers_sent = $wpdb->get_results($wpdb->prepare(
            "SELECT t.*, u.display_name as to_name FROM {$transfers_table} t LEFT JOIN {$wpdb->users} u ON t.to_user_id = u.ID WHERE t.from_user_id = %d ORDER BY t.created_at DESC LIMIT 10",
            $user_id
        ));
        
        $transfers_received = $wpdb->get_results($wpdb->prepare(
            "SELECT t.*, u.display_name as from_name FROM {$transfers_table} t LEFT JOIN {$wpdb->users} u ON t.from_user_id = u.ID WHERE t.to_user_id = %d ORDER BY t.created_at DESC LIMIT 10",
            $user_id
        ));
        
        $wpdb->suppress_errors(false);
        
        // Ensure arrays if queries failed
        if (!is_array($deposits)) $deposits = array();
        if (!is_array($withdrawals)) $withdrawals = array();
        if (!is_array($transfers_sent)) $transfers_sent = array();
        if (!is_array($transfers_received)) $transfers_received = array();
        
        $back_url = admin_url('admin.php?page=globalswiftpay-users');
        ?>
        <div class="wrap gsp-admin-wrap">
            <div style="margin-bottom: 20px;">
                <a href="<?php echo esc_url($back_url); ?>" class="gsp-admin-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                    <span class="dashicons dashicons-arrow-left-alt" style="font-size: 16px; width: 16px; height: 16px;"></span>
                    <?php esc_html_e('Back to Users', 'globalswiftpay-dashboard'); ?>
                </a>
            </div>
            
            <h1><?php printf(esc_html__('User Details: %s', 'globalswiftpay-dashboard'), esc_html($user->display_name)); ?></h1>
            
            <!-- User Info Card -->
            <div class="gsp-admin-cards" style="margin-bottom: 30px;">
                <div class="gsp-admin-card">
                    <div class="gsp-admin-card-icon deposits">
                        <span class="dashicons dashicons-admin-users"></span>
                    </div>
                    <div class="gsp-admin-card-content">
                        <h3 style="font-size: 16px; margin: 0;"><?php echo esc_html($user->display_name); ?></h3>
                        <p style="margin: 5px 0 0; color: #718096; font-size: 13px;"><?php echo esc_html($user->user_email); ?></p>
                        <p style="margin: 5px 0 0; color: #a0aec0; font-size: 12px;">
                            <?php esc_html_e('Username:', 'globalswiftpay-dashboard'); ?> <?php echo esc_html($user->user_login); ?> &bull;
                            <?php esc_html_e('Roles:', 'globalswiftpay-dashboard'); ?> <?php echo esc_html(implode(', ', $user->roles)); ?> &bull;
                            <?php esc_html_e('Registered:', 'globalswiftpay-dashboard'); ?> <?php echo esc_html(date('M j, Y', strtotime($user->user_registered))); ?>
                        </p>
                        <?php if ($user_phone || $user_country || $user_account): ?>
                        <p style="margin: 8px 0 0; color: #4a5568; font-size: 13px;">
                            <?php if ($user_phone): ?>
                                <strong><?php esc_html_e('Phone:', 'globalswiftpay-dashboard'); ?></strong> <?php echo esc_html($user_phone); ?>
                            <?php endif; ?>
                            <?php if ($user_phone && $user_country): ?> &bull; <?php endif; ?>
                            <?php if ($user_country): ?>
                                <strong><?php esc_html_e('Country:', 'globalswiftpay-dashboard'); ?></strong> <?php echo esc_html($user_country); ?>
                            <?php endif; ?>
                            <?php if (($user_phone || $user_country) && $user_account): ?> &bull; <?php endif; ?>
                            <?php if ($user_account): ?>
                                <strong><?php esc_html_e('GSP Account:', 'globalswiftpay-dashboard'); ?></strong> <?php echo esc_html($user_account); ?>
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                        <?php if ($user_tier2_approved): ?>
                        <p style="margin: 4px 0 0; color: #38a169; font-size: 12px;">
                            <strong><?php esc_html_e('Tier 2 Approved:', 'globalswiftpay-dashboard'); ?></strong> <?php echo esc_html(date('M j, Y g:i A', strtotime($user_tier2_approved))); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="gsp-admin-card">
                    <div class="gsp-admin-card-icon withdrawals">
                        <span class="dashicons dashicons-money-alt"></span>
                    </div>
                    <div class="gsp-admin-card-content">
                        <h3 style="font-size: 24px; margin: 0;">$<?php echo esc_html(number_format($balance->wallet_balance, 2)); ?></h3>
                        <p style="margin: 5px 0 0; color: #718096;"><?php esc_html_e('Wallet Balance', 'globalswiftpay-dashboard'); ?></p>
                    </div>
                </div>
                
                <div class="gsp-admin-card">
                    <div class="gsp-admin-card-icon transfers">
                        <span class="dashicons dashicons-vault"></span>
                    </div>
                    <div class="gsp-admin-card-content">
                        <h3 style="font-size: 24px; margin: 0;">$<?php echo esc_html(number_format($balance->savings_balance, 2)); ?></h3>
                        <p style="margin: 5px 0 0; color: #718096;"><?php esc_html_e('Savings Balance', 'globalswiftpay-dashboard'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Deposits -->
            <div class="gsp-admin-table-container" style="margin-bottom: 30px;">
                <h2><?php esc_html_e('Recent Deposits', 'globalswiftpay-dashboard'); ?></h2>
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deposits)): ?>
                            <tr><td colspan="4" class="gsp-admin-no-data"><?php esc_html_e('No deposits found.', 'globalswiftpay-dashboard'); ?></td></tr>
                        <?php else: ?>
                            <?php foreach ($deposits as $deposit): ?>
                                <tr>
                                    <td><?php echo esc_html($deposit->id); ?></td>
                                    <td>$<?php echo esc_html(number_format($deposit->amount, 2)); ?></td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($deposit->status); ?>"><?php echo esc_html(ucfirst($deposit->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($deposit->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Withdrawals -->
            <div class="gsp-admin-table-container" style="margin-bottom: 30px;">
                <h2><?php esc_html_e('Recent Withdrawals', 'globalswiftpay-dashboard'); ?></h2>
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Method', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($withdrawals)): ?>
                            <tr><td colspan="5" class="gsp-admin-no-data"><?php esc_html_e('No withdrawals found.', 'globalswiftpay-dashboard'); ?></td></tr>
                        <?php else: ?>
                            <?php foreach ($withdrawals as $withdrawal): ?>
                                <tr>
                                    <td><?php echo esc_html($withdrawal->id); ?></td>
                                    <td>$<?php echo esc_html(number_format($withdrawal->amount, 2)); ?></td>
                                    <td><?php echo esc_html(ucfirst($withdrawal->withdrawal_method)); ?></td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($withdrawal->status); ?>"><?php echo esc_html(ucfirst($withdrawal->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($withdrawal->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Transfers Sent -->
            <div class="gsp-admin-table-container" style="margin-bottom: 30px;">
                <h2><?php esc_html_e('Transfers Sent', 'globalswiftpay-dashboard'); ?></h2>
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('To', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transfers_sent)): ?>
                            <tr><td colspan="5" class="gsp-admin-no-data"><?php esc_html_e('No transfers sent.', 'globalswiftpay-dashboard'); ?></td></tr>
                        <?php else: ?>
                            <?php foreach ($transfers_sent as $transfer): ?>
                                <tr>
                                    <td><?php echo esc_html($transfer->id); ?></td>
                                    <td><?php echo esc_html($transfer->to_name ?: __('Unknown', 'globalswiftpay-dashboard')); ?></td>
                                    <td>$<?php echo esc_html(number_format($transfer->amount, 2)); ?></td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($transfer->status); ?>"><?php echo esc_html(ucfirst($transfer->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($transfer->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Transfers Received -->
            <div class="gsp-admin-table-container" style="margin-bottom: 30px;">
                <h2><?php esc_html_e('Transfers Received', 'globalswiftpay-dashboard'); ?></h2>
                <table class="gsp-admin-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('From', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transfers_received)): ?>
                            <tr><td colspan="5" class="gsp-admin-no-data"><?php esc_html_e('No transfers received.', 'globalswiftpay-dashboard'); ?></td></tr>
                        <?php else: ?>
                            <?php foreach ($transfers_received as $transfer): ?>
                                <tr>
                                    <td><?php echo esc_html($transfer->id); ?></td>
                                    <td><?php echo esc_html($transfer->from_name ?: __('Unknown', 'globalswiftpay-dashboard')); ?></td>
                                    <td>$<?php echo esc_html(number_format($transfer->amount, 2)); ?></td>
                                    <td><span class="gsp-status gsp-status-<?php echo esc_attr($transfer->status); ?>"><?php echo esc_html(ucfirst($transfer->status)); ?></span></td>
                                    <td><?php echo esc_html(date('M j, Y g:i A', strtotime($transfer->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
