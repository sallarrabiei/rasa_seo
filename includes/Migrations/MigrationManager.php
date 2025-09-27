<?php

namespace WP_SEO_Pro\Migrations;

use WP_SEO_Pro\Migrations\YoastMigration;
use WP_SEO_Pro\Migrations\AllInOneMigration;
use WP_SEO_Pro\Migrations\RankMathMigration;

/**
 * Migration manager
 */
class MigrationManager {
    
    /**
     * Available migrations
     */
    private $migrations = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_migrations();
        $this->init_hooks();
    }
    
    /**
     * Initialize migrations
     */
    private function init_migrations() {
        $this->migrations = array(
            'yoast' => new YoastMigration(),
            'all_in_one_seo' => new AllInOneMigration(),
            'rank_math' => new RankMathMigration(),
        );
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_ajax_wp_seo_pro_migrate', array($this, 'handle_migration'));
        add_action('wp_ajax_wp_seo_pro_check_migration', array($this, 'check_migration_status'));
    }
    
    /**
     * Get available migrations
     */
    public function get_available_migrations() {
        $available = array();
        
        foreach ($this->migrations as $key => $migration) {
            if ($migration->is_available()) {
                $available[$key] = array(
                    'name' => $migration->get_name(),
                    'description' => $migration->get_description(),
                    'version' => $migration->get_version(),
                );
            }
        }
        
        return $available;
    }
    
    /**
     * Handle migration request
     */
    public function handle_migration() {
        check_ajax_referer('wp_seo_pro_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wp-seo-pro'));
        }
        
        $migration_type = sanitize_text_field($_POST['migration_type']);
        
        if (!isset($this->migrations[$migration_type])) {
            wp_send_json_error(__('Invalid migration type.', 'wp-seo-pro'));
        }
        
        $migration = $this->migrations[$migration_type];
        
        if (!$migration->is_available()) {
            wp_send_json_error(__('Migration source not available.', 'wp-seo-pro'));
        }
        
        try {
            $result = $migration->migrate();
            
            if ($result['success']) {
                wp_send_json_success(array(
                    'message' => $result['message'],
                    'stats' => $result['stats']
                ));
            } else {
                wp_send_json_error($result['message']);
            }
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
    
    /**
     * Check migration status
     */
    public function check_migration_status() {
        check_ajax_referer('wp_seo_pro_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wp-seo-pro'));
        }
        
        $migration_type = sanitize_text_field($_POST['migration_type']);
        
        if (!isset($this->migrations[$migration_type])) {
            wp_send_json_error(__('Invalid migration type.', 'wp-seo-pro'));
        }
        
        $migration = $this->migrations[$migration_type];
        $status = $migration->get_migration_status();
        
        wp_send_json_success($status);
    }
}