<?php

/**
 *
 * Field: fontierwelcometext
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'CSF_Field_fontierwelcometext' ) ) {
  class CSF_Field_fontierwelcometext extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      echo $this->field_before();

      echo '<h3>Welcome to Fontier Dashboard!</h3>
Fontier is now installed and ready to use!';

      echo $this->field_after();

    }

  }
}


/**
 *
 * Field: mayosisbanner
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'CSF_Field_mayosisbanner' ) ) {
  class CSF_Field_mayosisbanner extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      echo $this->field_before();

      echo '<div class="about-description wezido_mayosis_license">
            If you want to sell your items such as templates, arts, tutorials, music, ebooks, stock photography, stock footage, themes, plugins, code snippets, Softwares or digital services then Mayosis is for you. Built on WordPress and free Easy Digital Downloads. Mayosis – Digital Marketplace Theme allows you to create your own marketplace such as Amazon, eBay, Etsy, Themeforest or CreativeMarket and it takes only a few hours to set up your website and sell goods. The theme is heavily customized and organized for selling a different kind of digital products as we mentioned above. Integrated Visual Composer plugin allows you to create your web pages quickly &amp; visually without knowing a single piece of code.            <div class="img-box-mayo" style="margin-top:15px;">
            <a href="https://teconce.com/item/mayosis-digital-marketplace-theme/"><img src="https://teconce.files.wordpress.com/2018/09/mayosis.jpg" alt="mayosis" class="mayosis-banner"></a>
            </div>
        </div>';

      echo $this->field_after();

    }

  }
}
