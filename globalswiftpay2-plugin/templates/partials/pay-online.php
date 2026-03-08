<?php
/**
 * GSP2 Pay Online Section Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$laptop_image = get_option('gsp2_laptop_image', '');
?>

<section class="gsp2-pay-online gsp2-section">
    <div class="gsp2-container">
        <div class="gsp2-pay-online-wrapper">
            <div class="gsp2-pay-online-inner">
            <div class="gsp2-pay-online-content">
                <h2 class="gsp2-h2">Pay Online with Confidence</h2>
                <p>
                    Experience seamless online payments using your GSP funds. Our secure platform enables you to 
                    make purchases, pay bills, and transact with businesses worldwide. With advanced encryption 
                    and fraud protection, your money is always safe.
                </p>
                <p>
                    Join thousands of users who trust Global Swift Pay for their daily transactions. 
                    Whether you're shopping online or sending money to loved ones, we've got you covered.
                </p>
                <a href="<?php echo esc_url(get_option('gsp2_dashboard_link', '/dashboard')); ?>" class="gsp2-btn gsp2-btn-glass">
                    <span class="iconify" data-icon="solar:wallet-money-linear"></span>
                    Start Using GSP Funds
                </a>
            </div>
            
            <div class="gsp2-laptop-wrapper">
                <?php if ($laptop_image): ?>
                    <img src="<?php echo esc_url($laptop_image); ?>" alt="Pay Online with GSP" class="gsp2-laptop-image">
                <?php else: ?>
                    <div class="gsp2-laptop">
                        <div class="gsp2-laptop-screen">
                            <div class="gsp2-crypto-icon gsp2-beam-animate">
                                <span class="iconify" data-icon="cryptocurrency:btc" style="color: #f7931a;"></span>
                            </div>
                            <div class="gsp2-crypto-icon gsp2-beam-animate">
                                <span class="iconify" data-icon="cryptocurrency:eth" style="color: #627eea;"></span>
                            </div>
                            <div class="gsp2-crypto-icon gsp2-beam-animate">
                                <span class="iconify" data-icon="cryptocurrency:usdt" style="color: #26a17b;"></span>
                            </div>
                            <div class="gsp2-crypto-icon gsp2-beam-animate">
                                <span class="iconify" data-icon="cryptocurrency:bnb" style="color: #f0b90b;"></span>
                            </div>
                            <div class="gsp2-crypto-icon gsp2-beam-animate">
                                <span class="iconify" data-icon="cryptocurrency:sol" style="color: #9945ff;"></span>
                            </div>
                            <div class="gsp2-crypto-icon gsp2-beam-animate">
                                <span class="iconify" data-icon="cryptocurrency:xrp" style="color: #23292f;"></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
                </div>
            </div>
        </div>
    </div>
</section>
