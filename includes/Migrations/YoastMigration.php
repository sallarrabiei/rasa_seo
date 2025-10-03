<?php

namespace WP_SEO_Pro\Migrations;

/**
 * Yoast SEO migration
 */
class YoastMigration {
    
    /**
     * Check if migration is available
     */
    public function is_available() {
        return class_exists('WPSEO_Options') || get_option('wpseo') !== false;
    }
    
    /**
     * Get migration name
     */
    public function get_name() {
        return __('Yoast SEO', 'wp-seo-pro');
    }
    
    /**
     * Get migration description
     */
    public function get_description() {
        return __('Migrate SEO data from Yoast SEO plugin', 'wp-seo-pro');
    }
    
    /**
     * Get migration version
     */
    public function get_version() {
        return '1.0.0';
    }
    
    /**
     * Get migration status
     */
    public function get_migration_status() {
        $migrated = get_option('wp_seo_pro_migrated_yoast', false);
        
        return array(
            'migrated' => $migrated,
            'migrated_date' => $migrated ? get_option('wp_seo_pro_migrated_yoast_date') : null,
        );
    }
    
    /**
     * Migrate data
     */
    public function migrate() {
        global $wpdb;
        
        $stats = array(
            'posts_processed' => 0,
            'meta_imported' => 0,
            'settings_imported' => 0,
        );
        
        try {
            // Migrate post meta
            $this->migrate_post_meta($stats);
            
            // Migrate settings
            $this->migrate_settings($stats);
            
            // Mark as migrated
            update_option('wp_seo_pro_migrated_yoast', true);
            update_option('wp_seo_pro_migrated_yoast_date', current_time('mysql'));
            
            return array(
                'success' => true,
                'message' => __('Yoast SEO data migrated successfully!', 'wp-seo-pro'),
                'stats' => $stats
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
    
    /**
     * Migrate post meta
     */
    private function migrate_post_meta(&$stats) {
        global $wpdb;
        
        // Get all posts with Yoast meta
        $posts = $wpdb->get_results("
            SELECT p.ID, p.post_type, p.post_title
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key LIKE '_yoast_wpseo_%'
            AND p.post_status = 'publish'
        ");
        
        foreach ($posts as $post) {
            $stats['posts_processed']++;
            
            // Migrate title
            $yoast_title = get_post_meta($post->ID, '_yoast_wpseo_title', true);
            if ($yoast_title) {
                update_post_meta($post->ID, '_wp_seo_pro_title', $yoast_title);
                $stats['meta_imported']++;
            }
            
            // Migrate description
            $yoast_desc = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
            if ($yoast_desc) {
                update_post_meta($post->ID, '_wp_seo_pro_description', $yoast_desc);
                $stats['meta_imported']++;
            }
            
            // Migrate keywords
            $yoast_keywords = get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);
            if ($yoast_keywords) {
                update_post_meta($post->ID, '_wp_seo_pro_keywords', $yoast_keywords);
                $stats['meta_imported']++;
            }
            
            // Migrate noindex
            $yoast_noindex = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true);
            if ($yoast_noindex === '1') {
                update_post_meta($post->ID, '_wp_seo_pro_noindex', true);
                $stats['meta_imported']++;
            }
            
            // Migrate nofollow
            $yoast_nofollow = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true);
            if ($yoast_nofollow === '1') {
                update_post_meta($post->ID, '_wp_seo_pro_nofollow', true);
                $stats['meta_imported']++;
            }
            
            // Migrate canonical
            $yoast_canonical = get_post_meta($post->ID, '_yoast_wpseo_canonical', true);
            if ($yoast_canonical) {
                update_post_meta($post->ID, '_wp_seo_pro_canonical', $yoast_canonical);
                $stats['meta_imported']++;
            }
            
            // Migrate social media
            $yoast_og_title = get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true);
            if ($yoast_og_title) {
                update_post_meta($post->ID, '_wp_seo_pro_og_title', $yoast_og_title);
                $stats['meta_imported']++;
            }
            
            $yoast_og_desc = get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true);
            if ($yoast_og_desc) {
                update_post_meta($post->ID, '_wp_seo_pro_og_description', $yoast_og_desc);
                $stats['meta_imported']++;
            }
            
            $yoast_og_image = get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true);
            if ($yoast_og_image) {
                update_post_meta($post->ID, '_wp_seo_pro_og_image', $yoast_og_image);
                $stats['meta_imported']++;
            }
        }
    }
    
    /**
     * Migrate settings
     */
    private function migrate_settings(&$stats) {
        $yoast_options = get_option('wpseo', array());
        
        if (empty($yoast_options)) {
            return;
        }
        
        $general_options = get_option('wp_seo_pro_general', array());
        
        // Migrate home title
        if (isset($yoast_options['title-home-wpseo'])) {
            $general_options['home_title'] = $yoast_options['title-home-wpseo'];
            $stats['settings_imported']++;
        }
        
        // Migrate home description
        if (isset($yoast_options['metadesc-home-wpseo'])) {
            $general_options['home_description'] = $yoast_options['metadesc-home-wpseo'];
            $stats['settings_imported']++;
        }
        
        // Migrate separator
        if (isset($yoast_options['separator'])) {
            $general_options['separator'] = $yoast_options['separator'];
            $stats['settings_imported']++;
        }
        
        update_option('wp_seo_pro_general', $general_options);
        
        // Migrate social settings
        $social_options = get_option('wp_seo_pro_social', array());
        
        if (isset($yoast_options['facebook_site'])) {
            $social_options['facebook_app_id'] = $yoast_options['facebook_site'];
            $stats['settings_imported']++;
        }
        
        if (isset($yoast_options['twitter_site'])) {
            $social_options['twitter_username'] = $yoast_options['twitter_site'];
            $stats['settings_imported']++;
        }
        
        update_option('wp_seo_pro_social', $social_options);
    }
}