<?php
/**
 * Upgrade to Tier 2 Platform Page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade to Tier 2 - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gsp2-page gsp2-upgrade-page'); ?>>
    
    <div class="gsp2-main">
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php'; ?>
        
        <!-- Upgrade Hero Section -->
        <section class="gsp2-upgrade-hero">
            <div class="gsp2-container">
                <div class="gsp2-upgrade-badge">
                    <span class="gsp2-badge-new">Premium</span>
                    <span>Tier 2 Platform Access</span>
                </div>
                <h1 class="gsp2-sparkle-heading">Upgrade to Tier 2 Platform</h1>
                <p class="gsp2-hero-subtitle">Unlock advanced features, higher transaction limits, and exclusive benefits with our premium tier platform.</p>
            </div>
        </section>
        
        <!-- Upgrade Form Section -->
        <section class="gsp2-upgrade-form-section">
            <div class="gsp2-container">
                <div class="gsp2-section-wrapper gsp2-glassmorphic">
                    <div class="gsp2-upgrade-form-container">
                        <div class="gsp2-upgrade-form-header">
                            <h2 class="gsp2-sparkle-heading">Create Your Tier 2 Account</h2>
                            <p>Complete the form below to request access to our premium Tier 2 platform. Your application will be reviewed by our team.</p>
                        </div>
                        
                        <form id="gsp2-upgrade-form" class="gsp2-smart-form">
                            <?php wp_nonce_field('gsp2_upgrade_nonce', 'upgrade_nonce'); ?>
                            
                            <div class="gsp2-form-row">
                                <div class="gsp2-form-group">
                                    <label for="first_name">First Name</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:user-linear" class="gsp2-input-icon"></iconify-icon>
                                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                                    </div>
                                </div>
                                <div class="gsp2-form-group">
                                    <label for="last_name">Last Name</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:user-linear" class="gsp2-input-icon"></iconify-icon>
                                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="gsp2-form-row">
                                <div class="gsp2-form-group gsp2-full-width">
                                    <label for="username">Username</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:user-check-linear" class="gsp2-input-icon"></iconify-icon>
                                        <input type="text" id="username" name="username" placeholder="Choose a unique username" required pattern="[a-zA-Z0-9_]{3,20}">
                                    </div>
                                    <p class="gsp2-input-hint">3-20 characters, letters, numbers, and underscores only</p>
                                </div>
                            </div>
                            
                            <div class="gsp2-form-row">
                                <div class="gsp2-form-group">
                                    <label for="email">Email Address</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:letter-linear" class="gsp2-input-icon"></iconify-icon>
                                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                                    </div>
                                </div>
                                <div class="gsp2-form-group">
                                    <label for="gsp_account">GSP Account Number</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:wallet-linear" class="gsp2-input-icon"></iconify-icon>
                                        <input type="text" id="gsp_account" name="gsp_account" placeholder="Enter your GSP account number" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="gsp2-form-row">
                                <div class="gsp2-form-group">
                                    <label for="country">Country</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:globe-linear" class="gsp2-input-icon"></iconify-icon>
                                        <select id="country" name="country" required>
                                            <option value="">Select your country</option>
                                            <option value="US">United States</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="CA">Canada</option>
                                            <option value="AU">Australia</option>
                                            <option value="DE">Germany</option>
                                            <option value="FR">France</option>
                                            <option value="ES">Spain</option>
                                            <option value="IT">Italy</option>
                                            <option value="JP">Japan</option>
                                            <option value="CN">China</option>
                                            <option value="IN">India</option>
                                            <option value="BR">Brazil</option>
                                            <option value="MX">Mexico</option>
                                            <option value="NG">Nigeria</option>
                                            <option value="ZA">South Africa</option>
                                            <option value="AE">United Arab Emirates</option>
                                            <option value="SG">Singapore</option>
                                            <option value="KR">South Korea</option>
                                            <option value="RU">Russia</option>
                                            <option value="OTHER">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="gsp2-form-group">
                                    <label for="mobile">Mobile Number</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:phone-linear" class="gsp2-input-icon"></iconify-icon>
                                        <input type="tel" id="mobile" name="mobile" placeholder="+1 234 567 8900" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="gsp2-form-row">
                                <div class="gsp2-form-group gsp2-full-width">
                                    <label for="password">Password</label>
                                    <div class="gsp2-input-wrapper">
                                        <iconify-icon icon="solar:lock-linear" class="gsp2-input-icon"></iconify-icon>
                                        <input type="password" id="password" name="password" placeholder="Create a strong password" required minlength="8">
                                        <button type="button" class="gsp2-toggle-password" aria-label="Toggle password visibility">
                                            <iconify-icon icon="solar:eye-linear" class="gsp2-eye-icon"></iconify-icon>
                                        </button>
                                    </div>
                                    <p class="gsp2-input-hint">Password must be at least 8 characters long</p>
                                </div>
                            </div>
                            
                            <div class="gsp2-form-row">
                                <div class="gsp2-form-group gsp2-full-width">
                                    <label class="gsp2-checkbox-wrapper">
                                        <input type="checkbox" name="terms_agree" required>
                                        <span class="gsp2-checkmark"></span>
                                        <span>I agree to the <a href="/gsp2-terms/" target="_blank">Terms and Conditions</a> and <a href="/gsp2-privacy-policy/" target="_blank">Privacy Policy</a></span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="gsp2-form-submit">
                                <button type="submit" class="gsp2-btn gsp2-btn-primary gsp2-btn-lg gsp2-submit-upgrade">
                                    <iconify-icon icon="solar:arrow-up-linear" class="gsp2-btn-icon"></iconify-icon>
                                    <span>Upgrade Account</span>
                                </button>
                            </div>
                            
                            <div class="gsp2-form-message" style="display: none;"></div>
                        </form>
                    </div>
                    
                    <!-- Benefits Sidebar -->
                    <div class="gsp2-upgrade-benefits">
                        <h3>Tier 2 Benefits</h3>
                        <ul class="gsp2-benefits-list">
                            <li>
                                <iconify-icon icon="solar:check-circle-bold" style="color: #22c55e;"></iconify-icon>
                                <span>Higher transaction limits</span>
                            </li>
                            <li>
                                <iconify-icon icon="solar:check-circle-bold" style="color: #22c55e;"></iconify-icon>
                                <span>Priority support 24/7</span>
                            </li>
                            <li>
                                <iconify-icon icon="solar:check-circle-bold" style="color: #22c55e;"></iconify-icon>
                                <span>Lower transaction fees</span>
                            </li>
                            <li>
                                <iconify-icon icon="solar:check-circle-bold" style="color: #22c55e;"></iconify-icon>
                                <span>Access to exclusive features</span>
                            </li>
                            <li>
                                <iconify-icon icon="solar:check-circle-bold" style="color: #22c55e;"></iconify-icon>
                                <span>Advanced security options</span>
                            </li>
                            <li>
                                <iconify-icon icon="solar:check-circle-bold" style="color: #22c55e;"></iconify-icon>
                                <span>Crypto conversion tools</span>
                            </li>
                        </ul>
                        
                        <div class="gsp2-security-badge">
                            <iconify-icon icon="solar:shield-check-bold" style="color: #3b82f6; font-size: 40px;"></iconify-icon>
                            <div>
                                <strong>Secure Application</strong>
                                <span>256-bit SSL encryption</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Toggle password visibility
        $('.gsp2-toggle-password').on('click', function() {
            var input = $(this).siblings('input');
            var icon = $(this).find('.gsp2-eye-icon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.attr('icon', 'solar:eye-closed-linear');
            } else {
                input.attr('type', 'password');
                icon.attr('icon', 'solar:eye-linear');
            }
        });
        
        // Form submission
        $('#gsp2-upgrade-form').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $form.find('.gsp2-submit-upgrade');
            var $message = $form.find('.gsp2-form-message');
            
            // Disable button and show loading
            $submitBtn.prop('disabled', true).html('<iconify-icon icon="solar:refresh-linear" class="gsp2-spin"></iconify-icon> Processing...');
            
            $.ajax({
                url: gsp2_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp2_submit_upgrade',
                    nonce: gsp2_ajax.nonce,
                    first_name: $('#first_name').val(),
                    last_name: $('#last_name').val(),
                    username: $('#username').val(),
                    email: $('#email').val(),
                    gsp_account: $('#gsp_account').val(),
                    country: $('#country').val(),
                    mobile: $('#mobile').val(),
                    password: $('#password').val()
                },
                success: function(response) {
                    if (response.success) {
                        $message.removeClass('error').addClass('success').html(response.data.message).fadeIn();
                        $form[0].reset();
                    } else {
                        $message.removeClass('success').addClass('error').html(response.data.message).fadeIn();
                    }
                },
                error: function() {
                    $message.removeClass('success').addClass('error').html('An error occurred. Please try again.').fadeIn();
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('<iconify-icon icon="solar:arrow-up-linear" class="gsp2-btn-icon"></iconify-icon> <span>Upgrade Account</span>');
                }
            });
        });
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>
