/**
 * GlobalSwiftPay2 - Animations
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        GSP2Animations.init();
    });
    
    window.GSP2Animations = {
        
        init: function() {
            this.initHeroAnimations();
            this.initScrollAnimations();
            this.initBeamAnimations();
            this.initNoodleAnimations();
            this.initFloatingIcons();
        },
        
        // Hero Section Animations
        initHeroAnimations: function() {
            var $heroTitle = $('.gsp2-hero-title');
            var $heroBadge = $('.gsp2-hero-badge');
            var $heroSubtitle = $('.gsp2-hero-subtitle');
            var $heroButtons = $('.gsp2-hero-buttons');
            
            // Staggered entrance animation
            setTimeout(function() {
                $heroBadge.css({ opacity: 1, transform: 'translateY(0)' });
            }, 100);
            
            setTimeout(function() {
                $heroTitle.css({ opacity: 1, transform: 'translateY(0)' });
            }, 300);
            
            setTimeout(function() {
                $heroSubtitle.css({ opacity: 1, transform: 'translateY(0)' });
            }, 500);
            
            setTimeout(function() {
                $heroButtons.css({ opacity: 1, transform: 'translateY(0)' });
            }, 700);
            
            // Initial state
            $heroBadge.add($heroTitle).add($heroSubtitle).add($heroButtons).css({
                opacity: 0,
                transform: 'translateY(20px)',
                transition: 'all 0.6s cubic-bezier(0.16, 1, 0.3, 1)'
            });
        },
        
        // Scroll-triggered Animations
        initScrollAnimations: function() {
            var $sections = $('.gsp2-section, .gsp2-glass-card, .gsp2-trust-card, .gsp2-how-works-card');
            
            // Check if IntersectionObserver is supported
            if (!('IntersectionObserver' in window)) {
                // Fallback: show all elements immediately
                $sections.addClass('gsp2-animated');
                return;
            }
            
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('gsp2-animated');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            $sections.each(function() {
                observer.observe(this);
            });
            
            // Add base animation styles
            $sections.css({
                opacity: 0,
                transform: 'translateY(30px)',
                transition: 'opacity 0.6s ease, transform 0.6s ease'
            });
        },
        
        // Beam Animations for various elements
        initBeamAnimations: function() {
            var $beamElements = $('.gsp2-beam-animate');
            
            $beamElements.each(function(index) {
                var $el = $(this);
                var delay = index * 0.3;
                
                $el.css('animation-delay', delay + 's');
            });
            
            // Icon beam animation
            $('.gsp2-btn .iconify').each(function() {
                var $icon = $(this);
                
                setInterval(function() {
                    $icon.addClass('gsp2-beam-pulse');
                    setTimeout(function() {
                        $icon.removeClass('gsp2-beam-pulse');
                    }, 1000);
                }, 3000);
            });
        },
        
        // Noodle connecting animations
        initNoodleAnimations: function() {
            var $noodleContainer = $('.gsp2-hero-noodle');
            
            if ($noodleContainer.length === 0) return;
            
            // Create SVG noodles connecting circles
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 800 800');
            svg.setAttribute('width', '800');
            svg.setAttribute('height', '800');
            
            // Noodle paths
            var paths = [
                'M 400 300 Q 500 350 400 400',
                'M 300 400 Q 350 300 400 300',
                'M 400 500 Q 300 450 300 400',
                'M 500 400 Q 450 500 400 500',
                'M 350 325 Q 425 375 475 325',
                'M 475 475 Q 425 425 350 475',
                'M 325 350 Q 375 425 325 475',
                'M 475 350 Q 425 375 475 475'
            ];
            
            paths.forEach(function(d, i) {
                var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', d);
                path.style.animationDelay = (i * 0.5) + 's';
                svg.appendChild(path);
            });
            
            $noodleContainer.html(svg);
        },
        
        // Floating crypto icons animation
        initFloatingIcons: function() {
            var $cryptoIcons = $('.gsp2-crypto-icon');
            
            $cryptoIcons.each(function(index) {
                var $icon = $(this);
                var delay = index * 0.2;
                var duration = 3 + (Math.random() * 2);
                
                $icon.css({
                    'animation-delay': delay + 's',
                    'animation-duration': duration + 's'
                });
            });
        }
    };
    
    // Add animated class styles
    var animStyles = document.createElement('style');
    animStyles.textContent = `
        .gsp2-animated {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        .gsp2-beam-pulse {
            animation: gsp2-beam-pulse-anim 1s ease;
        }
        
        @keyframes gsp2-beam-pulse-anim {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.5); }
        }
    `;
    document.head.appendChild(animStyles);
    
})(jQuery);
