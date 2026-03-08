<?php
/**
 * GSP2 How It Works Section Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$upgrade_link = get_option('gsp2_upgrade_link', home_url('/gsp2-upgrade/'));
$convert_link = get_option('gsp2_convert_link', '#');
$save_link = get_option('gsp2_save_link', '#');

$steps = array(
    array(
        'icon' => 'solar:user-plus-linear',
        'title' => 'Upgrade Account',
        'description' => 'Unlock premium features and higher transaction limits by upgrading your account to tier 2.',
        'link' => $upgrade_link,
        'link_text' => 'Upgrade Now'
    ),
    array(
        'icon' => 'solar:transfer-horizontal-linear',
        'title' => 'Convert GSP Funds',
        'description' => 'Seamlessly convert your GSP funds to other currencies or cryptocurrencies at competitive rates.',
        'link' => $convert_link,
        'link_text' => 'Start Converting'
    ),
    array(
        'icon' => 'solar:safe-square-linear',
        'title' => 'Save Funds',
        'description' => 'Securely store your funds and watch your balance grow with our interest-bearing accounts.',
        'link' => $save_link,
        'link_text' => 'Save Now'
    )
);
?>

<section class="gsp2-how-works gsp2-section">
    <div class="gsp2-container">
        <div class="gsp2-how-works-wrapper">
            <div class="gsp2-how-works-header">
                <h2 class="gsp2-h2">How GSP2 Works</h2>
                <p class="gsp2-text-muted">Simple steps to manage your global finances</p>
            </div>
            
            <div class="gsp2-how-works-grid">
                <?php foreach ($steps as $index => $step): ?>
                <div class="gsp2-how-works-card gsp2-glass-card">
                    <div class="gsp2-how-works-step">
                        <span class="iconify gsp2-how-works-icon" data-icon="<?php echo esc_attr($step['icon']); ?>"></span>
                    </div>
                    <h3 class="gsp2-how-works-title"><?php echo esc_html($step['title']); ?></h3>
                    <p class="gsp2-how-works-desc"><?php echo esc_html($step['description']); ?></p>
                    <a href="<?php echo esc_url($step['link']); ?>" class="gsp2-btn gsp2-btn-primary">
                        <?php echo esc_html($step['link_text']); ?>
                        <span class="iconify" data-icon="solar:arrow-right-linear"></span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
