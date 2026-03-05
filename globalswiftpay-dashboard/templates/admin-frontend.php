<?php
/**
 * Frontend Admin Dashboard template for GlobalSwiftPay
 * Allows admin users to manage user balances and accounts from the frontend
 */

if (!defined('ABSPATH')) {
    exit;
}

// Verify admin access
if (!current_user_can('manage_options')) {
    echo '<div class="gsp-access-denied"><p>' . esc_html__('You do not have permission to access this page.', 'globalswiftpay-dashboard') . '</p></div>';
    return;
}

$current_user = wp_get_current_user();

// Get all users with their balances
$users_query = new WP_User_Query(array(
    'orderby' => 'display_name',
    'order' => 'ASC',
    'number' => -1
));
$all_users = $users_query->get_results();

// Get pending counts
$pending_deposits = GSP_Transactions::get_all_deposits('pending');
$pending_withdrawals = GSP_Transactions::get_all_withdrawals('pending');
$pending_transfers = GSP_Transactions::get_all_transfers('pending');
$pending_conversions = GSP_Transactions::get_all_conversions('pending');
?>

<div class="gsp-dashboard gsp-admin-frontend">
    <!-- Noodles Beam Animation Background -->
    <div class="gsp-noodles-container">
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle"></div>
        <div class="gsp-noodle-h"></div>
        <div class="gsp-noodle-h"></div>
        <div class="gsp-noodle-h"></div>
        <div class="gsp-noodle-h"></div>
        <div class="gsp-noodle-h"></div>
    </div>

    <!-- Header Section -->
    <div class="gsp-header">
        <div class="gsp-header-content">
            <div class="gsp-user-info">
                <div class="gsp-avatar">
                    <span class="iconify" data-icon="solar:shield-user-linear" width="48" height="48" style="color: white"></span>
                </div>
                <div class="gsp-user-details">
                    <h2><?php esc_html_e('Admin Panel', 'globalswiftpay-dashboard'); ?></h2>
                    <h1><?php echo esc_html($current_user->display_name); ?></h1>
                </div>
            </div>
            <div class="gsp-header-actions">
                <button class="gsp-theme-toggle" id="gsp-theme-toggle" title="Toggle Dark/Light Mode">
                    <span class="iconify" data-icon="solar:moon-linear" data-theme-icon="dark"></span>
                    <span class="iconify" data-icon="solar:sun-linear" data-theme-icon="light" style="display: none;"></span>
                </button>
                <a href="<?php echo esc_url(home_url('/gsp-dashboard')); ?>" class="gsp-btn gsp-btn-logout">
                    <span class="iconify" data-icon="solar:widget-linear" width="18" height="18"></span>
                    <?php esc_html_e('User Dashboard', 'globalswiftpay-dashboard'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards Section -->
    <div class="gsp-admin-stats-section">
        <div class="gsp-glass-card gsp-stat-card">
            <div class="gsp-card-icon gsp-icon-withdrawals">
                <span class="iconify" data-icon="solar:card-send-linear" width="28" height="28"></span>
            </div>
            <div class="gsp-card-content">
                <h3><?php esc_html_e('Pending Withdrawals', 'globalswiftpay-dashboard'); ?></h3>
                <p class="gsp-stat-number" id="gsp-stat-withdrawals"><?php echo count($pending_withdrawals); ?></p>
            </div>
        </div>
        
        <div class="gsp-glass-card gsp-stat-card">
            <div class="gsp-card-icon gsp-icon-transfers">
                <span class="iconify" data-icon="solar:transfer-horizontal-linear" width="28" height="28"></span>
            </div>
            <div class="gsp-card-content">
                <h3><?php esc_html_e('Pending Transfers', 'globalswiftpay-dashboard'); ?></h3>
                <p class="gsp-stat-number" id="gsp-stat-transfers"><?php echo count($pending_transfers); ?></p>
            </div>
        </div>
        
        <div class="gsp-glass-card gsp-stat-card">
            <div class="gsp-card-icon gsp-icon-conversions">
                <span class="iconify" data-icon="solar:refresh-circle-linear" width="28" height="28"></span>
            </div>
            <div class="gsp-card-content">
                <h3><?php esc_html_e('Pending Conversions', 'globalswiftpay-dashboard'); ?></h3>
                <p class="gsp-stat-number" id="gsp-stat-conversions"><?php echo count($pending_conversions); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Actions for Admin -->
    <div class="gsp-actions-section">
        <h2 class="gsp-section-title"><?php esc_html_e('Quick Actions', 'globalswiftpay-dashboard'); ?></h2>
        <div class="gsp-actions-grid gsp-admin-actions">
            <button class="gsp-glass-btn gsp-action-btn" data-modal="admin-add-user-modal">
                <span class="iconify gsp-btn-icon" data-icon="solar:user-plus-linear" width="32" height="32"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Add User', 'globalswiftpay-dashboard'); ?></span>
            </button>
            
            <button class="gsp-glass-btn gsp-action-btn" data-modal="admin-add-balance-modal">
                <span class="iconify gsp-btn-icon" data-icon="solar:wallet-money-linear" width="32" height="32"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Add Balance', 'globalswiftpay-dashboard'); ?></span>
            </button>
            
            <button class="gsp-glass-btn gsp-action-btn" data-modal="admin-deduct-balance-modal">
                <span class="iconify gsp-btn-icon" data-icon="solar:wallet-linear" width="32" height="32"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Deduct Balance', 'globalswiftpay-dashboard'); ?></span>
            </button>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=globalswiftpay')); ?>" class="gsp-glass-btn gsp-action-btn">
                <span class="iconify gsp-btn-icon" data-icon="solar:settings-linear" width="32" height="32"></span>
                <span class="gsp-btn-text"><?php esc_html_e('WP Admin', 'globalswiftpay-dashboard'); ?></span>
            </a>
        </div>
    </div>

    <!-- Pending Transactions Section -->
    <div class="gsp-pending-section">
        <h2 class="gsp-section-title"><?php esc_html_e('Pending Transactions', 'globalswiftpay-dashboard'); ?></h2>
        
        <!-- Tabs -->
        <div class="gsp-tabs">
            <button class="gsp-tab-btn active" data-tab="deposits-tab">
                <span class="iconify" data-icon="solar:card-receive-linear" width="18" height="18"></span>
                <?php esc_html_e('Deposits', 'globalswiftpay-dashboard'); ?> 
                <span class="gsp-tab-count"><?php echo count($pending_deposits); ?></span>
            </button>
            <button class="gsp-tab-btn" data-tab="withdrawals-tab">
                <span class="iconify" data-icon="solar:card-send-linear" width="18" height="18"></span>
                <?php esc_html_e('Withdrawals', 'globalswiftpay-dashboard'); ?>
                <span class="gsp-tab-count"><?php echo count($pending_withdrawals); ?></span>
            </button>
            <button class="gsp-tab-btn" data-tab="transfers-tab">
                <span class="iconify" data-icon="solar:transfer-horizontal-linear" width="18" height="18"></span>
                <?php esc_html_e('Transfers', 'globalswiftpay-dashboard'); ?>
                <span class="gsp-tab-count"><?php echo count($pending_transfers); ?></span>
            </button>
            <button class="gsp-tab-btn" data-tab="conversions-tab">
                <span class="iconify" data-icon="solar:refresh-circle-linear" width="18" height="18"></span>
                <?php esc_html_e('Conversions', 'globalswiftpay-dashboard'); ?>
                <span class="gsp-tab-count"><?php echo count($pending_conversions); ?></span>
            </button>
        </div>
        
        <!-- Deposits Tab -->
        <div class="gsp-tab-content active" id="deposits-tab">
            <div class="gsp-glass-card gsp-transactions-card">
                <div class="gsp-transactions-table-wrapper">
                    <table class="gsp-transactions-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('User', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_deposits)): ?>
                                <tr><td colspan="4" class="gsp-no-data"><?php esc_html_e('No pending deposits.', 'globalswiftpay-dashboard'); ?></td></tr>
                            <?php else: ?>
                                <?php foreach ($pending_deposits as $deposit): 
                                    // Use display_name from JOIN if available, otherwise fallback to get_userdata
                                    $user_name = !empty($deposit->display_name) ? $deposit->display_name : '';
                                    if (empty($user_name) && !empty($deposit->user_id)) {
                                        $user = get_userdata($deposit->user_id);
                                        $user_name = $user ? $user->display_name : '';
                                    }
                                ?>
                                    <tr data-id="<?php echo esc_attr($deposit->id); ?>" data-type="deposit">
                                        <td><?php echo !empty($user_name) ? esc_html($user_name) : esc_html__('Unknown', 'globalswiftpay-dashboard'); ?></td>
                                        <td>$<?php echo esc_html(number_format($deposit->amount, 2)); ?></td>
                                        <td><?php echo esc_html(date('M j, Y', strtotime($deposit->created_at))); ?></td>
                                        <td class="gsp-actions-cell">
                                            <button class="gsp-action-approve" data-action="approve">
                                                <span class="iconify" data-icon="solar:check-circle-linear" width="18" height="18"></span>
                                            </button>
                                            <button class="gsp-action-decline" data-action="decline">
                                                <span class="iconify" data-icon="solar:close-circle-linear" width="18" height="18"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Withdrawals Tab -->
        <div class="gsp-tab-content" id="withdrawals-tab">
            <div class="gsp-glass-card gsp-transactions-card">
                <div class="gsp-transactions-table-wrapper">
                    <table class="gsp-transactions-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('User', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Method', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_withdrawals)): ?>
                                <tr><td colspan="5" class="gsp-no-data"><?php esc_html_e('No pending withdrawals.', 'globalswiftpay-dashboard'); ?></td></tr>
                            <?php else: ?>
                                <?php foreach ($pending_withdrawals as $withdrawal): 
                                    // Use display_name from JOIN if available, otherwise fallback to get_userdata
                                    $user_name = !empty($withdrawal->display_name) ? $withdrawal->display_name : '';
                                    if (empty($user_name) && !empty($withdrawal->user_id)) {
                                        $user = get_userdata($withdrawal->user_id);
                                        $user_name = $user ? $user->display_name : '';
                                    }
                                    // Get withdrawal method - could be 'method' or 'withdrawal_method' depending on DB
                                    $method = !empty($withdrawal->withdrawal_method) ? $withdrawal->withdrawal_method : (!empty($withdrawal->method) ? $withdrawal->method : 'N/A');
                                ?>
                                    <tr data-id="<?php echo esc_attr($withdrawal->id); ?>" data-type="withdrawal">
                                        <td><?php echo !empty($user_name) ? esc_html($user_name) : esc_html__('Unknown', 'globalswiftpay-dashboard'); ?></td>
                                        <td>$<?php echo esc_html(number_format($withdrawal->amount, 2)); ?></td>
                                        <td><?php echo esc_html(ucfirst($method)); ?></td>
                                        <td><?php echo esc_html(date('M j, Y', strtotime($withdrawal->created_at))); ?></td>
                                        <td class="gsp-actions-cell">
                                            <button class="gsp-action-approve" data-action="approve">
                                                <span class="iconify" data-icon="solar:check-circle-linear" width="18" height="18"></span>
                                            </button>
                                            <button class="gsp-action-decline" data-action="decline">
                                                <span class="iconify" data-icon="solar:close-circle-linear" width="18" height="18"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Transfers Tab -->
        <div class="gsp-tab-content" id="transfers-tab">
            <div class="gsp-glass-card gsp-transactions-card">
                <div class="gsp-transactions-table-wrapper">
                    <table class="gsp-transactions-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('From', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('To', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_transfers)): ?>
                                <tr><td colspan="5" class="gsp-no-data"><?php esc_html_e('No pending transfers.', 'globalswiftpay-dashboard'); ?></td></tr>
                            <?php else: ?>
                                <?php foreach ($pending_transfers as $transfer): 
                                    // Use from_name if available (from JOIN), otherwise get user data
                                    $sender_name = !empty($transfer->from_name) ? $transfer->from_name : '';
                                    $recipient_name = !empty($transfer->to_name) ? $transfer->to_name : '';
                                    if (empty($sender_name) && !empty($transfer->from_user_id)) {
                                        $sender = get_userdata($transfer->from_user_id);
                                        $sender_name = $sender ? $sender->display_name : '';
                                    }
                                    if (empty($recipient_name) && !empty($transfer->to_user_id)) {
                                        $recipient = get_userdata($transfer->to_user_id);
                                        $recipient_name = $recipient ? $recipient->display_name : '';
                                    }
                                ?>
                                    <tr data-id="<?php echo esc_attr($transfer->id); ?>" data-type="transfer">
                                        <td><?php echo !empty($sender_name) ? esc_html($sender_name) : esc_html__('Unknown', 'globalswiftpay-dashboard'); ?></td>
                                        <td><?php echo !empty($recipient_name) ? esc_html($recipient_name) : esc_html__('Unknown', 'globalswiftpay-dashboard'); ?></td>
                                        <td>$<?php echo esc_html(number_format($transfer->amount, 2)); ?></td>
                                        <td><?php echo esc_html(date('M j, Y', strtotime($transfer->created_at))); ?></td>
                                        <td class="gsp-actions-cell">
                                            <button class="gsp-action-approve" data-action="approve">
                                                <span class="iconify" data-icon="solar:check-circle-linear" width="18" height="18"></span>
                                            </button>
                                            <button class="gsp-action-decline" data-action="decline">
                                                <span class="iconify" data-icon="solar:close-circle-linear" width="18" height="18"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Conversions Tab -->
        <div class="gsp-tab-content" id="conversions-tab">
            <div class="gsp-glass-card gsp-transactions-card">
                <div class="gsp-transactions-table-wrapper">
                    <table class="gsp-transactions-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('User', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Type', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                                <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_conversions)): ?>
                                <tr><td colspan="5" class="gsp-no-data"><?php esc_html_e('No pending conversions.', 'globalswiftpay-dashboard'); ?></td></tr>
                            <?php else: ?>
                                <?php foreach ($pending_conversions as $conversion): 
                                    // Use display_name from JOIN if available, otherwise fallback to get_userdata
                                    $user_name = !empty($conversion->display_name) ? $conversion->display_name : '';
                                    if (empty($user_name) && !empty($conversion->user_id)) {
                                        $user = get_userdata($conversion->user_id);
                                        $user_name = $user ? $user->display_name : '';
                                    }
                                ?>
                                    <tr data-id="<?php echo esc_attr($conversion->id); ?>" data-type="conversion">
                                        <td><?php echo !empty($user_name) ? esc_html($user_name) : esc_html__('Unknown', 'globalswiftpay-dashboard'); ?></td>
                                        <td>$<?php echo esc_html(number_format($conversion->amount, 2)); ?></td>
                                        <td><?php echo esc_html(strtoupper($conversion->conversion_type)); ?></td>
                                        <td><?php echo esc_html(date('M j, Y', strtotime($conversion->created_at))); ?></td>
                                        <td class="gsp-actions-cell">
                                            <button class="gsp-action-approve" data-action="approve">
                                                <span class="iconify" data-icon="solar:check-circle-linear" width="18" height="18"></span>
                                            </button>
                                            <button class="gsp-action-decline" data-action="decline">
                                                <span class="iconify" data-icon="solar:close-circle-linear" width="18" height="18"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Section -->
    <div class="gsp-users-section">
        <div class="gsp-section-header">
            <h2 class="gsp-section-title"><?php esc_html_e('User Management', 'globalswiftpay-dashboard'); ?></h2>
            <div class="gsp-users-controls">
                <div class="gsp-filter-box">
                    <select id="gsp-user-status-filter" class="gsp-filter-select">
                        <option value="all"><?php esc_html_e('All Users', 'globalswiftpay-dashboard'); ?></option>
                        <option value="approved"><?php esc_html_e('Approved', 'globalswiftpay-dashboard'); ?></option>
                        <option value="pending"><?php esc_html_e('Pending', 'globalswiftpay-dashboard'); ?></option>
                        <option value="declined"><?php esc_html_e('Declined', 'globalswiftpay-dashboard'); ?></option>
                    </select>
                </div>
                <div class="gsp-search-box">
                    <span class="iconify" data-icon="solar:magnifier-linear" width="18" height="18"></span>
                    <input type="text" id="gsp-user-search" placeholder="<?php esc_attr_e('Search users...', 'globalswiftpay-dashboard'); ?>" class="gsp-search-input">
                </div>
            </div>
        </div>
        
        <div class="gsp-glass-card gsp-users-card">
            <div class="gsp-users-table-wrapper">
                <table class="gsp-users-table" id="gsp-users-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('User', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Phone', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Country', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Wallet Balance', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Savings', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Actions', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="gsp-users-tbody">
                        <?php if (empty($all_users)): ?>
                            <tr>
                                <td colspan="8" class="gsp-no-data"><?php esc_html_e('No users found.', 'globalswiftpay-dashboard'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($all_users as $index => $user): 
                                $balance = GSP_User::get_balance($user->ID);
                                $user_status = get_user_meta($user->ID, 'gsp_user_status', true);
                                $user_phone = get_user_meta($user->ID, 'gsp_mobile', true);
                                $user_country = get_user_meta($user->ID, 'gsp_country', true);
                                
                                // Auto-detect status if not explicitly set
                                if (empty($user_status)) {
                                    // Users with any balance are considered approved
                                    if ($balance->wallet_balance > 0 || $balance->savings_balance > 0) {
                                        $user_status = 'approved';
                                        // Store the status for future use
                                        update_user_meta($user->ID, 'gsp_user_status', 'approved');
                                    }
                                    // Admins are always approved
                                    elseif (user_can($user->ID, 'manage_options')) {
                                        $user_status = 'approved';
                                        update_user_meta($user->ID, 'gsp_user_status', 'approved');
                                    }
                                    // New users without balance are pending
                                    else {
                                        $user_status = 'pending';
                                    }
                                }
                                // Only show first 20 users by default (pagination)
                                $display_style = $index >= 20 ? 'display: none;' : '';
                            ?>
                                <tr class="gsp-user-row" data-user-id="<?php echo esc_attr($user->ID); ?>" 
                                    data-user-email="<?php echo esc_attr($user->user_email); ?>"
                                    data-user-name="<?php echo esc_attr($user->display_name); ?>"
                                    data-user-status="<?php echo esc_attr($user_status); ?>"
                                    data-wallet-balance="<?php echo esc_attr($balance->wallet_balance); ?>"
                                    data-savings-balance="<?php echo esc_attr($balance->savings_balance); ?>"
                                    style="<?php echo esc_attr($display_style); ?>">
                                    <td class="gsp-user-cell">
                                        <div class="gsp-user-avatar">
                                            <?php echo get_avatar($user->ID, 40); ?>
                                        </div>
                                        <div class="gsp-user-info-cell">
                                            <span class="gsp-user-name"><?php echo esc_html($user->display_name); ?></span>
                                            <span class="gsp-user-role"><?php echo esc_html(implode(', ', $user->roles)); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                    <td><?php echo $user_phone ? esc_html($user_phone) : '<span style="color: rgba(255,255,255,0.3);">—</span>'; ?></td>
                                    <td><?php echo $user_country ? esc_html($user_country) : '<span style="color: rgba(255,255,255,0.3);">—</span>'; ?></td>
                                    <td class="gsp-status-cell">
                                        <span class="gsp-user-status gsp-status-<?php echo esc_attr($user_status); ?>">
                                            <?php echo esc_html(ucfirst($user_status)); ?>
                                        </span>
                                    </td>
                                    <td class="gsp-balance-cell">$<?php echo esc_html(number_format($balance->wallet_balance, 2)); ?></td>
                                    <td class="gsp-balance-cell">$<?php echo esc_html(number_format($balance->savings_balance, 2)); ?></td>
                                    <td class="gsp-actions-cell">
                                        <?php if ($user->ID !== get_current_user_id() && $user_status !== 'approved'): ?>
                                        <button class="gsp-icon-btn gsp-approve-user-btn gsp-btn-success" title="<?php esc_attr_e('Approve User', 'globalswiftpay-dashboard'); ?>">
                                            <span class="iconify" data-icon="solar:check-circle-linear" width="18" height="18"></span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($user->ID !== get_current_user_id() && $user_status !== 'declined'): ?>
                                        <button class="gsp-icon-btn gsp-decline-user-btn gsp-btn-warning" title="<?php esc_attr_e('Decline User', 'globalswiftpay-dashboard'); ?>">
                                            <span class="iconify" data-icon="solar:close-circle-linear" width="18" height="18"></span>
                                        </button>
                                        <?php endif; ?>
                                        <button class="gsp-icon-btn gsp-add-balance-btn gsp-btn-success" title="<?php esc_attr_e('Add Balance', 'globalswiftpay-dashboard'); ?>">
                                            <span class="iconify" data-icon="solar:add-circle-linear" width="18" height="18"></span>
                                        </button>
                                        <button class="gsp-icon-btn gsp-deduct-balance-btn gsp-btn-warning" title="<?php esc_attr_e('Deduct Balance', 'globalswiftpay-dashboard'); ?>">
                                            <span class="iconify" data-icon="solar:minus-circle-linear" width="18" height="18"></span>
                                        </button>
                                        <button class="gsp-icon-btn gsp-edit-balance-btn" title="<?php esc_attr_e('Edit Balance', 'globalswiftpay-dashboard'); ?>">
                                            <span class="iconify" data-icon="solar:pen-linear" width="18" height="18"></span>
                                        </button>
                                        <button class="gsp-icon-btn gsp-edit-user-btn" title="<?php esc_attr_e('Edit User', 'globalswiftpay-dashboard'); ?>">
                                            <span class="iconify" data-icon="solar:user-linear" width="18" height="18"></span>
                                        </button>
                                        <?php if ($user->ID !== get_current_user_id()): ?>
                                        <button class="gsp-icon-btn gsp-delete-user-btn gsp-btn-danger" title="<?php esc_attr_e('Delete User', 'globalswiftpay-dashboard'); ?>">
                                            <span class="iconify" data-icon="solar:trash-bin-trash-linear" width="18" height="18"></span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php 
            $total_users = count($all_users);
            $per_page = 20;
            $total_pages = ceil($total_users / $per_page);
            if ($total_pages > 1): 
            ?>
            <div class="gsp-pagination" id="gsp-user-pagination" data-total="<?php echo esc_attr($total_users); ?>" data-per-page="<?php echo esc_attr($per_page); ?>">
                <button class="gsp-page-btn gsp-prev-btn" disabled>
                    <span class="iconify" data-icon="solar:arrow-left-linear" width="18" height="18"></span>
                </button>
                <span class="gsp-page-info">
                    <?php esc_html_e('Page', 'globalswiftpay-dashboard'); ?> 
                    <span id="gsp-current-page">1</span> 
                    <?php esc_html_e('of', 'globalswiftpay-dashboard'); ?> 
                    <span id="gsp-total-pages"><?php echo esc_html($total_pages); ?></span>
                </span>
                <button class="gsp-page-btn gsp-next-btn" <?php echo $total_pages <= 1 ? 'disabled' : ''; ?>>
                    <span class="iconify" data-icon="solar:arrow-right-linear" width="18" height="18"></span>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Add New User -->
<div id="admin-add-user-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Add New User', 'globalswiftpay-dashboard'); ?></h2>
        <form id="gsp-admin-add-user-form" class="gsp-form">
            <div class="gsp-form-group">
                <label for="add-username"><?php esc_html_e('Username', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="add-username" name="username" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="add-email"><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></label>
                <input type="email" id="add-email" name="email" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="add-display-name"><?php esc_html_e('Display Name', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="add-display-name" name="display_name" class="gsp-input">
            </div>
            <div class="gsp-form-group">
                <label for="add-password"><?php esc_html_e('Password', 'globalswiftpay-dashboard'); ?></label>
                <input type="password" id="add-password" name="password" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="add-balance"><?php esc_html_e('Initial Wallet Balance ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="number" id="add-balance" name="balance" class="gsp-input" step="0.01" min="0" value="0">
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Create User', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Add Balance -->
<div id="admin-add-balance-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Add Balance to User', 'globalswiftpay-dashboard'); ?></h2>
        <form id="gsp-admin-add-balance-form" class="gsp-form">
            <div class="gsp-form-group">
                <label for="add-bal-user"><?php esc_html_e('Select User', 'globalswiftpay-dashboard'); ?></label>
                <select id="add-bal-user" name="user_id" class="gsp-input gsp-select" required>
                    <option value=""><?php esc_html_e('Choose user...', 'globalswiftpay-dashboard'); ?></option>
                    <?php foreach ($all_users as $user): ?>
                        <option value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_html($user->display_name . ' (' . $user->user_email . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="gsp-form-group">
                <label for="add-bal-amount"><?php esc_html_e('Amount to Add ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="number" id="add-bal-amount" name="amount" class="gsp-input" step="0.01" min="0.01" required>
            </div>
            <div class="gsp-form-group">
                <label for="add-bal-type"><?php esc_html_e('Balance Type', 'globalswiftpay-dashboard'); ?></label>
                <select id="add-bal-type" name="balance_type" class="gsp-input gsp-select" required>
                    <option value="wallet"><?php esc_html_e('Wallet Balance', 'globalswiftpay-dashboard'); ?></option>
                    <option value="savings"><?php esc_html_e('Savings Balance', 'globalswiftpay-dashboard'); ?></option>
                </select>
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Add Balance', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Deduct Balance -->
<div id="admin-deduct-balance-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Deduct Balance from User', 'globalswiftpay-dashboard'); ?></h2>
        <form id="gsp-admin-deduct-balance-form" class="gsp-form">
            <div class="gsp-form-group">
                <label for="deduct-bal-user"><?php esc_html_e('Select User', 'globalswiftpay-dashboard'); ?></label>
                <select id="deduct-bal-user" name="user_id" class="gsp-input gsp-select" required>
                    <option value=""><?php esc_html_e('Choose user...', 'globalswiftpay-dashboard'); ?></option>
                    <?php foreach ($all_users as $user): ?>
                        <option value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_html($user->display_name . ' (' . $user->user_email . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="gsp-form-group">
                <label for="deduct-bal-amount"><?php esc_html_e('Amount to Deduct ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="number" id="deduct-bal-amount" name="amount" class="gsp-input" step="0.01" min="0.01" required>
            </div>
            <div class="gsp-form-group">
                <label for="deduct-bal-type"><?php esc_html_e('Balance Type', 'globalswiftpay-dashboard'); ?></label>
                <select id="deduct-bal-type" name="balance_type" class="gsp-input gsp-select" required>
                    <option value="wallet"><?php esc_html_e('Wallet Balance', 'globalswiftpay-dashboard'); ?></option>
                    <option value="savings"><?php esc_html_e('Savings Balance', 'globalswiftpay-dashboard'); ?></option>
                </select>
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Deduct Balance', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Edit User Balance -->
<div id="admin-edit-balance-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Edit User Balance', 'globalswiftpay-dashboard'); ?></h2>
        <p class="gsp-modal-subtitle" id="edit-balance-user-info"></p>
        <form id="gsp-admin-edit-balance-form" class="gsp-form">
            <input type="hidden" id="edit-bal-user-id" name="user_id" value="">
            <div class="gsp-form-group">
                <label for="edit-wallet-balance"><?php esc_html_e('Wallet Balance ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="number" id="edit-wallet-balance" name="wallet_balance" class="gsp-input" step="0.01" min="0" required>
            </div>
            <div class="gsp-form-group">
                <label for="edit-savings-balance"><?php esc_html_e('Savings Balance ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="number" id="edit-savings-balance" name="savings_balance" class="gsp-input" step="0.01" min="0" required>
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Save Balance', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Edit User -->
<div id="admin-edit-user-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Edit User', 'globalswiftpay-dashboard'); ?></h2>
        <form id="gsp-admin-edit-user-form" class="gsp-form">
            <input type="hidden" id="edit-user-id" name="user_id" value="">
            <div class="gsp-form-group">
                <label for="edit-user-email"><?php esc_html_e('Email', 'globalswiftpay-dashboard'); ?></label>
                <input type="email" id="edit-user-email" name="email" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="edit-user-display-name"><?php esc_html_e('Display Name', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="edit-user-display-name" name="display_name" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="edit-user-password"><?php esc_html_e('New Password (leave empty to keep current)', 'globalswiftpay-dashboard'); ?></label>
                <input type="password" id="edit-user-password" name="password" class="gsp-input">
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Update User', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Delete User Confirmation -->
<div id="admin-delete-user-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Delete User', 'globalswiftpay-dashboard'); ?></h2>
        <p class="gsp-modal-warning" id="delete-user-warning"></p>
        <form id="gsp-admin-delete-user-form" class="gsp-form">
            <input type="hidden" id="delete-user-id" name="user_id" value="">
            <div class="gsp-form-buttons">
                <button type="button" class="gsp-btn gsp-btn-secondary gsp-modal-close-btn"><?php esc_html_e('Cancel', 'globalswiftpay-dashboard'); ?></button>
                <button type="submit" class="gsp-btn gsp-btn-danger"><?php esc_html_e('Delete User', 'globalswiftpay-dashboard'); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="gsp-toast" class="gsp-toast"></div>

<!-- Iconify Initialization Script -->
<script>
(function() {
    // SVG icon definitions for fallback
    var iconSVGs = {
        'solar:shield-user-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/><path d="M2 20c0-3.3 2.7-6 6-6h8c3.3 0 6 2.7 6 6"/></svg>',
        'solar:moon-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21.5 14.078A8.5 8.5 0 0 1 9.922 2.5 8.5 8.5 0 1 0 21.5 14.078Z"/></svg>',
        'solar:sun-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="5"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>',
        'solar:widget-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
        'solar:card-receive-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="M12 9v6m0 0-2.5-2.5M12 15l2.5-2.5"/></svg>',
        'solar:card-send-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="M12 15V9m0 0L9.5 11.5M12 9l2.5 2.5"/></svg>',
        'solar:transfer-horizontal-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 17H4m0 0 4-4m-4 4 4 4M4 7h16m0 0-4-4m4 4-4 4"/></svg>',
        'solar:refresh-circle-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M16 12a4 4 0 0 0-4-4m0 8a4 4 0 0 0 4-4m-4-4V5m0 3L9.5 6.5M12 16v3m0-3l2.5 1.5"/></svg>',
        'solar:user-plus-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="10" cy="8" r="5"/><path d="M2 21a8 8 0 0 1 16 0"/><path d="M18 8v6m3-3h-6"/></svg>',
        'solar:wallet-money-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20M6 14h.01M10 14h4"/></svg>',
        'solar:wallet-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20M16 14h.01"/></svg>',
        'solar:settings-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
        'solar:magnifier-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
        'solar:pen-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
        'solar:user-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="5"/><path d="M3 21a9 9 0 0 1 18 0"/></svg>',
        'solar:trash-bin-trash-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14zM10 11v6M14 11v6"/></svg>',
        'solar:check-circle-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
        'solar:close-circle-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>',
        'solar:user-circle-linear': '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/></svg>'
    };
    
    // Function to scan for Iconify icons
    function scanIcons() {
        if (typeof Iconify !== 'undefined' && Iconify.scan) {
            Iconify.scan();
        }
    }
    
    // Function to apply SVG fallbacks
    function applyFallbacks() {
        document.querySelectorAll('.iconify').forEach(function(el) {
            // Check if Iconify hasn't rendered this icon (no SVG child)
            if (!el.querySelector('svg') && el.innerHTML.trim() === '') {
                var iconName = el.getAttribute('data-icon');
                if (iconName && iconSVGs[iconName]) {
                    el.innerHTML = iconSVGs[iconName];
                }
            }
        });
    }
    
    // Scan when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scanIcons);
    } else {
        scanIcons();
    }
    
    // Also scan after a short delay to catch any late-loading icons
    setTimeout(scanIcons, 500);
    setTimeout(scanIcons, 1500);
    
    // Apply fallbacks after Iconify has had time to load
    setTimeout(applyFallbacks, 2000);
    setTimeout(applyFallbacks, 4000);
})();
</script>
