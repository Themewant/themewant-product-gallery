# CLAUDE.md

This file guides Claude Code when working in this repository.

## Overview

**Themewant Product Gallery** (marketed as "Easy Product Gallery", WordPress.org slug `themewant-product-gallery`) is a free WooCommerce plugin. It replaces the single-product image gallery with a Swiper-based slider and adds variation image galleries, YouTube video slides, hover zoom, a Fancybox lightbox and a 360° view.

- Requires WooCommerce (`Requires Plugins: woocommerce`), WP 5.8+, PHP 7.4+. Declares HPOS compatibility.
- Text domain: `themewant-product-gallery`. POT file: `languages/themewant-product-gallery.pot`.
- Version lives in **three places** and must stay in sync: the plugin header and `TWPGY_VERSION` in [themewant-product-gallery.php](themewant-product-gallery.php), and `Stable tag` in [readme.txt](readme.txt).
- This is a WordPress.org plugin, so code must follow the directory guidelines: no trialware, no locked features, no remote assets, and GPL-compatible bundled libraries. Pro features are added by the separate `themewant-product-gallery-pro` plugin (in the sibling folder `../themewant-product-gallery-pro`) through filters, never by gating code here.

## No build step

There is no `package.json` or bundler.
- SCSS sources (`admin/assets/css/admin-style.scss`, `public/assets/css/public.scss`, `public/assets/css/woodmart.scss`) are compiled by hand, with an editor Sass extension, into `.css`, `.min.css` and `.map` files next to the source. When you edit the SCSS, update the compiled CSS too, or tell the user it needs recompiling.
- JS files (`admin/assets/js/admin.js`, `public/assets/js/public.js`) are plain jQuery and are enqueued as they are.
- `composer.json` only pulls in `appsero/client`. The vendored copy in `vendor/appsero/client` has been **namespace-prefixed by hand** to `Twpgy\Appsero` (to avoid clashes with other plugins), so **do not run `composer update`/`install`**; it would overwrite the prefixing.

## Naming conventions

- All PHP functions, classes, hooks, options, meta keys, AJAX actions and nonces use the `twpgy_` / `TWPGY_` prefix. Some older CSS classes and JS use `eapgy-` and `easyel_`; keep the existing names when editing that code.
- Constants: `TWPGY_VERSION`, `TWPGY_GALLERY_PL_PATH`, `TWPGY_GALLERY_PL_URL`, `TWPGY_GALLERY_DIR_URL`, `TWPGY_GALLERY_PLUGIN_BASE`, `TWPGY_GALLERY_PL_ROOT`.
- Classes use the singleton pattern (`get_instance()` / `instance()`) and are created at the bottom of their own file.
- The bundled Swiper is configured with `twpgy-swiper-*` class names (see the top of `public/assets/js/public.js`), so it doesn't conflict with a theme's own Swiper. Gallery markup must use the `twpgy-swiper-*` classes, not `swiper-*`.

## Architecture

### Bootstrap
1. [themewant-product-gallery.php](themewant-product-gallery.php) defines the constants, loads the Composer autoloader, declares HPOS compatibility, registers the activation hook and starts the Appsero tracker.
2. [includes/class-woo-gallery-activation.php](includes/class-woo-gallery-activation.php) runs on activation. It seeds default `twpgy_woo_gallery_options['settings']`, or merges the defaults into options that already exist.
3. [includes/helper-function.php](includes/helper-function.php) holds global helpers: `twpgy_allowed_html()` / `twpgy_kses()` for output, `twpgy_get_display_settings_for_product()`, the 360° metabox, its save and render code, and the admin notices.
4. [includes/class-easy-woo-gallery.php](includes/class-easy-woo-gallery.php) (`TWPGY_WOO_GALLERY`) registers the custom image sizes `twpgy_woo_gallery_image_size` and `twpgy_woo_gallery_image_size_grid_large`. It resizes images on the fly through `wp_get_attachment_image_src`, and when an image size setting changes it schedules a WP-Cron regeneration (`twpgy_regenerate_images`). On `plugins_loaded` it loads the admin and public layers.

### Admin (`admin/`)
- [class-woo-product-gallery-admin.php](admin/class-woo-product-gallery-admin.php) (`TWPGY_Gallery_Admin`) adds the top-level menu `twpgy-woo-gallery` with two submenus: **Gallery Layouts** (the CPT list) and **Upgrade to Pro** (`twpgy-overview`, a Free vs Pro comparison page). It also:
  - saves the global settings form (nonce `twpgy_woo_gallery_action`, `map_deep( …, 'sanitize_text_field' )`, then `update_option`) and handles the reset action;
  - renders the fields generically in `twpgy_render_settings_fields()`, which includes `settings/fields/{type}.php` for each field;
  - adds the layout settings metabox for the CPT and saves it to the post meta `twpgy_layout_settings` (nonce `twpgy_layout_settings_save`).
- [class-woo-gallery-setting-fields.php](admin/class-woo-gallery-setting-fields.php) (`TWPGY_Admin_Settings_Fields`) declares **every setting as an array**, one method per tab: general, slider, navigation, pagination, thumbs, lightbox, zoom, video and 360°. `twpgy_get_all_settings_fields()` merges them. Each field looks like `['id','name','type','label','options','desc','condition']`. `type` must match a template in `admin/settings/fields/`, and `condition` is a CSS class that the JS show/hide logic uses.
- [settings/posttype/easy-post-type.php](admin/settings/posttype/easy-post-type.php) registers the CPT `twpgy_woo_layouts`, which holds per-product layout presets. Products are linked to a preset through the post meta `twpgy_assigned_products` (an array of product IDs) using the AJAX actions `twpgy_save_assigned_products` and `twpgy_get_assigned_products`.
- [class-woocommerce-hooks.php](admin/class-woocommerce-hooks.php) (`TWPGY_Woo_Hooks_Admin`) adds:
  - the variation gallery field (`woocommerce_variation_options`) → variation meta `twpgy_woo_gallery_slider` (comma-separated attachment IDs);
  - a "video URL" field on attachments → attachment meta `twpgy_eswoglry_video`.

