<?php

namespace WP_SEO_Pro\Admin\Settings;

/**
 * Easy Digital Downloads settings
 */
class EDDSettings {
    
    /**
     * Constructor
     */
    public function __construct() {
        if (!class_exists('Easy_Digital_Downloads')) {
            return;
        }
        
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        add_settings_section(
            'wp_seo_pro_edd_section',
            __('Easy Digital Downloads SEO Settings', 'wp-seo-pro'),
            array($this, 'section_callback'),
            'wp_seo_pro_edd'
        );
        
        // Enable download schema
        add_settings_field(
            'enable_download_schema',
            __('Enable Download Schema', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_edd',
            'wp_seo_pro_edd_section',
            array('field' => 'enable_download_schema', 'description' => __('Add structured data for downloads', 'wp-seo-pro'))
        );
        
        // Download title template
        add_settings_field(
            'download_title_template',
            __('Download Title Template', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_edd',
            'wp_seo_pro_edd_section',
            array('field' => 'download_title_template', 'description' => __('Template for download titles. Use {download_name} and {site_name}', 'wp-seo-pro'))
        );
        
        // Download description template
        add_settings_field(
            'download_description_template',
            __('Download Description Template', 'wp-seo-pro'),
            array($this, 'textarea_callback'),
            'wp_seo_pro_edd',
            'wp_seo_pro_edd_section',
            array('field' => 'download_description_template', 'description' => __('Template for download descriptions. Use {download_excerpt}', 'wp-seo-pro'))
        );
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>' . __('Configure SEO settings specifically for Easy Digital Downloads.', 'wp-seo-pro') . '</p>';
    }
    
    /**
     * Checkbox callback
     */
    public function checkbox_callback($args) {
        $options = get_option('wp_seo_pro_edd');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : true;
        
        echo '<input type="checkbox" id="' . $args['field'] . '" name="wp_seo_pro_edd[' . $args['field'] . ']" value="1" ' . checked(1, $value, false) . ' />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Text callback
     */
    public function text_callback($args) {
        $options = get_option('wp_seo_pro_edd');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        
        echo '<input type="text" id="' . $args['field'] . '" name="wp_seo_pro_edd[' . $args['field'] . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Textarea callback
     */
    public function textarea_callback($args) {
        $options = get_option('wp_seo_pro_edd');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        
        echo '<textarea id="' . $args['field'] . '" name="wp_seo_pro_edd[' . $args['field'] . ']" rows="3" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
}