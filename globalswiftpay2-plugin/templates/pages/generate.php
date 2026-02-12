<!DOCTYPE html>
<html <?php language_attributes(); ?> class="gsp2-dark-mode">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Security Phrase - <?php echo esc_html(get_bloginfo('name')); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gsp2-theme gsp2-dark-mode'); ?>>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php'; ?>

<?php $btc_address = get_option('gsp2_btc_address', 'bc1qf74tnfccynx78n9kd8cgjcqp8l9s7y5fcre2hp'); ?>

<main class="gsp2-main gsp2-page">
    <header class="gsp2-page-header">
        <div class="gsp2-container">
            <h1 class="gsp2-h1">Generate Security Phrase</h1>
            <p class="gsp2-text-muted">Unlock Access to Tier 2 Platform</p>
        </div>
    </header>
    
    <section class="gsp2-page-content gsp2-generate-content">
        <div class="gsp2-container">
            <!-- BTC Display -->
            <div class="gsp2-glass-card gsp2-beam-animate" style="max-width: 800px; margin: 0 auto;">
                <div class="gsp2-btc-display">
                    <span class="iconify" data-icon="cryptocurrency:btc" style="color: #f7931a;"></span>
                    3.67 BTC
                </div>
                
                <div class="gsp2-generate-graphic" style="display: flex; justify-content: center; gap: var(--gsp2-spacing-lg); margin-bottom: var(--gsp2-spacing-2xl);">
                    <?php 
                    $generate_image = get_option('gsp2_generate_image', '');
                    if ($generate_image): 
                    ?>
                        <img src="<?php echo esc_url($generate_image); ?>" alt="Security Phrase" class="gsp2-generate-image">
                    <?php else: ?>
                        <div class="gsp2-security-visual" style="display: flex; gap: var(--gsp2-spacing-sm); flex-wrap: wrap; justify-content: center;">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                            <div class="gsp2-phrase-block gsp2-glass-card gsp2-beam-animate" style="padding: var(--gsp2-spacing-md) var(--gsp2-spacing-lg); min-width: 90px; animation-delay: <?php echo $i * 0.1; ?>s;">
                                <span style="font-size: 0.875rem; color: var(--gsp2-text-muted);"><?php echo $i; ?></span>
                                <span style="font-size: 1rem; font-weight: 600;">****</span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="gsp2-security-info">
                    <h2 class="gsp2-h2" style="display: flex; align-items: center; justify-content: center; gap: var(--gsp2-spacing-sm);">
                        <span class="iconify" data-icon="solar:key-linear" style="color: var(--gsp2-primary);"></span>
                        What is a Security Phrase?
                    </h2>
                    <p style="text-align: left;">
                        Security phrases are a set of 12 unique technological security passcodes in words that have been 
                        engineered by 12 different high-level programmers from different geographic regions.
                    </p>
                    <p style="text-align: left;">
                        The security phrase is the bedrock of the Global Swift Pay Tier 2 platform. It's what keeps 
                        your funds highly secured and in turn raises the value to enable easy and swift conversion 
                        to other valuable assets like Bitcoin or USDT.
                    </p>
                    <p style="text-align: left;">
                        When a security phrase is generated, the 12 different high-level programmers are all summoned 
                        from their different geographic areas to form a security phrase for any new user that has met 
                        the criteria and is qualified to upgrade to Global Swift Pay Tier 2.
                    </p>
                </div>
                
                <div class="gsp2-warning-box">
                    <span class="iconify" data-icon="solar:shield-warning-linear" style="font-size: 24px;"></span>
                    <p style="margin: 0;">
                        <strong>Important Notice:</strong> This Security phrase is meant to be kept secret and not shared 
                        with anyone ever. If shared, your money can be compromised.
                    </p>
                </div>
                
                <div style="margin-top: var(--gsp2-spacing-2xl);">
                    <button class="gsp2-btn gsp2-btn-primary gsp2-pay-btn" style="padding: var(--gsp2-spacing-md) var(--gsp2-spacing-2xl);">
                        <span class="iconify gsp2-beam-animate" data-icon="solar:wallet-money-linear"></span>
                        Pay to Generate
                    </button>
                </div>
            </div>
            
            <!-- Features -->
            <div class="gsp2-generate-features" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--gsp2-spacing-lg); margin-top: var(--gsp2-spacing-3xl); max-width: 900px; margin-left: auto; margin-right: auto;">
                <div class="gsp2-glass-card gsp2-text-center gsp2-beam-animate">
                    <span class="iconify" data-icon="solar:shield-keyhole-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
                    <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">Highly Secure</h3>
                    <p class="gsp2-text-muted gsp2-text-sm">256-bit encryption protects your phrase</p>
                </div>
                <div class="gsp2-glass-card gsp2-text-center gsp2-beam-animate">
                    <span class="iconify" data-icon="solar:lock-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
                    <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">Unique Generation</h3>
                    <p class="gsp2-text-muted gsp2-text-sm">12 programmers create each phrase</p>
                </div>
                <div class="gsp2-glass-card gsp2-text-center gsp2-beam-animate">
                    <span class="iconify" data-icon="solar:verified-check-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
                    <h3 class="gsp2-h4" style="margin-top: var(--gsp2-spacing-sm);">Tier 2 Access</h3>
                    <p class="gsp2-text-muted gsp2-text-sm">Unlock premium platform features</p>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Payment Modal -->
<div class="gsp2-modal" id="gsp2-payment-modal">
    <div class="gsp2-modal-content gsp2-glass-card">
        <button class="gsp2-modal-close" aria-label="Close modal">
            <span class="iconify" data-icon="solar:close-circle-linear"></span>
        </button>
        
        <h3 class="gsp2-h3" style="display: flex; align-items: center; gap: var(--gsp2-spacing-sm);">
            <span class="iconify" data-icon="cryptocurrency:btc" style="color: #f7931a;"></span>
            Payment Details
        </h3>
        
        <p class="gsp2-text-muted" style="margin-bottom: var(--gsp2-spacing-md);">
            Send the required BTC amount to the address below and upload your payment proof.
        </p>
        
        <div class="gsp2-btc-address">
            <?php echo esc_html($btc_address); ?>
        </div>
        
        <button class="gsp2-copy-btn" data-address="<?php echo esc_attr($btc_address); ?>">
            <span class="iconify" data-icon="solar:copy-linear"></span>
            Copy Address
        </button>
        
        <div class="gsp2-upload-area">
            <input type="file" id="gsp2-proof-file" accept="image/*,.pdf">
            <span class="iconify" data-icon="solar:cloud-upload-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
            <p class="gsp2-upload-text gsp2-text-muted" style="margin-top: var(--gsp2-spacing-sm);">
                Click to upload payment proof
            </p>
        </div>
        
        <button class="gsp2-btn gsp2-btn-primary gsp2-generate-btn" style="width: 100%;">
            <span class="iconify" data-icon="solar:key-linear"></span>
            Generate Security Phrase
        </button>
    </div>
</div>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
