<?php
/**
 * GSP2 Hero Section Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_logged_in = is_user_logged_in();
$dashboard_link = get_option('gsp2_dashboard_link', 'https://globalswiftpay2.com/gsp2-dashboard/');
$login_link = home_url('/gsp2-login/');
$register_link = get_option('gsp2_register_link', wp_registration_url());
$hero_image = get_option('gsp2_hero_image', '');
?>

<section class="gsp2-hero" <?php if ($hero_image): ?>style="background-image: url('<?php echo esc_url($hero_image); ?>');"<?php endif; ?>>
    <!-- Grid Background -->
    <div class="gsp2-hero-grid"></div>
    
    <!-- Animated Circles -->
    <div class="gsp2-hero-circles">
        <div class="gsp2-hero-circle"></div>
        <div class="gsp2-hero-circle"></div>
        <div class="gsp2-hero-circle"></div>
        <div class="gsp2-hero-circle"></div>
    </div>
    
    <!-- Noodle Connectors -->
    <div class="gsp2-hero-noodle">
        <svg viewBox="0 0 800 800" width="800" height="800">
            <path d="M 400 300 Q 500 350 400 400" />
            <path d="M 300 400 Q 350 300 400 300" />
            <path d="M 400 500 Q 300 450 300 400" />
            <path d="M 500 400 Q 450 500 400 500" />
            <path d="M 350 325 Q 425 375 475 325" />
            <path d="M 475 475 Q 425 425 350 475" />
        </svg>
    </div>
    
    <!-- Content -->
    <div class="gsp2-hero-content">
        <div class="gsp2-hero-badge">
            <span class="gsp2-hero-badge-new">New</span>
            <span>Secure Global Payments</span>
            <span class="iconify" data-icon="solar:arrow-right-linear"></span>
        </div>
        
        <h1 class="gsp2-hero-title">Send and Receive Money Instantly</h1>
        
        <p class="gsp2-hero-subtitle">
            You can instantly receive and send money to almost anyone anywhere in the world, anytime.
        </p>
        
        <div class="gsp2-hero-buttons">
            <?php if ($is_logged_in): ?>
                <a href="<?php echo esc_url($dashboard_link); ?>" class="gsp2-btn gsp2-hero-btn gsp2-hero-btn-primary">
                    <span class="iconify gsp2-beam-animate" data-icon="solar:chart-square-linear"></span>
                    Dashboard
                </a>
                <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="gsp2-btn gsp2-hero-btn gsp2-hero-btn-secondary">
                    <span class="iconify gsp2-beam-animate" data-icon="solar:logout-linear"></span>
                    Logout
                </a>
            <?php else: ?>
                <a href="<?php echo esc_url(home_url('/gsp2-upgrade/')); ?>" class="gsp2-btn gsp2-hero-btn gsp2-hero-btn-primary">
                    <span class="iconify gsp2-beam-animate" data-icon="solar:arrow-up-linear"></span>
                    Upgrade Account
                </a>
                <a href="<?php echo esc_url($login_link); ?>" class="gsp2-btn gsp2-hero-btn gsp2-hero-btn-secondary">
                    <span class="iconify gsp2-beam-animate" data-icon="solar:login-linear"></span>
                    Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
