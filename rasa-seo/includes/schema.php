<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_output_schema_jsonld() {
	if (!rasa_bool(rasa_get_option('schema_enabled', true))) {
		return;
	}
	$graph = array();
	$site_url = home_url('/');
	$site_name = get_bloginfo('name');
	$graph[] = array(
		'@context' => 'https://schema.org',
		'@type' => 'WebSite',
		'@id' => $site_url . '#website',
		'url' => $site_url,
		'name' => $site_name,
		'potentialAction' => array(
			'@type' => 'SearchAction',
			'target' => home_url('/?s={search_term_string}'),
			'query-input' => 'required name=search_term_string'
		)
	);
	if (rasa_bool(rasa_get_option('breadcrumbs_enabled', true))) {
		if (function_exists('rasa_breadcrumbs_jsonld_graph')) {
			$graph[] = rasa_breadcrumbs_jsonld_graph();
		}
	}
	if (is_singular()) {
		global $post;
		$type = 'Article';
		if ('product' === get_post_type($post)) {
			$type = 'Product';
		}
		$entity = array(
			'@context' => 'https://schema.org',
			'@type' => $type,
			'@id' => get_permalink($post) . '#main',
			'url' => get_permalink($post),
			'headline' => get_the_title($post),
			'description' => rasa_generate_seo_description($post),
			'datePublished' => get_post_time('c', true, $post),
			'dateModified' => get_post_modified_time('c', true, $post),
			'author' => array(
				'@type' => 'Person',
				'name' => get_the_author_meta('display_name', $post->post_author)
			)
		);
		$graph[] = $entity;
	}
	echo '<script type="application/ld+json">' . wp_json_encode(count($graph) === 1 ? $graph[0] : array('@graph' => $graph)) . '</script>' . "\n";
}