<?php
/**
 * Plugin Name: Better SEO Suite
 * Description: Comprehensive SEO plugin for Posts, Pages, WooCommerce, and Easy Digital Downloads. Includes patterns, sitemaps, social, schema, and importer from Yoast, AIOSEO, and Rank Math.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Your Company
 * Text Domain: better-seo-suite
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BSS_SEO_VERSION', '1.0.0');
define('BSS_SEO_FILE', __FILE__);
define('BSS_SEO_DIR', plugin_dir_path(__FILE__));
define('BSS_SEO_URL', plugin_dir_url(__FILE__));

// Autoloader and helpers
require_once BSS_SEO_DIR . 'src/Core/Autoloader.php';
\BSS\Core\Autoloader::register(BSS_SEO_DIR . 'src');
require_once BSS_SEO_DIR . 'inc/helpers.php';

// Load i18n
add_action('plugins_loaded', function () {
    load_plugin_textdomain('better-seo-suite', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Bootstrap plugin
add_action('plugins_loaded', function () {
    \BSS\Core\Plugin::get_instance()->boot();
});

register_activation_hook(__FILE__, function () {
    \BSS\Core\Plugin::get_instance()->on_activate();
});

register_deactivation_hook(__FILE__, function () {
    \BSS\Core\Plugin::get_instance()->on_deactivate();
});

register_uninstall_hook(__FILE__, '\\BSS\\Core\\Plugin::on_uninstall');

