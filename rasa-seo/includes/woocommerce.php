<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_wc_extend_tokens($tokens, $post) {
	if (function_exists('wc_get_product') && $post && $post->post_type === 'product') {
		$product = wc_get_product($post->ID);
		if ($product) {
			$tokens['%sku%'] = $product->get_sku();
			$tokens['%price%'] = $product->get_price() !== '' ? $product->get_price() : '';
		}
	}
	return $tokens;
}