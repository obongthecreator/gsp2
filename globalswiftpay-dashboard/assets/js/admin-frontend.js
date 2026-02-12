/**
 * GlobalSwiftPay Dashboard - Frontend Admin JavaScript
 */

(function($) {
    'use strict';

    // Theme Manager
    const ThemeManager = {
        init: function() {
            const savedTheme = localStorage.getItem('gsp-theme') || 'dark';
            this.setTheme(savedTheme);
            
            $('#gsp-theme-toggle').on('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                ThemeManager.setTheme(newTheme);
            });
        },
        
        setTheme: function(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('gsp-theme', theme);
            
            const $toggle = $('#gsp-theme-toggle');
            if (theme === 'dark') {
                $toggle.find('[data-theme-icon="dark"]').show();
                $toggle.find('[data-theme-icon="light"]').hide();
            } else {
                $toggle.find('[data-theme-icon="dark"]').hide();
                $toggle.find('[data-theme-icon="light"]').show();
            }
        }
    };

    // Toast notification system
    const Toast = {
        show: function(message, type = 'success') {
            const toast = $('#gsp-toast');
            toast.removeClass('success error').addClass(type).text(message).addClass('show');
            
            setTimeout(function() {
                toast.removeClass('show');
            }, 4000);
        }
    };

    // Modal handler
    const Modal = {
        open: function(modalId) {
            $('#' + modalId).addClass('active');
            $('body').css('overflow', 'hidden');
        },
        
        close: function(modal) {
            $(modal).removeClass('active');
            $('body').css('overflow', '');
            $(modal).find('form')[0]?.reset();
        },
        
        init: function() {
            $(document).on('click', '[data-modal]', function(e) {
                e.preventDefault();
                const modalId = $(this).data('modal');
                Modal.open(modalId);
            });
            
            $(document).on('click', '.gsp-modal-close, .gsp-modal-close-btn', function() {
                Modal.close($(this).closest('.gsp-modal'));
            });
            
            $(document).on('click', '.gsp-modal-overlay', function() {
                Modal.close($(this).closest('.gsp-modal'));
            });
            
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.gsp-modal.active').each(function() {
                        Modal.close(this);
                    });
                }
            });
        }
    };

    // Tabs handler
    const Tabs = {
        init: function() {
            $(document).on('click', '.gsp-tab-btn', function() {
                const tabId = $(this).data('tab');
                
                $('.gsp-tab-btn').removeClass('active');
                $(this).addClass('active');
                
                $('.gsp-tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
            });
        }
    };

    // User search
    const UserSearch = {
        init: function() {
            const self = this;
            $('#gsp-user-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase().trim();
                
                if (searchTerm === '') {
                    // Reset to original state with pagination
                    UserFilter.applyFilters();
                    return;
                }
                
                // Search all users and track visibility
                const $rows = $('#gsp-users-tbody .gsp-user-row');
                const currentStatusFilter = $('#gsp-user-status-filter').val();
                let visibleCount = 0;
                
                $rows.each(function() {
                    const userName = $(this).find('.gsp-user-name').text().toLowerCase();
                    const userEmail = $(this).find('td:nth-child(2)').text().toLowerCase();
                    const userStatus = $(this).data('user-status');
                    
                    // Check if matches search and status filter
                    const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm);
                    const matchesStatus = currentStatusFilter === 'all' || userStatus === currentStatusFilter;
                    
                    if (matchesSearch && matchesStatus) {
                        $(this).data('search-visible', true);
                        // Show first 20 matching results
                        if (visibleCount < 20) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                        visibleCount++;
                    } else {
                        $(this).data('search-visible', false);
                        $(this).hide();
                    }
                });
                
                // Update pagination for search results
                Pagination.updateVisibility(visibleCount);
            });
        }
    };

    // User Status Filter
    const UserFilter = {
        init: function() {
            $('#gsp-user-status-filter').on('change', function() {
                UserFilter.applyFilters();
            });
        },
        
        applyFilters: function() {
            const statusFilter = $('#gsp-user-status-filter').val();
            const searchTerm = $('#gsp-user-search').val().toLowerCase().trim();
            const $rows = $('#gsp-users-tbody .gsp-user-row');
            let visibleCount = 0;
            
            $rows.each(function() {
                const userStatus = $(this).data('user-status');
                const userName = $(this).find('.gsp-user-name').text().toLowerCase();
                const userEmail = $(this).find('td:nth-child(2)').text().toLowerCase();
                
                // Check status filter
                const matchesStatus = statusFilter === 'all' || userStatus === statusFilter;
                // Check search term
                const matchesSearch = searchTerm === '' || userName.includes(searchTerm) || userEmail.includes(searchTerm);
                
                if (matchesStatus && matchesSearch) {
                    $(this).data('search-visible', true);
                    if (visibleCount < 20) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                    visibleCount++;
                } else {
                    $(this).data('search-visible', false);
                    $(this).hide();
                }
            });
            
            Pagination.updateVisibility(visibleCount);
        }
    };

    // Admin Forms
    const AdminForms = {
        init: function() {
            // Add User Form
            $('#gsp-admin-add-user-form').on('submit', function(e) {
                e.preventDefault();
                AdminForms.submitAddUser($(this));
            });
            
            // Add Balance Form
            $('#gsp-admin-add-balance-form').on('submit', function(e) {
                e.preventDefault();
                AdminForms.submitBalanceChange($(this), 'add');
            });
            
            // Deduct Balance Form
            $('#gsp-admin-deduct-balance-form').on('submit', function(e) {
                e.preventDefault();
                AdminForms.submitBalanceChange($(this), 'deduct');
            });
            
            // Edit Balance Form
            $('#gsp-admin-edit-balance-form').on('submit', function(e) {
                e.preventDefault();
                AdminForms.submitEditBalance($(this));
            });
            
            // Edit User Form
            $('#gsp-admin-edit-user-form').on('submit', function(e) {
                e.preventDefault();
                AdminForms.submitEditUser($(this));
            });
            
            // Delete User Form
            $('#gsp-admin-delete-user-form').on('submit', function(e) {
                e.preventDefault();
                AdminForms.submitDeleteUser($(this));
            });
            
            // Edit Balance Button
            $(document).on('click', '.gsp-edit-balance-btn', function() {
                const $row = $(this).closest('tr');
                const userId = $row.data('user-id');
                const userName = $row.data('user-name');
                const walletBalance = $row.data('wallet-balance');
                const savingsBalance = $row.data('savings-balance');
                
                $('#edit-bal-user-id').val(userId);
                $('#edit-balance-user-info').text('Editing balance for: ' + userName);
                $('#edit-wallet-balance').val(walletBalance);
                $('#edit-savings-balance').val(savingsBalance);
                
                Modal.open('admin-edit-balance-modal');
            });
            
            // Edit User Button
            $(document).on('click', '.gsp-edit-user-btn', function() {
                const $row = $(this).closest('tr');
                const userId = $row.data('user-id');
                const userEmail = $row.data('user-email');
                const userName = $row.data('user-name');
                
                $('#edit-user-id').val(userId);
                $('#edit-user-email').val(userEmail);
                $('#edit-user-display-name').val(userName);
                $('#edit-user-password').val('');
                
                Modal.open('admin-edit-user-modal');
            });
            
            // Delete User Button
            $(document).on('click', '.gsp-delete-user-btn', function() {
                const $row = $(this).closest('tr');
                const userId = $row.data('user-id');
                const userName = $row.data('user-name');
                
                $('#delete-user-id').val(userId);
                $('#delete-user-warning').text('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.');
                
                Modal.open('admin-delete-user-modal');
            });
            
            // Transaction Actions (Approve/Decline)
            $(document).on('click', '.gsp-action-approve, .gsp-action-decline', function() {
                const $row = $(this).closest('tr');
                const id = $row.data('id');
                const type = $row.data('type');
                const action = $(this).data('action');
                const status = action === 'approve' ? 'approved' : 'declined';
                
                AdminForms.updateTransactionStatus(type, id, status, $row);
            });
            
            // User Approve Button
            $(document).on('click', '.gsp-approve-user-btn', function() {
                const $row = $(this).closest('tr');
                const userId = $row.data('user-id');
                AdminForms.updateUserStatus(userId, 'approved', $row);
            });
            
            // User Decline Button
            $(document).on('click', '.gsp-decline-user-btn', function() {
                const $row = $(this).closest('tr');
                const userId = $row.data('user-id');
                AdminForms.updateUserStatus(userId, 'declined', $row);
            });
        },
        
        updateUserStatus: function(userId, status, $row) {
            $.ajax({
                url: gsp_admin_frontend_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_update_user_status',
                    nonce: gsp_admin_frontend_ajax.nonce,
                    user_id: userId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        
                        // Update the row's data attribute and status display
                        $row.data('user-status', status);
                        $row.attr('data-user-status', status);
                        
                        // Update the status cell
                        const $statusCell = $row.find('.gsp-user-status');
                        $statusCell.removeClass('gsp-status-pending gsp-status-approved gsp-status-declined');
                        $statusCell.addClass('gsp-status-' + status);
                        $statusCell.text(status.charAt(0).toUpperCase() + status.slice(1));
                        
                        // Update action buttons visibility
                        const $actionsCell = $row.find('.gsp-actions-cell');
                        $actionsCell.find('.gsp-approve-user-btn').toggle(status !== 'approved');
                        $actionsCell.find('.gsp-decline-user-btn').toggle(status !== 'declined');
                        
                        // Reapply filters in case the status filter is active
                        UserFilter.applyFilters();
                    } else {
                        Toast.show(response.data.message, 'error');
                    }
                },
                error: function() {
                    Toast.show('An error occurred. Please try again.', 'error');
                }
            });
        },
        
        submitAddUser: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            $.ajax({
                url: gsp_admin_frontend_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_add_user',
                    nonce: gsp_admin_frontend_ajax.nonce,
                    username: $form.find('[name="username"]').val(),
                    email: $form.find('[name="email"]').val(),
                    display_name: $form.find('[name="display_name"]').val(),
                    password: $form.find('[name="password"]').val(),
                    balance: $form.find('[name="balance"]').val()
                },
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        Toast.show(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.removeClass('loading');
                    Toast.show('An error occurred. Please try again.', 'error');
                }
            });
        },
        
        submitBalanceChange: function($form, operation) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            const userId = $form.find('[name="user_id"]').val();
            const amount = parseFloat($form.find('[name="amount"]').val());
            const balanceType = $form.find('[name="balance_type"]').val();
            
            // First get current balance
            $.ajax({
                url: gsp_admin_frontend_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_get_user_balance',
                    nonce: gsp_admin_frontend_ajax.nonce,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        let newWalletBalance = parseFloat(response.data.wallet_balance);
                        let newSavingsBalance = parseFloat(response.data.savings_balance);
                        
                        if (balanceType === 'wallet') {
                            newWalletBalance = operation === 'add' ? newWalletBalance + amount : newWalletBalance - amount;
                        } else {
                            newSavingsBalance = operation === 'add' ? newSavingsBalance + amount : newSavingsBalance - amount;
                        }
                        
                        if (newWalletBalance < 0 || newSavingsBalance < 0) {
                            $btn.removeClass('loading');
                            Toast.show('Insufficient balance for deduction.', 'error');
                            return;
                        }
                        
                        // Update balance
                        $.ajax({
                            url: gsp_admin_frontend_ajax.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'gsp_admin_update_user_balance',
                                nonce: gsp_admin_frontend_ajax.nonce,
                                user_id: userId,
                                balance: newWalletBalance,
                                savings_balance: newSavingsBalance
                            },
                            success: function(updateResponse) {
                                $btn.removeClass('loading');
                                if (updateResponse.success) {
                                    Toast.show('Balance updated successfully.', 'success');
                                    Modal.close($form.closest('.gsp-modal'));
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);
                                } else {
                                    Toast.show(updateResponse.data.message, 'error');
                                }
                            },
                            error: function() {
                                $btn.removeClass('loading');
                                Toast.show('An error occurred. Please try again.', 'error');
                            }
                        });
                    } else {
                        $btn.removeClass('loading');
                        Toast.show('Failed to get user balance.', 'error');
                    }
                },
                error: function() {
                    $btn.removeClass('loading');
                    Toast.show('An error occurred. Please try again.', 'error');
                }
            });
        },
        
        submitEditBalance: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            $.ajax({
                url: gsp_admin_frontend_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_update_user_balance',
                    nonce: gsp_admin_frontend_ajax.nonce,
                    user_id: $form.find('[name="user_id"]').val(),
                    balance: $form.find('[name="wallet_balance"]').val(),
                    savings_balance: $form.find('[name="savings_balance"]').val()
                },
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        Toast.show(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.removeClass('loading');
                    Toast.show('An error occurred. Please try again.', 'error');
                }
            });
        },
        
        submitEditUser: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            $.ajax({
                url: gsp_admin_frontend_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_edit_user',
                    nonce: gsp_admin_frontend_ajax.nonce,
                    user_id: $form.find('[name="user_id"]').val(),
                    email: $form.find('[name="email"]').val(),
                    display_name: $form.find('[name="display_name"]').val(),
                    password: $form.find('[name="password"]').val()
                },
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        Toast.show(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.removeClass('loading');
                    Toast.show('An error occurred. Please try again.', 'error');
                }
            });
        },
        
        submitDeleteUser: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            $.ajax({
                url: gsp_admin_frontend_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_admin_delete_user',
                    nonce: gsp_admin_frontend_ajax.nonce,
                    user_id: $form.find('[name="user_id"]').val()
                },
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        Toast.show(response.data.message, 'error');
                    }
                },
                error: function() {
                    $btn.removeClass('loading');
                    Toast.show('An error occurred. Please try again.', 'error');
                }
            });
        },
        
        updateTransactionStatus: function(type, id, status, $row) {
            let actionName;
            let idField;
            
            switch (type) {
                case 'deposit':
                    actionName = 'gsp_admin_update_deposit';
                    idField = 'deposit_id';
                    break;
                case 'withdrawal':
                    actionName = 'gsp_admin_update_withdrawal';
                    idField = 'withdrawal_id';
                    break;
                case 'transfer':
                    actionName = 'gsp_admin_update_transfer';
                    idField = 'transfer_id';
                    break;
                case 'conversion':
                    actionName = 'gsp_admin_update_conversion';
                    idField = 'conversion_id';
                    break;
            }
            
            const data = {
                action: actionName,
                nonce: gsp_admin_frontend_ajax.nonce,
                status: status
            };
            data[idField] = id;
            
            $.ajax({
                url: gsp_admin_frontend_ajax.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        $row.fadeOut(400, function() {
                            $(this).remove();
                            // Update tab count
                            const $tab = $('[data-tab="' + type + 's-tab"]');
                            const $count = $tab.find('.gsp-tab-count');
                            const currentCount = parseInt($count.text());
                            const newCount = Math.max(0, currentCount - 1);
                            $count.text(newCount);
                            
                            // Update stat card count
                            const statMap = {
                                'withdrawal': '#gsp-stat-withdrawals',
                                'transfer': '#gsp-stat-transfers',
                                'conversion': '#gsp-stat-conversions'
                            };
                            if (statMap[type]) {
                                const $statNumber = $(statMap[type]);
                                const statCount = parseInt($statNumber.text());
                                $statNumber.text(Math.max(0, statCount - 1));
                            }
                            
                            // Check if table is now empty and show "no pending" message
                            const $tbody = $('#' + type + 's-tab').find('tbody');
                            if ($tbody.find('tr').length === 0) {
                                const noDataMessages = {
                                    'deposit': 'No pending deposits.',
                                    'withdrawal': 'No pending withdrawals.',
                                    'transfer': 'No pending transfers.',
                                    'conversion': 'No pending conversions.'
                                };
                                const colSpan = type === 'deposit' ? 4 : 5;
                                $tbody.append('<tr><td colspan="' + colSpan + '" class="gsp-no-data">' + noDataMessages[type] + '</td></tr>');
                            }
                        });
                    } else {
                        Toast.show(response.data.message, 'error');
                    }
                },
                error: function() {
                    Toast.show('An error occurred. Please try again.', 'error');
                }
            });
        }
    };

    // Pagination handler for user management table
    const Pagination = {
        currentPage: 1,
        perPage: 20,
        totalUsers: 0,
        totalPages: 0,
        
        init: function() {
            const $pagination = $('#gsp-user-pagination');
            if ($pagination.length === 0) return;
            
            this.totalUsers = parseInt($pagination.data('total')) || 0;
            this.perPage = parseInt($pagination.data('per-page')) || 20;
            this.totalPages = Math.ceil(this.totalUsers / this.perPage);
            
            const self = this;
            
            $('.gsp-prev-btn').on('click', function() {
                if (self.currentPage > 1) {
                    self.goToPage(self.currentPage - 1);
                }
            });
            
            $('.gsp-next-btn').on('click', function() {
                if (self.currentPage < self.totalPages) {
                    self.goToPage(self.currentPage + 1);
                }
            });
        },
        
        goToPage: function(page) {
            this.currentPage = page;
            
            const start = (page - 1) * this.perPage;
            const end = start + this.perPage;
            
            // Show/hide rows based on pagination
            const $rows = $('#gsp-users-tbody .gsp-user-row');
            
            $rows.each(function(index) {
                if (index >= start && index < end) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            // Update page info
            $('#gsp-current-page').text(this.currentPage);
            
            // Update button states
            $('.gsp-prev-btn').prop('disabled', this.currentPage <= 1);
            $('.gsp-next-btn').prop('disabled', this.currentPage >= this.totalPages);
            
            // Scroll to top of table
            const $usersSection = $('.gsp-users-section');
            if ($usersSection.length) {
                $('html, body').animate({
                    scrollTop: $usersSection.offset().top - 100
                }, 300);
            }
        },
        
        // Called when search filters results
        updateVisibility: function(visibleRows) {
            const $rows = $('#gsp-users-tbody .gsp-user-row');
            const filteredRows = [];
            
            $rows.each(function(index) {
                if ($(this).data('search-visible') !== false) {
                    filteredRows.push(this);
                }
            });
            
            // Update total pages based on filtered results
            const filteredTotal = filteredRows.length;
            this.totalPages = Math.ceil(filteredTotal / this.perPage);
            this.currentPage = 1;
            
            // Show first page of filtered results
            filteredRows.forEach((row, index) => {
                if (index < this.perPage) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
            
            // Update pagination info
            $('#gsp-current-page').text(this.currentPage);
            $('#gsp-total-pages').text(this.totalPages || 1);
            
            // Update button states
            $('.gsp-prev-btn').prop('disabled', this.currentPage <= 1);
            $('.gsp-next-btn').prop('disabled', this.currentPage >= this.totalPages || this.totalPages <= 1);
        },
        
        // Reset pagination to original state
        reset: function() {
            this.currentPage = 1;
            this.totalPages = Math.ceil(this.totalUsers / this.perPage);
            
            const $rows = $('#gsp-users-tbody .gsp-user-row');
            $rows.each(function(index) {
                $(this).removeData('search-visible');
                if (index < 20) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            $('#gsp-current-page').text(1);
            $('#gsp-total-pages').text(this.totalPages);
            
            $('.gsp-prev-btn').prop('disabled', true);
            $('.gsp-next-btn').prop('disabled', this.totalPages <= 1);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        ThemeManager.init();
        Modal.init();
        Tabs.init();
        UserSearch.init();
        UserFilter.init();
        AdminForms.init();
        Pagination.init();
        
        // Iconify icon scanning - ensure all icons load properly
        const scanIconify = function() {
            if (typeof Iconify !== 'undefined' && typeof Iconify.scan === 'function') {
                Iconify.scan();
            }
        };
        
        // Scan immediately and after short delays to catch late-loading icons
        scanIconify();
        setTimeout(scanIconify, 100);
        setTimeout(scanIconify, 300);
        setTimeout(scanIconify, 500);
        setTimeout(scanIconify, 1000);
        setTimeout(scanIconify, 2000);
        
        // If Iconify isn't loaded yet, wait and retry
        if (typeof Iconify === 'undefined') {
            let retryCount = 0;
            const maxRetries = 20;
            const retryInterval = setInterval(function() {
                retryCount++;
                if (typeof Iconify !== 'undefined') {
                    Iconify.scan();
                    clearInterval(retryInterval);
                } else if (retryCount >= maxRetries) {
                    clearInterval(retryInterval);
                    console.warn('Iconify failed to load after ' + maxRetries + ' attempts');
                }
            }, 200);
        }
    });

})(jQuery);
