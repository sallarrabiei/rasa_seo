<?php
/**
 * Plugin Name: Rasa SEO
 * Description: Comprehensive SEO plugin for WordPress for Blogs, Pages, WooCommerce, and Easy Digital Downloads. Includes migrations from Yoast, All in One SEO, and Rank Math.
 * Version: 1.0.0
 * Author: Rasa
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Text Domain: rasa-seo
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!defined('RASA_SEO_VERSION')) {
	define('RASA_SEO_VERSION', '1.0.0');
}
if (!defined('RASA_SEO_FILE')) {
	define('RASA_SEO_FILE', __FILE__);
}
if (!defined('RASA_SEO_BASENAME')) {
	define('RASA_SEO_BASENAME', plugin_basename(__FILE__));
}
if (!defined('RASA_SEO_DIR')) {
	define('RASA_SEO_DIR', plugin_dir_path(__FILE__));
}
if (!defined('RASA_SEO_URL')) {
	define('RASA_SEO_URL', plugin_dir_url(__FILE__));
}

add_action('init', 'rasa_register_sitemap_rewrites');

register_activation_hook(__FILE__, 'rasa_activate');
register_deactivation_hook(__FILE__, 'rasa_deactivate');

function rasa_activate() {
	rasa_register_sitemap_rewrites();
	flush_rewrite_rules();
}

function rasa_deactivate() {
	flush_rewrite_rules();
}

function rasa_register_sitemap_rewrites() {
	add_rewrite_rule('^rasa-sitemap\.xml$', 'index.php?rasa_sitemap=index', 'top');
	add_rewrite_rule('^rasa-sitemap-([^/]+)-(\d+)\.xml$', 'index.php?rasa_sitemap=posttype&type=$matches[1]&paged=$matches[2]', 'top');
	add_rewrite_rule('^rasa-sitemap-([^/]+)\.xml$', 'index.php?rasa_sitemap=posttype&type=$matches[1]', 'top');
	add_rewrite_rule('^rasa-tax-sitemap-([^/]+)-(\d+)\.xml$', 'index.php?rasa_sitemap=taxonomy&tax=$matches[1]&paged=$matches[2]', 'top');
	add_rewrite_rule('^rasa-tax-sitemap-([^/]+)\.xml$', 'index.php?rasa_sitemap=taxonomy&tax=$matches[1]', 'top');
	add_filter('query_vars', 'rasa_filter_query_vars');
}

function rasa_filter_query_vars($vars) {
	$vars[] = 'rasa_sitemap';
	$vars[] = 'type';
	$vars[] = 'tax';
	$vars[] = 'paged';
	return $vars;
}

add_action('plugins_loaded', 'rasa_bootstrap');

function rasa_bootstrap() {
	require_once RASA_SEO_DIR . 'includes/helpers.php';
	require_once RASA_SEO_DIR . 'includes/settings.php';
	require_once RASA_SEO_DIR . 'includes/admin.php';
	require_once RASA_SEO_DIR . 'includes/patterns.php';
	require_once RASA_SEO_DIR . 'includes/metaboxes.php';
	require_once RASA_SEO_DIR . 'includes/sitemaps.php';
	require_once RASA_SEO_DIR . 'includes/robots.php';
	require_once RASA_SEO_DIR . 'includes/breadcrumbs.php';
	require_once RASA_SEO_DIR . 'includes/schema.php';
	require_once RASA_SEO_DIR . 'includes/integrations.php';
	require_once RASA_SEO_DIR . 'includes/migrate.php';
	if (rasa_is_woocommerce_active()) {
		require_once RASA_SEO_DIR . 'includes/woocommerce.php';
	}
	if (rasa_is_edd_active()) {
		require_once RASA_SEO_DIR . 'includes/edd.php';
	}

	add_action('init', 'rasa_register_settings');
	add_action('init', 'rasa_register_meta');

	add_action('admin_menu', 'rasa_register_admin_menu');
	add_action('admin_enqueue_scripts', 'rasa_enqueue_admin_assets');

	add_filter('pre_get_document_title', 'rasa_filter_document_title', 20);
	add_action('wp_head', 'rasa_output_meta_tags', 1);
	add_action('wp_head', 'rasa_output_verification_tags', 2);
	add_action('wp_head', 'rasa_output_canonical_tag', 3);
	add_action('wp_head', 'rasa_output_meta_robots', 4);
	add_action('wp_head', 'rasa_output_schema_jsonld', 5);

	add_action('template_redirect', 'rasa_handle_sitemap_request');
	add_filter('robots_txt', 'rasa_filter_robots_txt', 10, 2);
}

// Uninstall logic in uninstall.php