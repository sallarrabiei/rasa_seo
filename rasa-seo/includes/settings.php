<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_register_settings() {
	register_setting('rasa_seo', rasa_option_name(), array(
		'type' => 'array',
		'sanitize_callback' => 'rasa_sanitize_options',
		'default' => rasa_default_options()
	));
}

function rasa_sanitize_options($input) {
	$defaults = rasa_default_options();
	$output = array();
	foreach ($defaults as $key => $def) {
		if (isset($input[$key])) {
			$value = $input[$key];
			if (is_bool($def)) {
				$output[$key] = rasa_bool($value);
			} else {
				$output[$key] = rasa_sanitize_text($value);
			}
		} else {
			$output[$key] = $def;
		}
	}
	return $output;
}

function rasa_meta_auth_callback($allowed, $meta_key, $object_id, $user_id, $cap, $caps) {
	return current_user_can('edit_post', (int)$object_id);
}

function rasa_register_meta() {
	$args_text = array(
		'show_in_rest' => true,
		'single' => true,
		'type' => 'string',
		'auth_callback' => 'rasa_meta_auth_callback'
	);
	$post_types = rasa_get_target_post_types();
	foreach ($post_types as $pt) {
		register_post_meta($pt, 'rasa_title', $args_text);
		register_post_meta($pt, 'rasa_description', $args_text);
		register_post_meta($pt, 'rasa_canonical', $args_text);
		register_post_meta($pt, 'rasa_robots', $args_text);
		register_post_meta($pt, 'rasa_focus_keyword', $args_text);
	}
}