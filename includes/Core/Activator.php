<?php

namespace WP_SEO_Pro\Core;

/**
 * Plugin activation handler
 */
class Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set activation flag
        set_transient('wp_seo_pro_activated', true, 60);
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // SEO data table
        $table_name = $wpdb->prefix . 'wp_seo_pro_data';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY meta_key (meta_key)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Analytics data table
        $analytics_table = $wpdb->prefix . 'wp_seo_pro_analytics';
        
        $analytics_sql = "CREATE TABLE $analytics_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            date date NOT NULL,
            metric varchar(100) NOT NULL,
            value bigint(20) DEFAULT 0,
            post_id bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY date (date),
            KEY metric (metric),
            KEY post_id (post_id)
        ) $charset_collate;";
        
        dbDelta($analytics_sql);
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $default_options = array(
            'wp_seo_pro_general' => array(
                'enable_meta_tags' => true,
                'enable_schema' => true,
                'enable_sitemap' => true,
                'enable_analytics' => true,
                'separator' => '|',
                'home_title' => get_bloginfo('name'),
                'home_description' => get_bloginfo('description'),
            ),
            'wp_seo_pro_social' => array(
                'facebook_app_id' => '',
                'twitter_username' => '',
                'linkedin_username' => '',
                'instagram_username' => '',
                'youtube_username' => '',
            ),
            'wp_seo_pro_advanced' => array(
                'noindex_archive' => false,
                'noindex_search' => true,
                'noindex_404' => true,
                'remove_wp_generator' => true,
                'remove_rsd_link' => true,
                'remove_wlwmanifest_link' => true,
                'remove_shortlink' => true,
            ),
            'wp_seo_pro_sitemap' => array(
                'enable_xml_sitemap' => true,
                'sitemap_posts_per_page' => 1000,
                'exclude_post_types' => array(),
                'exclude_taxonomies' => array(),
            ),
            'wp_seo_pro_woocommerce' => array(
                'enable_product_schema' => true,
                'enable_review_schema' => true,
                'product_title_template' => '{product_name} | {site_name}',
                'product_description_template' => '{product_excerpt}',
            ),
            'wp_seo_pro_edd' => array(
                'enable_download_schema' => true,
                'download_title_template' => '{download_name} | {site_name}',
                'download_description_template' => '{download_excerpt}',
            ),
        );
        
        foreach ($default_options as $option_name => $option_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $option_value);
            }
        }
    }
}