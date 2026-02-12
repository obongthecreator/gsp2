/**
 * GlobalSwiftPay Dashboard - Main JavaScript
 */

(function($) {
    'use strict';

    // Theme Manager
    const ThemeManager = {
        init: function() {
            // Check for saved theme preference or default to dark
            const savedTheme = localStorage.getItem('gsp-theme') || 'dark';
            this.setTheme(savedTheme);
            
            // Toggle button click handler
            $('#gsp-theme-toggle').on('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                ThemeManager.setTheme(newTheme);
            });
        },
        
        setTheme: function(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('gsp-theme', theme);
            
            // Toggle icon visibility
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
            const $modal = $('#' + modalId);
            $modal.addClass('active');
            $('body').css('overflow', 'hidden');
            
            // List of modals that show balance information
            const balanceModals = ['transfer-modal', 'convert-btc-modal', 'convert-usdt-modal', 'convert-bank-modal', 'withdraw-modal'];
            
            // If this modal shows balance, refresh it immediately
            if (balanceModals.includes(modalId)) {
                Modal.refreshModalBalance($modal);
            }
        },
        
        refreshModalBalance: function($modal) {
            const $submitBtn = $modal.find('button[type="submit"]');
            const $balanceDisplay = $modal.find('.gsp-modal-subtitle strong');
            
            // Show loading state
            $submitBtn.prop('disabled', true);
            $balanceDisplay.text('Loading...');
            
            $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_get_balance',
                    nonce: gsp_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const walletBalance = response.data.wallet_balance;
                        const rawBalance = parseFloat(walletBalance.replace(/,/g, ''));
                        
                        // Update modal subtitle balance display
                        $balanceDisplay.text('$' + walletBalance);
                        
                        // Update data-max on amount inputs
                        $modal.find('.gsp-amount-input').attr('data-max', rawBalance);
                    }
                },
                complete: function() {
                    // Re-enable submit button after balance loaded
                    $submitBtn.prop('disabled', false);
                }
            });
        },
        
        close: function(modal) {
            $(modal).removeClass('active');
            $('body').css('overflow', '');
            // Reset forms
            $(modal).find('form')[0]?.reset();
        },
        
        init: function() {
            // Open modal on button click
            $(document).on('click', '[data-modal]', function(e) {
                e.preventDefault();
                const modalId = $(this).data('modal');
                Modal.open(modalId);
            });
            
            // Close modal on X click
            $(document).on('click', '.gsp-modal-close', function() {
                Modal.close($(this).closest('.gsp-modal'));
            });
            
            // Close modal on overlay click
            $(document).on('click', '.gsp-modal-overlay', function() {
                Modal.close($(this).closest('.gsp-modal'));
            });
            
            // Close modal on ESC key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.gsp-modal.active').each(function() {
                        Modal.close(this);
                    });
                }
            });
        }
    };

    // Amount Formatter - Real-time comma formatting for price fields
    const AmountFormatter = {
        init: function() {
            // Initialize all amount inputs
            $(document).on('input', '.gsp-amount-input', function() {
                AmountFormatter.formatInput(this);
            });
            
            // Handle blur to ensure proper formatting
            $(document).on('blur', '.gsp-amount-input', function() {
                AmountFormatter.formatOnBlur(this);
            });
            
            // Handle focus to select all
            $(document).on('focus', '.gsp-amount-input', function() {
                $(this).select();
            });
        },
        
        formatInput: function(input) {
            let value = input.value.replace(/[^\d.]/g, '');
            
            // Handle multiple decimal points
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            // Get the integer and decimal parts
            let integerPart = parts[0] || '';
            let decimalPart = parts.length > 1 ? parts[1] : '';
            
            // Limit decimal to 2 places
            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }
            
            // Format integer part with commas
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            
            // Rebuild the value
            if (parts.length > 1 || input.value.includes('.')) {
                input.value = integerPart + '.' + decimalPart;
            } else {
                input.value = integerPart;
            }
        },
        
        formatOnBlur: function(input) {
            let value = input.value.replace(/,/g, '');
            
            if (value === '' || value === '.') {
                input.value = '';
                return;
            }
            
            // Parse the number
            let num = parseFloat(value);
            
            if (isNaN(num) || num < 0) {
                input.value = '';
                return;
            }
            
            // Check max value if specified
            const max = parseFloat($(input).data('max'));
            if (!isNaN(max) && num > max) {
                num = max;
                Toast.show('Amount adjusted to maximum available balance', 'error');
            }
            
            // Format with commas and 2 decimal places
            input.value = num.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
        
        // Get raw number value from formatted input
        getRawValue: function(formattedValue) {
            return parseFloat(formattedValue.replace(/,/g, '')) || 0;
        }
    };

    // Form handlers
    const Forms = {
        balanceRequest: null,
        transactionsRequest: null,
        init: function() {
            // Deposit form
            $('#gsp-deposit-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitDeposit($(this));
            });
            
            // Add balance form
            $('#gsp-add-balance-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitAddBalance($(this));
            });
            
            // Withdraw form
            $('#gsp-withdraw-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitWithdraw($(this));
            });
            
            // Transfer form
            $('#gsp-transfer-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitTransfer($(this));
            });
            
            // Convert BTC form
            $('#gsp-convert-btc-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitConversion($(this), 'btc');
            });
            
            // Convert USDT form
            $('#gsp-convert-usdt-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitConversion($(this), 'usdt');
            });
            
            // Convert Bank form
            $('#gsp-convert-bank-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitConversion($(this), 'bank');
            });
            
            // Profile form
            $('#gsp-profile-form').on('submit', function(e) {
                e.preventDefault();
                Forms.submitProfile($(this));
            });
            
            // Profile avatar preview
            $('#profile-avatar').on('change', function() {
                const file = this.files[0];
                if (file) {
                    // Validate file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        Toast.show('Image must be less than 2MB', 'error');
                        this.value = '';
                        return;
                    }
                    
                    // Preview the image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#gsp-profile-avatar-preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Withdrawal method change
            $('#withdraw-method').on('change', function() {
                const method = $(this).val();
                $('#withdraw-bank-details, #withdraw-crypto-details').hide();
                
                if (method === 'bank') {
                    $('#withdraw-bank-details').show();
                } else if (method === 'btc' || method === 'usdt') {
                    $('#withdraw-crypto-details').show();
                }
            });
            
            // File input display
            $('.gsp-file-input').on('change', function() {
                const fileName = this.files[0]?.name || 'Choose file...';
                $(this).siblings('.gsp-file-label').find('.gsp-file-text').text(fileName);
            });
        },
        
        submitProfile: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            // Use FormData for file upload
            const formData = new FormData($form[0]);
            formData.append('action', 'gsp_update_profile');
            formData.append('nonce', gsp_ajax.nonce);
            
            $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        
                        // Update header with new name and avatar
                        if (response.data.display_name) {
                            $('#gsp-header-name').text(response.data.display_name);
                        }
                        if (response.data.avatar_url) {
                            $('#gsp-header-avatar').attr('src', response.data.avatar_url);
                            $('#gsp-profile-avatar-preview').attr('src', response.data.avatar_url);
                        }
                        
                        Modal.close($form.closest('.gsp-modal'));
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
        
        submitDeposit: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_submit_deposit',
                    nonce: gsp_ajax.nonce,
                    name: $form.find('[name="name"]').val(),
                    email: $form.find('[name="email"]').val(),
                    amount: $form.find('[name="amount"]').val()
                },
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        Forms.refreshTransactions();
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
        
        submitAddBalance: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            const formData = new FormData($form[0]);
            formData.append('action', 'gsp_submit_add_balance');
            formData.append('nonce', gsp_ajax.nonce);
            
            $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        Forms.refreshTransactions();
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
        
        submitWithdraw: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            const formData = {
                action: 'gsp_submit_withdrawal',
                nonce: gsp_ajax.nonce,
                amount: $form.find('[name="amount"]').val(),
                method: $form.find('[name="method"]').val(),
                details: {}
            };
            
            // Get conditional details
            $form.find('[name^="details"]').each(function() {
                const name = $(this).attr('name').replace(/details\[/, '').replace(/\]/g, '');
                formData.details[name] = $(this).val();
            });
            
            $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        Forms.refreshTransactions();
                        Forms.refreshBalance();
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
        
        submitTransfer: function($form) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            // Get raw amount value (remove commas)
            const amountRaw = AmountFormatter.getRawValue($form.find('[name="amount"]').val());
            
            $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_submit_transfer',
                    nonce: gsp_ajax.nonce,
                    recipient_email: $form.find('[name="recipient_email"]').val(),
                    amount: amountRaw,
                    token_code: $form.find('[name="token_code"]').val()
                },
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        Forms.refreshTransactions();
                        Forms.refreshBalance();
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
        
        submitConversion: function($form, type) {
            const $btn = $form.find('button[type="submit"]');
            $btn.addClass('loading');
            
            // Get raw amount value (remove commas)
            const amountRaw = AmountFormatter.getRawValue($form.find('[name="amount"]').val());
            
            const data = {
                action: 'gsp_submit_conversion',
                nonce: gsp_ajax.nonce,
                conversion_type: type,
                email: $form.find('[name="email"]').val(),
                amount: amountRaw,
                security_phrase: $form.find('[name="security_phrase"]').val()
            };
            
            // Add type-specific fields
            if (type === 'btc') {
                data.btc_address = $form.find('[name="btc_address"]').val();
            } else if (type === 'usdt') {
                data.usdt_address = $form.find('[name="usdt_address"]').val();
            } else if (type === 'bank') {
                data.bank_name = $form.find('[name="bank_name"]').val();
                data.account_name = $form.find('[name="account_name"]').val();
                data.account_number = $form.find('[name="account_number"]').val();
                data.swift_code = $form.find('[name="swift_code"]').val();
                data.bank_address = $form.find('[name="bank_address"]').val();
                data.country = $form.find('[name="country"]').val();
            }
            
            $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    $btn.removeClass('loading');
                    if (response.success) {
                        Toast.show(response.data.message, 'success');
                        Modal.close($form.closest('.gsp-modal'));
                        Forms.refreshTransactions();
                        Forms.refreshBalance();
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
        
        refreshTransactions: function() {
            if (Forms.transactionsRequest) {
                return;
            }

            Forms.transactionsRequest = $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_get_transactions',
                    nonce: gsp_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.transactions) {
                        Forms.updateTransactionsTable(response.data.transactions);
                    }
                },
                complete: function() {
                    Forms.transactionsRequest = null;
                }
            });
        },
        
        refreshBalance: function() {
            if (Forms.balanceRequest) {
                return;
            }

            Forms.balanceRequest = $.ajax({
                url: gsp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp_get_balance',
                    nonce: gsp_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#gsp-wallet-balance').text('$' + response.data.wallet_balance);
                        $('#gsp-savings-balance').text('$' + response.data.savings_balance);
                    }
                },
                complete: function() {
                    Forms.balanceRequest = null;
                }
            });
        },
        
        updateTransactionsTable: function(transactions) {
            const $tbody = $('#gsp-transactions-table tbody');
            $tbody.empty();
            
            if (transactions.length === 0) {
                $tbody.append('<tr><td colspan="5" class="gsp-no-transactions">No transactions yet.</td></tr>');
                return;
            }
            
            transactions.forEach(function(tx) {
                const typeClass = 'gsp-type-' + tx.type.replace(/_/g, '-');
                const statusClass = 'gsp-status-' + tx.status;
                const date = new Date(tx.created_at);
                const formattedDate = date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit'
                });
                
                const senderName = tx.sender_name || '';
                const row = `
                    <tr>
                        <td>
                            <span class="gsp-transaction-type ${typeClass}">
                                ${tx.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                            </span>
                        </td>
                        <td class="gsp-transaction-sender">${senderName}</td>
                        <td class="gsp-transaction-amount">$${parseFloat(tx.amount).toFixed(2)}</td>
                        <td>
                            <span class="gsp-status ${statusClass}">
                                ${tx.status.charAt(0).toUpperCase() + tx.status.slice(1)}
                            </span>
                        </td>
                        <td class="gsp-transaction-date">${formattedDate}</td>
                    </tr>
                `;
                
                $tbody.append(row);
            });
        }
    };

    // Copy to clipboard
    const Clipboard = {
        init: function() {
            $(document).on('click', '.gsp-copy-text', function() {
                const text = $(this).data('copy');
                if (text) {
                    navigator.clipboard.writeText(text).then(function() {
                        Toast.show('Copied to clipboard!', 'success');
                    }).catch(function() {
                        Toast.show('Failed to copy', 'error');
                    });
                }
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        ThemeManager.init();
        Modal.init();
        Forms.init();
        Clipboard.init();
        AmountFormatter.init();
        
        Forms.refreshBalance();
        Forms.refreshTransactions();

        // Auto-refresh balance and transactions every 5 seconds
        // Only run when page is visible to save resources
        let refreshInterval = null;
        
        const startRefreshInterval = function() {
            if (refreshInterval === null) {
                refreshInterval = setInterval(function() {
                    Forms.refreshBalance();
                    Forms.refreshTransactions();
                }, 5000);
            }
        };
        
        const stopRefreshInterval = function() {
            if (refreshInterval !== null) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        };
        
        // Start refresh interval initially
        startRefreshInterval();
        
        // Use Page Visibility API to pause/resume refreshes
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopRefreshInterval();
            } else {
                // Refresh immediately when page becomes visible
                Forms.refreshBalance();
                Forms.refreshTransactions();
                startRefreshInterval();
            }
        });

        $(window).on('beforeunload', function() {
            stopRefreshInterval();
        });
        
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
    });

})(jQuery);
