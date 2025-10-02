<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

delete_option('rasa_seo_options');

$post_types = array('post','page','product','download');
$meta_keys = array('rasa_title','rasa_description','rasa_canonical','rasa_robots','rasa_focus_keyword');

foreach ($post_types as $pt) {
	$ids = get_posts(array('post_type'=>$pt,'post_status'=>'any','numberposts'=>-1,'fields'=>'ids'));
	foreach ($ids as $id) {
		foreach ($meta_keys as $key) {
			delete_post_meta($id, $key);
		}
	}
}