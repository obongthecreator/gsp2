/**
 * GlobalSwiftPay Dashboard - Admin JavaScript
 */

(function($) {
    'use strict';

    // Admin Actions Handler
    const AdminActions = {
        errorMessage: 'An error occurred. Please try again.',
        init: function() {
            // Approve/Decline buttons
            $(document).on('click', '.gsp-admin-btn[data-action]', function() {
                const $btn = $(this);
                const action = $btn.data('action');
                const type = $btn.data('type');
                const id = $btn.data('id');
                
                const confirmMsg = action === 'approve' 
                    ? 'Are you sure you want to approve this request?' 
                    : 'Are you sure you want to decline this request?';
                
                if (confirm(confirmMsg)) {
                    AdminActions.updateStatus(type, id, action === 'approve' ? 'approved' : 'declined', $btn);
                }
            });
            
            // Edit balance button
            $(document).on('click', '.gsp-btn-edit-balance', function() {
                const userId = $(this).data('user-id');
                const currentBalance = $(this).data('balance');
                const currentSavings = $(this).data('savings') || 0;
                
                $('#edit-balance-user-id').val(userId);
                $('#edit-balance-amount').val(currentBalance);
                $('#edit-savings-amount').val(currentSavings);
                $('#gsp-edit-balance-modal').addClass('active').show();
            });
            
            // Edit balance form
            $('#gsp-edit-balance-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.updateUserBalance($(this));
            });
            
            // Add balance button
            $(document).on('click', '.gsp-btn-add-balance', function() {
                const userId = $(this).data('user-id');
                const username = $(this).data('username');
                
                $('#add-balance-user-id').val(userId);
                $('#add-balance-wallet-amount').val('');
                $('#add-balance-savings-amount').val('');
                $('#gsp-add-balance-username').text('User: ' + username);
                $('#gsp-add-balance-modal').addClass('active').show();
            });
            
            // Add balance form
            $('#gsp-add-balance-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.adjustUserBalance($(this), 'add');
            });
            
            // Deduct balance button
            $(document).on('click', '.gsp-btn-deduct-balance', function() {
                const userId = $(this).data('user-id');
                const username = $(this).data('username');
                const walletBalance = parseFloat($(this).data('balance')) || 0;
                const savingsBalance = parseFloat($(this).data('savings')) || 0;
                
                $('#deduct-balance-user-id').val(userId);
                $('#deduct-balance-wallet-amount').val('');
                $('#deduct-balance-savings-amount').val('');
                $('#gsp-deduct-balance-username').text('User: ' + username);
                $('#deduct-wallet-max').text('Available: $' + walletBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#deduct-savings-max').text('Available: $' + savingsBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#deduct-balance-wallet-amount').attr('max', walletBalance);
                $('#deduct-balance-savings-amount').attr('max', savingsBalance);
                $('#gsp-deduct-balance-modal').addClass('active').show();
            });
            
            // Deduct balance form
            $('#gsp-deduct-balance-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.adjustUserBalance($(this), 'subtract');
            });
            
            // Edit user button
            $(document).on('click', '.gsp-btn-edit-user', function() {
                const userId = $(this).data('user-id');
                const email = $(this).data('email');
                const displayName = $(this).data('display-name');
                
                $('#edit-user-id').val(userId);
                $('#edit-user-email').val(email);
                $('#edit-user-display-name').val(displayName);
                $('#edit-user-password').val('');
                $('#gsp-edit-user-modal').addClass('active').show();
            });
            
            // Edit user form
            $('#gsp-edit-user-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.editUser($(this));
            });
            
            // Add user button
            $('#gsp-add-user-btn').on('click', function() {
                $('#add-user-username').val('');
                $('#add-user-email').val('');
                $('#add-user-display-name').val('');
                $('#add-user-password').val('');
                $('#add-user-balance').val('0');
                $('#gsp-add-user-modal').addClass('active').show();
            });
            
            // Add user form
            $('#gsp-add-user-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.addUser($(this));
            });
            
            // Delete user button
            $(document).on('click', '.gsp-btn-delete-user', function() {
                const userId = $(this).data('user-id');
                const username = $(this).data('username');
                
                $('#delete-user-id').val(userId);
                $('#gsp-delete-user-message').text('Are you sure you want to delete user "' + username + '"? This action cannot be undone.');
                $('#gsp-delete-user-modal').addClass('active').show();
            });
            
            // Delete user form
            $('#gsp-delete-user-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.deleteUser($(this));
            });
            
            // Settings form
            $('#gsp-settings-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.saveSettings($(this));
            });

            // Wallet migration form
            $('#gsp-wallet-migration-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.runWalletMigration($(this));
            });

            $('#gsp-detect-wallet-sources').on('click', function() {
                AdminActions.detectWalletSources($(this));
            });

            $('#gsp-migrate-all-sources').on('click', function() {
                AdminActions.runAllWalletMigrations($(this));
            });

            // CSV import form
            $('#gsp-csv-import-form').on('submit', function(e) {
                e.preventDefault();
                AdminActions.importCsvBalances($(this));
            });
            
            // Registration Magic import button
            $('#gsp-import-rm-users').on('click', function() {
                AdminActions.importRmUsers($(this));
            });
            
            // Modal close
            $(document).on('click', '.gsp-modal-close', function() {
                $(this).closest('.gsp-modal').removeClass('active').hide();
            });
            
            // View details button
            $(document).on('click', '.gsp-view-details-btn', function() {
                const details = $(this).data('details');
                AdminActions.showDetailsModal(details);
            });
        },
        
        updateStatus: function(type, id, status, $btn) {
            const $row = $btn.closest('tr');
            $btn.prop('disabled', true).text('Processing...');
            
            const actionMap = {
                'deposit': 'gsp_admin_update_deposit',
                'withdrawal': 'gsp_admin_update_withdrawal',
                'transfer': 'gsp_admin_update_transfer',
                'conversion': 'gsp_admin_update_conversion'
            };
            
            const idMap = {
                'deposit': 'deposit_id',
                'withdrawal': 'withdrawal_id',
                'transfer': 'transfer_id',
                'conversion': 'conversion_id'
            };
            
            const data = {
                action: actionMap[type],
                nonce: gsp_admin_ajax.nonce,
                status: status,
                notes: ''
            };
            data[idMap[type]] = id;
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        // Update the row
                        const statusClass = status === 'approved' ? 'gsp-status-approved' : 'gsp-status-declined';
                        const statusText = status.charAt(0).toUpperCase() + status.slice(1);
                        
                        $row.find('.gsp-status').removeClass('gsp-status-pending gsp-status-approved gsp-status-declined')
                            .addClass(statusClass)
                            .text(statusText);
                        
                        $row.find('.gsp-admin-actions').html('<span class="gsp-action-completed">' + statusText + '</span>');
                        
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        $btn.prop('disabled', false).text(status === 'approved' ? 'Approve' : 'Decline');
                        AdminActions.showNotification(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text(status === 'approved' ? 'Approve' : 'Decline');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        updateUserBalance: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');
            
            const userId = $('#edit-balance-user-id').val();
            const balance = $('#edit-balance-amount').val();
            const savingsBalance = $('#edit-savings-amount').val();
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_update_user_balance',
                    nonce: gsp_admin_ajax.nonce,
                    user_id: userId,
                    balance: balance,
                    savings_balance: savingsBalance
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Save Balance');
                    
                    if (response.success) {
                        // Update the table
                        const $row = $('tr[data-user-id="' + userId + '"]');
                        $row.find('.gsp-user-wallet-balance').text('$' + parseFloat(balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        $row.find('.gsp-btn-edit-balance').data('balance', balance);
                        
                        if (savingsBalance) {
                            $row.find('.gsp-user-savings-balance').text('$' + parseFloat(savingsBalance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $row.find('.gsp-btn-edit-balance').data('savings', savingsBalance);
                        }
                        
                        $('#gsp-edit-balance-modal').removeClass('active').hide();
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        AdminActions.showNotification(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Save Balance');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        adjustUserBalance: function($form, operation) {
            const $btn = $form.find('button[type="submit"]');
            const originalText = $btn.text();
            $btn.prop('disabled', true).text('Processing...');
            
            const userId = $form.find('input[name="user_id"]').val();
            const walletAmount = parseFloat($form.find('input[name="wallet_amount"]').val()) || 0;
            const savingsAmount = parseFloat($form.find('input[name="savings_amount"]').val()) || 0;
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_adjust_user_balance',
                    nonce: gsp_admin_ajax.nonce,
                    user_id: userId,
                    operation: operation,
                    wallet_amount: walletAmount,
                    savings_amount: savingsAmount
                },
                success: function(response) {
                    $btn.prop('disabled', false).text(originalText);
                    
                    if (response.success) {
                        // Update the table with new balances
                        const $row = $('tr[data-user-id="' + userId + '"]');
                        const newWallet = parseFloat(response.data.wallet_balance);
                        const newSavings = parseFloat(response.data.savings_balance);
                        
                        $row.find('.gsp-user-wallet-balance').text('$' + newWallet.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        $row.find('.gsp-user-savings-balance').text('$' + newSavings.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        
                        // Update data attributes on buttons
                        $row.find('.gsp-btn-edit-balance').data('balance', newWallet).data('savings', newSavings);
                        $row.find('.gsp-btn-deduct-balance').data('balance', newWallet).data('savings', newSavings);
                        
                        // Close modal
                        $form.closest('.gsp-modal').removeClass('active').hide();
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        AdminActions.showNotification(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text(originalText);
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        addUser: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Creating...');
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_add_user',
                    nonce: gsp_admin_ajax.nonce,
                    username: $('#add-user-username').val(),
                    email: $('#add-user-email').val(),
                    display_name: $('#add-user-display-name').val(),
                    password: $('#add-user-password').val(),
                    balance: $('#add-user-balance').val()
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Create User');
                    
                    if (response.success) {
                        $('#gsp-add-user-modal').removeClass('active').hide();
                        AdminActions.showNotification(response.data.message, 'success');
                        // Reload page to show new user
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        AdminActions.showNotification(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Create User');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        editUser: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Updating...');
            
            const userId = $('#edit-user-id').val();
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_edit_user',
                    nonce: gsp_admin_ajax.nonce,
                    user_id: userId,
                    email: $('#edit-user-email').val(),
                    display_name: $('#edit-user-display-name').val(),
                    password: $('#edit-user-password').val()
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Update User');
                    
                    if (response.success) {
                        // Update the table
                        const $row = $('tr[data-user-id="' + userId + '"]');
                        $row.find('.gsp-user-email').text($('#edit-user-email').val());
                        $row.find('.gsp-user-display-name').text($('#edit-user-display-name').val());
                        
                        // Update button data attributes
                        $row.find('.gsp-btn-edit-user')
                            .data('email', $('#edit-user-email').val())
                            .data('display-name', $('#edit-user-display-name').val());
                        
                        $('#gsp-edit-user-modal').removeClass('active').hide();
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        AdminActions.showNotification(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Update User');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        deleteUser: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Deleting...');
            
            const userId = $('#delete-user-id').val();
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_delete_user',
                    nonce: gsp_admin_ajax.nonce,
                    user_id: userId
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Delete User');
                    
                    if (response.success) {
                        // Remove the row from table
                        $('tr[data-user-id="' + userId + '"]').fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        $('#gsp-delete-user-modal').removeClass('active').hide();
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        AdminActions.showNotification(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Delete User');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        saveSettings: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');
            
            const settings = {};
            $form.find('input[name^="settings"]').each(function() {
                const name = $(this).attr('name').replace(/settings\[/, '').replace(/\]/g, '');
                settings[name] = $(this).val();
            });
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_update_settings',
                    nonce: gsp_admin_ajax.nonce,
                    settings: settings
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Save Settings');
                    
                    if (response.success) {
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        AdminActions.showNotification(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Save Settings');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },

        detectWalletSources: function($button) {
            $button.prop('disabled', true).text('Detecting...');

            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_detect_wallet_sources',
                    nonce: gsp_admin_ajax.nonce
                },
                success: function(response) {
                    $button.prop('disabled', false).text('Detect Sources');
                    if (response.success) {
                        const $select = $('#gsp-wallet-source');
                        $select.empty();
                        $select.append($('<option>').val('').text('Select a source'));
                        if (response.data.sources && response.data.sources.length) {
                            response.data.sources.forEach(function(source) {
                                $select.append($('<option>').val(source.id).text(source.label));
                            });
                            AdminActions.showNotification('Sources detected successfully.', 'success');
                        } else {
                            AdminActions.showNotification('No wallet sources found.', 'error');
                        }
                    } else {
                        AdminActions.showNotification(response.data.message || 'Failed to detect sources.', 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).text('Detect Sources');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },

        runWalletMigration: function($form) {
            const $btn = $form.find('button[type="submit"]');
            const sourceId = $('#gsp-wallet-source').val();
            if (!sourceId) {
                AdminActions.showNotification('Select a wallet source before migrating.', 'error');
                return;
            }

            $btn.prop('disabled', true).text('Migrating...');

            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_migrate_wallet_balances',
                    nonce: gsp_admin_ajax.nonce,
                    source_id: sourceId
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Run Migration');
                    if (response.success) {
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        AdminActions.showNotification(response.data.message || 'Migration failed.', 'error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Run Migration');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },

        runAllWalletMigrations: function($button) {
            $button.prop('disabled', true).text('Migrating...');

            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_migrate_all_wallet_sources',
                    nonce: gsp_admin_ajax.nonce
                },
                success: function(response) {
                    $button.prop('disabled', false).text('Migrate All Sources');
                    if (response.success) {
                        AdminActions.showNotification(response.data.message, 'success');
                    } else {
                        AdminActions.showNotification(response.data.message || 'Migration failed.', 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).text('Migrate All Sources');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },

        importCsvBalances: function($form) {
            const $btn = $form.find('button[type="submit"]');
            const fileInput = document.getElementById('gsp-csv-file');
            
            if (!fileInput.files || !fileInput.files[0]) {
                AdminActions.showNotification('Please select a CSV file.', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'gsp_admin_import_csv_balances');
            formData.append('nonce', gsp_admin_ajax.nonce);
            formData.append('csv_file', fileInput.files[0]);
            
            $btn.prop('disabled', true).text('Importing...');
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $btn.prop('disabled', false).text('Import Balances from CSV');
                    
                    const $results = $('#gsp-csv-import-results');
                    const $message = $('#gsp-csv-import-message');
                    
                    if (response.success) {
                        AdminActions.showNotification('CSV import completed successfully.', 'success');
                        $message.html('<div style="color: green; white-space: pre-line;">' + response.data.message + '</div>');
                    } else {
                        AdminActions.showNotification('CSV import completed with issues.', 'error');
                        $message.html('<div style="color: red; white-space: pre-line;">' + response.data.message + '</div>');
                    }
                    
                    $results.show();
                    fileInput.value = '';
                },
                error: function() {
                    $btn.prop('disabled', false).text('Import Balances from CSV');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        importRmUsers: function($btn) {
            if (!confirm('This will import user details (phone, country, GSP account) from the Registration Magic XML file. Continue?')) {
                return;
            }
            
            $btn.prop('disabled', true).text('Importing...');
            
            $.ajax({
                url: gsp_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_import_rm_users',
                    nonce: gsp_admin_ajax.nonce
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Import Registration Magic Users');
                    
                    const $results = $('#gsp-rm-import-results');
                    const $message = $('#gsp-rm-import-message');
                    
                    if (response.success) {
                        AdminActions.showNotification('Registration Magic import completed successfully.', 'success');
                        $message.text(response.data.message).css('color', 'green');
                    } else {
                        AdminActions.showNotification('Registration Magic import failed.', 'error');
                        $message.text(response.data.message).css('color', 'red');
                    }
                    
                    $results.show();
                },
                error: function() {
                    $btn.prop('disabled', false).text('Import Registration Magic Users');
                    AdminActions.showNotification(AdminActions.errorMessage, 'error');
                }
            });
        },
        
        showDetailsModal: function(details) {
            let html = '<div class="gsp-details-modal-content">';
            
            if (typeof details === 'object') {
                for (const key in details) {
                    if (details[key] && details[key] !== null) {
                        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        let value = details[key];
                        
                        if (typeof value === 'object') {
                            value = '<ul>';
                            for (const subKey in details[key]) {
                                const subLabel = subKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                value += '<li><strong>' + subLabel + ':</strong> ' + details[key][subKey] + '</li>';
                            }
                            value += '</ul>';
                        }
                        
                        html += '<p><strong>' + label + ':</strong> ' + value + '</p>';
                    }
                }
            }
            
            html += '</div>';
            
            // Create and show modal
            const modalHtml = `
                <div id="gsp-details-modal" class="gsp-modal active" style="display: flex;">
                    <div class="gsp-modal-content" style="max-width: 500px;">
                        <span class="gsp-modal-close">&times;</span>
                        <h2>Request Details</h2>
                        ${html}
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            $('#gsp-details-modal').remove();
            
            // Add new modal
            $('body').append(modalHtml);
        },
        
        showNotification: function(message, type) {
            // Create notification element if it doesn't exist
            let $notification = $('#gsp-admin-notification');
            if ($notification.length === 0) {
                $notification = $('<div id="gsp-admin-notification" style="position: fixed; top: 50px; right: 20px; padding: 15px 25px; border-radius: 8px; z-index: 100001; font-weight: 500; box-shadow: 0 4px 20px rgba(0,0,0,0.2); transition: all 0.3s ease;"></div>');
                $('body').append($notification);
            }
            
            // Set styles based on type
            if (type === 'success') {
                $notification.css({
                    'background': 'linear-gradient(135deg, #00c853, #69f0ae)',
                    'color': '#ffffff'
                });
            } else {
                $notification.css({
                    'background': 'linear-gradient(135deg, #ff5252, #ff8a80)',
                    'color': '#ffffff'
                });
            }
            
            $notification.text(message).fadeIn();
            
            setTimeout(function() {
                $notification.fadeOut();
            }, 4000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        AdminActions.init();
    });

})(jQuery);
