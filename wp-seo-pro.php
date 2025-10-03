<?php
/**
 * Plugin Name: WP SEO Pro
 * Plugin URI: https://example.com/wp-seo-pro
 * Description: A comprehensive SEO plugin for WordPress with advanced features for Blogs, Pages, WooCommerce, and Easy Digital Downloads. Includes migration tools from popular SEO plugins.
 * Version: 1.0.0
 * Author: WP SEO Pro Team
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-seo-pro
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_SEO_PRO_VERSION', '1.0.0');
define('WP_SEO_PRO_PLUGIN_FILE', __FILE__);
define('WP_SEO_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_SEO_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_SEO_PRO_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'WP_SEO_Pro\\';
    $base_dir = WP_SEO_PRO_PLUGIN_DIR . 'includes/';
    
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

// Initialize the plugin
function wp_seo_pro_init() {
    // Check WordPress version
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . 
                 __('WP SEO Pro requires WordPress 5.0 or higher.', 'wp-seo-pro') . 
                 '</p></div>';
        });
        return;
    }
    
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . 
                 __('WP SEO Pro requires PHP 7.4 or higher.', 'wp-seo-pro') . 
                 '</p></div>';
        });
        return;
    }
    
    // Initialize the main plugin class
    new WP_SEO_Pro\Core\Plugin();
}

// Hook into WordPress
add_action('plugins_loaded', 'wp_seo_pro_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    WP_SEO_Pro\Core\Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    WP_SEO_Pro\Core\Deactivator::deactivate();
});

// Uninstall hook
register_uninstall_hook(__FILE__, function() {
    WP_SEO_Pro\Core\Uninstaller::uninstall();
});