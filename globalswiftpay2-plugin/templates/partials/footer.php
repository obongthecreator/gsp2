<?php
/**
 * GSP2 Footer Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$footer_text = get_option('gsp2_footer_text', 'Global Swift Pay is an Online e-wallet.');
$copyright_text = get_option('gsp2_copyright_text', 'GSP Financial Services Commission (GSPVFSC) License #17098. from 16.05.2019 under Financial Dealers Licensing Act.');
?>

<footer class="gsp2-footer">
    <div class="gsp2-container">
        <div class="gsp2-footer-inner">
            <div class="gsp2-footer-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="gsp2-nav-logo">
                    <span class="iconify gsp2-nav-logo-icon" data-icon="solar:wallet-money-linear"></span>
                    <span>GlobalSwiftPay2</span>
                </a>
                <p><?php echo esc_html($footer_text); ?></p>
                <p class="gsp2-footer-license">
                    <?php echo esc_html($copyright_text); ?>
                </p>
            </div>
            
            <div class="gsp2-footer-section">
                <h4 class="gsp2-footer-title">Quick Links</h4>
                <ul class="gsp2-footer-links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url(home_url('/gsp2-about/')); ?>">About Us</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                    <li><a href="<?php echo esc_url(home_url('/gsp2-generate/')); ?>">Generate</a></li>
                </ul>
            </div>
            
            <div class="gsp2-footer-section">
                <h4 class="gsp2-footer-title">Features</h4>
                <ul class="gsp2-footer-links">
                    <li><a href="https://globalswiftpay2.com/dashboard/">Dashboard</a></li>
                    <li><a href="https://globalswiftpay2.com/upgrade/">Upgrade Account</a></li>
                    <li><a href="https://globalswiftpay2.com/dashboard/">Convert Funds</a></li>
                    <li><a href="https://globalswiftpay2.com/dashboard/">Save Funds</a></li>
                </ul>
            </div>
            
            <div class="gsp2-footer-section">
                <h4 class="gsp2-footer-title">Legal</h4>
                <ul class="gsp2-footer-links">
                    <li><a href="<?php echo esc_url(home_url('/gsp2-privacy-policy/')); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo esc_url(home_url('/gsp2-security-policy/')); ?>">Security Policy</a></li>
                    <li><a href="<?php echo esc_url(home_url('/gsp2-terms/')); ?>">Terms & Conditions</a></li>
                </ul>
            </div>
        </div>
        
        <div class="gsp2-footer-bottom">
            <p>&copy; 2010 - <?php echo date('Y'); ?> Global Swift Pay. All rights reserved.</p>
        </div>
    </div>
</footer>
