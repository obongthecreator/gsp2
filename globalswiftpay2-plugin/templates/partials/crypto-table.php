<?php
/**
 * GSP2 Crypto Table Section Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="gsp2-crypto-section gsp2-section">
    <div class="gsp2-container">
        <div class="gsp2-crypto-header">
            <h2 class="gsp2-h2">Convert to Top Cryptocurrencies</h2>
            <p class="gsp2-text-muted">
                On our Tier 2 platform, convert your GSP funds to the world's leading cryptocurrencies 
                at real-time market rates.
            </p>
        </div>
        
        <div class="gsp2-crypto-table-wrapper">
            <table class="gsp2-crypto-table" id="gsp2-crypto-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Supply</th>
                        <th>Volume (24h)</th>
                        <th>Market Cap</th>
                        <th>Change (24h)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="gsp2-text-center gsp2-text-muted">Loading cryptocurrency data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="gsp2-text-center" style="margin-top: var(--gsp2-spacing-lg);">
            <a href="<?php echo esc_url(home_url('/gsp2-generate/')); ?>" class="gsp2-btn gsp2-btn-primary">
                <span class="iconify" data-icon="solar:key-linear"></span>
                Generate Security Phrase
            </a>
        </div>
    </div>
</section>
