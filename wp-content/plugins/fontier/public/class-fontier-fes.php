<?php
if (class_exists( 'EDD_Front_End_Submissions' ) ) {

function fontier_fes_save_data( $post_id ) {
    // Save the checkbox value
    if ( isset( $_POST['font_preview_enable'] ) ) {
        update_post_meta( $post_id, 'font_preview_enable', '1' );
    } else {
        update_post_meta( $post_id, 'font_preview_enable', '0' );
    }

    // Save the repeater data
    if ( isset( $_POST['font_repeater'] ) && is_array( $_POST['font_repeater'] ) ) {
        update_post_meta( $post_id, 'font_repeater', array_values( $_POST['font_repeater'] ) );
    }
}
add_action( 'save_post', 'fontier_fes_save_data' );

function fontier_display_repeater_data( $post_id ) {
    // Use the correct meta key
    $repeater_data = get_post_meta( $post_id, 'font_repeater', true );

    if ( ! empty( $repeater_data ) ) {
        echo '<ul class="font-repeater-list">';
        foreach ( $repeater_data as $font ) {
            echo '<li>';
            echo '<strong>Font Title:</strong> ' . esc_html( $font['font_title'] ) . '<br>';
            echo '<strong>Font File:</strong> <a href="' . esc_url( $font['font_file'] ) . '" target="_blank">Download</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No fonts uploaded.</p>';
    }
}


function fontier_fes_render_repeater_with_checkbox( $form_id, $post_id, $form_settings ) {
    // Fetch existing data
    $font_preview_enabled = get_post_meta( $post_id, 'font_preview_enable', true );
    $repeater_data = get_post_meta( $post_id, 'font_repeater', true ) ?: [];
    ?>
    <div class="fes-checkbox-field">
        <label for="font_preview_enable" class="fes_font_enbale_label">
            <input type="checkbox" id="font_preview_enable" name="font_preview_enable" value="1" <?php checked( $font_preview_enabled, '1' ); ?> />
            Enable Font Preview
        </label>
    </div>

    <div class="fes-repeater-fields" id="font-repeater-section" style="<?php echo $font_preview_enabled ? '' : 'display: none;'; ?>">
        <h3>Upload Fonts</h3>
        <div id="font-repeater-wrapper">
            <?php if ( ! empty( $repeater_data ) ): ?>
                <?php foreach ( $repeater_data as $index => $font ): ?>
                    <div class="repeater-item fontier_font_fes_repeater_item" data-index="<?php echo $index; ?>">
                        <label for="font_title_<?php echo $index; ?>">Font Title</label>
                        <input type="text" name="font_repeater[<?php echo $index; ?>][font_title]" value="<?php echo esc_attr( $font['font_title'] ); ?>" />

                        <label for="font_file_<?php echo $index; ?>">Font File</label>
                        <div class="fontier_font_files_fes">
                        <input type="url" id="font_file_<?php echo $index; ?>" name="font_repeater[<?php echo $index; ?>][font_file]" value="<?php echo esc_url( $font['font_file'] ); ?>" />
                        <button type="button" class="upload-font-button button" data-target="#font_file_<?php echo $index; ?>">Upload <i class="zil zi-upload"></i></button>

                        <button type="button" class="remove-repeater-item button"><i class="zil zi-cross"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" id="add-repeater-item" class="button"><i class="zil zi-plus"></i> Add Font</button>
    </div>
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

add_action( 'fes_render_field_fontier_repeater', 'fontier_fes_render_repeater_with_checkbox', 10, 3 );


}
