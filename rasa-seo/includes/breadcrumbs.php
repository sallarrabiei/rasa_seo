<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_breadcrumbs_items() {
	$items = array();
	$items[] = array('label' => get_bloginfo('name'), 'url' => home_url('/'));
	if (is_singular()) {
		global $post;
		$ancestors = array_reverse(get_post_ancestors($post));
		foreach ($ancestors as $ancestor_id) {
			$items[] = array('label' => get_the_title($ancestor_id), 'url' => get_permalink($ancestor_id));
		}
		$items[] = array('label' => get_the_title($post), 'url' => get_permalink($post));
	} elseif (is_category() || is_tag()) {
		$term = get_queried_object();
		$items[] = array('label' => single_term_title('', false), 'url' => get_term_link($term));
	} elseif (is_search()) {
		$items[] = array('label' => sprintf(__('Search results for "%s"'), get_search_query()), 'url' => home_url('/?s=' . urlencode(get_search_query())));
	} elseif (is_home()) {
		$items[] = array('label' => __('Blog'), 'url' => get_permalink(get_option('page_for_posts')));
	}
	return $items;
}

function rasa_breadcrumbs_render($args = array()) {
	if (!rasa_bool(rasa_get_option('breadcrumbs_enabled', true))) { return ''; }
	$items = rasa_breadcrumbs_items();
	$sep = ' / ';
	$out = '<nav class="rasa-breadcrumbs" aria-label="Breadcrumbs">';
	$links = array();
	$last_index = count($items) - 1;
	foreach ($items as $index => $item) {
		$label = esc_html($item['label']);
		$url = esc_url($item['url']);
		if ($index < $last_index) {
			$links[] = '<a href="' . $url . '">' . $label . '</a>';
		} else {
			$links[] = '<span aria-current="page">' . $label . '</span>';
		}
	}
	$out .= implode('<span class="sep">' . esc_html($sep) . '</span>', $links);
	$out .= '</nav>';
	return $out;
}

function rasa_breadcrumbs_shortcode($atts = array()) {
	return rasa_breadcrumbs_render();
}
add_shortcode('rasa_breadcrumbs', 'rasa_breadcrumbs_shortcode');

function rasa_breadcrumbs_jsonld_graph() {
	$items = rasa_breadcrumbs_items();
	$list = array();
	$pos = 1;
	foreach ($items as $item) {
		$list[] = array(
			'@type' => 'ListItem',
			'position' => $pos++,
			'name' => $item['label'],
			'item' => $item['url']
		);
	}
	return array(
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => $list
	);
}