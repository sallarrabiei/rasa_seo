<?php

namespace WP_SEO_Pro\Admin\Settings;

/**
 * General settings
 */
class GeneralSettings {
    
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
            'wp_seo_pro_general_section',
            __('General SEO Settings', 'wp-seo-pro'),
            array($this, 'section_callback'),
            'wp_seo_pro_general'
        );
        
        // Enable meta tags
        add_settings_field(
            'enable_meta_tags',
            __('Enable Meta Tags', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_general',
            'wp_seo_pro_general_section',
            array('field' => 'enable_meta_tags', 'description' => __('Enable automatic meta tag generation', 'wp-seo-pro'))
        );
        
        // Enable schema markup
        add_settings_field(
            'enable_schema',
            __('Enable Schema Markup', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_general',
            'wp_seo_pro_general_section',
            array('field' => 'enable_schema', 'description' => __('Enable structured data markup', 'wp-seo-pro'))
        );
        
        // Enable sitemap
        add_settings_field(
            'enable_sitemap',
            __('Enable XML Sitemap', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_general',
            'wp_seo_pro_general_section',
            array('field' => 'enable_sitemap', 'description' => __('Generate XML sitemap automatically', 'wp-seo-pro'))
        );
        
        // Title separator
        add_settings_field(
            'separator',
            __('Title Separator', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_general',
            'wp_seo_pro_general_section',
            array('field' => 'separator', 'description' => __('Character used to separate title parts', 'wp-seo-pro'))
        );
        
        // Home page title
        add_settings_field(
            'home_title',
            __('Home Page Title', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_general',
            'wp_seo_pro_general_section',
            array('field' => 'home_title', 'description' => __('Title for your homepage', 'wp-seo-pro'))
        );
        
        // Home page description
        add_settings_field(
            'home_description',
            __('Home Page Description', 'wp-seo-pro'),
            array($this, 'textarea_callback'),
            'wp_seo_pro_general',
            'wp_seo_pro_general_section',
            array('field' => 'home_description', 'description' => __('Meta description for your homepage', 'wp-seo-pro'))
        );
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>' . __('Configure your general SEO settings below.', 'wp-seo-pro') . '</p>';
    }
    
    /**
     * Checkbox callback
     */
    public function checkbox_callback($args) {
        $options = get_option('wp_seo_pro_general');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : false;
        
        echo '<input type="checkbox" id="' . $args['field'] . '" name="wp_seo_pro_general[' . $args['field'] . ']" value="1" ' . checked(1, $value, false) . ' />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Text callback
     */
    public function text_callback($args) {
        $options = get_option('wp_seo_pro_general');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        
        echo '<input type="text" id="' . $args['field'] . '" name="wp_seo_pro_general[' . $args['field'] . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Textarea callback
     */
    public function textarea_callback($args) {
        $options = get_option('wp_seo_pro_general');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        
        echo '<textarea id="' . $args['field'] . '" name="wp_seo_pro_general[' . $args['field'] . ']" rows="3" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
}