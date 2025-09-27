<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_edd_extend_tokens($tokens, $post) {
	if ($post && $post->post_type === 'download') {
		$tokens['%price%'] = function_exists('edd_get_download_price') ? edd_get_download_price($post->ID) : '';
	}
	return $tokens;
}