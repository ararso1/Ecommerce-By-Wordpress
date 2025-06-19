<?php

if ( ! function_exists( 'fontier_is_woocommerce_and_dokan_active' ) ) {
    /**
     * Check if WooCommerce and Dokan are active
     */
    function fontier_is_woocommerce_and_dokan_active() {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        return is_plugin_active( 'woocommerce/woocommerce.php' ) && is_plugin_active( 'dokan-lite/dokan.php' );
    }
}

// Add the functionality only if WooCommerce and Dokan are active
if ( fontier_is_woocommerce_and_dokan_active() ) {

    // Add the Font Repeater Field in Dokan New Product Form
    add_action('dokan_new_product_after_product_tags', 'fontier_add_repeater_field_to_dokan', 10);
    function fontier_add_repeater_field_to_dokan() { 
        ?>
        <div class="dokan-form-group fes-checkbox-field">
            <label for="font_preview_enable" class="fes_font_enbale_label">
                <input type="checkbox" id="font_preview_enable" name="font_preview_enable" value="1" />
                <?php esc_html_e('Enable Font Preview', 'dokan-lite'); ?>
            </label>
        </div>

        <div id="font-repeater-section" style="display: none;">
            <h3><?php esc_html_e('Upload Fonts', 'dokan-lite'); ?></h3>
            <div id="font-repeater-wrapper">
                <div class="repeater-item fontier_font_fes_repeater_item" data-index="0">
                    <label for="font_title_0"><?php esc_html_e('Font Title', 'dokan-lite'); ?></label>
                    <input type="text" name="font_repeater[0][font_title]" value="" />

                    <label for="font_file_0"><?php esc_html_e('Font File', 'dokan-lite'); ?></label>
                    <div class="fontier_font_files_fes">
                        <input type="url" id="font_file_0" name="font_repeater[0][font_file]" value="" />
                        <button type="button" class="upload-font-button button" data-target="#font_file_0">
                            <?php esc_html_e('Upload', 'dokan-lite'); ?> <i class="zil zi-upload"></i>
                        </button>
                        <button type="button" class="remove-repeater-item button">
                            <i class="zil zi-cross"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" id="add-repeater-item" class="button">
                <i class="zil zi-plus"></i> <?php esc_html_e('Add Font', 'dokan-lite'); ?>
            </button>
        </div>
        <?php
    }

    // Save Font Repeater and Checkbox Meta on Dokan Product Save
    add_action('dokan_new_product_added', 'fontier_save_dokan_product_meta', 10, 2);
    add_action('dokan_product_updated', 'fontier_save_dokan_product_meta', 10, 2);
    function fontier_save_dokan_product_meta($product_id, $postdata) {
        if (!dokan_is_user_seller(get_current_user_id())) {
            return;
        }

        // Save checkbox value
        $font_preview_enable = isset($postdata['font_preview_enable']) ? '1' : '0';
        update_post_meta($product_id, 'font_preview_enable', $font_preview_enable);

        // Save repeater field data
        if (!empty($postdata['font_repeater']) && is_array($postdata['font_repeater'])) {
            update_post_meta($product_id, 'font_repeater', array_values($postdata['font_repeater']));
        }
    }

    // Show Font Repeater and Checkbox on Dokan Edit Product Form
    add_action('dokan_product_edit_after_product_tags', 'fontier_show_repeater_on_edit_page', 99, 2);
    function fontier_show_repeater_on_edit_page($post, $post_id) {
        $font_preview_enable = get_post_meta($post_id, 'font_preview_enable', true);
        $repeater_data = get_post_meta($post_id, 'font_repeater', true) ?: [];
        ?>
        <div class="dokan-form-group fes-checkbox-field">
            <label for="font_preview_enable" class="fes_font_enbale_label">
                <input type="checkbox" id="font_preview_enable" name="font_preview_enable" value="1" <?php checked($font_preview_enable, '1'); ?> />
                <?php esc_html_e('Enable Font Preview', 'dokan-lite'); ?>
            </label>
        </div>

        <div id="font-repeater-section" style="<?php echo $font_preview_enable ? '' : 'display: none;'; ?>">
            <h3><?php esc_html_e('Upload Fonts', 'dokan-lite'); ?></h3>
            <div id="font-repeater-wrapper">
                <?php if (!empty($repeater_data)) : ?>
                    <?php foreach ($repeater_data as $index => $font) : ?>
                        <div class="repeater-item fontier_font_fes_repeater_item" data-index="<?php echo esc_attr($index); ?>">
                            <label for="font_title_<?php echo esc_attr($index); ?>"><?php esc_html_e('Font Title', 'dokan-lite'); ?></label>
                            <input type="text" name="font_repeater[<?php echo esc_attr($index); ?>][font_title]" value="<?php echo esc_attr($font['font_title']); ?>" />

                            <label for="font_file_<?php echo esc_attr($index); ?>"><?php esc_html_e('Font File', 'dokan-lite'); ?></label>
                            <div class="fontier_font_files_fes">
                                <input type="url" id="font_file_<?php echo esc_attr($index); ?>" name="font_repeater[<?php echo esc_attr($index); ?>][font_file]" value="<?php echo esc_url($font['font_file']); ?>" />
                                <button type="button" class="upload-font-button button" data-target="#font_file_<?php echo esc_attr($index); ?>">
                                    <?php esc_html_e('Upload', 'dokan-lite'); ?> <i class="zil zi-upload"></i>
                                </button>
                                <button type="button" class="remove-repeater-item button">
                                    <i class="zil zi-cross"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" id="add-repeater-item" class="button">
                <i class="zil zi-plus"></i> <?php esc_html_e('Add Font', 'dokan-lite'); ?>
            </button>
        </div>
        <?php
    }

    // Add JavaScript for Font Repeater Functionality
    add_action('wp_footer', 'fontier_dokan_repeater_script');
    function fontier_dokan_repeater_script() {
        ?>
        <script>
            (function($) {
                $(document).ready(function() {
                    var index = $('#font-repeater-wrapper .repeater-item').length || 0;

                    // Toggle the repeater visibility based on checkbox
                    $('#font_preview_enable').change(function() {
                        if ($(this).is(':checked')) {
                            $('#font-repeater-section').slideDown();
                        } else {
                            $('#font-repeater-section').slideUp();
                        }
                    });

                    // Add new repeater item
                    $('#add-repeater-item').click(function() {
                        var newItem = `
                            <div class="repeater-item fontier_font_fes_repeater_item" data-index="${index}">
                                <label for="font_title_${index}">Font Title</label>
                                <input type="text" name="font_repeater[${index}][font_title]" value="" />

                                <label for="font_file_${index}">Font File</label>
                                <div class="fontier_font_files_fes">
                                    <input type="url" id="font_file_${index}" name="font_repeater[${index}][font_file]" value="" />
                                    <button type="button" class="upload-font-button button" data-target="#font_file_${index}">Upload <i class="zil zi-upload"></i></button>

                                    <button type="button" class="remove-repeater-item button"><i class="zil zi-cross"></i></button>
                                </div>
                            </div>`;
                        $('#font-repeater-wrapper').append(newItem);
                        index++;
                    });

                    // Remove repeater item
                    $(document).on('click', '.remove-repeater-item', function() {
                        $(this).closest('.repeater-item').remove();
                    });

                    // Media Uploader for font files
                    $(document).on('click', '.upload-font-button', function(e) {
                        e.preventDefault();
                        var button = $(this);
                        var target = $(button.data('target'));

                        var file_frame = wp.media({
                            title: 'Upload or Select a Font',
                            button: {
                                text: 'Use this Font'
                            },
                            multiple: false
                        });

                        file_frame.on('select', function() {
                            var attachment = file_frame.state().get('selection').first().toJSON();
                            target.val(attachment.url);
                        });

                        file_frame.open();
                    });
                });
            })(jQuery);
        </script>
        <?php
    }
}
