<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_output_verification_tags() {
	if (is_admin()) { return; }
	$opts = rasa_get_options();
	$map = array(
		'verify_google' => 'google-site-verification',
		'verify_bing' => 'msvalidate.01',
		'verify_yandex' => 'yandex-verification',
		'verify_pinterest' => 'p:domain_verify'
	);
	foreach ($map as $key => $name) {
		$val = isset($opts[$key]) ? trim((string)$opts[$key]) : '';
		if ($val !== '') {
			echo '<meta name="' . esc_attr($name) . '" content="' . esc_attr($val) . '" />' . "\n";
		}
	}
}

add_action('admin_post_rasa_migrate', 'rasa_handle_migration');