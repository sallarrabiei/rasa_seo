<?php

namespace WP_SEO_Pro\Core;

/**
 * Plugin uninstall handler
 */
class Uninstaller {
    
    /**
     * Uninstall the plugin
     */
    public static function uninstall() {
        // Check if user has permission
        if (!current_user_can('delete_plugins')) {
            return;
        }
        
        // Check if we should remove data
        $remove_data = get_option('wp_seo_pro_remove_data_on_uninstall', false);
        
        if (!$remove_data) {
            return;
        }
        
        // Remove database tables
        self::remove_tables();
        
        // Remove options
        self::remove_options();
        
        // Remove transients
        self::remove_transients();
    }
    
    /**
     * Remove database tables
     */
    private static function remove_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'wp_seo_pro_data',
            $wpdb->prefix . 'wp_seo_pro_analytics',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
    
    /**
     * Remove plugin options
     */
    private static function remove_options() {
        $options = array(
            'wp_seo_pro_general',
            'wp_seo_pro_social',
            'wp_seo_pro_advanced',
            'wp_seo_pro_sitemap',
            'wp_seo_pro_woocommerce',
            'wp_seo_pro_edd',
            'wp_seo_pro_remove_data_on_uninstall',
        );
        
        foreach ($options as $option) {
            delete_option($option);
        }
    }
    
    /**
     * Remove transients
     */
    private static function remove_transients() {
        global $wpdb;
        
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_wp_seo_pro_%' 
             OR option_name LIKE '_transient_timeout_wp_seo_pro_%'"
        );
    }
}