<?php

namespace WP_SEO_Pro\Migrations;

/**
 * Rank Math migration
 */
class RankMathMigration {
    
    /**
     * Check if migration is available
     */
    public function is_available() {
        return class_exists('RankMath') || get_option('rank_math_options') !== false;
    }
    
    /**
     * Get migration name
     */
    public function get_name() {
        return __('Rank Math', 'wp-seo-pro');
    }
    
    /**
     * Get migration description
     */
    public function get_description() {
        return __('Migrate SEO data from Rank Math plugin', 'wp-seo-pro');
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
        $migrated = get_option('wp_seo_pro_migrated_rankmath', false);
        
        return array(
            'migrated' => $migrated,
            'migrated_date' => $migrated ? get_option('wp_seo_pro_migrated_rankmath_date') : null,
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
            update_option('wp_seo_pro_migrated_rankmath', true);
            update_option('wp_seo_pro_migrated_rankmath_date', current_time('mysql'));
            
            return array(
                'success' => true,
                'message' => __('Rank Math data migrated successfully!', 'wp-seo-pro'),
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
        
        // Get all posts with Rank Math meta
        $posts = $wpdb->get_results("
            SELECT p.ID, p.post_type, p.post_title
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key LIKE 'rank_math_%'
            AND p.post_status = 'publish'
        ");
        
        foreach ($posts as $post) {
            $stats['posts_processed']++;
            
            // Migrate title
            $rm_title = get_post_meta($post->ID, 'rank_math_title', true);
            if ($rm_title) {
                update_post_meta($post->ID, '_wp_seo_pro_title', $rm_title);
                $stats['meta_imported']++;
            }
            
            // Migrate description
            $rm_desc = get_post_meta($post->ID, 'rank_math_description', true);
            if ($rm_desc) {
                update_post_meta($post->ID, '_wp_seo_pro_description', $rm_desc);
                $stats['meta_imported']++;
            }
            
            // Migrate keywords
            $rm_keywords = get_post_meta($post->ID, 'rank_math_focus_keyword', true);
            if ($rm_keywords) {
                update_post_meta($post->ID, '_wp_seo_pro_keywords', $rm_keywords);
                $stats['meta_imported']++;
            }
            
            // Migrate noindex
            $rm_noindex = get_post_meta($post->ID, 'rank_math_robots', true);
            if (is_array($rm_noindex) && in_array('noindex', $rm_noindex)) {
                update_post_meta($post->ID, '_wp_seo_pro_noindex', true);
                $stats['meta_imported']++;
            }
            
            // Migrate nofollow
            if (is_array($rm_noindex) && in_array('nofollow', $rm_noindex)) {
                update_post_meta($post->ID, '_wp_seo_pro_nofollow', true);
                $stats['meta_imported']++;
            }
            
            // Migrate canonical
            $rm_canonical = get_post_meta($post->ID, 'rank_math_canonical_url', true);
            if ($rm_canonical) {
                update_post_meta($post->ID, '_wp_seo_pro_canonical', $rm_canonical);
                $stats['meta_imported']++;
            }
            
            // Migrate social media
            $rm_og_title = get_post_meta($post->ID, 'rank_math_facebook_title', true);
            if ($rm_og_title) {
                update_post_meta($post->ID, '_wp_seo_pro_og_title', $rm_og_title);
                $stats['meta_imported']++;
            }
            
            $rm_og_desc = get_post_meta($post->ID, 'rank_math_facebook_description', true);
            if ($rm_og_desc) {
                update_post_meta($post->ID, '_wp_seo_pro_og_description', $rm_og_desc);
                $stats['meta_imported']++;
            }
            
            $rm_og_image = get_post_meta($post->ID, 'rank_math_facebook_image', true);
            if ($rm_og_image) {
                update_post_meta($post->ID, '_wp_seo_pro_og_image', $rm_og_image);
                $stats['meta_imported']++;
            }
        }
    }
    
    /**
     * Migrate settings
     */
    private function migrate_settings(&$stats) {
        $rm_options = get_option('rank_math_options', array());
        
        if (empty($rm_options)) {
            return;
        }
        
        $general_options = get_option('wp_seo_pro_general', array());
        
        // Migrate home title
        if (isset($rm_options['titles']['homepage_title'])) {
            $general_options['home_title'] = $rm_options['titles']['homepage_title'];
            $stats['settings_imported']++;
        }
        
        // Migrate home description
        if (isset($rm_options['titles']['homepage_description'])) {
            $general_options['home_description'] = $rm_options['titles']['homepage_description'];
            $stats['settings_imported']++;
        }
        
        // Migrate separator
        if (isset($rm_options['titles']['title_separator'])) {
            $general_options['separator'] = $rm_options['titles']['title_separator'];
            $stats['settings_imported']++;
        }
        
        update_option('wp_seo_pro_general', $general_options);
        
        // Migrate social settings
        $social_options = get_option('wp_seo_pro_social', array());
        
        if (isset($rm_options['titles']['social_url_facebook'])) {
            $social_options['facebook_app_id'] = $rm_options['titles']['social_url_facebook'];
            $stats['settings_imported']++;
        }
        
        if (isset($rm_options['titles']['social_url_twitter'])) {
            $social_options['twitter_username'] = $rm_options['titles']['social_url_twitter'];
            $stats['settings_imported']++;
        }
        
        update_option('wp_seo_pro_social', $social_options);
    }
}