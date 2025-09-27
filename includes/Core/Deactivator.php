<?php

namespace WP_SEO_Pro\Core;

/**
 * Plugin deactivation handler
 */
class Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear scheduled events
        wp_clear_scheduled_hook('wp_seo_pro_analytics_update');
        wp_clear_scheduled_hook('wp_seo_pro_sitemap_update');
        
        // Set deactivation flag
        set_transient('wp_seo_pro_deactivated', true, 60);
    }
}