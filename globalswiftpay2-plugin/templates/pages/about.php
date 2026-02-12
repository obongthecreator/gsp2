<!DOCTYPE html>
<html <?php language_attributes(); ?> class="gsp2-dark-mode">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo esc_html(get_bloginfo('name')); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gsp2-theme gsp2-dark-mode'); ?>>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php'; ?>

<main class="gsp2-main gsp2-page">
    <header class="gsp2-page-header">
        <div class="gsp2-container">
            <h1 class="gsp2-h1">About Global Swift Pay</h1>
            <p class="gsp2-text-muted">A Decade of Excellence in Global Payment Solutions</p>
        </div>
    </header>
    
    <section class="gsp2-page-content">
        <div class="gsp2-container">
            <!-- History Section -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-beam-animate">
                <div class="gsp2-about-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--gsp2-spacing-2xl); align-items: center;">
                    <div class="gsp2-about-content">
                        <h2 class="gsp2-h2">
                            <span class="iconify" data-icon="solar:history-linear" style="color: var(--gsp2-primary);"></span>
                            History of GSP
                        </h2>
                        <p>
                            With over a decade and a half of expertise, we pride ourselves on our ever-evolving payment products. 
                            Our ethos is to always listen to our customers and commit to providing the best service and solutions.
                        </p>
                        <p>
                            Founded on the principle of making global payments accessible to everyone, Global Swift Pay has grown 
                            from a small fintech startup to a globally recognized payment solutions provider.
                        </p>
                    </div>
                    <div class="gsp2-about-graphic">
                        <?php 
                        $about_image = get_option('gsp2_about_image', '');
                        if ($about_image): 
                        ?>
                            <img src="<?php echo esc_url($about_image); ?>" alt="GSP History" class="gsp2-about-image">
                        <?php else: ?>
                            <div class="gsp2-placeholder-image">
                                <span class="iconify" data-icon="solar:buildings-linear" style="font-size: 48px;"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Mission Section -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-beam-animate" style="margin-top: var(--gsp2-spacing-2xl);">
                <h2 class="gsp2-h2">
                    <span class="iconify" data-icon="solar:target-linear" style="color: var(--gsp2-primary);"></span>
                    Our Mission
                </h2>
                <p>
                    Since its foundation, Global Swift Pay has successfully provided end-to-end, account and card-based 
                    payment solutions to businesses and individuals worldwide, offering an instant, safe and fast means 
                    of moving money around the world.
                </p>
                <p>
                    Global Swift Pay is a global payment solutions provider offering instant, safe and convenient payment 
                    services to customers and businesses across the globe.
                </p>
            </div>
            
            <!-- Values Section -->
            <div class="gsp2-content-section" style="margin-top: var(--gsp2-spacing-2xl);">
                <h2 class="gsp2-h2 gsp2-text-center">Our Core Values</h2>
                <div class="gsp2-values-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--gsp2-spacing-lg); margin-top: var(--gsp2-spacing-xl);">
                    <div class="gsp2-value-card gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:shield-check-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
                        <h3 class="gsp2-h3">Trust & Security</h3>
                        <p class="gsp2-text-muted">We prioritize the security of every transaction and the trust of our customers above all else.</p>
                    </div>
                    <div class="gsp2-value-card gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:lightbulb-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
                        <h3 class="gsp2-h3">Innovation</h3>
                        <p class="gsp2-text-muted">Constantly evolving our technology to provide cutting-edge payment solutions.</p>
                    </div>
                    <div class="gsp2-value-card gsp2-glass-card gsp2-beam-animate">
                        <span class="iconify" data-icon="solar:users-group-rounded-linear" style="font-size: 32px; color: var(--gsp2-primary);"></span>
                        <h3 class="gsp2-h3">Customer First</h3>
                        <p class="gsp2-text-muted">Every decision we make is guided by the needs and feedback of our valued customers.</p>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="gsp2-content-section" style="margin-top: var(--gsp2-spacing-2xl);">
                <div class="gsp2-stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--gsp2-spacing-lg);">
                    <div class="gsp2-stat-card gsp2-glass-card gsp2-text-center">
                        <div class="gsp2-stat-number" style="font-size: 2rem; font-weight: 700; color: var(--gsp2-primary);">15+</div>
                        <div class="gsp2-stat-label gsp2-text-muted">Years of Experience</div>
                    </div>
                    <div class="gsp2-stat-card gsp2-glass-card gsp2-text-center">
                        <div class="gsp2-stat-number" style="font-size: 2rem; font-weight: 700; color: var(--gsp2-primary);">150+</div>
                        <div class="gsp2-stat-label gsp2-text-muted">Countries Served</div>
                    </div>
                    <div class="gsp2-stat-card gsp2-glass-card gsp2-text-center">
                        <div class="gsp2-stat-number" style="font-size: 2rem; font-weight: 700; color: var(--gsp2-primary);">1M+</div>
                        <div class="gsp2-stat-label gsp2-text-muted">Active Users</div>
                    </div>
                    <div class="gsp2-stat-card gsp2-glass-card gsp2-text-center">
                        <div class="gsp2-stat-number" style="font-size: 2rem; font-weight: 700; color: var(--gsp2-primary);">$5B+</div>
                        <div class="gsp2-stat-label gsp2-text-muted">Transactions Processed</div>
                    </div>
                </div>
            </div>
            
            <!-- License Info -->
            <div class="gsp2-content-section gsp2-glass-card gsp2-text-center" style="margin-top: var(--gsp2-spacing-2xl);">
                <span class="iconify" data-icon="solar:verified-check-linear" style="font-size: 48px; color: var(--gsp2-accent);"></span>
                <h3 class="gsp2-h3" style="margin-top: var(--gsp2-spacing-md);">Licensed & Regulated</h3>
                <p class="gsp2-text-muted">
                    GSP Financial Services Commission (GSPVFSC) License #17098<br>
                    Licensed from 16.05.2019 under Financial Dealers Licensing Act
                </p>
            </div>
        </div>
    </section>
</main>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
