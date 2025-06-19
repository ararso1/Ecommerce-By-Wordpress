<?php

class Fontier_Admin {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_filter( 'upload_mimes', array( $this, 'fontier_allowed_mimes' ) );
        add_filter( 'wp_check_filetype_and_ext', array( $this, 'fontier_update_mime_types' ), 10, 3 );
        add_action( 'admin_notices', array( $this, 'fontier_admin_notice' ) );
        add_action( 'wp_ajax_fontier_dismiss_notice', array( $this, 'fontier_dismiss_notice' ) );
    }

    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fontier-admin.css', array(), $this->version, 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fontier-admin.js', array( 'jquery' ), $this->version, false );
    }

    public function fontier_allowed_mimes( $mimes ) {
        $mimes['woff']  = 'application/x-font-woff';
        $mimes['woff2'] = 'application/x-font-woff2';
        $mimes['ttf']   = 'application/x-font-ttf';
        $mimes['svg']   = 'image/svg+xml';
        $mimes['eot']   = 'application/vnd.ms-fontobject';
        $mimes['otf']   = 'font/otf';

        return $mimes;
    }

    public function fontier_update_mime_types( $defaults, $file, $filename ) {
        if ( 'ttf' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
            $defaults['type'] = 'application/x-font-ttf';
            $defaults['ext']  = 'ttf';
        }

        if ( 'otf' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
            $defaults['type'] = 'application/x-font-otf';
            $defaults['ext']  = 'otf';
        }

        return $defaults;
    }

    public function fontier_admin_notice() {
        if ( get_user_meta( get_current_user_id(), 'fontier_notice_dismissed', true ) ) {
            return;
        }
        ?>
      <div class="notice notice-warning is-dismissible" id="fontier-admin-notice">
            <h3>Fontier Plugin Update</h3>
            <p>
                The <strong>Fontier Plugin</strong> now features a <strong>Font Repeater</strong> system with full <strong>FES (Frontend Submission)</strong> support, replacing the previous ZIP-based system.
            </p>
            <p>
                <strong>WooCommerce Support:</strong> We've added full support for WooCommerce products! You can now use the font repeater and glyph generator functionality for both WooCommerce and Easy Digital Downloads (EDD) products.
            </p>
            <p>
                <strong>Fallback Support:</strong> Existing ZIP-based fonts for EDD products will continue to work. However, we recommend transitioning to the new repeater system for enhanced functionality.
            </p>
            <p>
                <strong>Backup Recommendation:</strong> Before upgrading, ensure you have a backup of the previous plugin version. If you've already upgraded, revert to the older version to create a backup and then reinstall this update.
            </p>
            <p>
                For assistance, please refer to the plugin's <a href="options-general.php?page=fontier_options">Settings Page</a>.
            </p>
        </div>
        <script>
            (function($) {
                $(document).ready(function() {
                    $('#fontier-admin-notice .notice-dismiss').click(function() {
                        $.post(ajaxurl, {
                            action: 'fontier_dismiss_notice',
                            user_id: <?php echo get_current_user_id(); ?>
                        });
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    public function fontier_dismiss_notice() {
        if ( isset( $_POST['user_id'] ) ) {
            update_user_meta( sanitize_text_field( $_POST['user_id'] ), 'fontier_notice_dismissed', true );
        }
        wp_die();
    }

    public function fontier_settings_link( $links ) {
        $settings_link = '<a href="options-general.php?page=fontier_options">Settings</a>'; 
        array_unshift( $links, $settings_link ); 
        return $links; 
    }
}
