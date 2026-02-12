<?php
/**
 * GSP2 Admin Panel
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSP2_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'GlobalSwiftPay2',
            'GSP2 Settings',
            'manage_options',
            'gsp2-settings',
            array($this, 'render_settings_page'),
            'dashicons-money-alt',
            30
        );
        
        add_submenu_page(
            'gsp2-settings',
            'General Settings',
            'General',
            'manage_options',
            'gsp2-settings',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'gsp2-settings',
            'Page Links',
            'Page Links',
            'manage_options',
            'gsp2-links',
            array($this, 'render_links_page')
        );
        
        add_submenu_page(
            'gsp2-settings',
            'Images',
            'Images',
            'manage_options',
            'gsp2-images',
            array($this, 'render_images_page')
        );
        
        add_submenu_page(
            'gsp2-settings',
            'Payment Proofs',
            'Payment Proofs',
            'manage_options',
            'gsp2-proofs',
            array($this, 'render_proofs_page')
        );
        
        add_submenu_page(
            'gsp2-settings',
            'Pending Upgrades',
            'Pending Upgrades',
            'manage_options',
            'gsp2-upgrades',
            array($this, 'render_upgrades_page')
        );
    }
    
    public function register_settings() {
        // General Settings
        register_setting('gsp2_general', 'gsp2_btc_address');
        register_setting('gsp2_general', 'gsp2_support_email');
        register_setting('gsp2_general', 'gsp2_dark_mode_default');
        register_setting('gsp2_general', 'gsp2_footer_text');
        register_setting('gsp2_general', 'gsp2_copyright_text');
        
        // Links Settings
        register_setting('gsp2_links', 'gsp2_upgrade_link');
        register_setting('gsp2_links', 'gsp2_convert_link');
        register_setting('gsp2_links', 'gsp2_save_link');
        register_setting('gsp2_links', 'gsp2_dashboard_link');
        register_setting('gsp2_links', 'gsp2_login_link');
        register_setting('gsp2_links', 'gsp2_register_link');
        
        // Image Settings
        register_setting('gsp2_images', 'gsp2_hero_image');
        register_setting('gsp2_images', 'gsp2_laptop_image');
        register_setting('gsp2_images', 'gsp2_about_image');
        register_setting('gsp2_images', 'gsp2_generate_image');
        register_setting('gsp2_images', 'gsp2_security_image');
        register_setting('gsp2_images', 'gsp2_privacy_image');
    }
    
    public function render_settings_page() {
        ?>
        <div class="wrap gsp2-admin-wrap">
            <h1>GlobalSwiftPay2 - General Settings</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('gsp2_general'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="gsp2_btc_address">BTC Payment Address</label>
                        </th>
                        <td>
                            <input type="text" id="gsp2_btc_address" name="gsp2_btc_address" 
                                   value="<?php echo esc_attr(get_option('gsp2_btc_address', 'bc1qf74tnfccynx78n9kd8cgjcqp8l9s7y5fcre2hp')); ?>" 
                                   class="regular-text" />
                            <p class="description">Bitcoin address for payment collection on Generate page</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_support_email">Support Email</label>
                        </th>
                        <td>
                            <input type="email" id="gsp2_support_email" name="gsp2_support_email" 
                                   value="<?php echo esc_attr(get_option('gsp2_support_email', 'support@globalswiftpay2.com')); ?>" 
                                   class="regular-text" />
                            <p class="description">Email for contact form submissions and payment proofs</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_dark_mode_default">Default Theme</label>
                        </th>
                        <td>
                            <select id="gsp2_dark_mode_default" name="gsp2_dark_mode_default">
                                <option value="dark" <?php selected(get_option('gsp2_dark_mode_default'), 'dark'); ?>>Dark Mode</option>
                                <option value="light" <?php selected(get_option('gsp2_dark_mode_default'), 'light'); ?>>Light Mode</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_footer_text">Footer Text</label>
                        </th>
                        <td>
                            <textarea id="gsp2_footer_text" name="gsp2_footer_text" rows="3" class="large-text"><?php echo esc_textarea(get_option('gsp2_footer_text', 'Global Swift Pay is an Online e-wallet.')); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_copyright_text">Copyright Text</label>
                        </th>
                        <td>
                            <textarea id="gsp2_copyright_text" name="gsp2_copyright_text" rows="3" class="large-text"><?php echo esc_textarea(get_option('gsp2_copyright_text', 'GSP Financial Services Commission (GSPVFSC) License #17098. from 16.05.2019 under Financial Dealers Licensing Act.')); ?></textarea>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    public function render_links_page() {
        ?>
        <div class="wrap gsp2-admin-wrap">
            <h1>GlobalSwiftPay2 - Page Links</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('gsp2_links'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="gsp2_upgrade_link">Upgrade Account Link</label>
                        </th>
                        <td>
                            <input type="url" id="gsp2_upgrade_link" name="gsp2_upgrade_link" 
                                   value="<?php echo esc_url(get_option('gsp2_upgrade_link', '#')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_convert_link">Convert GSP Funds Link</label>
                        </th>
                        <td>
                            <input type="url" id="gsp2_convert_link" name="gsp2_convert_link" 
                                   value="<?php echo esc_url(get_option('gsp2_convert_link', '#')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_save_link">Save Funds Link</label>
                        </th>
                        <td>
                            <input type="url" id="gsp2_save_link" name="gsp2_save_link" 
                                   value="<?php echo esc_url(get_option('gsp2_save_link', '#')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_dashboard_link">Dashboard Link</label>
                        </th>
                        <td>
                            <input type="url" id="gsp2_dashboard_link" name="gsp2_dashboard_link" 
                                   value="<?php echo esc_url(get_option('gsp2_dashboard_link', 'https://globalswiftpay2.com/gsp2-dashboard/')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_login_link">Login Page Link</label>
                        </th>
                        <td>
                            <input type="url" id="gsp2_login_link" name="gsp2_login_link" 
                                   value="<?php echo esc_url(get_option('gsp2_login_link', wp_login_url())); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_register_link">Registration Page Link</label>
                        </th>
                        <td>
                            <input type="url" id="gsp2_register_link" name="gsp2_register_link" 
                                   value="<?php echo esc_url(get_option('gsp2_register_link', wp_registration_url())); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    public function render_images_page() {
        ?>
        <div class="wrap gsp2-admin-wrap">
            <h1>GlobalSwiftPay2 - Images</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('gsp2_images'); ?>
                
                <table class="form-table">
                    <?php
                    $image_fields = array(
                        'hero_image' => 'Hero Section Background',
                        'laptop_image' => 'Laptop Graphic (Pay Online Section)',
                        'about_image' => 'About Page Image',
                        'generate_image' => 'Generate Page Image',
                        'security_image' => 'Security Policy Image',
                        'privacy_image' => 'Privacy Policy Image'
                    );
                    
                    foreach ($image_fields as $key => $label):
                        $image_url = get_option('gsp2_' . $key, '');
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="gsp2_<?php echo $key; ?>"><?php echo $label; ?></label>
                        </th>
                        <td>
                            <div class="gsp2-image-upload-wrap">
                                <input type="hidden" id="gsp2_<?php echo $key; ?>" name="gsp2_<?php echo $key; ?>" 
                                       value="<?php echo esc_url($image_url); ?>" class="gsp2-image-url" />
                                <div class="gsp2-image-preview">
                                    <?php if ($image_url): ?>
                                        <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 200px;" />
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button gsp2-upload-button" data-target="gsp2_<?php echo $key; ?>">
                                    Upload Image
                                </button>
                                <button type="button" class="button gsp2-remove-button" data-target="gsp2_<?php echo $key; ?>" 
                                        style="<?php echo $image_url ? '' : 'display:none;'; ?>">
                                    Remove
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    public function render_proofs_page() {
        global $wpdb;
        
        $uploads_dir = wp_upload_dir();
        $proofs_path = $uploads_dir['basedir'];
        
        ?>
        <div class="wrap gsp2-admin-wrap">
            <h1>GlobalSwiftPay2 - Payment Proofs</h1>
            
            <p>Payment proof submissions are sent to: <strong><?php echo esc_html(get_option('gsp2_support_email', 'support@globalswiftpay2.com')); ?></strong></p>
            
            <h2>Recent Uploads</h2>
            <p>Payment proofs are stored in the WordPress media library and emailed to the support email address.</p>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $attachments = get_posts(array(
                        'post_type' => 'attachment',
                        'posts_per_page' => 20,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'inherit'
                    ));
                    
                    if ($attachments):
                        foreach ($attachments as $attachment):
                    ?>
                    <tr>
                        <td><?php echo get_the_date('Y-m-d H:i', $attachment); ?></td>
                        <td>
                            <a href="<?php echo wp_get_attachment_url($attachment->ID); ?>" target="_blank">
                                <?php echo basename(get_attached_file($attachment->ID)); ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo wp_get_attachment_url($attachment->ID); ?>" target="_blank" class="button">View</a>
                        </td>
                    </tr>
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <tr>
                        <td colspan="3">No uploads found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    public function render_upgrades_page() {
        $pending_upgrades = get_option('gsp2_pending_upgrades', array());
        $approved_upgrades = get_option('gsp2_approved_upgrades', array());
        $rejected_upgrades = get_option('gsp2_rejected_upgrades', array());
        
        ?>
        <div class="wrap gsp2-admin-wrap">
            <h1>GlobalSwiftPay2 - Tier 2 Upgrade Requests</h1>
            
            <!-- Pending Upgrades -->
            <h2>Pending Upgrades <span class="count">(<?php echo count($pending_upgrades); ?>)</span></h2>
            
            <?php if (!empty($pending_upgrades)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>GSP Account</th>
                        <th>Country</th>
                        <th>Mobile</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_upgrades as $upgrade): ?>
                    <tr data-upgrade-id="<?php echo esc_attr($upgrade['id']); ?>">
                        <td><?php echo esc_html($upgrade['first_name'] . ' ' . $upgrade['last_name']); ?></td>
                        <td><strong><?php echo esc_html($upgrade['username'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo esc_html($upgrade['email']); ?></td>
                        <td><?php echo esc_html($upgrade['gsp_account']); ?></td>
                        <td><?php echo esc_html($upgrade['country']); ?></td>
                        <td><?php echo esc_html($upgrade['mobile']); ?></td>
                        <td><?php echo esc_html($upgrade['submitted_at']); ?></td>
                        <td>
                            <button class="button button-primary gsp2-approve-btn" data-id="<?php echo esc_attr($upgrade['id']); ?>">
                                Approve
                            </button>
                            <button class="button gsp2-reject-btn" data-id="<?php echo esc_attr($upgrade['id']); ?>">
                                Reject
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No pending upgrade requests.</p>
            <?php endif; ?>
            
            <!-- Approved Upgrades -->
            <h2 style="margin-top: 30px;">Approved Upgrades <span class="count">(<?php echo count($approved_upgrades); ?>)</span></h2>
            
            <?php if (!empty($approved_upgrades)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>GSP Account</th>
                        <th>Country</th>
                        <th>WP User Created</th>
                        <th>Approved At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($approved_upgrades) as $upgrade): ?>
                    <tr>
                        <td><?php echo esc_html($upgrade['first_name'] . ' ' . $upgrade['last_name']); ?></td>
                        <td><strong><?php echo esc_html($upgrade['username'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo esc_html($upgrade['email']); ?></td>
                        <td><?php echo esc_html($upgrade['gsp_account']); ?></td>
                        <td><?php echo esc_html($upgrade['country']); ?></td>
                        <td><?php echo isset($upgrade['wp_user_id']) ? '<span style="color:green;">Yes (ID: ' . esc_html($upgrade['wp_user_id']) . ')</span>' : '<span style="color:gray;">No</span>'; ?></td>
                        <td><?php echo esc_html($upgrade['approved_at'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No approved upgrades yet.</p>
            <?php endif; ?>
            
            <!-- Rejected Upgrades -->
            <h2 style="margin-top: 30px;">Rejected Upgrades <span class="count">(<?php echo count($rejected_upgrades); ?>)</span></h2>
            
            <?php if (!empty($rejected_upgrades)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>GSP Account</th>
                        <th>Reason</th>
                        <th>Rejected At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($rejected_upgrades) as $upgrade): ?>
                    <tr>
                        <td><?php echo esc_html($upgrade['first_name'] . ' ' . $upgrade['last_name']); ?></td>
                        <td><?php echo esc_html($upgrade['email']); ?></td>
                        <td><?php echo esc_html($upgrade['gsp_account']); ?></td>
                        <td><?php echo esc_html($upgrade['rejection_reason'] ?? 'N/A'); ?></td>
                        <td><?php echo esc_html($upgrade['rejected_at'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No rejected upgrades.</p>
            <?php endif; ?>
        </div>
        
        <!-- Rejection Modal -->
        <div id="gsp2-reject-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; max-width: 400px; width: 90%;">
                <h3>Reject Upgrade Request</h3>
                <p>Please provide a reason for rejection (optional):</p>
                <textarea id="gsp2-reject-reason" rows="4" style="width: 100%;"></textarea>
                <div style="margin-top: 15px; text-align: right;">
                    <button class="button" id="gsp2-cancel-reject">Cancel</button>
                    <button class="button button-primary" id="gsp2-confirm-reject">Confirm Rejection</button>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            var currentRejectId = null;
            
            // Approve button
            $('.gsp2-approve-btn').on('click', function() {
                var upgradeId = $(this).data('id');
                var $row = $(this).closest('tr');
                
                if (confirm('Are you sure you want to approve this upgrade request?')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'gsp2_approve_upgrade',
                            nonce: '<?php echo wp_create_nonce('gsp2_admin_nonce'); ?>',
                            upgrade_id: upgradeId
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Upgrade approved successfully!');
                                $row.fadeOut(function() { $(this).remove(); });
                            } else {
                                alert('Error: ' + response.data.message);
                            }
                        }
                    });
                }
            });
            
            // Reject button - show modal
            $('.gsp2-reject-btn').on('click', function() {
                currentRejectId = $(this).data('id');
                $('#gsp2-reject-modal').fadeIn();
            });
            
            // Cancel rejection
            $('#gsp2-cancel-reject').on('click', function() {
                $('#gsp2-reject-modal').fadeOut();
                currentRejectId = null;
            });
            
            // Confirm rejection
            $('#gsp2-confirm-reject').on('click', function() {
                var reason = $('#gsp2-reject-reason').val();
                var $row = $('tr[data-upgrade-id="' + currentRejectId + '"]');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gsp2_reject_upgrade',
                        nonce: '<?php echo wp_create_nonce('gsp2_admin_nonce'); ?>',
                        upgrade_id: currentRejectId,
                        reason: reason
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Upgrade rejected successfully!');
                            $row.fadeOut(function() { $(this).remove(); });
                            $('#gsp2-reject-modal').fadeOut();
                        } else {
                            alert('Error: ' + response.data.message);
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }
}

new GSP2_Admin();
