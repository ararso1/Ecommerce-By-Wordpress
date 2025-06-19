<?php

use FontLib\Font;

// Autoloader for FontLib
spl_autoload_register(function ($class) {
    $prefix = 'FontLib\\';
    $base_dir = __DIR__ . '/vendor/php-font-lib/src/FontLib/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

class Fontier_Public {
    private $fontier_product_type_edd = 'download'; // EDD
    private $fontier_product_type_wc = 'product'; // WooCommerce
    private $fontier_product_taxonomy_edd = 'download_category';
    private $layout;
    private $folder;
    private $dir;
    private $version;

    public function __construct() {
        define('FONTIER_PATH', plugin_dir_path(__FILE__));
        $this->layout = FONTIER_PATH . '/partials/fontier-public-display.php';
        $this->folder = basename(FONTIER_PATH);
        $this->dir = plugin_dir_url(__FILE__);
        $this->version = '1.0';

        // Content modification for both EDD and WooCommerce
        add_action('the_content', array($this, 'fontier_before_post_content'));

        // AJAX Actions
        add_action('wp_ajax_fontGenrate', array($this, 'fontier_font_preview_generator'));
        add_action('wp_ajax_nopriv_fontGenrate', array($this, 'fontier_font_preview_generator'));
        add_action('wp_ajax_fontier_glyph_generator', array($this, 'fontier_glyph_generator'));
        add_action('wp_ajax_nopriv_fontier_glyph_generator', array($this, 'fontier_glyph_generator'));

        // Styles
        add_action('wp_enqueue_scripts', array($this, 'fontier_enqueue_styles'));
    }

    public function fontier_before_post_content($content) {
        if (is_singular($this->fontier_product_type_edd) || is_singular($this->fontier_product_type_wc)) {
            $id = get_the_ID();
            $font_enable = $this->fontier_is_font_enable($id);

            if ($font_enable) {
                ob_start();
                $font_files = $this->fontierGetFontFiles($id);

                if (!empty($font_files)) {
                    include $this->layout;
                } else {
                    echo '<p>No fonts available for preview. Please update to the new font management system.</p>';
                }

                $fontier_content = ob_get_contents();
                ob_end_clean();
                $content = $fontier_content . $content;
            }
        }
        return $content;
    }

    public function fontier_is_font_enable($id) {
        $enable_check = get_post_meta($id, 'font_preview_enable', true);
        return $enable_check === '1';
    }

    public function fontierGetFontFiles($id) {
        $result = array();
        $post_type = get_post_type($id);

        // Repeater handling for both WooCommerce and EDD
        $repeater_fonts = get_post_meta($id, 'font_repeater', true);
        if (!empty($repeater_fonts) && is_array($repeater_fonts)) {
            foreach ($repeater_fonts as $font) {
                if (!empty($font['font_file'])) {
                    $font_file = $font['font_file'];

                    // Convert URL to server path if necessary
                    if (filter_var($font_file, FILTER_VALIDATE_URL)) {
                        $font_file = str_replace(
                            wp_get_upload_dir()['baseurl'],
                            wp_get_upload_dir()['basedir'],
                            $font_file
                        );
                    }

                    if (file_exists($font_file)) {
                        $result[] = $font_file;
                    } else {
                        error_log("Repeater font file not found: $font_file");
                    }
                }
            }
        }

        // Add ZIP fallback for EDD only
        if ($post_type === $this->fontier_product_type_edd) {
            $download_files = get_post_meta($id, 'edd_download_files', true);
            if (isset($download_files) && is_array($download_files)) {
                foreach ($download_files as $fi) {
                    $getAID = attachment_url_to_postid($fi['file']);
                    $zipfile = get_attached_file($getAID);

                    $Fetchinfo = pathinfo($zipfile);
                    $getDirectory = $Fetchinfo['dirname'];
                    $targetFolder = $getDirectory . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $id;

                    if (!file_exists($targetFolder)) {
                        mkdir($targetFolder, 0777, true);
                        $result = array_merge($result, $this->fontierCheckFont($targetFolder));
                    } else {
                        $result = array_merge($result, $this->fontierCheckFont($targetFolder));
                    }
                }
            }
        }

        return $result;
    }

    public function fontierCheckFont($path) {
        if (file_exists($path)) {
            $folder = opendir($path);
            $pic_types = array("ttf", "otf");
            $index = array();
            while ($file = readdir($folder)) {
                $parts = explode(".", $file);
                $ext = strtolower(end($parts));
                if (in_array($ext, $pic_types)) {
                    array_push($index, $path . DIRECTORY_SEPARATOR . $file);
                }
            }
            closedir($folder);
            return $index;
        }
        return array();
    }

    public function fontier_font_preview_generator() {
        $retArr = array('status' => 0, 'msg' => '', 'images' => array(), 'glyphs' => array());
        $nonce = sanitize_text_field($_REQUEST['auth']);
        if (wp_verify_nonce($nonce, '_prevnounce')) {
            $size = intval(sanitize_text_field($_REQUEST['size']));
            $text = sanitize_text_field($_REQUEST['text']);
            $pid = intval(sanitize_text_field($_REQUEST['pid']));

            $font_Files = $this->fontierGetFontFiles($pid);
            $repeater_fonts = get_post_meta($pid, 'font_repeater', true);

            if (!empty($font_Files)) {
                foreach ($font_Files as $index => $f) {
                    if (!file_exists($f)) {
                        error_log("Font file does not exist: $f");
                        continue;
                    }

                    // Use the repeater title if available, fallback to font file name
                    $title = !empty($repeater_fonts[$index]['font_title'])
                        ? $repeater_fonts[$index]['font_title']
                        : pathinfo($f, PATHINFO_FILENAME);

                    $url = $this->fontier_create_image(700, 200, $text, $size, $f);

                    if ($url) {
                        $retArr['images'][] = array('name' => ucwords($title), 'url' => $url);
                        $retArr['status'] = 1;
                    } else {
                        error_log("Failed to create preview for font: $f");
                    }

                    // Generate glyph list for the font
                    $glyphs = $this->generate_glyph_list($f, $size);
                    if ($glyphs) {
                        $retArr['glyphs'][] = array(
                            'font' => ucwords($title),
                            'glyphs' => $glyphs,
                        );
                    } else {
                        error_log("Failed to generate glyphs for font: $f");
                    }
                }
            } else {
                $retArr['msg'] = 'No fonts available.';
                error_log("No fonts available for Post ID $pid");
            }
        } else {
            $retArr['msg'] = 'Auth Failed!';
            error_log("Nonce validation failed in fontier_font_preview_generator");
        }

        wp_send_json($retArr);
        wp_die();
    }

    public function fontier_glyph_generator() {
        $retArr = array('status' => 0, 'msg' => '', 'glyphs' => array());
        $nonce = sanitize_text_field($_REQUEST['auth']);

        if (wp_verify_nonce($nonce, '_prevnounce')) {
            $size = intval(sanitize_text_field($_REQUEST['size']));
            $pid = intval(sanitize_text_field($_REQUEST['pid']));

            if ($size < 10 || $size > 100) {
                $size = 36;
            }

            $font_Files = $this->fontierGetFontFiles($pid);

            if (!empty($font_Files)) {
                foreach ($font_Files as $f) {
                    if (!file_exists($f)) {
                        error_log("Font file does not exist: $f");
                        continue;
                    }

                    $filename = pathinfo($f, PATHINFO_FILENAME);
                    $glyphs = $this->generate_glyph_list($f, $size);

                    if ($glyphs) {
                        $retArr['glyphs'][] = array(
                            'font' => $filename,
                            'glyphs' => $glyphs,
                        );
                        $retArr['status'] = 1;
                    } else {
                        error_log("Failed to generate glyphs for font: $f");
                    }
                }
            } else {
                $retArr['msg'] = 'No fonts available.';
            }
        } else {
            $retArr['msg'] = 'Auth Failed!';
        }

        wp_send_json($retArr);
        wp_die();
    }

    public function fontier_create_image($width, $height, $text, $fontsize, $font_file) {
        $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));
        $bbox = imagettfbbox($fontsize, 0, $font_file, $text);
        $imageWidth = min(abs($bbox[2] - $bbox[0]) + 20, $width);
        $imageHeight = min(abs($bbox[1] - $bbox[7]) + 20, $height);

        $image = imagecreatetruecolor($imageWidth, $imageHeight);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $transparent);
        $black = imagecolorallocate($image, 0, 0, 0);
        $x = 10;
        $y = abs($bbox[5]) + 10;

        imagettftext($image, $fontsize, 0, $x, $y, $black, $font_file, $text);

        ob_start();
        imagepng($image);
        $image_data = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);
        return "data:image/png;base64," . base64_encode($image_data);
    }

    public function generate_glyph_list($font_file, $fontsize) {
        $glyphs = array();
        $font = Font::load($font_file);
        $font->parse();

        $glyphList = $font->getUnicodeCharMap();
        foreach ($glyphList as $code => $glyph) {
            $char = mb_convert_encoding(pack('N', $code), 'UTF-8', 'UCS-4BE');
            $image = $this->fontier_create_image(50, 50, $char, $fontsize, $font_file);
            $glyphs[] = array('char' => $char, 'image' => $image);
        }

        return $glyphs;
    }

    public function fontier_enqueue_styles() {
        wp_enqueue_style('fontier-public', plugin_dir_url(__FILE__) . 'css/fontier-public.css', array(), $this->version, 'all');
    }
}
