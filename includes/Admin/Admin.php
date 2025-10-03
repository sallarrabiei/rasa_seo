<?php

namespace WP_SEO_Pro\Admin;

use WP_SEO_Pro\Admin\Settings\GeneralSettings;
use WP_SEO_Pro\Admin\Settings\SocialSettings;
use WP_SEO_Pro\Admin\Settings\AdvancedSettings;
use WP_SEO_Pro\Admin\Settings\SitemapSettings;
use WP_SEO_Pro\Admin\Settings\WooCommerceSettings;
use WP_SEO_Pro\Admin\Settings\EDDSettings;
use WP_SEO_Pro\Admin\Migration\MigrationPage;
use WP_SEO_Pro\Admin\Analytics\AnalyticsPage;

/**
 * Admin functionality
 */
class Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
        $this->init_settings();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * Initialize settings classes
     */
    private function init_settings() {
        new GeneralSettings();
        new SocialSettings();
        new AdvancedSettings();
        new SitemapSettings();
        new WooCommerceSettings();
        new EDDSettings();
        new MigrationPage();
        new AnalyticsPage();
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('WP SEO Pro', 'wp-seo-pro'),
            __('SEO Pro', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro',
            array($this, 'admin_page'),
            'dashicons-search',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'wp-seo-pro',
            __('Dashboard', 'wp-seo-pro'),
            __('Dashboard', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro',
            array($this, 'admin_page')
        );
        
        // General Settings
        add_submenu_page(
            'wp-seo-pro',
            __('General Settings', 'wp-seo-pro'),
            __('General', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro-general',
            array($this, 'general_settings_page')
        );
        
        // Social Settings
        add_submenu_page(
            'wp-seo-pro',
            __('Social Settings', 'wp-seo-pro'),
            __('Social', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro-social',
            array($this, 'social_settings_page')
        );
        
        // Advanced Settings
        add_submenu_page(
            'wp-seo-pro',
            __('Advanced Settings', 'wp-seo-pro'),
            __('Advanced', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro-advanced',
            array($this, 'advanced_settings_page')
        );
        
        // Sitemap Settings
        add_submenu_page(
            'wp-seo-pro',
            __('Sitemap Settings', 'wp-seo-pro'),
            __('Sitemap', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro-sitemap',
            array($this, 'sitemap_settings_page')
        );
        
        // WooCommerce Settings
        if (class_exists('WooCommerce')) {
            add_submenu_page(
                'wp-seo-pro',
                __('WooCommerce Settings', 'wp-seo-pro'),
                __('WooCommerce', 'wp-seo-pro'),
                'manage_options',
                'wp-seo-pro-woocommerce',
                array($this, 'woocommerce_settings_page')
            );
        }
        
        // EDD Settings
        if (class_exists('Easy_Digital_Downloads')) {
            add_submenu_page(
                'wp-seo-pro',
                __('EDD Settings', 'wp-seo-pro'),
                __('EDD', 'wp-seo-pro'),
                'manage_options',
                'wp-seo-pro-edd',
                array($this, 'edd_settings_page')
            );
        }
        
        // Migration
        add_submenu_page(
            'wp-seo-pro',
            __('Migration', 'wp-seo-pro'),
            __('Migration', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro-migration',
            array($this, 'migration_page')
        );
        
        // Analytics
        add_submenu_page(
            'wp-seo-pro',
            __('Analytics', 'wp-seo-pro'),
            __('Analytics', 'wp-seo-pro'),
            'manage_options',
            'wp-seo-pro-analytics',
            array($this, 'analytics_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('wp_seo_pro_general', 'wp_seo_pro_general');
        register_setting('wp_seo_pro_social', 'wp_seo_pro_social');
        register_setting('wp_seo_pro_advanced', 'wp_seo_pro_advanced');
        register_setting('wp_seo_pro_sitemap', 'wp_seo_pro_sitemap');
        register_setting('wp_seo_pro_woocommerce', 'wp_seo_pro_woocommerce');
        register_setting('wp_seo_pro_edd', 'wp_seo_pro_edd');
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'wp-seo-pro') === false) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        if (get_transient('wp_seo_pro_activated')) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . __('WP SEO Pro has been activated successfully!', 'wp-seo-pro') . '</p>';
            echo '</div>';
            delete_transient('wp_seo_pro_activated');
        }
    }
    
    /**
     * Main admin page
     */
    public function admin_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * General settings page
     */
    public function general_settings_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/general-settings.php';
    }
    
    /**
     * Social settings page
     */
    public function social_settings_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/social-settings.php';
    }
    
    /**
     * Advanced settings page
     */
    public function advanced_settings_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/advanced-settings.php';
    }
    
    /**
     * Sitemap settings page
     */
    public function sitemap_settings_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/sitemap-settings.php';
    }
    
    /**
     * WooCommerce settings page
     */
    public function woocommerce_settings_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/woocommerce-settings.php';
    }
    
    /**
     * EDD settings page
     */
    public function edd_settings_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/edd-settings.php';
    }
    
    /**
     * Migration page
     */
    public function migration_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/migration.php';
    }
    
    /**
     * Analytics page
     */
    public function analytics_page() {
        include WP_SEO_PRO_PLUGIN_DIR . 'admin/views/analytics.php';
    }
}