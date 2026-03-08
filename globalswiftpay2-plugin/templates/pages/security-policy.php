<!DOCTYPE html>
<html <?php language_attributes(); ?> class="gsp2-dark-mode">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Policy - <?php echo esc_html(get_bloginfo('name')); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gsp2-theme gsp2-dark-mode'); ?>>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php'; ?>

<main class="gsp2-main gsp2-page">
    <header class="gsp2-page-header">
        <div class="gsp2-container">
            <span class="iconify" data-icon="solar:shield-check-linear" style="font-size: 48px; color: var(--gsp2-primary);"></span>
            <h1 class="gsp2-h1">Security Policy</h1>
            <p class="gsp2-text-muted">Fraud and Payment Security Controls</p>
        </div>
    </header>
    
    <section class="gsp2-page-content">
        <div class="gsp2-container" style="max-width: 900px;">
            
            <!-- Intro Section -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-beam-animate">
                <div style="display: flex; align-items: center; gap: var(--gsp2-spacing-md); margin-bottom: var(--gsp2-spacing-lg);">
                    <span class="iconify" data-icon="solar:shield-warning-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
                    <div>
                        <h2 class="gsp2-h3" style="margin: 0;">Protecting Your Business</h2>
                        <p class="gsp2-text-muted" style="margin: 0;">For more than a decade of trusted security</p>
                    </div>
                </div>
                <p>
                    For more than a decade, we have been helping businesses to reduce fraudulent activity and 
                    chargebacks with our proven fraud and payment security tools. Contact us now to get these 
                    online fraud protection tools working for you.
                </p>
            </div>
            
            <!-- Security Features Grid -->
            <div class="gsp2-content-section" style="margin-top: var(--gsp2-spacing-2xl);">
                <h2 class="gsp2-h2 gsp2-text-center">Our Security Features</h2>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--gsp2-spacing-lg); margin-top: var(--gsp2-spacing-xl);">
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:verified-check-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">PCI DSS Compliant</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">Full compliance with Payment Card Industry Data Security Standards.</p>
                    </div>
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:shield-check-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">3-D Secure™</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">Provides indemnification for Visa and MasterCard® transactions.</p>
                    </div>
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:radar-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">BIN/IP Checking</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">Advanced verification of card BIN numbers and IP addresses.</p>
                    </div>
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:eye-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">Real-time Monitoring</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">24/7 real-time transaction monitoring and account surveillance.</p>
                    </div>
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:list-check-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">Fraud Blacklists</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">Access to payment industry fraud blacklists and databases.</p>
                    </div>
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:fingerprint-scan-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">Device Fingerprinting</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">Advanced device identification and fingerprinting technology.</p>
                    </div>
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:graph-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">Velocity Controls</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">Transaction velocity controls for risk scoring and assessment.</p>
                    </div>
                    
                    <div class="gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:lock-password-linear" style="font-size: 24px; color: var(--gsp2-accent);"></span>
                        <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">256-bit Encryption</h3>
                        <p class="gsp2-text-muted gsp2-text-sm">256-bit encryption and multiple firewalls protect all data.</p>
                    </div>
                    
                </div>
            </div>
            
            <!-- 100% Guaranteed Section -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-beam-animate" style="margin-top: var(--gsp2-spacing-2xl); text-align: center;">
                <span class="iconify" data-icon="solar:medal-ribbon-star-linear" style="font-size: 48px; color: var(--gsp2-primary);"></span>
                <h2 class="gsp2-h2" style="margin-top: var(--gsp2-spacing-md);">100% Guaranteed Transactions</h2>
                <p>
                    All transactions processed through our payment gateway are 100% indemnified. 
                    This means your business is guaranteed protection against fraud.
                </p>
            </div>
            
            <!-- Identity Verification -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-beam-animate" style="margin-top: var(--gsp2-spacing-2xl);">
                <div style="display: flex; align-items: flex-start; gap: var(--gsp2-spacing-lg);">
                    <span class="iconify" data-icon="solar:user-id-linear" style="font-size: 48px; color: var(--gsp2-primary); flex-shrink: 0;"></span>
                    <div>
                        <h2 class="gsp2-h3">Identity Verification</h2>
                        <p>
                            Our stringent identity controls, including real-time identity and age verification 
                            scrubbing technology in many countries, help you to reduce fraud on your site.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Minimize Bad Debt -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-beam-animate" style="margin-top: var(--gsp2-spacing-lg);">
                <div style="display: flex; align-items: flex-start; gap: var(--gsp2-spacing-lg);">
                    <span class="iconify" data-icon="solar:wallet-minus-linear" style="font-size: 48px; color: var(--gsp2-primary); flex-shrink: 0;"></span>
                    <div>
                        <h2 class="gsp2-h3">Minimize Bad Debt</h2>
                        <p>
                            Is your business capable of shouldering the high cost of fraud? Our online payment 
                            gateway can help you eliminate disputed transactions and chargebacks.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Database Integration -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-beam-animate" style="margin-top: var(--gsp2-spacing-lg);">
                <div style="display: flex; align-items: flex-start; gap: var(--gsp2-spacing-lg);">
                    <span class="iconify" data-icon="solar:database-linear" style="font-size: 48px; color: var(--gsp2-primary); flex-shrink: 0;"></span>
                    <div>
                        <h2 class="gsp2-h3">Database Integration</h2>
                        <p>
                            We've developed strong relationships with some of the leading regional identification 
                            verification systems in the world to ensure you have the best online payment security 
                            and fraud protection available.
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</main>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
