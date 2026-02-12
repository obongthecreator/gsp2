<?php
/**
 * GSP2 Navigation Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_logged_in = is_user_logged_in();
$is_admin = current_user_can('manage_options');
$dashboard_link = get_option('gsp2_dashboard_link', 'https://globalswiftpay2.com/gsp2-dashboard/');
$login_link = home_url('/gsp2-login/');
$register_url = wp_registration_url();
$register_link = get_option('gsp2_register_link', $register_url ? $register_url : home_url('/wp-login.php?action=register'));
?>

<nav class="gsp2-nav">
    <div class="gsp2-nav-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="gsp2-nav-logo">
            <span class="iconify gsp2-nav-logo-icon" data-icon="solar:wallet-money-linear"></span>
            <span>GlobalSwiftPay2</span>
        </a>
        
        <ul class="gsp2-nav-menu">
            <li>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="gsp2-nav-link">
                    <span class="iconify" data-icon="solar:home-linear"></span>
                    Home
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url(home_url('/gsp2-about/')); ?>" class="gsp2-nav-link">
                    <span class="iconify" data-icon="solar:info-circle-linear"></span>
                    About
                </a>
            </li>
            <?php if ($is_logged_in): ?>
            <li>
                <a href="<?php echo esc_url($dashboard_link); ?>" class="gsp2-nav-link">
                    <span class="iconify" data-icon="solar:chart-square-linear"></span>
                    Dashboard
                </a>
            </li>
            <?php endif; ?>
            <?php if ($is_admin): ?>
            <li>
                <a href="<?php echo esc_url(home_url('/gsp2-admin-panel/')); ?>" class="gsp2-nav-link">
                    <span class="iconify" data-icon="solar:settings-linear"></span>
                    Admin Panel
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo esc_url(home_url('/gsp2-generate/')); ?>" class="gsp2-nav-link">
                    <span class="iconify" data-icon="solar:key-linear"></span>
                    Generate
                </a>
            </li>
        </ul>
        
        <div class="gsp2-nav-actions">
            <button class="gsp2-dark-mode-toggle" aria-label="Toggle dark mode" id="gsp2-dark-mode-btn">
                <span class="gsp2-mode-icon gsp2-sun-icon">
                    <iconify-icon icon="solar:sun-bold" width="20" height="20"></iconify-icon>
                </span>
                <span class="gsp2-mode-icon gsp2-moon-icon">
                    <iconify-icon icon="solar:moon-bold" width="20" height="20"></iconify-icon>
                </span>
            </button>
            
            <?php if ($is_logged_in): ?>
                <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="gsp2-btn gsp2-btn-sm gsp2-btn-glass">
                    <span class="iconify" data-icon="solar:logout-linear"></span>
                    Logout
                </a>
            <?php else: ?>
                <a href="<?php echo esc_url($login_link); ?>" class="gsp2-btn gsp2-btn-sm gsp2-btn-glass">
                    <span class="iconify" data-icon="solar:login-linear"></span>
                    Login
                </a>
            <?php endif; ?>
            
            <button class="gsp2-nav-hamburger" aria-label="Open menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="gsp2-mobile-overlay"></div>
<div class="gsp2-mobile-menu">
    <button class="gsp2-mobile-menu-close" aria-label="Close menu">
        <span class="iconify" data-icon="solar:close-circle-linear"></span>
    </button>
    
    <ul class="gsp2-mobile-menu-list">
        <li>
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <span class="iconify" data-icon="solar:home-linear"></span>
                Home
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url('/gsp2-about/')); ?>">
                <span class="iconify" data-icon="solar:info-circle-linear"></span>
                About
            </a>
        </li>
        <?php if ($is_logged_in): ?>
        <li>
            <a href="<?php echo esc_url($dashboard_link); ?>">
                <span class="iconify" data-icon="solar:chart-square-linear"></span>
                Dashboard
            </a>
        </li>
        <?php endif; ?>
        <?php if ($is_admin): ?>
        <li>
            <a href="<?php echo esc_url(home_url('/gsp2-admin-panel/')); ?>">
                <span class="iconify" data-icon="solar:settings-linear"></span>
                Admin Panel
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="<?php echo esc_url(home_url('/gsp2-generate/')); ?>">
                <span class="iconify" data-icon="solar:key-linear"></span>
                Generate
            </a>
        </li>
        <li>
            <?php if ($is_logged_in): ?>
                <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">
                    <span class="iconify" data-icon="solar:logout-linear"></span>
                    Logout
                </a>
            <?php else: ?>
                <a href="<?php echo esc_url($login_link); ?>">
                    <span class="iconify" data-icon="solar:login-linear"></span>
                    Login
                </a>
            <?php endif; ?>
        </li>
    </ul>
</div>
