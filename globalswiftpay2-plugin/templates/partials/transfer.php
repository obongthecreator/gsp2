<?php
/**
 * GSP2 Transfer Section Template
 * FORCED STROKE SVG ICONS - Guaranteed to display
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="gsp2-transfer gsp2-section">
    <div class="gsp2-container">
        <div class="gsp2-transfer-wrapper">
            <div class="gsp2-transfer-inner">
            <div class="gsp2-transfer-graphic">
                <div class="gsp2-transfer-visual">
                    <div class="gsp2-transfer-user gsp2-glass-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block !important;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    
                    <div class="gsp2-transfer-arrow">
                        <svg viewBox="0 0 100 20">
                            <path d="M 0 10 L 85 10 L 75 5 M 85 10 L 75 15" />
                        </svg>
                    </div>
                    
                    <div class="gsp2-transfer-user gsp2-glass-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block !important;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="gsp2-transfer-content">
                <h2 class="gsp2-h2">Transfer GSP Funds Between Accounts</h2>
                <p>
                    Seamlessly move your GSP funds between your accounts or send to other GSP2 users instantly. 
                    Our peer-to-peer transfer system ensures your money reaches its destination in seconds, 
                    not days.
                </p>
                <p>
                    Whether you're paying a friend, splitting bills, or managing multiple accounts, 
                    our transfer system makes it effortless. All transfers are secured with 
                    end-to-end encryption and verified through our multi-layer authentication system.
                </p>
                <ul class="gsp2-feature-list">
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; vertical-align: middle; margin-right: 8px;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Instant transfers between GSP2 users
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; vertical-align: middle; margin-right: 8px;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Zero fees for internal transfers
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; vertical-align: middle; margin-right: 8px;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        24/7 availability worldwide
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; vertical-align: middle; margin-right: 8px;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Complete transaction history
                    </li>
                </ul>
                <a href="<?php echo esc_url(get_option('gsp2_dashboard_link', '/dashboard')); ?>" class="gsp2-btn gsp2-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; vertical-align: middle; margin-right: 8px;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                    Transfer Now
                </a>
            </div>
            </div>
        </div>
    </div>
</section>
