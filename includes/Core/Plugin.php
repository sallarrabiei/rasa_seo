<?php

namespace WP_SEO_Pro\Core;

use WP_SEO_Pro\Admin\Admin;
use WP_SEO_Pro\Frontend\Frontend;
use WP_SEO_Pro\Integrations\WooCommerce;
use WP_SEO_Pro\Integrations\EasyDigitalDownloads;
use WP_SEO_Pro\Analytics\Analytics;
use WP_SEO_Pro\Migrations\MigrationManager;

/**
 * Main plugin class
 */
class Plugin {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Admin instance
     */
    public $admin;
    
    /**
     * Frontend instance
     */
    public $frontend;
    
    /**
     * Analytics instance
     */
    public $analytics;
    
    /**
     * Migration manager instance
     */
    public $migration_manager;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->init_components();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('wp_head', array($this, 'output_meta_tags'), 1);
        add_action('wp_footer', array($this, 'output_schema_markup'));
    }
    
    /**
     * Initialize components
     */
    private function init_components() {
        // Initialize admin
        if (is_admin()) {
            $this->admin = new Admin();
        }
        
        // Initialize frontend
        $this->frontend = new Frontend();
        
        // Initialize analytics
        $this->analytics = new Analytics();
        
        // Initialize migration manager
        $this->migration_manager = new MigrationManager();
        
        // Initialize integrations
        if (class_exists('WooCommerce')) {
            new WooCommerce();
        }
        
        if (class_exists('Easy_Digital_Downloads')) {
            new EasyDigitalDownloads();
        }
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'wp-seo-pro',
            false,
            dirname(plugin_basename(WP_SEO_PRO_PLUGIN_FILE)) . '/languages'
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_scripts($hook) {
        if (strpos($hook, 'wp-seo-pro') === false) {
            return;
        }
        
        wp_enqueue_style(
            'wp-seo-pro-admin',
            WP_SEO_PRO_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            WP_SEO_PRO_VERSION
        );
        
        wp_enqueue_script(
            'wp-seo-pro-admin',
            WP_SEO_PRO_PLUGIN_URL . 'admin/js/admin.js',
            array('jquery', 'wp-util'),
            WP_SEO_PRO_VERSION,
            true
        );
        
        wp_localize_script('wp-seo-pro-admin', 'wpSeoPro', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_seo_pro_nonce'),
            'strings' => array(
                'confirmMigration' => __('Are you sure you want to migrate data? This action cannot be undone.', 'wp-seo-pro'),
                'migrationSuccess' => __('Migration completed successfully!', 'wp-seo-pro'),
                'migrationError' => __('Migration failed. Please try again.', 'wp-seo-pro'),
            )
        ));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function frontend_scripts() {
        wp_enqueue_style(
            'wp-seo-pro-frontend',
            WP_SEO_PRO_PLUGIN_URL . 'public/css/frontend.css',
            array(),
            WP_SEO_PRO_VERSION
        );
    }
    
    /**
     * Output meta tags in head
     */
    public function output_meta_tags() {
        if (is_admin()) {
            return;
        }
        
        $meta_tags = $this->frontend->get_meta_tags();
        
        if (!empty($meta_tags)) {
            echo "\n<!-- WP SEO Pro Meta Tags -->\n";
            echo $meta_tags;
            echo "\n<!-- /WP SEO Pro Meta Tags -->\n";
        }
    }
    
    /**
     * Output schema markup in footer
     */
    public function output_schema_markup() {
        if (is_admin()) {
            return;
        }
        
        $schema = $this->frontend->get_schema_markup();
        
        if (!empty($schema)) {
            echo "\n<!-- WP SEO Pro Schema Markup -->\n";
            echo '<script type="application/ld+json">' . $schema . '</script>';
            echo "\n<!-- /WP SEO Pro Schema Markup -->\n";
        }
    }
}