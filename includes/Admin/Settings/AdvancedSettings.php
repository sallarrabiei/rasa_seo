<?php

namespace WP_SEO_Pro\Admin\Settings;

/**
 * Advanced settings
 */
class AdvancedSettings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        add_settings_section(
            'wp_seo_pro_advanced_section',
            __('Advanced SEO Settings', 'wp-seo-pro'),
            array($this, 'section_callback'),
            'wp_seo_pro_advanced'
        );
        
        // Noindex archive pages
        add_settings_field(
            'noindex_archive',
            __('Noindex Archive Pages', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_advanced',
            'wp_seo_pro_advanced_section',
            array('field' => 'noindex_archive', 'description' => __('Add noindex to archive pages', 'wp-seo-pro'))
        );
        
        // Noindex search pages
        add_settings_field(
            'noindex_search',
            __('Noindex Search Pages', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_advanced',
            'wp_seo_pro_advanced_section',
            array('field' => 'noindex_search', 'description' => __('Add noindex to search result pages', 'wp-seo-pro'))
        );
        
        // Noindex 404 pages
        add_settings_field(
            'noindex_404',
            __('Noindex 404 Pages', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_advanced',
            'wp_seo_pro_advanced_section',
            array('field' => 'noindex_404', 'description' => __('Add noindex to 404 error pages', 'wp-seo-pro'))
        );
        
        // Remove WordPress generator
        add_settings_field(
            'remove_wp_generator',
            __('Remove WordPress Generator', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_advanced',
            'wp_seo_pro_advanced_section',
            array('field' => 'remove_wp_generator', 'description' => __('Remove WordPress version from head', 'wp-seo-pro'))
        );
        
        // Remove RSD link
        add_settings_field(
            'remove_rsd_link',
            __('Remove RSD Link', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_advanced',
            'wp_seo_pro_advanced_section',
            array('field' => 'remove_rsd_link', 'description' => __('Remove Really Simple Discovery link', 'wp-seo-pro'))
        );
        
        // Remove WLW manifest
        add_settings_field(
            'remove_wlwmanifest_link',
            __('Remove WLW Manifest', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_advanced',
            'wp_seo_pro_advanced_section',
            array('field' => 'remove_wlwmanifest_link', 'description' => __('Remove Windows Live Writer manifest link', 'wp-seo-pro'))
        );
        
        // Remove shortlink
        add_settings_field(
            'remove_shortlink',
            __('Remove Shortlink', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_advanced',
            'wp_seo_pro_advanced_section',
            array('field' => 'remove_shortlink', 'description' => __('Remove WordPress shortlink', 'wp-seo-pro'))
        );
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>' . __('Advanced settings for fine-tuning your SEO configuration.', 'wp-seo-pro') . '</p>';
    }
    
    /**
     * Checkbox callback
     */
    public function checkbox_callback($args) {
        $options = get_option('wp_seo_pro_advanced');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : false;
        
        echo '<input type="checkbox" id="' . $args['field'] . '" name="wp_seo_pro_advanced[' . $args['field'] . ']" value="1" ' . checked(1, $value, false) . ' />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
}