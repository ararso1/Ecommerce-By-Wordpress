<?php
/**
 * Settings class.
 */
class Fontier_Admin_Settings {
    /**
     * Settings constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->settings_options();
        $this->fontier_font_metabox();
    }


    /**
     * Settings sections.
     *
     * @since 1.0.0
     */
    protected function settings_sections() {
        $settings_sections = array(
            array(
                'id'     => 'general',
                'title'  => 'Welcome',
                'icon' =>'dashicons dashicons-buddicons-groups',
                'class' =>'wezido-col-4',
                'fields' => array(

                    array(
                        'id'         => 'wezido_w_text',
                        'type'       => 'fontierwelcometext',
                        'title'      => '',
                    ),

                    array(
                        'id'         => 'wezido_m_banner',
                        'type'       => 'mayosisbanner',
                        'title'      => '',
                    ),

                )),


            array(
                'id'     => 'settings',
                'title'  => 'Settings',
                'icon' =>'dashicons dashicons-admin-generic',
                'fields' => array(


                    array(
                        'id'      => 'fontier_title',
                        'type'    => 'text',
                        'title'   => 'Preview Title',
                        'default' => 'Preview Demo Fonts'
                    ),

                    array(
                        'id'      => 'fontier_default_text',
                        'type'    => 'text',
                        'title'   => 'Default Preview Text',
                        'default' => 'A Quick Brown Fox Jump Over a Lazy Dog'
                    ),
                    
                    array(
                          'id'         => 'fontier_glyph_ebl',
                          'type'       => 'switcher',
                          'title'      => 'Glyph Section',
                          'text_on'    => 'Enabled',
                          'text_off'   => 'Disabled',
                          'text_width' => 100,
                           'default' => true
                        ),
                    
                     array(
                        'id'      => 'fontier_glyph_text',
                        'type'    => 'text',
                        'title'   => 'Glyph Section Title',
                        'default' => 'Glyph List',
                        'dependency' => array( 'fontier_glyph_ebl', '==', 'true' ) 
                    ),
                    

                    array(
                        'id'          => 'fontier_main_color',
                        'type'        => 'color',
                        'title'       => 'Primary Color',
                        'default' => '#5a01f0',
                        'output' => array( 'background-color' => '.element-1', 'color' => '.font-settings .font-settings-size a.active', 'border-color' => '.font-settings .text .settings-input:hover,.font-settings .text .settings-input:focus' )
                    ),

                    array(
                        'id'          => 'fontier_common_bg_color',
                        'type'        => 'color',
                        'title'       => 'Searchbar Background Color',
                        'default' =>'#faf9fb',
                        'output'      => '.font-settings',
                        'output_mode' => 'background-color',
                    ),
                    
                    
                    array(
                        'id'          => 'fontier_btn_color',
                        'type'        => 'color',
                        'title'       => 'FES Add Button Background Color',
                        'default' => '#5a01f0',
                        'output' => array( 'background-color' => '#add-repeater-item' )
                    ),
                    
                     array(
                        'id'          => 'fontier_btn_border_color',
                        'type'        => 'color',
                        'title'       => 'FES Add Button Border Color',
                        'default' => '#5a01f0',
                        'output' => array( 'border-color' => '#add-repeater-item' )
                    ),
                    
                     array(
                        'id'          => 'fontier_btn_text_color',
                        'type'        => 'color',
                        'title'       => 'FES Add Button Text Color',
                        'default' => '#fff',
                        'output' => array( 'color' => '#add-repeater-item' )
                    ),

                    array(
                        'id'          => 'fontier_common_txt_color',
                        'type'        => 'color',
                        'title'       => 'Searchbar Text Color',
                        'default' =>'#59608e',
                        'output'      => '.font-settings .font-settings-size a',
                        'output_mode' => 'color',
                    ),


                    array(
                        'id'          => 'fontier_input_field_bg_color',
                        'type'        => 'color',
                        'title'       => 'Searchbar Input Field Background Color',
                        'default' =>'#e9edf7',
                        'output'      => '.font-settings input.settings-input',
                        'output_mode' => 'background-color',
                    ),


                    array(
                        'id'                              => 'font_overlay_color',
                        'type'                            => 'background',
                        'title'                           => 'Preview Overlay Color',
                        'background_gradient'             => true,
                        'background_origin'               => false,
                        'background_clip'                 => false,
                        'background_blend_mode'           => false,
                        'background_image'                => false,
                        'background_repeat'                => false,
                        'background_position'                => false,
                        'background_attachment'                => false,
                        'background_size'                => false,
                        'default'                         => array(
                            'background-color'              => 'rgba(255,255,255,0)',
                            'background-gradient-color'     => '#ffffff',
                            'background-gradient-direction' => 'to right',
                        ),

                        'output' =>'.fontier-list li:after',
                    ),
                    
                    
                    
                    array(
                        'id'          => 'fontier_glyph_ttl_color',
                        'type'        => 'color',
                        'title'       => 'Glyph Title Color',
                        'default' =>'#164a41',
                        'output'      => '#fontier-glyph-generate h3',
                        'output_mode' => 'color',
                        'dependency' => array( 'fontier_glyph_ebl', '==', 'true' ) 
                    ),
                    
                     array(
                        'id'          => 'fontier_glyph_border_color',
                        'type'        => 'color',
                        'title'       => 'Glyph Border Color',
                        'default' =>'#ccc',
                        'output'      => '.fontier-glyph-list li,.fontier-glyph-list',
                        'output_mode' => 'border-color',
                        'dependency' => array( 'fontier_glyph_ebl', '==', 'true' ) 
                    ),
                    
                    
                    array(
  'type'    => 'notice',
  'style'   => 'warning',
  'content' => 'Starting from version 1.3 of the plugin, the category selector will no longer function. However, it has been retained as a fallback for existing users',
),
       
                    
                      array(
                        'id'          => 'select_font_category',
                        'type'        => 'select',
                        'title'       => 'Select Category',
                        'placeholder' => 'Select a category where you want to show font',
                        'chosen'      => true,
                        'ajax'        => true,
                        'options'     => 'categories',
                        'query_args'  => array(
                            'taxonomy'  => 'download_category'
                        )
                    ),
                    
                    
                // A Notice
array(
  'type'    => 'notice',
  'style'   => 'success',
  'content' => 'To display the font repeater in the FES Submission form, utilize the action hook "fes_render_field_fontier_repeater"',
),


             
                )),

        );
        
        return apply_filters( 'fontier_settings_sections', $settings_sections );

    }

