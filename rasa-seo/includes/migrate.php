<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_handle_migration() {
	if (!current_user_can('manage_options')) {
		wp_die('Insufficient permissions');
	}
	check_admin_referer('rasa_migrate');
	$vendor = isset($_GET['vendor']) ? sanitize_key($_GET['vendor']) : '';
	$count = 0;
	$updated = 0;
	$post_types = rasa_get_target_post_types();
	$q = new WP_Query(array(
		'post_type' => $post_types,
		'post_status' => 'any',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'no_found_rows' => true
	));
	foreach ($q->posts as $post_id) {
		$count++;
		list($title, $desc, $canon, $robots) = rasa_get_vendor_meta($vendor, $post_id);
		$changed = false;
		if ($title) { update_post_meta($post_id, 'rasa_title', rasa_sanitize_text($title)); $changed = true; }
		if ($desc) { update_post_meta($post_id, 'rasa_description', rasa_sanitize_text($desc)); $changed = true; }
		if ($canon) { update_post_meta($post_id, 'rasa_canonical', esc_url_raw($canon)); $changed = true; }
		if ($robots) { update_post_meta($post_id, 'rasa_robots', rasa_sanitize_text($robots)); $changed = true; }
		if ($changed) { $updated++; }
	}
	$redirect = add_query_arg(array('rasa_migrated' => $vendor, 'count' => $count, 'updated' => $updated), admin_url('admin.php?page=rasa-seo-migrations'));
	wp_safe_redirect($redirect);
	exit;
}

function rasa_get_vendor_meta($vendor, $post_id) {
	$title = $desc = $canon = $robots = '';
	switch ($vendor) {
		case 'yoast':
			$title = get_post_meta($post_id, '_yoast_wpseo_title', true);
			$desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
			$canon = get_post_meta($post_id, '_yoast_wpseo_canonical', true);
			$robots_index = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);
			$robots_follow = get_post_meta($post_id, '_yoast_wpseo_meta-robots-nofollow', true);
			if ($robots_index === '1') { $robots = 'noindex'; }
			if ($robots_follow === '1') { $robots = trim($robots . ',nofollow', ','); }
			break;
		case 'rankmath':
			$title = get_post_meta($post_id, 'rank_math_title', true);
			$desc = get_post_meta($post_id, 'rank_math_description', true);
			$canon = get_post_meta($post_id, 'rank_math_canonical_url', true);
			$robots_arr = get_post_meta($post_id, 'rank_math_robots', true);
			if (is_array($robots_arr)) { $robots = implode(',', $robots_arr); }
			break;
		case 'aioseo':
			$aio = get_post_meta($post_id, '_aioseo_meta', true);
			if (is_array($aio)) {
				$title = isset($aio['title']) ? $aio['title'] : '';
				$desc = isset($aio['description']) ? $aio['description'] : '';
				$canon = isset($aio['canonicalUrl']) ? $aio['canonicalUrl'] : '';
			}
			break;
	}
	return array($title, $desc, $canon, $robots);
}