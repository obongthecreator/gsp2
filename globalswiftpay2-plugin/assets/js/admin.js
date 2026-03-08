/**
 * GlobalSwiftPay2 - Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        GSP2Admin.init();
    });
    
    window.GSP2Admin = {
        
        init: function() {
            this.initImageUpload();
        },
        
        initImageUpload: function() {
            var $uploadBtns = $('.gsp2-upload-button');
            var $removeBtns = $('.gsp2-remove-button');
            
            $uploadBtns.on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var targetId = $btn.data('target');
                var $input = $('#' + targetId);
                var $preview = $btn.siblings('.gsp2-image-preview');
                var $removeBtn = $btn.siblings('.gsp2-remove-button');
                
                var frame = wp.media({
                    title: 'Select Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $input.val(attachment.url);
                    $preview.html('<img src="' + attachment.url + '" alt="" style="max-width: 200px;">');
                    $removeBtn.show();
                });
                
                frame.open();
            });
            
            $removeBtns.on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var targetId = $btn.data('target');
                var $input = $('#' + targetId);
                var $preview = $btn.siblings('.gsp2-image-preview');
                
                $input.val('');
                $preview.html('');
                $btn.hide();
            });
        }
    };
    
})(jQuery);
