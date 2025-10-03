<?php

namespace WP_SEO_Pro\Migrations;

/**
 * All in One SEO migration
 */
class AllInOneMigration {
    
    /**
     * Check if migration is available
     */
    public function is_available() {
        return class_exists('AIOSEO') || get_option('aioseo_options') !== false;
    }
    
    /**
     * Get migration name
     */
    public function get_name() {
        return __('All in One SEO Pack', 'wp-seo-pro');
    }
    
    /**
     * Get migration description
     */
    public function get_description() {
        return __('Migrate SEO data from All in One SEO Pack plugin', 'wp-seo-pro');
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
        $migrated = get_option('wp_seo_pro_migrated_aioseo', false);
        
        return array(
            'migrated' => $migrated,
            'migrated_date' => $migrated ? get_option('wp_seo_pro_migrated_aioseo_date') : null,
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
            update_option('wp_seo_pro_migrated_aioseo', true);
            update_option('wp_seo_pro_migrated_aioseo_date', current_time('mysql'));
            
            return array(
                'success' => true,
                'message' => __('All in One SEO data migrated successfully!', 'wp-seo-pro'),
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
        
        // Get all posts with AIOSEO meta
        $posts = $wpdb->get_results("
            SELECT p.ID, p.post_type, p.post_title
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key LIKE '_aioseo_%'
            AND p.post_status = 'publish'
        ");
        
        foreach ($posts as $post) {
            $stats['posts_processed']++;
            
            // Migrate title
            $aioseo_title = get_post_meta($post->ID, '_aioseo_title', true);
            if ($aioseo_title) {
                update_post_meta($post->ID, '_wp_seo_pro_title', $aioseo_title);
                $stats['meta_imported']++;
            }
            
            // Migrate description
            $aioseo_desc = get_post_meta($post->ID, '_aioseo_description', true);
            if ($aioseo_desc) {
                update_post_meta($post->ID, '_wp_seo_pro_description', $aioseo_desc);
                $stats['meta_imported']++;
            }
            
            // Migrate keywords
            $aioseo_keywords = get_post_meta($post->ID, '_aioseo_keywords', true);
            if ($aioseo_keywords) {
                update_post_meta($post->ID, '_wp_seo_pro_keywords', $aioseo_keywords);
                $stats['meta_imported']++;
            }
            
            // Migrate noindex
            $aioseo_noindex = get_post_meta($post->ID, '_aioseo_noindex', true);
            if ($aioseo_noindex === 'on') {
                update_post_meta($post->ID, '_wp_seo_pro_noindex', true);
                $stats['meta_imported']++;
            }
            
            // Migrate nofollow
            $aioseo_nofollow = get_post_meta($post->ID, '_aioseo_nofollow', true);
            if ($aioseo_nofollow === 'on') {
                update_post_meta($post->ID, '_wp_seo_pro_nofollow', true);
                $stats['meta_imported']++;
            }
            
            // Migrate canonical
            $aioseo_canonical = get_post_meta($post->ID, '_aioseo_canonical_url', true);
            if ($aioseo_canonical) {
                update_post_meta($post->ID, '_wp_seo_pro_canonical', $aioseo_canonical);
                $stats['meta_imported']++;
            }
        }
    }
    
    /**
     * Migrate settings
     */
    private function migrate_settings(&$stats) {
        $aioseo_options = get_option('aioseo_options', array());
        
        if (empty($aioseo_options)) {
            return;
        }
        
        $general_options = get_option('wp_seo_pro_general', array());
        
        // Migrate home title
        if (isset($aioseo_options['homePage']['title'])) {
            $general_options['home_title'] = $aioseo_options['homePage']['title'];
            $stats['settings_imported']++;
        }
        
        // Migrate home description
        if (isset($aioseo_options['homePage']['description'])) {
            $general_options['home_description'] = $aioseo_options['homePage']['description'];
            $stats['settings_imported']++;
        }
        
        update_option('wp_seo_pro_general', $general_options);
    }
}