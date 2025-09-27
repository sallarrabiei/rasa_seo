<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_is_woocommerce_active() {
	return class_exists('WooCommerce');
}

function rasa_is_edd_active() {
	return class_exists('Easy_Digital_Downloads') || class_exists('EDD');
}

function rasa_option_name() {
	return 'rasa_seo_options';
}

function rasa_default_options() {
	return array(
		'title_sep' => '–',
		'home_title' => get_bloginfo('name'),
		'home_desc' => get_bloginfo('description'),
		'pattern_post_title' => '%title% %sep% %sitename%',
		'pattern_post_desc' => '%excerpt%',
		'pattern_page_title' => '%title% %sep% %sitename%',
		'pattern_page_desc' => '%excerpt%',
		'pattern_product_title' => '%title% %sep% %sitename%',
		'pattern_product_desc' => '%excerpt%',
		'pattern_download_title' => '%title% %sep% %sitename%',
		'pattern_download_desc' => '%excerpt%',
		'sitemap_enabled' => true,
		'sitemap_entries_per_page' => 1000,
		'robots_noindex_search' => true,
		'robots_noindex_404' => true,
		'robots_noindex_paginated' => false,
		'breadcrumbs_enabled' => true,
		'schema_enabled' => true,
		'og_enabled' => true,
		'twitter_enabled' => true,
		'verify_google' => '',
		'verify_bing' => '',
		'verify_yandex' => '',
		'verify_pinterest' => ''
	);
}

function rasa_get_options() {
	$saved = get_option(rasa_option_name(), array());
	if (!is_array($saved)) {
		$saved = array();
	}
	return wp_parse_args($saved, rasa_default_options());
}

function rasa_get_option($key, $default = null) {
	$opts = rasa_get_options();
	return array_key_exists($key, $opts) ? $opts[$key] : $default;
}

function rasa_update_options($new) {
	$opts = rasa_get_options();
	$merged = array_merge($opts, is_array($new) ? $new : array());
	update_option(rasa_option_name(), $merged);
	return $merged;
}

function rasa_sanitize_text($text) {
	return trim(wp_strip_all_tags((string)$text));
}

function rasa_bool($value) {
	if (is_bool($value)) {
		return $value;
	}
	if (is_string($value)) {
		$value = strtolower($value);
		return in_array($value, array('1','true','yes','on'), true);
	}
	return (bool)$value;
}

function rasa_get_target_post_types() {
	$types = array('post', 'page');
	if (rasa_is_woocommerce_active()) {
		$types[] = 'product';
	}
	if (rasa_is_edd_active()) {
		$types[] = 'download';
	}
	return $types;
}

function rasa_xml_header() {
	header('Content-Type: application/xml; charset=' . get_bloginfo('charset'));
}

function rasa_esc_xml($string) {
	return esc_xml($string);
}