    /**
     * Settings Options.
     *
     * @since 1.0.3
     */
    protected function settings_options() {
        $settings_options_slug = 'fontier_options';

        \CSF::createOptions( $settings_options_slug, array(
            'framework_title'         => 'Fontier Options <small>by Teconce</small>',
            'menu_title'  => 'Fontier Options',
            'menu_slug'   => 'fontier_options',
            'menu_type'   => 'submenu',
            'menu_parent' => 'options-general.php',
            'sticky_header'           => false,
            'show_search'             => true,
            'show_reset_all'          => false,
            'show_reset_section'      => false,
            'show_footer'             => false,
            'show_all_options'        => true,
            'show_form_warning'       => true,
            'sticky_header'           => false,
            'save_defaults'           => true,
            'ajax_save'               => true,

            // admin bar menu settings
            'admin_bar_menu_icon'     => '',
            'admin_bar_menu_priority' => 80,

            // footer
            'footer_text'             => '',
            'footer_after'            => '',
            'footer_credit'           => ' Thank you for using Fontier. Powered by <a href="https://teconce.com">Teconce</a>',

            'nav'                     => 'inline',
            'theme'                   => 'light',
            'class'                   => '',
        ) );

        $settings_sections = $this->settings_sections();

        if ( is_array( $settings_sections ) && ! empty( $settings_sections ) ) {
            foreach ( $settings_sections as $settings_section ) {
                \CSF::createSection( $settings_options_slug, $settings_section );
            }
        }
    }
    
    
    	/**
	 *Fontier Metabox.
	 *
	 * @since 1.0.3
	 */

protected function fontier_font_metabox() {
    $metabox_slug = 'fontier_post_option';

   \CSF::createMetabox( $metabox_slug, array(
    'title'     => 'Fontier Options',
    'post_type' => array( 'download', 'product' ), // Add both EDD and WooCommerce post types
    'data_type' => 'unserialize',
) );


    \CSF::createSection( $metabox_slug, array(
        'fields' => array(

            array(
                'id'      => 'font_preview_enable',
                'type'    => 'checkbox',
                'title'   => 'Enable Font Preview',
                'default' => false // or false
            ),
            
            array(
                'id'      => 'custom_fontier_title',
                'type'    => 'text',
                'title'   => 'Custom Preview Title',
                'default' => ''
            ),

            array(
                'id'      => 'font_repeater',
                'type'    => 'repeater',
                'title'   => 'Upload Fonts',
                'fields'  => array(
                    array(
                        'id'    => 'font_title',
                        'type'  => 'text',
                        'title' => 'Font Title',
                    ),
                    array(
                        'id'    => 'font_file',
                        'type'  => 'upload',
                        'title' => 'Font File (TTF/OTF)',
                        'desc'  => 'Upload a TTF or OTF font file.',
                        'settings' => array(
                            'upload_type' => 'file',
                            'mime_types'  => 'ttf,otf',
                        ),
                    ),
                ),
                'default' => array(),
            ),
        ),
    ) );
}

}
