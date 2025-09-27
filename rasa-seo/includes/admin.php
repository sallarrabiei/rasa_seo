<?php
if (!defined('ABSPATH')) {
	exit;
}

function rasa_register_admin_menu() {
	add_menu_page(
		'Rasa SEO',
		'Rasa SEO',
		'manage_options',
		'rasa-seo',
		'rasa_render_admin_page',
		'dashicons-chart-line',
		59
	);
	add_submenu_page('rasa-seo', 'Dashboard', 'Dashboard', 'manage_options', 'rasa-seo');
	add_submenu_page('rasa-seo', 'Global Settings', 'Global Settings', 'manage_options', 'rasa-seo-global', 'rasa_render_admin_page');
	add_submenu_page('rasa-seo', 'Patterns', 'Patterns', 'manage_options', 'rasa-seo-patterns', 'rasa_render_admin_page');
	add_submenu_page('rasa-seo', 'Sitemap', 'Sitemap', 'manage_options', 'rasa-seo-sitemap', 'rasa_render_admin_page');
	add_submenu_page('rasa-seo', 'Integrations', 'Integrations', 'manage_options', 'rasa-seo-integrations', 'rasa_render_admin_page');
	add_submenu_page('rasa-seo', 'Migrations', 'Migrations', 'manage_options', 'rasa-seo-migrations', 'rasa_render_admin_page');
	add_submenu_page('rasa-seo', 'Tools', 'Tools', 'manage_options', 'rasa-seo-tools', 'rasa_render_admin_page');
}

function rasa_enqueue_admin_assets($hook) {
	if (strpos($hook, 'rasa-seo') === false) {
		return;
	}
	wp_enqueue_style('rasa-seo-admin', RASA_SEO_URL . 'assets/admin.css', array(), RASA_SEO_VERSION);
	wp_enqueue_script('rasa-seo-admin', RASA_SEO_URL . 'assets/admin.js', array('jquery'), RASA_SEO_VERSION, true);
}

function rasa_admin_tabs() {
	return array(
		'rasa-seo' => 'Dashboard',
		'rasa-seo-global' => 'Global Settings',
		'rasa-seo-patterns' => 'Patterns',
		'rasa-seo-sitemap' => 'Sitemap',
		'rasa-seo-integrations' => 'Integrations',
		'rasa-seo-migrations' => 'Migrations',
		'rasa-seo-tools' => 'Tools'
	);
}

function rasa_render_admin_page() {
	if (!current_user_can('manage_options')) {
		return;
	}
	$screen = isset($_GET['page']) ? sanitize_key($_GET['page']) : 'rasa-seo';
	$tabs = rasa_admin_tabs();
	$options = rasa_get_options();

	if (!empty($_POST['rasa_save_options']) && check_admin_referer('rasa_save_options')) {
		$posted = isset($_POST['rasa_options']) ? (array)$_POST['rasa_options'] : array();
		$sanitized = rasa_sanitize_options($posted);
		rasa_update_options($sanitized);
		$options = rasa_get_options();
		echo '<div class="updated"><p>Settings saved.</p></div>';
	}

	echo '<div class="wrap rasa-seo">';
	echo '<h1>Rasa SEO</h1>';
	echo '<h2 class="nav-tab-wrapper">';
	foreach ($tabs as $slug => $label) {
		$active = $screen === $slug ? ' nav-tab-active' : '';
		echo '<a class="nav-tab' . esc_attr($active) . '" href="' . esc_url(admin_url('admin.php?page=' . $slug)) . '">' . esc_html($label) . '</a>';
	}
	echo '</h2>';

	switch ($screen) {
		case 'rasa-seo-global':
			rasa_render_global_settings($options);
			break;
		case 'rasa-seo-patterns':
			rasa_render_patterns_settings($options);
			break;
		case 'rasa-seo-sitemap':
			rasa_render_sitemap_settings($options);
			break;
		case 'rasa-seo-integrations':
			rasa_render_integrations_settings($options);
			break;
		case 'rasa-seo-migrations':
			rasa_render_migrations_page();
			break;
		case 'rasa-seo-tools':
			rasa_render_tools_page($options);
			break;
		default:
			rasa_render_dashboard($options);
	}
	echo '</div>';
}

