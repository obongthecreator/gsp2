/**
 * GlobalSwiftPay2 - Main JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        GSP2.init();
    });
    
    // Main GSP2 Object
    window.GSP2 = {
        
        init: function() {
            this.initDarkMode();
            this.initNavigation();
            this.initMobileMenu();
            this.initScrollToTop();
            this.initContactForm();
            this.initGeneratePage();
            this.initIconify();
            this.initSmoothScroll();
        },
        
        // Dark Mode Toggle
        initDarkMode: function() {
            var $body = $('body');
            var $toggle = $('.gsp2-dark-mode-toggle');
            var savedMode = localStorage.getItem('gsp2_theme_mode');
            
            // Set initial mode
            if (savedMode) {
                $body.removeClass('gsp2-dark-mode gsp2-light-mode').addClass('gsp2-' + savedMode + '-mode');
            }
            
            // Toggle handler
            $toggle.on('click', function() {
                if ($body.hasClass('gsp2-dark-mode')) {
                    $body.removeClass('gsp2-dark-mode').addClass('gsp2-light-mode');
                    localStorage.setItem('gsp2_theme_mode', 'light');
                } else {
                    $body.removeClass('gsp2-light-mode').addClass('gsp2-dark-mode');
                    localStorage.setItem('gsp2_theme_mode', 'dark');
                }
            });
        },
        
        // Navigation active state
        initNavigation: function() {
            var currentPath = window.location.pathname;
            $('.gsp2-nav-link').each(function() {
                var href = $(this).attr('href');
                if (href && currentPath.indexOf(href) !== -1 && href !== '/') {
                    $(this).addClass('active');
                } else if (href === '/' && currentPath === '/') {
                    $(this).addClass('active');
                }
            });
        },
        
        // Mobile Menu
        initMobileMenu: function() {
            var $hamburger = $('.gsp2-nav-hamburger');
            var $menu = $('.gsp2-mobile-menu');
            var $overlay = $('.gsp2-mobile-overlay');
            var $close = $('.gsp2-mobile-menu-close');
            
            function openMenu() {
                $menu.addClass('active');
                $overlay.addClass('active');
                $('body').css('overflow', 'hidden');
            }
            
            function closeMenu() {
                $menu.removeClass('active');
                $overlay.removeClass('active');
                $('body').css('overflow', '');
            }
            
            $hamburger.on('click', openMenu);
            $close.on('click', closeMenu);
            $overlay.on('click', closeMenu);
            
            // Close on menu link click
            $menu.find('a').on('click', closeMenu);
            
            // Close on escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $menu.hasClass('active')) {
                    closeMenu();
                }
            });
        },
        
        // Scroll to Top
        initScrollToTop: function() {
            var $btn = $('.gsp2-scroll-top');
            var $progress = $btn.find('.progress');
            
            function updateScrollProgress() {
                var scrollTop = $(window).scrollTop();
                var docHeight = $(document).height() - $(window).height();
                var scrollPercent = (scrollTop / docHeight) * 100;
                var circumference = 151; // 2 * PI * 24
                var offset = circumference - (scrollPercent / 100 * circumference);
                
                $progress.css('stroke-dashoffset', offset);
                
                if (scrollTop > 300) {
                    $btn.addClass('visible');
                } else {
                    $btn.removeClass('visible');
                }
            }
            
            $(window).on('scroll', updateScrollProgress);
            
            $btn.on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 500);
            });
            
            updateScrollProgress();
        },
        
        // Contact Form
        initContactForm: function() {
            var $form = $('#gsp2-contact-form');
            
            $form.on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $form.find('button[type="submit"]');
                var originalText = $btn.text();
                
                $btn.prop('disabled', true).text('Sending...');
                
                $.ajax({
                    url: gsp2_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'gsp2_submit_contact',
                        nonce: gsp2_ajax.nonce,
                        name: $form.find('[name="name"]').val(),
                        email: $form.find('[name="email"]').val(),
                        subject: $form.find('[name="subject"]').val(),
                        message: $form.find('[name="message"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            GSP2.showNotification('success', response.data.message);
                            $form[0].reset();
                        } else {
                            GSP2.showNotification('error', response.data.message);
                        }
                    },
                    error: function() {
                        GSP2.showNotification('error', 'An error occurred. Please try again.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });
        },
        
        // Generate Page
        initGeneratePage: function() {
            var $modal = $('#gsp2-payment-modal');
            var $payBtn = $('.gsp2-pay-btn');
            var $closeBtn = $modal.find('.gsp2-modal-close');
            var $copyBtn = $('.gsp2-copy-btn');
            var $uploadArea = $('.gsp2-upload-area');
            var $fileInput = $('#gsp2-proof-file');
            var $generateBtn = $('.gsp2-generate-btn');
            
            // Open modal
            $payBtn.on('click', function() {
                $modal.addClass('active');
            });
            
            // Close modal
            $closeBtn.on('click', function() {
                $modal.removeClass('active');
            });
            
            $modal.on('click', function(e) {
                if ($(e.target).is($modal)) {
                    $modal.removeClass('active');
                }
            });
            
            // Copy BTC address
            $copyBtn.on('click', function() {
                var address = $(this).data('address') || gsp2_ajax.btc_address;
                
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(address).then(function() {
                        GSP2.showNotification('success', 'Address copied to clipboard!');
                    }).catch(function() {
                        GSP2.fallbackCopyToClipboard(address);
                    });
                } else {
                    GSP2.fallbackCopyToClipboard(address);
                }
            });
            
            // Upload area click
            $uploadArea.on('click', function() {
                $fileInput.trigger('click');
            });
            
            // File selected
            $fileInput.on('change', function() {
                var file = this.files[0];
                if (file) {
                    $uploadArea.find('.gsp2-upload-text').text(file.name);
                }
            });
            
            // Generate button
            $generateBtn.on('click', function() {
                var file = $fileInput[0].files[0];
                
                if (!file) {
                    GSP2.showNotification('error', 'Please upload payment proof first.');
                    return;
                }
                
                var formData = new FormData();
                formData.append('action', 'gsp2_upload_proof');
                formData.append('nonce', gsp2_ajax.nonce);
                formData.append('proof', file);
                
                var $btn = $(this);
                $btn.prop('disabled', true).text('Processing...');
                
                $.ajax({
                    url: gsp2_ajax.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            GSP2.showNotification('success', response.data.message);
                            $modal.removeClass('active');
                        } else {
                            GSP2.showNotification('error', response.data.message);
                        }
                    },
                    error: function() {
                        GSP2.showNotification('error', 'Upload failed. Please try again.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Generate');
                    }
                });
            });
        },
        
        // Initialize Iconify icons
        initIconify: function() {
            if (typeof Iconify !== 'undefined') {
                $('[data-icon]').each(function() {
                    var icon = $(this).data('icon');
                    $(this).replaceWith('<span class="iconify" data-icon="' + icon + '"></span>');
                });
            }
        },
        
        // Smooth scroll for anchor links
        initSmoothScroll: function() {
            $('a[href^="#"]').on('click', function(e) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 500);
                }
            });
        },
        
        // Language Switcher
        initLanguageSwitcher: function() {
            var $select = $('#gsp2-language-select');
            var savedLang = localStorage.getItem('gsp2_language') || 'en';
            var hasReloaded = sessionStorage.getItem('gsp2_lang_reloaded');
            
            // Set saved language
            $select.val(savedLang);
            
            // Apply saved language on load - only if not already reloaded
            if (savedLang !== 'en' && !hasReloaded) {
                this.loadGoogleTranslate(savedLang);
            }
            
            // Handle language change
            $select.on('change', function() {
                var lang = $(this).val();
                localStorage.setItem('gsp2_language', lang);
                
                // Clear reload flag so it will apply the new language
                sessionStorage.removeItem('gsp2_lang_reloaded');
                
                if (lang === 'en') {
                    // Reset to original language
                    GSP2.resetTranslation();
                } else {
                    GSP2.loadGoogleTranslate(lang);
                }
            });
        },
        
        // Load Google Translate
        loadGoogleTranslate: function(lang) {
            // Check if Google Translate element exists
            if (!$('#google_translate_element').length) {
                $('body').append('<div id="google_translate_element" style="display:none;"></div>');
            }
            
            // Check if script already loaded
            if (!window.googleTranslateScriptLoaded) {
                window.googleTranslateElementInit = function() {
                    new google.translate.TranslateElement({
                        pageLanguage: 'en',
                        autoDisplay: false
                    }, 'google_translate_element');
                    
                    // Set the language after initialization
                    setTimeout(function() {
                        GSP2.setGoogleTranslateLanguage(lang);
                    }, 500);
                };
                
                var script = document.createElement('script');
                script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
                script.async = true;
                document.head.appendChild(script);
                window.googleTranslateScriptLoaded = true;
            } else {
                // Script already loaded, just change language
                this.setGoogleTranslateLanguage(lang);
            }
        },
        
        // Set Google Translate language
        setGoogleTranslateLanguage: function(lang) {
            var frame = document.querySelector('.goog-te-menu-frame');
            if (frame) {
                var items = frame.contentDocument.querySelectorAll('.goog-te-menu2-item');
                items.forEach(function(item) {
                    if (item.textContent.toLowerCase().indexOf(GSP2.getLanguageName(lang)) !== -1) {
                        item.click();
                    }
                });
            } else {
                // Alternative: use cookie-based approach
                var langPair = 'en|' + lang;
                document.cookie = 'googtrans=/en/' + lang + '; path=/';
                document.cookie = 'googtrans=/en/' + lang + '; path=/; domain=' + window.location.hostname;
                
                // Only reload if cookie was set and we haven't already reloaded for this language
                if (document.cookie.indexOf('googtrans') !== -1 && !sessionStorage.getItem('gsp2_lang_reloaded')) {
                    sessionStorage.setItem('gsp2_lang_reloaded', 'true');
                    location.reload();
                }
            }
        },
        
        // Get language name from code
        getLanguageName: function(code) {
            var languages = {
                'en': 'english',
                'es': 'spanish',
                'fr': 'french',
                'de': 'german',
                'pt': 'portuguese',
                'zh': 'chinese',
                'ja': 'japanese',
                'ko': 'korean',
                'ar': 'arabic',
                'ru': 'russian'
            };
            return languages[code] || 'english';
        },
        
        // Reset translation to original
        resetTranslation: function() {
            // Clear Google Translate cookies
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;
            
            // Clear reload flag
            sessionStorage.removeItem('gsp2_lang_reloaded');
            
            // Check if there's a reset link
            var resetLink = document.querySelector('.goog-te-banner-frame');
            if (resetLink) {
                var doc = resetLink.contentDocument;
                if (doc) {
                    var showOriginal = doc.querySelector('.goog-te-button');
                    if (showOriginal) showOriginal.click();
                }
            }
            
            location.reload();
        },
        
        // Show notification
        showNotification: function(type, message) {
            var $notification = $('<div class="gsp2-notification gsp2-notification-' + type + '">' + message + '</div>');
            
            $notification.css({
                position: 'fixed',
                bottom: '20px',
                left: '50%',
                transform: 'translateX(-50%)',
                padding: '12px 24px',
                background: type === 'success' ? '#10b981' : '#ef4444',
                color: '#fff',
                borderRadius: '8px',
                fontSize: '14px',
                zIndex: 9999,
                animation: 'gsp2-slide-up 0.3s ease'
            });
            
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        },
        
        // Fallback clipboard copy for older browsers
        fallbackCopyToClipboard: function(text) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            textArea.style.top = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                GSP2.showNotification('success', 'Address copied to clipboard!');
            } catch (err) {
                GSP2.showNotification('error', 'Failed to copy address. Please copy manually.');
            }
            
            document.body.removeChild(textArea);
        }
    };
    
})(jQuery);

// Add notification animation
var style = document.createElement('style');
style.textContent = '@keyframes gsp2-slide-up { from { transform: translateX(-50%) translateY(20px); opacity: 0; } to { transform: translateX(-50%) translateY(0); opacity: 1; } }';
document.head.appendChild(style);
