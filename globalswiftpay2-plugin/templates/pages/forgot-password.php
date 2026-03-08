<?php
/**
 * GSP2 Forgot Password Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Redirect if already logged in
if (is_user_logged_in()) {
    $dashboard_url = get_option('gsp2_dashboard_link', 'https://globalswiftpay2.com/gsp2-dashboard/');
    wp_redirect($dashboard_url);
    exit;
}

// Handle forgot password form submission
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gsp2_forgot_password_nonce'])) {
    if (wp_verify_nonce($_POST['gsp2_forgot_password_nonce'], 'gsp2_forgot_password_action')) {
        $user_login = sanitize_text_field($_POST['user_login']);

        if (empty($user_login)) {
            $error_message = 'Please enter your username or email address.';
        } else {
            // Check if user entered email or username
            if (is_email($user_login)) {
                $user = get_user_by('email', $user_login);
            } else {
                $user = get_user_by('login', $user_login);
            }

            if (!$user) {
                $error_message = 'No account found with that username or email address.';
            } else {
                // Generate and send the password reset link
                $result = retrieve_password($user->user_login);

                if (is_wp_error($result)) {
                    $error_message = 'Unable to send password reset email. Please try again later or contact support.';
                } else {
                    $success_message = 'A password reset link has been sent to your email address. Please check your inbox.';
                }
            }
        }
    } else {
        $error_message = 'Security verification failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gsp2-page gsp2-login-page'); ?>>
    
    <div class="gsp2-main">
        <!-- Navigation -->
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php'; ?>
        
        <!-- Forgot Password Section -->
        <section class="gsp2-section gsp2-login-section">
            <div class="gsp2-container">
                <div class="gsp2-login-wrapper">
                    <div class="gsp2-login-card gsp2-glass-card">
                        <div class="gsp2-login-header">
                            <div class="gsp2-login-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <h1 class="gsp2-login-title">Forgot Password</h1>
                            <p class="gsp2-login-subtitle">Enter your username or email address and we'll send you a link to reset your password.</p>
                        </div>
                        
                        <?php if ($error_message): ?>
                            <div class="gsp2-alert gsp2-alert-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <span><?php echo esc_html($error_message); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success_message): ?>
                            <div class="gsp2-alert gsp2-alert-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                <span><?php echo esc_html($success_message); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($success_message)): ?>
                        <form method="post" class="gsp2-login-form" id="gsp2-forgot-password-form">
                            <?php wp_nonce_field('gsp2_forgot_password_action', 'gsp2_forgot_password_nonce'); ?>
                            
                            <div class="gsp2-form-group">
                                <label for="user_login">Username or Email</label>
                                <div class="gsp2-input-wrapper">
                                    <span class="gsp2-input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </span>
                                    <input type="text" id="user_login" name="user_login" placeholder="Enter your username or email" required value="<?php echo isset($_POST['user_login']) ? esc_attr($_POST['user_login']) : ''; ?>">
                                </div>
                            </div>
                            
                            <button type="submit" class="gsp2-btn gsp2-btn-primary gsp2-btn-block">
                                <span>Send Reset Link</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <div class="gsp2-login-footer">
                            <p>Remember your password? <a href="<?php echo esc_url(home_url('/gsp2-login/')); ?>">Back to Login</a></p>
                            <p>Don't have an account? <a href="<?php echo esc_url(home_url('/gsp2-upgrade/')); ?>">Upgrade to Tier 2</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Footer -->
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
        
        <!-- Scroll to Top -->
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/scroll-to-top.php'; ?>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>
