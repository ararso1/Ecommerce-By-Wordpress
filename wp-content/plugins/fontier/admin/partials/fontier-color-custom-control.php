<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.

/**
 * Custom Color Field: fontier_color
 */
if ( ! class_exists( 'CSF_Field_fontier_color' ) ) {
  class CSF_Field_fontier_color extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      // Default color attribute
      $default_attr = ( ! empty( $this->field['default'] ) ) ? ' data-default-color="'. esc_attr( $this->field['default'] ) .'"' : '';

      echo $this->field_before();
      echo '<input type="text" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="fontier-color-picker"'. $default_attr . $this->field_attributes() .'/>';
      echo $this->field_after();
    }

    public function output() {
      $output    = '';
      $elements  = ( isset( $this->field['output'] ) && is_array( $this->field['output'] ) ) ? $this->field['output'] : [];
      $important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';

      if ( ! empty( $elements ) && isset( $this->value ) && $this->value !== '' ) {
        foreach ( $elements as $property => $selectors ) {
          if ( is_array( $selectors ) ) {
            $selectors = implode( ',', $selectors );
          }

          if ( is_numeric( $property ) ) {
            $output .= $selectors . '{ color:' . $this->value . $important . '; }';
          } else {
            $output .= $selectors . '{ ' . $property . ':' . $this->value . $important . '; }';
          }
        }
      }

      $this->parent->output_css .= $output;

      return $output;
    }

  }
}
