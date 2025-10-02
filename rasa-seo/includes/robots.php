<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_filter_robots_txt($output, $public) {
	$lines = array();
	$lines[] = '# Rasa SEO';
	if (rasa_bool(rasa_get_option('robots_noindex_search', true))) {
		$lines[] = 'Disallow: /?s=';
		$lines[] = 'Disallow: /search/';
	}
	$lines[] = 'Sitemap: ' . home_url('/rasa-sitemap.xml');
	return trim($output) . "\n" . implode("\n", $lines) . "\n";
}