<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_add_meta_boxes() {
	$post_types = rasa_get_target_post_types();
	foreach ($post_types as $pt) {
		add_meta_box('rasa_seo_box', 'Rasa SEO', 'rasa_render_meta_box', $pt, 'normal', 'high');
	}
}
add_action('add_meta_boxes', 'rasa_add_meta_boxes');

function rasa_render_meta_box($post) {
	wp_nonce_field('rasa_save_meta', 'rasa_meta_nonce');
	$title = get_post_meta($post->ID, 'rasa_title', true);
	$desc = get_post_meta($post->ID, 'rasa_description', true);
	$canon = get_post_meta($post->ID, 'rasa_canonical', true);
	$robots = get_post_meta($post->ID, 'rasa_robots', true);
	$focus = get_post_meta($post->ID, 'rasa_focus_keyword', true);
	echo '<p><label>SEO Title</label><br /><input type="text" name="rasa_title" value="' . esc_attr($title) . '" class="widefat" /></p>';
	echo '<p><label>Meta Description</label><br /><textarea name="rasa_description" class="widefat" rows="3">' . esc_textarea($desc) . '</textarea></p>';
	echo '<p><label>Canonical URL</label><br /><input type="url" name="rasa_canonical" value="' . esc_attr($canon) . '" class="widefat" /></p>';
	echo '<p><label>Robots (e.g., index,follow or noindex,nofollow)</label><br /><input type="text" name="rasa_robots" value="' . esc_attr($robots) . '" class="widefat" /></p>';
	echo '<p><label>Focus Keyword</label><br /><input type="text" name="rasa_focus_keyword" value="' . esc_attr($focus) . '" class="widefat" /></p>';
}

function rasa_save_post_meta($post_id) {
	if (!isset($_POST['rasa_meta_nonce']) || !wp_verify_nonce($_POST['rasa_meta_nonce'], 'rasa_save_meta')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	$fields = array('rasa_title', 'rasa_description', 'rasa_canonical', 'rasa_robots', 'rasa_focus_keyword');
	foreach ($fields as $f) {
		if (isset($_POST[$f])) {
			$value = is_string($_POST[$f]) ? wp_unslash($_POST[$f]) : '';
			update_post_meta($post_id, $f, rasa_sanitize_text($value));
		} else {
			delete_post_meta($post_id, $f);
		}
	}
}
add_action('save_post', 'rasa_save_post_meta');