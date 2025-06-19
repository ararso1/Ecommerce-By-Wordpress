=== Fontier - Font Preview Plugin for Easy Digital Downloads, WooCommerce, and Dokan ===
Contributors: Teconce, nstuhin, apuzaman
Tags: elementor, elements, addons, elementor addon, elementor widget, page-builder, wordpress page builder, woocommerce, dokan, font preview, fes, frontend submission
Requires at least: 5.6
Requires PHP: 7.4
Tested up to: 6.5.5
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A font preview extension works with Easy Digital Downloads, WooCommerce, and Dokan products.

== Description ==

Fontier is a feature-rich font preview plugin developed for Easy Digital Downloads (EDD), WooCommerce, and Dokan. It empowers sellers to showcase fonts interactively, enhancing the buying experience for customers. Fontier supports multiple font formats (TTF/OTF), enabling seamless font management through a user-friendly repeater system.

**Key Features:**
- **Interactive Font Preview:** Customers can preview fonts with customizable text input.
- **Font Repeater System:** Easily upload and manage fonts with titles, files, and settings.
- **WooCommerce and Dokan Compatibility:** Seamless integration with WooCommerce product meta and Dokan vendor forms.
- **EDD Support with Fallback:** Full compatibility with EDD products, maintaining support for older ZIP-based uploads.
- **Glyph Preview and Generation:** Enables detailed glyph previews for uploaded fonts.
- **Frontend Submission (FES) Compatibility:** Allows font upload and management directly from the frontend submission form.
- **Customizable Preview Titles:** Set unique titles for font previews.
- **Responsive Design:** Optimized display across all devices.
- **Admin Options:** Simplified settings for managing font behavior and glyph visibility.

Fontier automatically processes uploaded fonts from WooCommerce, Dokan, or EDD products, dynamically generating previews and glyphs, ensuring a seamless experience for both sellers and customers.

Take a look at the demo: [demo preview](https://mayosis.themepreview.xyz/mayofont/downloads/chewy-font-family-typeface/).

### Fontier Options

Fontier options are available under settings, enabling you to configure preview behavior, upload options, and glyph generation settings.

### Requirements

[Easy Digital Downloads](https://wordpress.org/plugins/easy-digital-downloads/), WooCommerce, or Dokan is required for this plugin.

### Features

- Font repeater with FES (Frontend Submission) support.
- Glyph generator for uploaded fonts.
- Customizable font preview titles for each product.
- WooCommerce and Dokan support for managing and previewing fonts.
- Full compatibility with EDD for existing products.

== Screenshots ==

1. Settings page.
2. Frontend preview.
3. Font upload admin panel.
4. Font upload dokan and fes.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

== Frequently Asked Questions ==

= Where can I find the option? =

Fontier options will be available under settings.

= Does it work with any theme? =

Absolutely! You need to install either EDD, WooCommerce, or Dokan for the plugin to work.

= How do I upload fonts to use with Fontier? =

Uploading fonts for preview is simple and straightforward. Here's how:

### For Easy Digital Downloads (EDD):
1. **Enable Font Preview:**
   - Navigate to your EDD product page in the WordPress admin area.
   - Locate the **Fontier Options** section on the product edit page.
   - Check the **Enable Font Preview** checkbox.

2. **Add Fonts Using the Repeater Field:**
   - In the **Font Repeater** section, click the **Add Font** button.
   - Fill in the **Font Title** and upload the font file (TTF/OTF).

3. **Save the Product:**
   - Save the product to make the fonts available for preview.

### For WooCommerce:
1. **Enable Font Preview:**
   - Navigate to your WooCommerce product edit page.
   - Locate the **Fontier Options** section in the product meta box.
   - Check the **Enable Font Preview** checkbox.

2. **Add Fonts Using the Repeater Field:**
   - Click the **Add Font** button in the **Font Repeater** section.
   - Add the **Font Title** and upload the font file (TTF/OTF).

3. **Save the Product:**
   - Save the product to store your uploaded fonts.

### For Dokan Vendors:
1. **Enable Font Preview:**
   - Dokan vendors will see a **Fontier Options** section in their product submission form.
   - Check the **Enable Font Preview** checkbox to activate font preview for the product.

2. **Add Fonts Using the Repeater Field:**
   - In the repeater field, vendors can click **Add Font** to upload new fonts.
   - Vendors can add a **Font Title** and upload the font file directly from the frontend.

3. **Save the Product:**
   - Save the product to finalize the font uploads.

= What font file formats are supported? =

The plugin supports TTF (TrueType Font) and OTF (OpenType Font) formats for all integrations.

= Can I upload fonts through the frontend? =

Yes, if FES or Dokan is enabled, fonts can be uploaded directly from the frontend. Use the action hook `fes_render_field_fontier_repeater` for FES or `dokan_product_edit_after_product_tags` for Dokan.

= What happens to my old ZIP-based font uploads? =

The older ZIP-based font system is supported as a fallback for EDD. Transition to the new repeater system is recommended for better functionality.

= How can I revert to the older plugin version if needed? =

1. Ensure you have a backup of your website and database.
2. Deactivate and delete the current Fontier plugin.
3. Install the previous version from your backup.
4. Activate the plugin through the **Plugins** menu.

== Changelog ==

= 1.4 =
* Added Dokan support for frontend font upload and management.
* Font repeater and glyph functionalities extended to Dokan vendors.
* Improved admin UI for managing font previews.
* Bug fixes and performance enhancements.

= 1.3 =
* Added WooCommerce support for Font Repeater and Glyph Generator.
* Font preview and glyph functionalities are now available for WooCommerce products.
* New admin notice added to highlight changes and recommendations.
* Enhanced compatibility for Easy Digital Downloads (EDD).
* FES compatibility extended to WooCommerce.
* Bug fixes and performance improvements.

= 1.2 =
* Glyph list system added.
* Glyph enable/disable option added.
* Other improvements.

= 1.1 =
* OTF support added.
* ZIP unzip issue fixed.
* Language support added.
* Product-wise custom preview text field added.
* Framework updated.
* CSS issues fixed.
* Bug fixed.

= 1.0.4 =
* Functions updated.
* Div closing issue fixed.
* CSS issues fixed.
* Bug fixed.

= 1.0.3 =
* Functions updated.
* CSS issues fixed.
* Bug fixed.

= 1.0.2 =
* CSS issue fixed.
* Settings updated.
* Bug fixed.

= 1.0.1 =
* Added preview overlay color option.
* Settings added.
* Bug fixed.

= 1.0 =
* Initial Launch
