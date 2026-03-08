<?php
/**
 * Balance Manager Page Template
 * Renders the [gsp_admin_dashboard] shortcode for managing balances from another plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if user has admin access
if (!current_user_can('manage_options')) {
    wp_redirect(home_url('/gsp2-login/'));
    exit;
}

get_header();
?>

<div class="gsp2-main" style="min-height: 100vh; padding: 2rem;">
    <div class="gsp2-container" style="max-width: 1400px; margin: 0 auto;">
        
        <!-- Back to Admin Panel Link -->
        <div style="margin-bottom: 2rem;">
            <a href="<?php echo home_url('/gsp2-admin-panel/'); ?>" class="gsp2-btn gsp2-btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Admin Panel
            </a>
        </div>
        
        <!-- Page Title -->
        <div style="margin-bottom: 2rem; text-align: center;">
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Balance Manager</h1>
            <p style="color: #94a3b8; font-size: 1.1rem;">Manage user balances and transactions</p>
        </div>
        
        <!-- Shortcode Content -->
        <div class="gsp2-balance-manager-content">
            <?php 
            // Render the gsp_admin_dashboard shortcode from the other plugin
            if (shortcode_exists('gsp_admin_dashboard')) {
                echo do_shortcode('[gsp_admin_dashboard]'); 
            } else {
                ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 1rem; padding: 2rem; text-align: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h3 style="color: #ef4444; margin-bottom: 0.5rem;">Balance Manager Plugin Not Found</h3>
                    <p style="color: #94a3b8;">The <code>[gsp_admin_dashboard]</code> shortcode is not available. Please ensure the balance manager plugin is installed and activated.</p>
                </div>
                <?php
            }
            ?>
        </div>
        
    </div>
</div>

<style>
/* Ensure the balance manager content integrates with site styles */
.gsp2-balance-manager-content {
    background: rgba(30, 41, 59, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
    padding: 2rem;
    backdrop-filter: blur(10px);
}

body:not(.gsp2-dark) .gsp2-balance-manager-content {
    background: #f8fafc;
    border-color: #e2e8f0;
}

/* Back button styling */
.gsp2-btn-outline {
    background: transparent;
    border: 1px solid rgba(59, 130, 246, 0.5);
    color: #60a5fa;
    border-radius: 0.75rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.gsp2-btn-outline:hover {
    background: rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

body:not(.gsp2-dark) .gsp2-btn-outline {
    border-color: #3b82f6;
    color: #2563eb;
}
</style>

<?php
get_footer();
?>