### Public (`public/`)
- [class-woo-product-gallery-public.php](public/class-woo-product-gallery-public.php) loads and starts the classes in `public/includes/`.
- [class-woocommerce-hooks.php](public/includes/class-woocommerce-hooks.php) (`TWPGY_Woo_Hooks`) uses the `wc_get_template` filter to replace `single-product/product-image.php` with [public/templates/product-images.php](public/templates/product-images.php), but only when `enable_gallery === 'on'` and the product has images. It also removes the sale flash on single product pages.
- [class-woo-gallery-view.php](public/includes/class-woo-gallery-view.php) (`TWPGY_Woo_Gallery_View`) builds the gallery HTML (`twpgy_get_product_gallery_html( $product_id, $variation_id )`) from `sliders/preview-slider.php` and `sliders/thumb-slider.php`. It also provides the AJAX endpoint `twpgy_get_product_gallery_html` (for both logged-in and logged-out visitors, nonce `twpgy_woo_gallery_nonce`), which `public.js` calls to swap in the gallery for the selected variation.
- [class-woo-plugin-scripts.php](public/includes/class-woo-plugin-scripts.php) (`TWPGY_Enqueue_Scripts`) enqueues assets **only on `is_product()`**, localizes `twpgywoglry_vars` (including `pluginOptions` and `nonce`), adds `defer` and adds a body class.
- [class-woo-dynamic-css.php](public/includes/class-woo-dynamic-css.php) (`TWPGY_Dynamic_CSS`) builds inline CSS from the settings. All values go through `twpgy_sanitize_css_color` and `twpgy_sanitize_css_length`, and the result passes through the `twpgy_dynamic_css` filter.
- Bundled libraries (`public/assets/lib/`): Swiper and Fancybox. Keep them local; never load them from a CDN.

### How settings are resolved (important)
Settings are stored in the single option `twpgy_woo_gallery_options`, split into the sections `settings` and `design`. For each product:
1. Start from the global `settings`.
2. If a **published** `twpgy_woo_layouts` post lists the product in `twpgy_assigned_products`, use that post's `twpgy_layout_settings['post_meta']` **in place of** the global settings. The first matching layout wins.

This logic is **duplicated** in three places: `twpgy_get_display_settings_for_product()` (helper-function.php), `TWPGY_Woo_Gallery_View`, and `TWPGY_Enqueue_Scripts::twpgy_get_final_settings_for_product()` / `TWPGY_Dynamic_CSS::twpgy_get_css_settings_for_product()`. If you change how settings are resolved, update every copy.

Checkbox values are the strings `'on'` / `'off'`, not booleans.

## Extension points for Pro

The Pro plugin hooks into these. Keep their names and arguments stable:
- `twpgy_is_pro_plugin` (bool): hides the upsell UI.
- Option lists: `twpgy_gallery_layout_options`, `twpgy_slider_effect`, `twpgy_pagination_layout`, `twpgy_pagination_position`, `twpgy_zoom_style_filter`, `twpgy_video_icon_position`, `twpgy_popup_button_position_filter`, `twpgy_popup_360degree_layout_filter`.
- Field arrays: `twpgy_pagination_settings_fields`, `twpgy_zoom_settings_fields`, `twpgy_video_settings_fields`, `twpgy_360_degree_settings_field_filter`.
- Output: `twpgy_pro_grid_template`, `twpgy_gallery_wrapper_classes`, `twpgy_main_wrapper_class`, `twpgy_dynamic_css`, `twpgy_popup_button_position_css`, `twpgy_video_player_controls`, `twpgy_default_gallery_settings`, `twpgy_allowed_html`, `twpgy_video_link_help`.
- Actions: `twpgy_on_product_image`, `twpgy_admin_notices`.

If you add a filter that Pro will use, put it here so the free plugin works on its own when Pro is not active.

## Coding rules

- Follow the WordPress PHP Coding Standards and Plugin Check (PCP). Run the `plugin-check` plugin on the local site (it is installed) before a release.
- Security: sanitize input early (`wp_unslash` + a `sanitize_*` function), check `wp_verify_nonce` **and** `current_user_can` before any write, and escape output late. Gallery HTML is output through `twpgy_kses()`. If you add a tag or attribute, whitelist it in `twpgy_allowed_html()`.
- Start every PHP file with `if ( ! defined( 'ABSPATH' ) ) exit;`.
- Prefix global variables in templates with `$twpgy_` (PCP requirement).
- Every user-facing string goes through `__()` / `esc_html__()` and similar with the text domain `themewant-product-gallery`. Add translator comments for placeholders.
- When writing frontend JS, assume themes differ (Woodmart has its own stylesheet, `woodmart.scss`). Bind events with namespaces (`.EASYQUICKVIEW`, `.twpgy…`) and call `.off()` before `.on()`.

## Local environment

The plugin runs on a LocalWP site at `d:\wp-projects\product-gallery\app\public`. The neighbouring plugins (other gallery plugins, `easy-elements`, `themewant-notice-api` and others) are there for reference and compatibility testing only. Make changes inside this plugin unless the user says otherwise.

## Project skills

`.claude/skills/` contains WordPress-specific skills, including `wp-plugin-audit`, `wp-plugin-release`, `wp-i18n-workflow` and `wp-guideline`. Use them for audits, releases and i18n work.
