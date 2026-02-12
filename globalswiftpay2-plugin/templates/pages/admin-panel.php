<?php
/**
 * Frontend Admin Panel Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if user has admin access
if (!current_user_can('manage_options')) {
    wp_redirect(home_url('/gsp2-login/'));
    exit;
}

// Get upgrade data
$pending_upgrades = get_option('gsp2_pending_upgrades', array());
$approved_upgrades = get_option('gsp2_approved_upgrades', array());
$rejected_upgrades = get_option('gsp2_rejected_upgrades', array());

// Get current user
$current_user = wp_get_current_user();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GlobalSwiftPay2</title>
    <?php wp_head(); ?>
    <style>
        /* Frontend Admin Panel Specific Styles */
        .gsp2-admin-panel {
            min-height: 100vh;
            padding: 2rem;
        }
        
        .gsp2-admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .gsp2-admin-header h1 {
            font-size: 2rem;
            margin: 0;
        }
        
        .gsp2-admin-nav {
            display: flex;
            gap: 1rem;
        }
        
        .gsp2-admin-nav a {
            padding: 0.75rem 1.5rem;
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 0.5rem;
            text-decoration: none;
            color: #60a5fa;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .gsp2-admin-nav a:hover {
            background: rgba(59, 130, 246, 0.3);
        }
        
        .gsp2-admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .gsp2-stat-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
        }
        
        .gsp2-stat-card h3 {
            font-size: 2.5rem;
            margin: 0 0 0.5rem 0;
            color: #3b82f6;
        }
        
        .gsp2-stat-card p {
            margin: 0;
            color: #94a3b8;
            font-size: 1rem;
        }
        
        .gsp2-stat-card.pending h3 { color: #f59e0b; }
        .gsp2-stat-card.approved h3 { color: #10b981; }
        .gsp2-stat-card.rejected h3 { color: #ef4444; }
        
        .gsp2-admin-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 0.5rem;
        }
        
        .gsp2-admin-tab {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 1rem;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: all 0.3s ease;
        }
        
        .gsp2-admin-tab:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }
        
        .gsp2-admin-tab.active {
            color: #fff;
            background: rgba(59, 130, 246, 0.2);
            border-bottom: 2px solid #3b82f6;
        }
        
        .gsp2-tab-content {
            display: none;
        }
        
        .gsp2-tab-content.active {
            display: block;
        }
        
        .gsp2-admin-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(30, 41, 59, 0.6);
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .gsp2-admin-table th,
        .gsp2-admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 1rem;
        }
        
        .gsp2-admin-table th {
            background: rgba(30, 41, 59, 0.9);
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.875rem;
        }
        
        .gsp2-admin-table tr:hover td {
            background: rgba(255,255,255,0.02);
        }
        
        .gsp2-admin-table .username {
            font-weight: 600;
            color: #60a5fa;
        }
        
        .gsp2-admin-table .email {
            color: #a78bfa;
        }
        
        .gsp2-action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }
        
        .gsp2-action-btn.approve {
            background: #10b981;
            color: white;
        }
        
        .gsp2-action-btn.approve:hover {
            background: #059669;
        }
        
        .gsp2-action-btn.reject {
            background: #ef4444;
            color: white;
        }
        
        .gsp2-action-btn.reject:hover {
            background: #dc2626;
        }
        
        .gsp2-action-btn.view {
            background: #3b82f6;
            color: white;
        }
        
        .gsp2-action-btn.view:hover {
            background: #2563eb;
        }
        
        .gsp2-empty-state {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }
        
        .gsp2-empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Modal Styles */
        .gsp2-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        
        .gsp2-modal.active {
            display: flex;
        }
        
        .gsp2-modal-content {
            background: #1e293b;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .gsp2-modal h3 {
            margin: 0 0 1rem 0;
            font-size: 1.5rem;
        }
        
        .gsp2-modal p {
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }
        
        .gsp2-modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .gsp2-modal textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.5rem;
            background: rgba(0,0,0,0.3);
            color: #fff;
            font-size: 1rem;
            margin-bottom: 1rem;
            min-height: 100px;
            resize: vertical;
        }
        
        .gsp2-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .gsp2-badge.pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .gsp2-badge.approved { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .gsp2-badge.rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        
        /* User details row */
        .gsp2-user-details {
            display: none;
            background: rgba(0,0,0,0.2);
        }
        
        .gsp2-user-details.active {
            display: table-row;
        }
        
        .gsp2-user-details td {
            padding: 1.5rem;
        }
        
        .gsp2-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .gsp2-detail-item label {
            display: block;
            color: #94a3b8;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        
        .gsp2-detail-item span {
            font-size: 1rem;
        }
        
        /* Light mode overrides */
        body:not(.gsp2-dark) .gsp2-admin-panel {
            background: #ffffff;
            color: #1e293b;
        }
        
        body:not(.gsp2-dark) .gsp2-stat-card,
        body:not(.gsp2-dark) .gsp2-admin-table {
            background: #f8fafc;
            border-color: #e2e8f0;
        }
        
        body:not(.gsp2-dark) .gsp2-admin-table th {
            background: #f1f5f9;
            color: #475569;
        }
        
        body:not(.gsp2-dark) .gsp2-modal-content {
            background: #ffffff;
            color: #1e293b;
        }
        
        body:not(.gsp2-dark) .gsp2-modal textarea {
            background: #f8fafc;
            color: #1e293b;
            border-color: #e2e8f0;
        }
        
        @media (max-width: 768px) {
            .gsp2-admin-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .gsp2-admin-table {
                display: block;
                overflow-x: auto;
            }
            
            .gsp2-admin-tabs {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body <?php body_class('gsp2-dark'); ?>>
    
    <div class="gsp2-main gsp2-admin-panel">
        <!-- Header -->
        <div class="gsp2-admin-header">
            <div>
                <h1>Admin Panel</h1>
                <p style="color: #94a3b8; margin: 0.5rem 0 0 0;">Welcome, <?php echo esc_html($current_user->display_name); ?></p>
            </div>
            <div class="gsp2-admin-nav">
                <a href="<?php echo home_url(); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Home
                </a>
                <a href="<?php echo admin_url('admin.php?page=gsp2-settings'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    WP Admin
                </a>
                <a href="https://globalswiftpay2.com/admin-dashboard/" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.3); color: #10b981;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    Manage Balances
                </a>
                <a href="<?php echo wp_logout_url(home_url()); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 0.5rem;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="gsp2-admin-stats">
            <div class="gsp2-stat-card pending">
                <h3><?php echo count($pending_upgrades); ?></h3>
                <p>Pending Upgrades</p>
            </div>
            <div class="gsp2-stat-card approved">
                <h3><?php echo count($approved_upgrades); ?></h3>
                <p>Approved Users</p>
            </div>
            <div class="gsp2-stat-card rejected">
                <h3><?php echo count($rejected_upgrades); ?></h3>
                <p>Rejected Requests</p>
            </div>
            <div class="gsp2-stat-card">
                <h3><?php 
                    $users = count_users();
                    echo isset($users['avail_roles']['subscriber']) ? $users['avail_roles']['subscriber'] : 0;
                ?></h3>
                <p>Total Subscribers</p>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="gsp2-admin-tabs">
            <button class="gsp2-admin-tab active" data-tab="pending">
                Pending (<?php echo count($pending_upgrades); ?>)
            </button>
            <button class="gsp2-admin-tab" data-tab="approved">
                Approved (<?php echo count($approved_upgrades); ?>)
            </button>
            <button class="gsp2-admin-tab" data-tab="rejected">
                Rejected (<?php echo count($rejected_upgrades); ?>)
            </button>
        </div>
        
        <!-- Pending Tab -->
        <div id="tab-pending" class="gsp2-tab-content active">
            <?php if (!empty($pending_upgrades)): ?>
            <table class="gsp2-admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>GSP Account</th>
                        <th>Country</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_upgrades as $upgrade): ?>
                    <tr data-id="<?php echo esc_attr($upgrade['id']); ?>">
                        <td><?php echo esc_html($upgrade['first_name'] . ' ' . $upgrade['last_name']); ?></td>
                        <td class="username"><?php echo esc_html($upgrade['username'] ?? 'N/A'); ?></td>
                        <td class="email"><?php echo esc_html($upgrade['email']); ?></td>
                        <td><?php echo esc_html($upgrade['gsp_account']); ?></td>
                        <td><?php echo esc_html($upgrade['country']); ?></td>
                        <td><?php echo esc_html(date('M j, Y', strtotime($upgrade['submitted_at']))); ?></td>
                        <td>
                            <button class="gsp2-action-btn approve" data-id="<?php echo esc_attr($upgrade['id']); ?>">Approve</button>
                            <button class="gsp2-action-btn reject" data-id="<?php echo esc_attr($upgrade['id']); ?>">Reject</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="gsp2-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3>No Pending Requests</h3>
                <p>All upgrade requests have been processed.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Approved Tab -->
        <div id="tab-approved" class="gsp2-tab-content">
            <?php if (!empty($approved_upgrades)): ?>
            <table class="gsp2-admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>GSP Account</th>
                        <th>WP User ID</th>
                        <th>Approved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($approved_upgrades) as $upgrade): ?>
                    <tr>
                        <td><?php echo esc_html($upgrade['first_name'] . ' ' . $upgrade['last_name']); ?></td>
                        <td class="username"><?php echo esc_html($upgrade['username'] ?? 'N/A'); ?></td>
                        <td class="email"><?php echo esc_html($upgrade['email']); ?></td>
                        <td><?php echo esc_html($upgrade['gsp_account']); ?></td>
                        <td>
                            <?php if (!empty($upgrade['wp_user_id'])): ?>
                            <a href="<?php echo admin_url('user-edit.php?user_id=' . $upgrade['wp_user_id']); ?>" target="_blank" style="color: #10b981;">
                                #<?php echo esc_html($upgrade['wp_user_id']); ?>
                            </a>
                            <?php else: ?>
                            <span style="color: #94a3b8;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo isset($upgrade['approved_at']) ? esc_html(date('M j, Y', strtotime($upgrade['approved_at']))) : 'N/A'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="gsp2-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3>No Approved Users</h3>
                <p>Approved upgrade requests will appear here.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Rejected Tab -->
        <div id="tab-rejected" class="gsp2-tab-content">
            <?php if (!empty($rejected_upgrades)): ?>
            <table class="gsp2-admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Reason</th>
                        <th>Rejected</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($rejected_upgrades) as $upgrade): ?>
                    <tr>
                        <td><?php echo esc_html($upgrade['first_name'] . ' ' . $upgrade['last_name']); ?></td>
                        <td class="username"><?php echo esc_html($upgrade['username'] ?? 'N/A'); ?></td>
                        <td class="email"><?php echo esc_html($upgrade['email']); ?></td>
                        <td><?php echo esc_html($upgrade['rejection_reason'] ?? 'No reason provided'); ?></td>
                        <td><?php echo isset($upgrade['rejected_at']) ? esc_html(date('M j, Y', strtotime($upgrade['rejected_at']))) : 'N/A'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="gsp2-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3>No Rejected Requests</h3>
                <p>Rejected upgrade requests will appear here.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Approve Modal -->
    <div id="approve-modal" class="gsp2-modal">
        <div class="gsp2-modal-content">
            <h3>Approve Upgrade Request</h3>
            <p>Are you sure you want to approve this upgrade request? A WordPress user account will be created and the user will receive login credentials via email.</p>
            <div class="gsp2-modal-actions">
                <button class="gsp2-action-btn" onclick="closeModal('approve-modal')" style="background: #475569;">Cancel</button>
                <button class="gsp2-action-btn approve" id="confirm-approve">Approve</button>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div id="reject-modal" class="gsp2-modal">
        <div class="gsp2-modal-content">
            <h3>Reject Upgrade Request</h3>
            <p>Please provide a reason for rejection (this will be sent to the user):</p>
            <textarea id="rejection-reason" placeholder="Enter rejection reason..."></textarea>
            <div class="gsp2-modal-actions">
                <button class="gsp2-action-btn" onclick="closeModal('reject-modal')" style="background: #475569;">Cancel</button>
                <button class="gsp2-action-btn reject" id="confirm-reject">Reject</button>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        let currentUpgradeId = null;
        
        // Tab switching
        $('.gsp2-admin-tab').on('click', function() {
            const tab = $(this).data('tab');
            
            // Update tab buttons
            $('.gsp2-admin-tab').removeClass('active');
            $(this).addClass('active');
            
            // Update tab content
            $('.gsp2-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');
        });
        
        // Approve button click
        $('.gsp2-action-btn.approve').on('click', function() {
            currentUpgradeId = $(this).data('id');
            $('#approve-modal').addClass('active');
        });
        
        // Reject button click
        $('.gsp2-action-btn.reject').on('click', function() {
            currentUpgradeId = $(this).data('id');
            $('#rejection-reason').val('');
            $('#reject-modal').addClass('active');
        });
        
        // Confirm approve
        $('#confirm-approve').on('click', function() {
            if (!currentUpgradeId) return;
            
            const $btn = $(this);
            $btn.prop('disabled', true).text('Processing...');
            
            $.ajax({
                url: gsp2_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp2_approve_upgrade',
                    nonce: gsp2_ajax.nonce,
                    upgrade_id: currentUpgradeId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Upgrade approved successfully! User account created.');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.data || 'Unknown error'));
                        $btn.prop('disabled', false).text('Approve');
                    }
                },
                error: function() {
                    alert('Request failed. Please try again.');
                    $btn.prop('disabled', false).text('Approve');
                }
            });
        });
        
        // Confirm reject
        $('#confirm-reject').on('click', function() {
            if (!currentUpgradeId) return;
            
            const reason = $('#rejection-reason').val().trim();
            if (!reason) {
                alert('Please provide a rejection reason.');
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).text('Processing...');
            
            $.ajax({
                url: gsp2_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp2_reject_upgrade',
                    nonce: gsp2_ajax.nonce,
                    upgrade_id: currentUpgradeId,
                    reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        alert('Upgrade request rejected.');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.data || 'Unknown error'));
                        $btn.prop('disabled', false).text('Reject');
                    }
                },
                error: function() {
                    alert('Request failed. Please try again.');
                    $btn.prop('disabled', false).text('Reject');
                }
            });
        });
        
        // Close modal on outside click
        $('.gsp2-modal').on('click', function(e) {
            if ($(e.target).hasClass('gsp2-modal')) {
                closeModal(this.id);
            }
        });
        
        // Close modal on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.gsp2-modal').removeClass('active');
            }
        });
    });
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>