function rasa_render_dashboard($options) {
	echo '<div class="card"><h2>Welcome</h2><p>Use the tabs above to configure SEO for your site. Sitemaps are available at <code>' . esc_html(home_url('/rasa-sitemap.xml')) . '</code>.</p></div>';
}

function rasa_render_global_settings($options) {
	echo '<form method="post">';
	wp_nonce_field('rasa_save_options');
	echo '<table class="form-table">';
	// Title separator
	echo '<tr><th scope="row"><label>Title Separator</label></th><td><input type="text" name="rasa_options[title_sep]" value="' . esc_attr($options['title_sep']) . '" class="regular-text" /></td></tr>';
	// Home title/desc
	echo '<tr><th scope="row"><label>Homepage Title</label></th><td><input type="text" name="rasa_options[home_title]" value="' . esc_attr($options['home_title']) . '" class="regular-text" /></td></tr>';
	echo '<tr><th scope="row"><label>Homepage Description</label></th><td><textarea name="rasa_options[home_desc]" class="large-text" rows="3">' . esc_textarea($options['home_desc']) . '</textarea></td></tr>';
	// Toggles
	echo '<tr><th scope="row">Features</th><td>';
	echo '<label><input type="checkbox" name="rasa_options[breadcrumbs_enabled]" ' . checked(true, rasa_bool($options['breadcrumbs_enabled']), false) . ' /> Breadcrumbs</label><br />';
	echo '<label><input type="checkbox" name="rasa_options[schema_enabled]" ' . checked(true, rasa_bool($options['schema_enabled']), false) . ' /> Schema JSON-LD</label><br />';
	echo '<label><input type="checkbox" name="rasa_options[og_enabled]" ' . checked(true, rasa_bool($options['og_enabled']), false) . ' /> Open Graph</label><br />';
	echo '<label><input type="checkbox" name="rasa_options[twitter_enabled]" ' . checked(true, rasa_bool($options['twitter_enabled']), false) . ' /> Twitter Cards</label>';
	echo '</td></tr>';
	// Robots
	echo '<tr><th scope="row">Robots</th><td>';
	echo '<label><input type="checkbox" name="rasa_options[robots_noindex_search]" ' . checked(true, rasa_bool($options['robots_noindex_search']), false) . ' /> Noindex search results</label><br />';
	echo '<label><input type="checkbox" name="rasa_options[robots_noindex_404]" ' . checked(true, rasa_bool($options['robots_noindex_404']), false) . ' /> Noindex 404 pages</label><br />';
	echo '<label><input type="checkbox" name="rasa_options[robots_noindex_paginated]" ' . checked(true, rasa_bool($options['robots_noindex_paginated']), false) . ' /> Noindex paginated archives</label>';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="submit"><button class="button button-primary" name="rasa_save_options" value="1">Save Changes</button></p>';
	echo '</form>';
}

function rasa_render_patterns_settings($options) {
	echo '<form method="post">';
	wp_nonce_field('rasa_save_options');
	echo '<h2>Title & Description Patterns</h2>';
	echo '<p>Use tokens like <code>%title%</code>, <code>%sitename%</code>, <code>%sitedesc%</code>, <code>%excerpt%</code>, <code>%category%</code>, <code>%sep%</code>.</p>';
	echo '<table class="form-table">';
	$fields = array(
		'pattern_post_title' => 'Post Title',
		'pattern_post_desc' => 'Post Description',
		'pattern_page_title' => 'Page Title',
		'pattern_page_desc' => 'Page Description',
		'pattern_product_title' => 'Product Title',
		'pattern_product_desc' => 'Product Description',
		'pattern_download_title' => 'Download Title',
		'pattern_download_desc' => 'Download Description'
	);
	foreach ($fields as $key => $label) {
		echo '<tr><th scope="row"><label>' . esc_html($label) . '</label></th><td><input type="text" name="rasa_options[' . esc_attr($key) . ']" value="' . esc_attr($options[$key]) . '" class="large-text" /></td></tr>';
	}
	echo '</table>';
	echo '<p class="submit"><button class="button button-primary" name="rasa_save_options" value="1">Save Changes</button></p>';
	echo '</form>';
}

