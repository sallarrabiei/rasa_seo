<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_handle_sitemap_request() {
	$which = get_query_var('rasa_sitemap');
	if (empty($which)) {
		return;
	}
	if (!rasa_bool(rasa_get_option('sitemap_enabled', true))) {
		status_header(404);
		exit;
	}
	rasa_xml_header();
	echo '<?xml version="1.0" encoding="' . esc_attr(get_bloginfo('charset')) . '"?>' . "\n";
	if ($which === 'index') {
		rasa_render_sitemap_index();
		exit;
	}
	if ($which === 'posttype') {
		$type = sanitize_key(get_query_var('type'));
		$paged = absint(get_query_var('paged'));
		rasa_render_posttype_sitemap($type, $paged);
		exit;
	}
	if ($which === 'taxonomy') {
		$tax = sanitize_key(get_query_var('tax'));
		$paged = absint(get_query_var('paged'));
		rasa_render_taxonomy_sitemap($tax, $paged);
		exit;
	}
	status_header(404);
	exit;
}

function rasa_render_sitemap_index() {
	echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	// post types
	$post_types = rasa_get_target_post_types();
	foreach ($post_types as $pt) {
		$max = rasa_count_sitemap_pages_for_posttype($pt);
		for ($i = 1; $i <= $max; $i++) {
			$url = home_url('/rasa-sitemap-' . $pt . '-' . $i . '.xml');
			echo '<sitemap><loc>' . esc_url($url) . '</loc></sitemap>';
		}
	}
	// taxonomies: categories and tags, plus product_cat if exists, download_category if exists
	$taxes = array('category', 'post_tag');
	if (taxonomy_exists('product_cat')) { $taxes[] = 'product_cat'; }
	if (taxonomy_exists('download_category')) { $taxes[] = 'download_category'; }
	foreach ($taxes as $tax) {
		$max = rasa_count_sitemap_pages_for_taxonomy($tax);
		for ($i = 1; $i <= $max; $i++) {
			$url = home_url('/rasa-tax-sitemap-' . $tax . '-' . $i . '.xml');
			echo '<sitemap><loc>' . esc_url($url) . '</loc></sitemap>';
		}
	}
	echo '</sitemapindex>';
}

function rasa_count_sitemap_pages_for_posttype($post_type) {
	$per = max(1, (int)rasa_get_option('sitemap_entries_per_page', 1000));
	$total = (int)wp_count_posts($post_type)->publish;
	if ($total < 1) { return 1; }
	return (int)ceil($total / $per);
}

function rasa_count_sitemap_pages_for_taxonomy($tax) {
	$per = max(1, (int)rasa_get_option('sitemap_entries_per_page', 1000));
	$total = (int)wp_count_terms(array('taxonomy' => $tax, 'hide_empty' => true));
	if ($total < 1) { return 1; }
	return (int)ceil($total / $per);
}

function rasa_render_posttype_sitemap($post_type, $paged) {
	$per = max(1, (int)rasa_get_option('sitemap_entries_per_page', 1000));
	$paged = max(1, (int)$paged);
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	$q = new WP_Query(array(
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => $per,
		'paged' => $paged,
		'orderby' => 'ID',
		'order' => 'ASC',
		'no_found_rows' => true,
		'ignore_sticky_posts' => true
	));
	while ($q->have_posts()) {
		$q->the_post();
		$loc = get_permalink();
		$lastmod = get_post_modified_time('c', true);
		echo '<url><loc>' . esc_url($loc) . '</loc><lastmod>' . esc_html($lastmod) . '</lastmod></url>';
	}
	wp_reset_postdata();
	echo '</urlset>';
}

function rasa_render_taxonomy_sitemap($tax, $paged) {
	$per = max(1, (int)rasa_get_option('sitemap_entries_per_page', 1000));
	$paged = max(1, (int)$paged);
	$terms = get_terms(array(
		'taxonomy' => $tax,
		'hide_empty' => true,
		'number' => $per,
		'offset' => ($paged - 1) * $per
	));
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	if (!is_wp_error($terms)) {
		foreach ($terms as $term) {
			$loc = get_term_link($term);
			$lastmod = mysql2date('c', $term->term_group ? $term->term_group : current_time('mysql'));
			echo '<url><loc>' . esc_url($loc) . '</loc></url>';
		}
	}
	echo '</urlset>';
}