/**
 * GlobalSwiftPay2 - Cryptocurrency Data
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        GSP2Crypto.init();
    });
    
    window.GSP2Crypto = {
        
        refreshInterval: 60000, // 1 minute
        
        init: function() {
            this.loadCryptoTable();
            this.startAutoRefresh();
        },
        
        loadCryptoTable: function() {
            var $table = $('#gsp2-crypto-table');
            var $tbody = $table.find('tbody');
            
            if ($table.length === 0) return;
            
            $tbody.addClass('loading');
            
            $.ajax({
                url: gsp2_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gsp2_get_crypto_prices'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        GSP2Crypto.renderTable(response.data, $tbody);
                    }
                },
                error: function() {
                    console.log('Failed to load crypto prices');
                },
                complete: function() {
                    $tbody.removeClass('loading');
                }
            });
        },
        
        renderTable: function(data, $tbody) {
            var html = '';
            
            data.forEach(function(coin, index) {
                var changeClass = coin.change_24h >= 0 ? 'gsp2-crypto-change-positive' : 'gsp2-crypto-change-negative';
                var changeSymbol = coin.change_24h >= 0 ? '+' : '';
                
                html += '<tr class="gsp2-crypto-row" style="animation-delay: ' + (index * 0.1) + 's">';
                html += '<td class="gsp2-crypto-name">';
                if (coin.image) {
                    html += '<img src="' + coin.image + '" alt="' + coin.name + '" loading="lazy">';
                } else {
                    html += '<span class="iconify" data-icon="solar:coin-linear"></span>';
                }
                html += '<span>' + coin.name + '</span>';
                html += '<span class="gsp2-crypto-symbol">' + coin.symbol + '</span>';
                html += '</td>';
                html += '<td>' + coin.price + '</td>';
                html += '<td>' + coin.supply + '</td>';
                html += '<td>' + coin.volume + '</td>';
                html += '<td>' + coin.market_cap + '</td>';
                html += '<td class="' + changeClass + '">' + changeSymbol + coin.change_24h + '%</td>';
                html += '</tr>';
            });
            
            $tbody.html(html);
            
            // Reinitialize Iconify if available
            if (typeof Iconify !== 'undefined') {
                Iconify.scan();
            }
        },
        
        startAutoRefresh: function() {
            var self = this;
            
            setInterval(function() {
                self.loadCryptoTable();
            }, self.refreshInterval);
        }
    };
    
    // Table row animation
    var cryptoStyles = document.createElement('style');
    cryptoStyles.textContent = `
        .gsp2-crypto-row {
            opacity: 0;
            animation: gsp2-fade-in 0.5s ease forwards;
        }
        
        @keyframes gsp2-fade-in {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        #gsp2-crypto-table tbody.loading {
            opacity: 0.5;
        }
    `;
    document.head.appendChild(cryptoStyles);
    
})(jQuery);
