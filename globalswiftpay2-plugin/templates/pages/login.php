<?php
/**
 * GSP2 Login Page Template
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

// Handle login form submission
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gsp2_login_nonce'])) {
    if (wp_verify_nonce($_POST['gsp2_login_nonce'], 'gsp2_login_action')) {
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;
        
        if (empty($username) || empty($password)) {
            $error_message = 'Please enter both username/email and password.';
        } else {
            // Check if user entered email or username
            if (is_email($username)) {
                $user = get_user_by('email', $username);
                if ($user) {
                    $username = $user->user_login;
                }
            }
            
            $credentials = array(
                'user_login'    => $username,
                'user_password' => $password,
                'remember'      => $remember
            );
            
            $user = wp_signon($credentials, false);
            
            if (is_wp_error($user)) {
                $error_message = 'Invalid username/email or password. Please try again.';
            } else {
                $dashboard_url = get_option('gsp2_dashboard_link', 'https://globalswiftpay2.com/gsp2-dashboard/');
                wp_redirect($dashboard_url);
                exit;
            }
        }
    } else {
        $error_message = 'Security verification failed. Please try again.';
    }
}

$settings = get_option('gsp2_settings', array());
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gsp2-page gsp2-login-page'); ?>>
    
    <div class="gsp2-main">
        <!-- Navigation -->
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php'; ?>
        
        <!-- Login Section -->
        <section class="gsp2-section gsp2-login-section">
            <div class="gsp2-container">
                <div class="gsp2-login-wrapper">
                    <div class="gsp2-login-card gsp2-glass-card">
                        <div class="gsp2-login-header">
                            <div class="gsp2-login-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                    <polyline points="10 17 15 12 10 7"/>
                                    <line x1="15" y1="12" x2="3" y2="12"/>
                                </svg>
                            </div>
                            <h1 class="gsp2-login-title">Welcome Back</h1>
                            <p class="gsp2-login-subtitle">Sign in to access your Tier 2 Platform account</p>
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
                        
                        <form method="post" class="gsp2-login-form" id="gsp2-login-form">
                            <?php wp_nonce_field('gsp2_login_action', 'gsp2_login_nonce'); ?>
                            
                            <div class="gsp2-form-group">
                                <label for="username">Username or Email</label>
                                <div class="gsp2-input-wrapper">
                                    <span class="gsp2-input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </span>
                                    <input type="text" id="username" name="username" placeholder="Enter your username or email" required value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="gsp2-form-group">
                                <label for="password">Password</label>
                                <div class="gsp2-input-wrapper">
                                    <span class="gsp2-input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    </span>
                                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                    <button type="button" class="gsp2-password-toggle" onclick="togglePasswordVisibility('password', this)">
                                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg class="eye-closed" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                            <line x1="1" y1="1" x2="23" y2="23"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="gsp2-form-row">
                                <label class="gsp2-checkbox-label">
                                    <input type="checkbox" name="remember" value="1">
                                    <span class="gsp2-checkbox-custom"></span>
                                    <span>Remember me</span>
                                </label>
                                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="gsp2-forgot-link">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="gsp2-btn gsp2-btn-primary gsp2-btn-block">
                                <span>Sign In</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <polyline points="12 5 19 12 12 19"/>
                                </svg>
                            </button>
                        </form>
                        
                        <div class="gsp2-login-footer">
                            <p>Don't have an account? <a href="<?php echo esc_url(home_url('/gsp2-upgrade/')); ?>">Upgrade to Tier 2</a></p>
                        </div>
                    </div>
                    
                    <div class="gsp2-login-benefits">
                        <h3>Tier 2 Platform Benefits</h3>
                        <ul class="gsp2-benefits-list">
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>Access to Security Phrase Generation</span>
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>Convert GSP Funds to Cryptocurrencies</span>
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>P2P Transfers Between Users</span>
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>Enhanced Security Features</span>
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>24/7 Priority Support</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Footer -->
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/footer.php'; ?>
        
        <!-- Scroll to Top -->
        <?php include GSP2_PLUGIN_DIR . 'templates/partials/scroll-to-top.php'; ?>
    </div>
    
    <script>
    function togglePasswordVisibility(fieldId, button) {
        const field = document.getElementById(fieldId);
        const eyeOpen = button.querySelector('.eye-open');
        const eyeClosed = button.querySelector('.eye-closed');
        
        if (field.type === 'password') {
            field.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            field.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>
