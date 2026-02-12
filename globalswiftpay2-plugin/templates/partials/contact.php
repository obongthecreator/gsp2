<?php
/**
 * GSP2 Contact Section Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="gsp2-contact gsp2-section" id="contact">
    <div class="gsp2-container">
        <div class="gsp2-contact-wrapper">
            <div class="gsp2-contact-inner">
            <div class="gsp2-contact-info">
                <h2 class="gsp2-h2">Get in Touch</h2>
                <p class="gsp2-text-muted" style="margin-bottom: var(--gsp2-spacing-lg);">
                    Have questions? Our support team is here to help you 24/7. 
                    Reach out and we'll get back to you as soon as possible.
                </p>
                
                <div class="gsp2-contact-details">
                    <div class="gsp2-contact-item">
                        <span class="iconify" data-icon="solar:mailbox-linear"></span>
                        <span><?php echo esc_html(get_option('gsp2_support_email', 'support@globalswiftpay2.com')); ?></span>
                    </div>
                    <div class="gsp2-contact-item">
                        <span class="iconify" data-icon="solar:globe-linear"></span>
                        <span>globalswiftpay2.com</span>
                    </div>
                    <div class="gsp2-contact-item">
                        <span class="iconify" data-icon="solar:clock-circle-linear"></span>
                        <span>24/7 Support Available</span>
                    </div>
                </div>
            </div>
            
            <div class="gsp2-contact-form-wrapper gsp2-glass-card">
                <form id="gsp2-contact-form" class="gsp2-contact-form">
                    <div class="gsp2-form-row">
                        <div class="gsp2-form-group">
                            <label class="gsp2-form-label" for="contact-name">
                                <span class="iconify" data-icon="solar:user-linear"></span>
                                Full Name
                            </label>
                            <div class="gsp2-form-icon">
                                <span class="iconify" data-icon="solar:user-linear"></span>
                                <input type="text" id="contact-name" name="name" class="gsp2-form-input" placeholder="John Doe" required>
                            </div>
                        </div>
                        <div class="gsp2-form-group">
                            <label class="gsp2-form-label" for="contact-email">
                                <span class="iconify" data-icon="solar:letter-linear"></span>
                                Email Address
                            </label>
                            <div class="gsp2-form-icon">
                                <span class="iconify" data-icon="solar:letter-linear"></span>
                                <input type="email" id="contact-email" name="email" class="gsp2-form-input" placeholder="john@example.com" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="gsp2-form-group">
                        <label class="gsp2-form-label" for="contact-subject">
                            <span class="iconify" data-icon="solar:document-text-linear"></span>
                            Subject
                        </label>
                        <div class="gsp2-form-icon">
                            <span class="iconify" data-icon="solar:document-text-linear"></span>
                            <input type="text" id="contact-subject" name="subject" class="gsp2-form-input" placeholder="How can we help?" required>
                        </div>
                    </div>
                    
                    <div class="gsp2-form-group">
                        <label class="gsp2-form-label" for="contact-message">
                            <span class="iconify" data-icon="solar:chat-line-linear"></span>
                            Message
                        </label>
                        <textarea id="contact-message" name="message" class="gsp2-form-textarea" placeholder="Type your message here..." required></textarea>
                    </div>
                    
                    <button type="submit" class="gsp2-btn gsp2-btn-primary gsp2-btn-lg" style="width: 100%;">
                        <span class="iconify" data-icon="solar:letter-linear"></span>
                        Send Message
                    </button>
                </form>
            </div>
            </div>
        </div>
    </div>
</section>