function rasa_render_sitemap_settings($options) {
	echo '<form method="post">';
	wp_nonce_field('rasa_save_options');
	echo '<table class="form-table">';
	echo '<tr><th scope="row">Enable Sitemaps</th><td><label><input type="checkbox" name="rasa_options[sitemap_enabled]" ' . checked(true, rasa_bool($options['sitemap_enabled']), false) . ' /> Enabled</label></td></tr>';
	echo '<tr><th scope="row">URLs per Sitemap</th><td><input type="number" min="1" max="50000" name="rasa_options[sitemap_entries_per_page]" value="' . esc_attr($options['sitemap_entries_per_page']) . '" /></td></tr>';
	echo '<tr><th scope="row">Sitemap URL</th><td><code>' . esc_html(home_url('/rasa-sitemap.xml')) . '</code></td></tr>';
	echo '</table>';
	echo '<p class="submit"><button class="button button-primary" name="rasa_save_options" value="1">Save Changes</button></p>';
	echo '</form>';
}

function rasa_render_integrations_settings($options) {
	echo '<form method="post">';
	wp_nonce_field('rasa_save_options');
	echo '<table class="form-table">';
	echo '<tr><th scope="row">Google Verification</th><td><input type="text" name="rasa_options[verify_google]" value="' . esc_attr($options['verify_google']) . '" class="regular-text" /></td></tr>';
	echo '<tr><th scope="row">Bing Verification</th><td><input type="text" name="rasa_options[verify_bing]" value="' . esc_attr($options['verify_bing']) . '" class="regular-text" /></td></tr>';
	echo '<tr><th scope="row">Yandex Verification</th><td><input type="text" name="rasa_options[verify_yandex]" value="' . esc_attr($options['verify_yandex']) . '" class="regular-text" /></td></tr>';
	echo '<tr><th scope="row">Pinterest Verification</th><td><input type="text" name="rasa_options[verify_pinterest]" value="' . esc_attr($options['verify_pinterest']) . '" class="regular-text" /></td></tr>';
	echo '</table>';
	echo '<p class="submit"><button class="button button-primary" name="rasa_save_options" value="1">Save Changes</button></p>';
	echo '</form>';
}

function rasa_render_migrations_page() {
	echo '<div class="card"><h2>Migration Tools</h2><p>Import SEO titles and descriptions from other plugins.</p>';
	$nonce = wp_create_nonce('rasa_migrate');
	echo '<p>';
	echo '<a href="' . esc_url(admin_url('admin-post.php?action=rasa_migrate&vendor=yoast&_wpnonce=' . $nonce)) . '" class="button">Migrate from Yoast</a> ';
	echo '<a href="' . esc_url(admin_url('admin-post.php?action=rasa_migrate&vendor=rankmath&_wpnonce=' . $nonce)) . '" class="button">Migrate from Rank Math</a> ';
	echo '<a href="' . esc_url(admin_url('admin-post.php?action=rasa_migrate&vendor=aioseo&_wpnonce=' . $nonce)) . '" class="button">Migrate from All in One SEO</a>';
	echo '</p></div>';
}

function rasa_render_tools_page($options) {
	$export = esc_html(json_encode($options));
	echo '<h2>Tools</h2>';
	echo '<h3>Export Settings</h3>';
	echo '<textarea class="large-text" rows="8" readonly>' . $export . '</textarea>';
	echo '<h3>Import Settings</h3>';
	echo '<form method="post">';
	wp_nonce_field('rasa_save_options');
	echo '<textarea class="large-text" rows="8" name="rasa_options_json"></textarea>';
	echo '<p class="submit"><button class="button" name="rasa_import_options" value="1">Import</button></p>';
	if (!empty($_POST['rasa_import_options']) && !empty($_POST['rasa_options_json']) && check_admin_referer('rasa_save_options')) {
		$decoded = json_decode(stripslashes((string)$_POST['rasa_options_json']), true);
		if (is_array($decoded)) {
			$sanitized = rasa_sanitize_options($decoded);
			rasa_update_options($sanitized);
			echo '<div class="updated"><p>Settings imported.</p></div>';
		} else {
			echo '<div class="error"><p>Invalid JSON.</p></div>';
		}
	}
	echo '</form>';
}