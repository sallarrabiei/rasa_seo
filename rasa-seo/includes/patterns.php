<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_tokens_for_post($post) {
	$author = get_userdata($post->post_author);
	$primary_cat = '';
	$cats = get_the_category($post);
	if (!empty($cats)) {
		$primary_cat = $cats[0]->name;
	}
	$site_name = get_bloginfo('name');
	$site_desc = get_bloginfo('description');
	$sep = rasa_get_option('title_sep', '–');
	$excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 24);
	$tokens = array(
		'%title%' => get_the_title($post),
		'%sitename%' => $site_name,
		'%sitedesc%' => $site_desc,
		'%sep%' => $sep,
		'%excerpt%' => $excerpt,
		'%category%' => $primary_cat,
		'%author%' => $author ? $author->display_name : '',
		'%date%' => mysql2date(get_option('date_format'), $post->post_date),
		'%modified%' => mysql2date(get_option('date_format'), $post->post_modified),
		'%id%' => (string)$post->ID
	);
	$tokens = apply_filters('rasa_tokens_pre', $tokens, $post);
	if ('product' === $post->post_type && function_exists('wc_get_product')) {
		$wc_product = wc_get_product($post->ID);
		if ($wc_product) {
			$tokens['%price%'] = wc_price($wc_product->get_price());
		}
	}
	$tokens = apply_filters('rasa_tokens_post', $tokens, $post);
	return $tokens;
}

function rasa_apply_pattern($pattern, $tokens) {
	if (!$pattern) {
		return '';
	}
	return trim(preg_replace('/\s+/', ' ', strtr($pattern, $tokens)));
}

function rasa_generate_seo_title($post = null) {
	if (is_null($post)) {
		$post = get_post();
	}
	if (!$post) {
		if (is_front_page()) {
			return rasa_get_option('home_title', get_bloginfo('name'));
		}
		return wp_get_document_title();
	}
	$custom = get_post_meta($post->ID, 'rasa_title', true);
	if (!empty($custom)) {
		return rasa_sanitize_text($custom);
	}
	$pt = $post->post_type;
	$pattern_key = 'pattern_' . $pt . '_title';
	$pattern = rasa_get_option($pattern_key, '%title% %sep% %sitename%');
	return rasa_apply_pattern($pattern, rasa_tokens_for_post($post));
}

function rasa_generate_seo_description($post = null) {
	if (is_null($post)) {
		$post = get_post();
	}
	if (!$post) {
		return rasa_get_option('home_desc', get_bloginfo('description'));
	}
	$custom = get_post_meta($post->ID, 'rasa_description', true);
	if (!empty($custom)) {
		return rasa_sanitize_text($custom);
	}
	$pt = $post->post_type;
	$pattern_key = 'pattern_' . $pt . '_desc';
	$pattern = rasa_get_option($pattern_key, '%excerpt%');
	return rasa_apply_pattern($pattern, rasa_tokens_for_post($post));
}

function rasa_filter_document_title($title) {
	if (is_admin()) {
		return $title;
	}
	if (is_front_page()) {
		return rasa_get_option('home_title', get_bloginfo('name'));
	}
	global $post;
	if ($post) {
		return rasa_generate_seo_title($post);
	}
	return $title;
}

function rasa_output_meta_tags() {
	if (is_admin()) {
		return;
	}
	$desc = '';
	$title = '';
	$url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));
	$site_name = get_bloginfo('name');
	if (is_front_page()) {
		$title = rasa_get_option('home_title', $site_name);
		$desc = rasa_get_option('home_desc', get_bloginfo('description'));
	} else {
		global $post;
		if ($post) {
			$title = rasa_generate_seo_title($post);
			$desc = rasa_generate_seo_description($post);
			$url = get_permalink($post);
		}
	}
	if (!empty($desc)) {
		echo '<meta name="description" content="' . esc_attr(wp_trim_words($desc, 48)) . '" />' . "\n";
	}
	if (rasa_bool(rasa_get_option('og_enabled', true))) {
		echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
		echo '<meta property="og:description" content="' . esc_attr(wp_trim_words($desc, 48)) . '" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
		echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '" />' . "\n";
	}
	if (rasa_bool(rasa_get_option('twitter_enabled', true))) {
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr(wp_trim_words($desc, 48)) . '" />' . "\n";
	}
}

function rasa_output_canonical_tag() {
	if (is_admin()) { return; }
	$link = '';
	if (is_singular()) {
		global $post;
		$custom = $post ? get_post_meta($post->ID, 'rasa_canonical', true) : '';
		$link = $custom ?: get_permalink($post);
	} else {
		$link = home_url(add_query_arg(array(), $GLOBALS['wp']->request));
	}
	if (!empty($link)) {
		echo '<link rel="canonical" href="' . esc_url($link) . '" />' . "\n";
	}
}

function rasa_output_meta_robots() {
	if (is_admin()) { return; }
	$directives = array();
	if (is_search() && rasa_bool(rasa_get_option('robots_noindex_search', true))) {
		$directives[] = 'noindex';
	}
	if (is_404() && rasa_bool(rasa_get_option('robots_noindex_404', true))) {
		$directives[] = 'noindex';
	}
	if ((is_archive() || is_home()) && is_paged() && rasa_bool(rasa_get_option('robots_noindex_paginated', false))) {
		$directives[] = 'noindex';
	}
	if (is_singular()) {
		global $post;
		$meta = $post ? trim((string)get_post_meta($post->ID, 'rasa_robots', true)) : '';
		if ($meta !== '') {
			echo '<meta name="robots" content="' . esc_attr($meta) . '" />' . "\n";
			return;
		}
	}
	if (!empty($directives)) {
		echo '<meta name="robots" content="' . esc_attr(implode(',', array_unique($directives))) . '" />' . "\n";
	}
}