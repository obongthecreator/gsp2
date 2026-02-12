<?php
/**
 * Dashboard template for GlobalSwiftPay
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$balance = GSP_User::get_balance($user_id);
$transactions = GSP_Transactions::get_user_transactions($user_id, 10);
$settings = GSP_Database::get_all_settings();

// Use custom avatar if set, otherwise use Gravatar
$custom_avatar_id = get_user_meta($user_id, 'gsp_custom_avatar', true);
if ($custom_avatar_id) {
    $avatar_url = wp_get_attachment_url($custom_avatar_id);
} else {
    $avatar_url = get_avatar_url($user_id, array('size' => 100));
}
$display_name = $current_user->display_name ?: $current_user->user_login;
?>

<div class="gsp-dashboard">
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
                <div class="gsp-avatar gsp-avatar-clickable" data-modal="profile-modal" title="<?php esc_attr_e('Edit Profile', 'globalswiftpay-dashboard'); ?>">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($display_name); ?>" id="gsp-header-avatar">
                    <div class="gsp-avatar-overlay">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                </div>
                <div class="gsp-user-details">
                    <h2><?php esc_html_e('Welcome back,', 'globalswiftpay-dashboard'); ?></h2>
                    <h1 id="gsp-header-name"><?php echo esc_html($display_name); ?></h1>
                </div>
            </div>
            <div class="gsp-header-actions">
                <button class="gsp-btn gsp-btn-icon-only gsp-theme-toggle" id="gsp-theme-toggle" title="Toggle Dark/Light Mode">
                    <span class="gsp-theme-icon gsp-moon-icon" data-theme-icon="dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </span>
                    <span class="gsp-theme-icon gsp-sun-icon" data-theme-icon="light" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                    </span>
                </button>
                <a href="https://globalswiftpay2.com" class="gsp-btn gsp-btn-home gsp-btn-icon-only" title="Go to Home">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
                <a href="<?php echo esc_url(wp_logout_url('https://globalswiftpay2.com/gsp2-login/')); ?>" class="gsp-btn gsp-btn-logout">
                    <?php esc_html_e('Logout', 'globalswiftpay-dashboard'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Balance Cards Section -->
    <div class="gsp-balance-section">
        <div class="gsp-glass-card gsp-balance-card gsp-wallet-balance">
            <div class="gsp-card-icon">
                <span class="iconify" data-icon="solar:wallet-linear" width="24" height="24"></span>
            </div>
            <div class="gsp-card-content">
                <h3><?php esc_html_e('Wallet Balance', 'globalswiftpay-dashboard'); ?></h3>
                <p class="gsp-amount" id="gsp-wallet-balance">$<?php echo esc_html(number_format($balance->wallet_balance, 2)); ?></p>
            </div>
        </div>
        
        <div class="gsp-glass-card gsp-balance-card gsp-savings-balance" id="savings-card">
            <div class="gsp-card-icon">
                <span class="iconify" data-icon="solar:safe-circle-linear" width="24" height="24"></span>
            </div>
            <div class="gsp-card-content">
                <h3><?php esc_html_e('Savings', 'globalswiftpay-dashboard'); ?></h3>
                <p class="gsp-amount" id="gsp-savings-balance">$<?php echo esc_html(number_format($balance->savings_balance, 2)); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="gsp-actions-section">
        <h2 class="gsp-section-title"><?php esc_html_e('Quick Actions', 'globalswiftpay-dashboard'); ?></h2>
        <div class="gsp-actions-grid">
            <button class="gsp-glass-btn gsp-action-btn" data-modal="profile-modal">
                <span class="iconify gsp-btn-icon" data-icon="solar:user-linear" width="24" height="24"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Edit Profile', 'globalswiftpay-dashboard'); ?></span>
            </button>
            
            <button class="gsp-glass-btn gsp-action-btn" data-modal="transfer-modal">
                <span class="iconify gsp-btn-icon" data-icon="solar:transfer-horizontal-linear" width="24" height="24"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Transfer', 'globalswiftpay-dashboard'); ?></span>
            </button>
            
            <button class="gsp-glass-btn gsp-action-btn gsp-convert-btc-btn" data-modal="convert-btc-modal">
                <span class="iconify gsp-btn-icon gsp-btc-icon" data-icon="cryptocurrency:btc" width="24" height="24"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Convert to BTC', 'globalswiftpay-dashboard'); ?></span>
            </button>
            
            <button class="gsp-glass-btn gsp-action-btn" data-modal="convert-usdt-modal">
                <span class="iconify gsp-btn-icon" data-icon="solar:dollar-linear" width="24" height="24"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Convert to USDT', 'globalswiftpay-dashboard'); ?></span>
            </button>
            
            <button class="gsp-glass-btn gsp-action-btn" data-modal="convert-bank-modal">
                <span class="iconify gsp-btn-icon" data-icon="solar:buildings-linear" width="24" height="24"></span>
                <span class="gsp-btn-text"><?php esc_html_e('Convert to Bank', 'globalswiftpay-dashboard'); ?></span>
            </button>
        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="gsp-transactions-section">
        <h2 class="gsp-section-title"><?php esc_html_e('Transaction History', 'globalswiftpay-dashboard'); ?></h2>
        <div class="gsp-glass-card gsp-transactions-card">
            <div class="gsp-transactions-table-wrapper">
                <table class="gsp-transactions-table" id="gsp-transactions-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Type', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Sender', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Amount', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Status', 'globalswiftpay-dashboard'); ?></th>
                            <th><?php esc_html_e('Date', 'globalswiftpay-dashboard'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="5" class="gsp-no-transactions"><?php esc_html_e('No transactions yet.', 'globalswiftpay-dashboard'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td>
                                        <span class="gsp-transaction-type gsp-type-<?php echo esc_attr(str_replace('_', '-', $transaction->type)); ?>">
                                            <?php echo esc_html(ucwords(str_replace('_', ' ', $transaction->type))); ?>
                                        </span>
                                    </td>
                                    <td class="gsp-transaction-sender"><?php echo esc_html($transaction->sender_name ?? ''); ?></td>
                                    <td class="gsp-transaction-amount">$<?php echo esc_html(number_format($transaction->amount, 2)); ?></td>
                                    <td>
                                        <span class="gsp-status gsp-status-<?php echo esc_attr($transaction->status); ?>">
                                            <?php echo esc_html(ucfirst($transaction->status)); ?>
                                        </span>
                                    </td>
                                    <td class="gsp-transaction-date"><?php echo esc_html(date('M j, Y g:i A', strtotime($transaction->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Transfer -->
<div id="transfer-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Transfer Funds', 'globalswiftpay-dashboard'); ?></h2>
        <p class="gsp-modal-subtitle"><?php esc_html_e('Available balance:', 'globalswiftpay-dashboard'); ?> <strong>$<?php echo esc_html(number_format($balance->wallet_balance, 2)); ?></strong></p>
        
        <form id="gsp-transfer-form" class="gsp-form">
            <div class="gsp-form-group">
                <label for="transfer-email"><?php esc_html_e('Recipient Email', 'globalswiftpay-dashboard'); ?></label>
                <input type="email" id="transfer-email" name="recipient_email" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="transfer-amount"><?php esc_html_e('Amount to Transfer ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="transfer-amount" name="amount" class="gsp-input gsp-amount-input" data-max="<?php echo esc_attr($balance->wallet_balance); ?>" placeholder="0.00" required>
            </div>
            <div class="gsp-form-group">
                <label for="transfer-token"><?php esc_html_e('Token Code', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="transfer-token" name="token_code" class="gsp-input" placeholder="<?php esc_attr_e('Enter your token code', 'globalswiftpay-dashboard'); ?>" required>
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Request Transfer', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Convert to BTC -->
<div id="convert-btc-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Convert to Bitcoin (BTC)', 'globalswiftpay-dashboard'); ?></h2>
        
        <form id="gsp-convert-btc-form" class="gsp-form">
            <input type="hidden" name="conversion_type" value="btc">
            <div class="gsp-form-group">
                <label for="btc-email"><?php esc_html_e('Email Address', 'globalswiftpay-dashboard'); ?></label>
                <input type="email" id="btc-email" name="email" class="gsp-input" value="<?php echo esc_attr($current_user->user_email); ?>" required>
            </div>
            <div class="gsp-form-group">
                <label for="btc-amount"><?php esc_html_e('Amount to Convert ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="btc-amount" name="amount" class="gsp-input gsp-amount-input" data-max="<?php echo esc_attr($balance->wallet_balance); ?>" placeholder="0.00" required>
            </div>
            <div class="gsp-form-group">
                <label for="btc-address"><?php esc_html_e('Bitcoin Address', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="btc-address" name="btc_address" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="btc-security"><?php esc_html_e('Security Phrase', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="btc-security" name="security_phrase" class="gsp-input" required>
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Convert to BTC', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Convert to USDT -->
<div id="convert-usdt-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Convert to Tether (USDT)', 'globalswiftpay-dashboard'); ?></h2>
        
        <form id="gsp-convert-usdt-form" class="gsp-form">
            <input type="hidden" name="conversion_type" value="usdt">
            <div class="gsp-form-group">
                <label for="usdt-email"><?php esc_html_e('Email Address', 'globalswiftpay-dashboard'); ?></label>
                <input type="email" id="usdt-email" name="email" class="gsp-input" value="<?php echo esc_attr($current_user->user_email); ?>" required>
            </div>
            <div class="gsp-form-group">
                <label for="usdt-amount"><?php esc_html_e('Amount to Convert ($)', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="usdt-amount" name="amount" class="gsp-input gsp-amount-input" data-max="<?php echo esc_attr($balance->wallet_balance); ?>" placeholder="0.00" required>
            </div>
            <div class="gsp-form-group">
                <label for="usdt-address"><?php esc_html_e('Tether (USDT) Address', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="usdt-address" name="usdt_address" class="gsp-input" required>
            </div>
            <div class="gsp-form-group">
                <label for="usdt-security"><?php esc_html_e('Security Phrase', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="usdt-security" name="security_phrase" class="gsp-input" required>
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Convert to USDT', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Convert to Bank -->
<div id="convert-bank-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card gsp-modal-large">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Convert to Bank Transfer', 'globalswiftpay-dashboard'); ?></h2>
        
        <form id="gsp-convert-bank-form" class="gsp-form">
            <input type="hidden" name="conversion_type" value="bank">
            <div class="gsp-form-row">
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-email"><?php esc_html_e('Email Address', 'globalswiftpay-dashboard'); ?></label>
                    <input type="email" id="bank-email" name="email" class="gsp-input" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                </div>
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-amount"><?php esc_html_e('Amount in Dollars ($)', 'globalswiftpay-dashboard'); ?></label>
                    <input type="text" id="bank-amount" name="amount" class="gsp-input gsp-amount-input" data-max="<?php echo esc_attr($balance->wallet_balance); ?>" placeholder="0.00" required>
                </div>
            </div>
            <div class="gsp-form-row">
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-name"><?php esc_html_e('Bank Name', 'globalswiftpay-dashboard'); ?></label>
                    <input type="text" id="bank-name" name="bank_name" class="gsp-input" required>
                </div>
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-account-name"><?php esc_html_e('Account Name', 'globalswiftpay-dashboard'); ?></label>
                    <input type="text" id="bank-account-name" name="account_name" class="gsp-input" required>
                </div>
            </div>
            <div class="gsp-form-row">
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-account-no"><?php esc_html_e('Bank Account No.', 'globalswiftpay-dashboard'); ?></label>
                    <input type="text" id="bank-account-no" name="account_number" class="gsp-input" required>
                </div>
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-swift"><?php esc_html_e('SWIFT/IFSC/Routing No./Sort Code/IBAN', 'globalswiftpay-dashboard'); ?></label>
                    <input type="text" id="bank-swift" name="swift_code" class="gsp-input" required>
                </div>
            </div>
            <div class="gsp-form-row">
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-address"><?php esc_html_e('Bank Address', 'globalswiftpay-dashboard'); ?></label>
                    <input type="text" id="bank-address" name="bank_address" class="gsp-input" required>
                </div>
                <div class="gsp-form-group gsp-form-half">
                    <label for="bank-country"><?php esc_html_e('Country', 'globalswiftpay-dashboard'); ?></label>
                    <select id="bank-country" name="country" class="gsp-select gsp-country-select" required>
                        <option value=""><?php esc_html_e('Select Country', 'globalswiftpay-dashboard'); ?></option>
                        <option value="Afghanistan">Afghanistan</option>
                        <option value="Albania">Albania</option>
                        <option value="Algeria">Algeria</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaijan">Azerbaijan</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Belgium">Belgium</option>
                        <option value="Belize">Belize</option>
                        <option value="Benin">Benin</option>
                        <option value="Bhutan">Bhutan</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Brazil">Brazil</option>
                        <option value="Brunei">Brunei</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cabo Verde">Cabo Verde</option>
                        <option value="Cambodia">Cambodia</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Canada">Canada</option>
                        <option value="Central African Republic">Central African Republic</option>
                        <option value="Chad">Chad</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Comoros">Comoros</option>
                        <option value="Congo">Congo</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Croatia">Croatia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Cyprus">Cyprus</option>
                        <option value="Czech Republic">Czech Republic</option>
                        <option value="Denmark">Denmark</option>
                        <option value="Djibouti">Djibouti</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Dominican Republic">Dominican Republic</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egypt">Egypt</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                        <option value="Eritrea">Eritrea</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Eswatini">Eswatini</option>
                        <option value="Ethiopia">Ethiopia</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Finland">Finland</option>
                        <option value="France">France</option>
                        <option value="Gabon">Gabon</option>
                        <option value="Gambia">Gambia</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Germany">Germany</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Greece">Greece</option>
                        <option value="Grenada">Grenada</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guinea">Guinea</option>
                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Haiti">Haiti</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hungary">Hungary</option>
                        <option value="Iceland">Iceland</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Iran">Iran</option>
                        <option value="Iraq">Iraq</option>
                        <option value="Ireland">Ireland</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japan">Japan</option>
                        <option value="Jordan">Jordan</option>
                        <option value="Kazakhstan">Kazakhstan</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Kiribati">Kiribati</option>
                        <option value="North Korea">North Korea</option>
                        <option value="South Korea">South Korea</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                        <option value="Laos">Laos</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Lebanon">Lebanon</option>
                        <option value="Lesotho">Lesotho</option>
                        <option value="Liberia">Liberia</option>
                        <option value="Libya">Libya</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Lithuania">Lithuania</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Madagascar">Madagascar</option>
                        <option value="Malawi">Malawi</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Mali">Mali</option>
                        <option value="Malta">Malta</option>
                        <option value="Marshall Islands">Marshall Islands</option>
                        <option value="Mauritania">Mauritania</option>
                        <option value="Mauritius">Mauritius</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Micronesia">Micronesia</option>
                        <option value="Moldova">Moldova</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Montenegro">Montenegro</option>
                        <option value="Morocco">Morocco</option>
                        <option value="Mozambique">Mozambique</option>
                        <option value="Myanmar">Myanmar</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Niger">Niger</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="North Macedonia">North Macedonia</option>
                        <option value="Norway">Norway</option>
                        <option value="Oman">Oman</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="Palau">Palau</option>
                        <option value="Palestine">Palestine</option>
                        <option value="Panama">Panama</option>
                        <option value="Papua New Guinea">Papua New Guinea</option>
                        <option value="Paraguay">Paraguay</option>
                        <option value="Peru">Peru</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Poland">Poland</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Romania">Romania</option>
                        <option value="Russia">Russia</option>
                        <option value="Rwanda">Rwanda</option>
                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                        <option value="Saint Lucia">Saint Lucia</option>
                        <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                        <option value="Samoa">Samoa</option>
                        <option value="San Marino">San Marino</option>
                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Serbia">Serbia</option>
                        <option value="Seychelles">Seychelles</option>
                        <option value="Sierra Leone">Sierra Leone</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Slovakia">Slovakia</option>
                        <option value="Slovenia">Slovenia</option>
                        <option value="Solomon Islands">Solomon Islands</option>
                        <option value="Somalia">Somalia</option>
                        <option value="South Africa">South Africa</option>
                        <option value="South Sudan">South Sudan</option>
                        <option value="Spain">Spain</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <option value="Sudan">Sudan</option>
                        <option value="Suriname">Suriname</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Switzerland">Switzerland</option>
                        <option value="Syria">Syria</option>
                        <option value="Taiwan">Taiwan</option>
                        <option value="Tajikistan">Tajikistan</option>
                        <option value="Tanzania">Tanzania</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Timor-Leste">Timor-Leste</option>
                        <option value="Togo">Togo</option>
                        <option value="Tonga">Tonga</option>
                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                        <option value="Tunisia">Tunisia</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Turkmenistan">Turkmenistan</option>
                        <option value="Tuvalu">Tuvalu</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="Uruguay">Uruguay</option>
                        <option value="Uzbekistan">Uzbekistan</option>
                        <option value="Vanuatu">Vanuatu</option>
                        <option value="Vatican City">Vatican City</option>
                        <option value="Venezuela">Venezuela</option>
                        <option value="Vietnam">Vietnam</option>
                        <option value="Yemen">Yemen</option>
                        <option value="Zambia">Zambia</option>
                        <option value="Zimbabwe">Zimbabwe</option>
                    </select>
                </div>
            </div>
            <div class="gsp-form-group">
                <label for="bank-security"><?php esc_html_e('Security Phrase', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="bank-security" name="security_phrase" class="gsp-input" required>
            </div>
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Convert to Bank Transfer', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Modal: Edit Profile -->
<div id="profile-modal" class="gsp-modal">
    <div class="gsp-modal-overlay"></div>
    <div class="gsp-modal-container gsp-glass-card">
        <button class="gsp-modal-close">&times;</button>
        <h2 class="gsp-modal-title"><?php esc_html_e('Edit Profile', 'globalswiftpay-dashboard'); ?></h2>
        
        <form id="gsp-profile-form" class="gsp-form" enctype="multipart/form-data">
            <div class="gsp-profile-avatar-section">
                <div class="gsp-profile-avatar-preview">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($display_name); ?>" id="gsp-profile-avatar-preview">
                    <div class="gsp-avatar-edit-overlay">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </div>
                </div>
                <input type="file" id="profile-avatar" name="profile_avatar" class="gsp-file-hidden" accept="image/*">
                <label for="profile-avatar" class="gsp-btn gsp-btn-secondary gsp-btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <?php esc_html_e('Upload Photo', 'globalswiftpay-dashboard'); ?>
                </label>
                <p class="gsp-avatar-hint"><?php esc_html_e('JPG, PNG or GIF. Max 2MB', 'globalswiftpay-dashboard'); ?></p>
            </div>
            
            <div class="gsp-form-group">
                <label for="profile-display-name"><?php esc_html_e('Display Name', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="profile-display-name" name="display_name" class="gsp-input" value="<?php echo esc_attr($display_name); ?>" required>
            </div>
            
            <div class="gsp-form-group">
                <label for="profile-email"><?php esc_html_e('Email Address', 'globalswiftpay-dashboard'); ?></label>
                <input type="email" id="profile-email" name="email" class="gsp-input" value="<?php echo esc_attr($current_user->user_email); ?>" required>
            </div>
            
            <div class="gsp-form-group">
                <label for="profile-first-name"><?php esc_html_e('First Name', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="profile-first-name" name="first_name" class="gsp-input" value="<?php echo esc_attr($current_user->first_name); ?>">
            </div>
            
            <div class="gsp-form-group">
                <label for="profile-last-name"><?php esc_html_e('Last Name', 'globalswiftpay-dashboard'); ?></label>
                <input type="text" id="profile-last-name" name="last_name" class="gsp-input" value="<?php echo esc_attr($current_user->last_name); ?>">
            </div>
            
            <div class="gsp-form-divider">
                <span><?php esc_html_e('Change Password (optional)', 'globalswiftpay-dashboard'); ?></span>
            </div>
            
            <div class="gsp-form-group">
                <label for="profile-current-password"><?php esc_html_e('Current Password', 'globalswiftpay-dashboard'); ?></label>
                <input type="password" id="profile-current-password" name="current_password" class="gsp-input" placeholder="<?php esc_attr_e('Enter to change password', 'globalswiftpay-dashboard'); ?>">
            </div>
            
            <div class="gsp-form-row">
                <div class="gsp-form-group gsp-form-half">
                    <label for="profile-new-password"><?php esc_html_e('New Password', 'globalswiftpay-dashboard'); ?></label>
                    <input type="password" id="profile-new-password" name="new_password" class="gsp-input" placeholder="<?php esc_attr_e('Leave blank to keep current', 'globalswiftpay-dashboard'); ?>">
                </div>
                <div class="gsp-form-group gsp-form-half">
                    <label for="profile-confirm-password"><?php esc_html_e('Confirm Password', 'globalswiftpay-dashboard'); ?></label>
                    <input type="password" id="profile-confirm-password" name="confirm_password" class="gsp-input" placeholder="<?php esc_attr_e('Confirm new password', 'globalswiftpay-dashboard'); ?>">
                </div>
            </div>
            
            <button type="submit" class="gsp-btn gsp-btn-primary gsp-btn-full">
                <?php esc_html_e('Save Profile', 'globalswiftpay-dashboard'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="gsp-toast" class="gsp-toast"></div>

<!-- Iconify Initialization Script with SVG Fallbacks -->
<script>
(function() {
    // SVG icon definitions for fallback
    var iconSVGs = {
        'solar:moon-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21.5 14.078A8.5 8.5 0 0 1 9.922 2.5 8.5 8.5 0 1 0 21.5 14.078Z"/></svg>',
        'solar:sun-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="5"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>',
        'solar:home-2-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>',
        'solar:logout-2-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>',
        'solar:wallet-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20M16 14h.01"/></svg>',
        'solar:safe-circle-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>',
        'solar:card-recive-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="M12 9v6m0 0-2.5-2.5M12 15l2.5-2.5"/></svg>',
        'solar:card-send-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="M12 15V9m0 0L9.5 11.5M12 9l2.5 2.5"/></svg>',
        'solar:transfer-horizontal-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 17H4m0 0 4-4m-4 4 4 4M4 7h16m0 0-4-4m4 4-4 4"/></svg>',
        'cryptocurrency:btc': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1.64 14.57h-1.09v1.29h-.78v-1.29h-.56v1.29h-.78v-1.29H9.1v-.84h.58v-3.46H9.1v-.84h1.33v-1.29h.78v1.29h.56v-1.29h.78v1.35a1.77 1.77 0 0 1 1.16 1.66 1.34 1.34 0 0 1-.72 1.25 1.54 1.54 0 0 1 .86 1.42c0 .99-.64 1.66-1.21 1.75zm-.56-4.59h-1.53v1.47h1.53a.74.74 0 0 0 0-1.47zm.22 2.18h-1.75v1.58h1.75a.79.79 0 0 0 0-1.58z"/></svg>',
        'solar:dollar-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v2m0 8v2m-4-8.5C8 8.12 9.79 7 12 7s4 1.12 4 2.5c0 1.5-1.5 2-4 2.5s-4 1-4 2.5c0 1.38 1.79 2.5 4 2.5s4-1.12 4-2.5"/></svg>',
        'solar:buildings-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>',
        'solar:wallet-money-linear': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20M6 14h.01M10 14h4"/></svg>'
    };
    
    // Force inject Bitcoin icon specifically
    function injectBtcIcon() {
        var btcIcons = document.querySelectorAll('.gsp-btc-icon');
        btcIcons.forEach(function(el) {
            if (!el.querySelector('svg') || el.innerHTML.trim() === '') {
                el.innerHTML = iconSVGs['cryptocurrency:btc'];
                el.style.display = 'inline-flex';
                el.style.alignItems = 'center';
                el.style.justifyContent = 'center';
            }
        });
    }
    
    function scanIcons() {
        if (typeof Iconify !== "undefined" && Iconify.scan) {
            Iconify.scan();
        }
    }
    
    function applyFallbacks() {
        document.querySelectorAll('.iconify').forEach(function(el) {
            if (!el.querySelector('svg') && el.innerHTML.trim() === '') {
                var iconName = el.getAttribute('data-icon');
                if (iconName && iconSVGs[iconName]) {
                    el.innerHTML = iconSVGs[iconName];
                    el.style.display = 'inline-flex';
                }
            }
        });
        // Always try to inject BTC icon
        injectBtcIcon();
    }
    
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function() {
            scanIcons();
            injectBtcIcon();
        });
    } else {
        scanIcons();
        injectBtcIcon();
    }
    setTimeout(scanIcons, 100);
    setTimeout(injectBtcIcon, 200);
    setTimeout(scanIcons, 500);
    setTimeout(applyFallbacks, 800);
    setTimeout(scanIcons, 1500);
    setTimeout(applyFallbacks, 2000);
    setTimeout(applyFallbacks, 4000);
})();
</script>
