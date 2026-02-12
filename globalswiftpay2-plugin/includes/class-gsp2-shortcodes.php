<?php
/**
 * GSP2 Shortcodes
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSP2_Shortcodes {
    
    public function __construct() {
        add_shortcode('gsp2_homepage', array($this, 'render_homepage'));
        add_shortcode('gsp2_hero', array($this, 'render_hero'));
        add_shortcode('gsp2_trust', array($this, 'render_trust_section'));
        add_shortcode('gsp2_how_it_works', array($this, 'render_how_it_works'));
        add_shortcode('gsp2_pay_online', array($this, 'render_pay_online'));
        add_shortcode('gsp2_transfer', array($this, 'render_transfer'));
        add_shortcode('gsp2_crypto_table', array($this, 'render_crypto_table'));
        add_shortcode('gsp2_contact', array($this, 'render_contact'));
        add_shortcode('gsp2_footer', array($this, 'render_footer'));
        add_shortcode('gsp2_navigation', array($this, 'render_navigation'));
        add_shortcode('gsp2_dark_mode_toggle', array($this, 'render_dark_mode_toggle'));
        add_shortcode('gsp2_upgrade', array($this, 'render_upgrade'));
        add_shortcode('gsp2_login', array($this, 'render_login'));
        add_shortcode('gsp2_admin_panel', array($this, 'render_admin_panel'));
        add_shortcode('gsp2_forgot_password', array($this, 'render_forgot_password'));
    }
    
    public function render_homepage($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/pages/home.php';
        return ob_get_clean();
    }
    
    public function render_hero($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/hero.php';
        return ob_get_clean();
    }
    
    public function render_trust_section($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/trust.php';
        return ob_get_clean();
    }
    
    public function render_how_it_works($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/how-it-works.php';
        return ob_get_clean();
    }
    
    public function render_pay_online($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/pay-online.php';
        return ob_get_clean();
    }
    
    public function render_transfer($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/transfer.php';
        return ob_get_clean();
    }
    
    public function render_crypto_table($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/crypto-table.php';
        return ob_get_clean();
    }
    
    public function render_contact($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/contact.php';
        return ob_get_clean();
    }
    
    public function render_footer($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/footer.php';
        return ob_get_clean();
    }
    
    public function render_navigation($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php';
        return ob_get_clean();
    }
    
    public function render_dark_mode_toggle($atts) {
        ob_start();
        ?>
        <button class="gsp2-dark-mode-toggle" aria-label="Toggle dark mode">
            <span class="gsp2-icon-sun" data-icon="solar:sun-linear"></span>
            <span class="gsp2-icon-moon" data-icon="solar:moon-linear"></span>
        </button>
        <?php
        return ob_get_clean();
    }
    
    public function render_upgrade($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/pages/upgrade.php';
        return ob_get_clean();
    }
    
    public function render_login($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/pages/login.php';
        return ob_get_clean();
    }
    
    public function render_admin_panel($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/pages/admin-panel.php';
        return ob_get_clean();
    }
    
    public function render_forgot_password($atts) {
        ob_start();
        include GSP2_PLUGIN_DIR . 'templates/pages/forgot-password.php';
        return ob_get_clean();
    }
}

new GSP2_Shortcodes();
