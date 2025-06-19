<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://teconce.com/about/
 * @since      1.0.0
 *
 * @package    Fontier
 * @subpackage Fontier/public/partials
 */
?>
<?php
     global $post;
     $fontiercustomtext = get_post_meta( get_the_ID(), 'custom_fontier_title', true );
     $fontieroptions= get_option( 'fontier_options' );
     if ($fontiercustomtext){
         $previewtitle = $fontiercustomtext;
     } else {
         $previewtitle = $fontieroptions['fontier_default_text'];
     }
     
     $fontier_glyph_ebl =  $fontieroptions['fontier_glyph_ebl']; 
     $fontier_glyph_text =  $fontieroptions['fontier_glyph_text'];
     
?>
<!-- Font Preview Section -->
<div class="clearfix">
    <div class="font-settings" data-settings="">
        <div class="text">
            <div id="fontier-font-loading" style="display: none;">
                <i class="zil zi-settings zi-spin"></i> Loading...
            </div>
            <input class="settings-input" type="text" name="text" value=""
                   data-default="<?php echo $previewtitle; ?>"
                   placeholder="Type here to preview text...">
        </div>
        <div class="font-settings-size">
            <a href="javascript:void(0)" data-size="24" class="s24">24pt</a>
            <a href="javascript:void(0)" data-size="36" class="s36 active">36pt</a>
            <a href="javascript:void(0)" data-size="48" class="s48">48pt</a>
            <a href="javascript:void(0)" data-size="72" class="s72">72pt</a>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="fontier-font-preview" style="visibility: visible;">
        <ul class="fontier-list">
            <li>
                <span></span>
                <div class="text-center">
                    <i class="zil zi-settings zi-spin"></i> Loading preview, please wait...
                </div>
            </li>
        </ul>
    </div>
</div>

<!-- Glyph Section -->
<?php if ($fontier_glyph_ebl){ ?>
    <div id="fontier-glyph-generate" class="fontier-glyph-container"></div>
<?php } ?>

<script>
    (function ($) {
        'use strict';

        $(document).ready(function () {
            var defaultText = $('.settings-input').data('default');
            var defaultSize = 36;

            // Load initial preview and glyphs
            loadFontPreview(defaultText, defaultSize);
            <?php if ($fontier_glyph_ebl) {?>
                loadGlyphs(defaultSize);
            <?php } ?>

            // Input field change event
            $('.settings-input').on('keyup paste', throttle(function () {
                var text = $(this).val().trim();
                if (text.length === 0) {
                    text = defaultText; // Reset to default text
                    $(this).val(text);
                }
                $("#fontier-font-loading").show();
                var size = $(".font-settings-size a.active").data("size");
                loadFontPreview(text, size);
            }, 900));

            // Font size switcher
            $(".font-settings-size a").click(function (e) {
                e.preventDefault();
                if (!$(this).hasClass("active")) {
                    var size = $(this).data("size");
                    $(".font-settings-size a.active").removeClass("active");
                    $(this).addClass("active");

                    var text = $('.settings-input').val().trim();
                    if (text.length === 0) {
                        text = defaultText; // Reset to default text
                        $('.settings-input').val(text);
                    }
                    $("#fontier-font-loading").show();
                    loadFontPreview(text, size);
                    <?php if ($fontier_glyph_ebl) : ?>
                        loadGlyphs(size);
                    <?php endif; ?>
                }
            });
        });

        function throttle(fn, delay) {
            var timer = null;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    fn.apply(context, args);
                }, delay || 900);
            };
        }

        function loadFontPreview(text, size) {
            var data = {
                action: 'fontGenrate',
                auth: '<?php echo wp_create_nonce("_prevnounce"); ?>',
                pid: '<?php echo get_the_ID(); ?>',
                size: size,
                text: text
            };

            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                dataType: 'json',
                data: data,
                success: function (response) {
                    if (response.status === 1) {
                        var images = response.images;
                        var html = '';
                        images.forEach(function (img) {
                            html += '<li><span>' + img.name + '</span><img src="' + img.url + '" /></li>';
                        });
                        $('.fontier-font-preview ul').html(html);
                    } else {
                        $('.fontier-font-preview ul').html('<li>No preview available.</li>');
                    }
                    $("#fontier-font-loading").hide();
                },
                error: function () {
                    console.error('Failed to load font preview.');
                    $('.fontier-font-preview ul').html('<li>Error loading preview.</li>');
                    $("#fontier-font-loading").hide();
                }
            });
        }

        function loadGlyphs(size) {
            var data = {
                action: 'fontier_glyph_generator',
                auth: '<?php echo wp_create_nonce("_prevnounce"); ?>',
                pid: '<?php echo get_the_ID(); ?>',
                size: size
            };

            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                dataType: 'json',
                data: data,
                success: function (response) {
                    if (response.status === 1) {
                        var html = '';
                        response.glyphs.forEach(function (fontGlyph) {
                            html += '<h3>' + fontGlyph.font + '</h3><ul class="fontier-glyph-list">';
                            fontGlyph.glyphs.forEach(function (glyph) {
                                html += '<li><img src="' + glyph.image + '" alt="' + glyph.char + '" /></li>';
                            });
                            html += '</ul>';
                        });
                        $('#fontier-glyph-generate').html(html);
                    } else {
                        $('#fontier-glyph-generate').html('<p>No glyphs available.</p>');
                    }
                },
                error: function () {
                    console.error('Failed to load glyphs.');
                    $('#fontier-glyph-generate').html('<p>Error loading glyphs.</p>');
                }
            });
        }
    })(jQuery);
</script>
